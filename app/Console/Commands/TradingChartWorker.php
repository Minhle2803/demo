<?php

namespace App\Console\Commands;

use App\Models\TradingChartCandle;
use App\Services\TradingChart\CandleGeneratorService;
use App\Services\TradingChart\ChartBroadcastService;
use App\Services\TradingChart\ChartRuleService;
use App\Services\TradingChart\ChartSummaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * TradingChartWorker
 *
 * Long-running Artisan command managed by Supervisor.
 * Generates and broadcasts fake K-line candle data continuously.
 *
 * Usage:
 *   php artisan chart:worker
 *   php artisan chart:worker --symbols=BTC_USDT,ETH_USDT --intervals=1m
 *
 * Design principles:
 *   - Zero in-memory state between ticks. Every tick reads from DB.
 *   - Safe to kill and restart at any time — resumes from latest DB candle.
 *   - One tick loop handles all symbol × interval pairs.
 *   - All business logic is delegated to services — this command only orchestrates.
 */
class TradingChartWorker extends Command
{
    protected $signature = 'chart:worker
        {--symbols=  : Comma-separated symbols to process. Defaults to config. }
        {--intervals= : Comma-separated intervals to process. Defaults to config. }
        {--tick=5    : Tick interval in seconds. }';

    protected $description = 'Long-running worker that generates fake trading chart candle data continuously.';

    // -------------------------------------------------------------------------
    // Interval → milliseconds map
    // -------------------------------------------------------------------------

    private const INTERVAL_MS = [
        '1m'  =>     60_000,
        '5m'  =>    300_000,
        '15m' =>    900_000,
        '1h'  =>  3_600_000,
        '4h'  => 14_400_000,
        '1d'  => 86_400_000,
    ];

    // -------------------------------------------------------------------------
    // Constructor DI
    // -------------------------------------------------------------------------

    public function __construct(
        private readonly CandleGeneratorService $generator,
        private readonly ChartBroadcastService  $broadcaster,
        private readonly ChartRuleService       $ruleService,
        private readonly ChartSummaryService $summaryService,
    ) {
        parent::__construct();
    }

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

    public function handle(): int
    {
        $symbols   = $this->resolveSymbols();
        $intervals = $this->resolveIntervals();
        $tickSec   = (int) $this->option('tick');
        $tickMs    = $tickSec * 1_000_000; // microseconds for usleep()

        $this->info('[chart:worker] Starting.');
        $this->info('[chart:worker] Symbols   : ' . implode(', ', $symbols));
        $this->info('[chart:worker] Intervals : ' . implode(', ', $intervals));
        $this->info('[chart:worker] Tick every : ' . $tickSec . 's');

        $pairs = $this->buildPairs($symbols, $intervals);

        // Infinite loop — Supervisor restarts the process if it dies.
        while (true) {
            $tickStart = microtime(true);

            foreach ($pairs as ['symbol' => $symbol, 'interval' => $interval]) {
                $this->processPair($symbol, $interval);
            }

            // Sleep for the remainder of the tick window.
            // Accounts for processing time so ticks stay on schedule.
            $elapsed  = microtime(true) - $tickStart;
            $sleepSec = max(0.0, $tickSec - $elapsed);

            $this->sleepPrecise($sleepSec);
        }

        // Unreachable — satisfies return type.
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Per-pair tick
    // -------------------------------------------------------------------------

    /**
     * Process one symbol × interval pair for this tick.
     *
     * Steps:
     *   1. Compute the canonical interval timestamp for right now.
     *   2. Load the latest candle from DB (no in-memory state).
     *   3. If the open candle belongs to a past interval → close it, create new.
     *   4. If the open candle belongs to the current interval → tick it.
     *   5. If no candle exists at all → create the first one from config seed price.
     *   6. Broadcast the updated candle.
     */
    private function processPair(string $symbol, string $interval): void
    {
        try {
            $intervalMs       = self::INTERVAL_MS[$interval];
            $nowMs            = $this->nowMs();
            $currentIntervalTs = $this->floorToInterval($nowMs, $intervalMs);

            // All DB reads and writes for this pair are wrapped in a transaction
            // to prevent a partial state if the process is killed mid-tick.
            DB::transaction(function () use (
                $symbol, $interval, $intervalMs, $currentIntervalTs
            ): void {
                $openCandle = TradingChartCandle::currentOpenFor($symbol, $interval);

                if ($openCandle === null) {
                    // No open candle at all — either first run or all candles are closed.
                    $this->handleNoOpenCandle($symbol, $interval, $currentIntervalTs);
                    return;
                }

                if ((int) $openCandle->timestamp < $currentIntervalTs) {
                    // The open candle belongs to a past interval.
                    // Close it and open a new one for the current interval.
                    $this->handleIntervalBoundary($openCandle, $symbol, $interval, $currentIntervalTs);
                    return;
                }

                // The open candle is current — tick it.
                $this->handleCurrentTick($openCandle, $symbol, $interval);
            });

        } catch (Throwable $e) {
            // Log and continue — a single pair failure must never crash the worker.
            Log::error('[chart:worker] Pair error', [
                'symbol'    => $symbol,
                'interval'  => $interval,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            $this->warn("[chart:worker] Error on {$symbol}/{$interval}: {$e->getMessage()}");
        }
    }

    // -------------------------------------------------------------------------
    // Tick handlers — three mutually exclusive states
    // -------------------------------------------------------------------------

    /**
     * State 1: No open candle exists.
     *
     * Happens on first run or after all candles were somehow closed.
     * Resume point: read the latest CLOSED candle to get a previousClose price.
     * If no closed candle either → use the configured seed price for the symbol.
     */
    private function handleNoOpenCandle(
        string $symbol,
        string $interval,
        int    $currentIntervalTs,
    ): void {
        $latestClosed = TradingChartCandle::forPair($symbol, $interval)
            ->closed()
            ->latestFirst()
            ->first();

        $previousClose = $latestClosed
            ? (string) $latestClosed->close
            : $this->seedPrice($symbol);

        [$direction, $biasPct] = $this->ruleService->getActiveDirectionAndBias(
            $symbol, $interval, $currentIntervalTs,
        );

        $newCandle = $this->generator->generateOpenCandle(
            symbol:        $symbol,
            interval:      $interval,
            timestamp:     $currentIntervalTs,
            previousClose: $previousClose,
            direction:     $direction,
            biasPct:       $biasPct,
        );
        // TODO: Summary update only needs the final closed candle, can be optimized to not recalc summary on every tick
        $this->summaryService->applyCandle($newCandle);

        $this->broadcaster->broadcastUpdate($newCandle);

        $this->line("[chart:worker] [{$symbol}/{$interval}] Opened first candle @ {$currentIntervalTs}");
    }

    /**
     * State 2: The open candle belongs to a past interval → boundary crossed.
     *
     * Actions:
     *   a) Close the stale open candle.
     *   b) Fill any gap intervals between the stale candle and now with closed candles.
     *   c) Open a fresh candle for the current interval.
     */
    private function handleIntervalBoundary(
        TradingChartCandle $staleCandle,
        string             $symbol,
        string             $interval,
        int                $currentIntervalTs,
    ): void {
        $intervalMs = self::INTERVAL_MS[$interval];

        // a) Close the stale open candle.
        $closed = $this->generator->closeCandle($staleCandle);
        // TODO: Summary update only needs the final closed candle, can be optimized to not recalc summary on every tick
        $this->summaryService->applyCandle($closed);
        $this->broadcaster->broadcastClose($closed);

        $this->line("[chart:worker] [{$symbol}/{$interval}] Closed candle @ {$staleCandle->timestamp}");

        // b) Fill any gap intervals that were missed (e.g. worker was down for several intervals).
        $previousClose = (string) $closed->close;
        $gapStart      = (int) $staleCandle->timestamp + $intervalMs;

        while ($gapStart < $currentIntervalTs) {
            [$direction, $biasPct] = $this->ruleService->getActiveDirectionAndBias(
                $symbol, $interval, $gapStart,
            );

            $gapCandle = $this->generator->generateOpenCandle(
                symbol:        $symbol,
                interval:      $interval,
                timestamp:     $gapStart,
                previousClose: $previousClose,
                direction:     $direction,
                biasPct:       $biasPct,
            );

            $gapCandle     = $this->generator->closeCandle($gapCandle);
            $previousClose = (string) $gapCandle->close;
            $gapStart     += $intervalMs;

            $this->line("[chart:worker] [{$symbol}/{$interval}] Filled gap candle @ {$gapCandle->timestamp}");
        }

        // c) Open the current interval candle.
        [$direction, $biasPct] = $this->ruleService->getActiveDirectionAndBias(
            $symbol, $interval, $currentIntervalTs,
        );

        $newCandle = $this->generator->generateOpenCandle(
            symbol:        $symbol,
            interval:      $interval,
            timestamp:     $currentIntervalTs,
            previousClose: $previousClose,
            direction:     $direction,
            biasPct:       $biasPct,
        );
        // TODO: Summary update only needs the final closed candle, can be optimized to not recalc summary on every tick
        $this->summaryService->applyCandle($newCandle);

        $this->broadcaster->broadcastUpdate($newCandle);

        $this->line("[chart:worker] [{$symbol}/{$interval}] Opened new candle @ {$currentIntervalTs}");
    }

    /**
     * State 3: The open candle is current → normal tick, update close/high/low.
     */
    private function handleCurrentTick(
        TradingChartCandle $openCandle,
        string             $symbol,
        string             $interval,
    ): void {
        [$direction, $biasPct] = $this->ruleService->getActiveDirectionAndBias(
            $symbol, $interval, (int) $openCandle->timestamp,
        );

        $ticked = $this->generator->tickOpenCandle($openCandle, $direction, $biasPct);
        //TODO: SUmmary update only needs the final closed candle, can be optimized to not recalc summary on every tick
        $this->summaryService->applyCandle($ticked);
        $this->broadcaster->broadcastUpdate($ticked);
    }

    // -------------------------------------------------------------------------
    // Interval boundary math
    // -------------------------------------------------------------------------

    /**
     * Floor a millisecond timestamp to the nearest interval boundary.
     *
     * Example for 1m (60_000 ms):
     *   nowMs = 1_710_000_037_500
     *   floor = 1_710_000_037_500 - (1_710_000_037_500 % 60_000)
     *         = 1_710_000_000_000   ← start of the current 1m candle
     *
     * This is the canonical timestamp for any candle — deterministic from wall clock.
     * No stored state is required to know which interval is current.
     */
    private function floorToInterval(int $nowMs, int $intervalMs): int
    {
        return intdiv($nowMs, $intervalMs) * $intervalMs;
    }

    /**
     * Current wall clock time in milliseconds.
     */
    private function nowMs(): int
    {
        return (int) (microtime(true) * 1000);
    }

    // -------------------------------------------------------------------------
    // Sleep
    // -------------------------------------------------------------------------

    /**
     * Sleep for a fractional number of seconds using microsecond precision.
     * Prevents tick drift accumulating over time.
     */
    private function sleepPrecise(float $seconds): void
    {
        if ($seconds <= 0) {
            return;
        }

        usleep((int) ($seconds * 1_000_000));
    }

    // -------------------------------------------------------------------------
    // Configuration helpers
    // -------------------------------------------------------------------------

    /**
     * Build all symbol × interval pairs to process.
     *
     * @return array<int, array{symbol: string, interval: string}>
     */
    private function buildPairs(array $symbols, array $intervals): array
    {
        $pairs = [];

        foreach ($symbols as $symbol) {
            foreach ($intervals as $interval) {
                $pairs[] = ['symbol' => $symbol, 'interval' => $interval];
            }
        }

        return $pairs;
    }

    /**
     * Resolve symbols from --symbols option or config fallback.
     */
    private function resolveSymbols(): array
    {
        $opt = $this->option('symbols');

        if ($opt) {
            return array_map('trim', explode(',', $opt));
        }

        return config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']);
    }

    /**
     * Resolve intervals from --intervals option or config fallback.
     */
    private function resolveIntervals(): array
    {
        $opt = $this->option('intervals');

        if ($opt) {
            return array_map('trim', explode(',', $opt));
        }

        return config('trading_chart.intervals', ['1m', '5m']);
    }

    /**
     * Seed price for a symbol — used when no candle exists yet in the DB.
     * Falls back to 1.0 if symbol is not configured (safe default).
     */
    private function seedPrice(string $symbol): string
    {
        $prices = config('trading_chart.initial_prices', [
            'BTC_USDT' => '60000',
            'ETH_USDT' => '3000',
            'SOL_USDT' => '150',
        ]);

        return (string) ($prices[$symbol] ?? '1.0');
    }
}

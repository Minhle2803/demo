<?php

namespace App\Console\Commands;

use App\Models\TradingChartCandle;
use App\Services\TradingChart\CandleGeneratorService;
use Illuminate\Console\Command;

/**
 * SeedTradingChartCandles
 *
 * Generates historical closed candles for all configured symbol × interval pairs.
 * Safe to re-run — uses upsert so existing candles are never duplicated.
 *
 * Usage:
 *   php artisan chart:seed
 *   php artisan chart:seed --count=1000
 *   php artisan chart:seed --symbols=BTC_USDT --intervals=1m
 *   php artisan chart:seed --fresh   ← truncate first, then seed
 */
class SeedTradingChartCandles extends Command
{
    protected $signature = 'chart:seed
        {--symbols=   : Comma-separated symbols. Defaults to config. }
        {--intervals= : Comma-separated intervals. Defaults to config. }
        {--count=500  : Number of candles to generate per pair. }
        {--fresh      : Truncate trading_chart_candles before seeding. }';

    protected $description = 'Seed historical fake candle data for all symbol × interval pairs.';

    // -------------------------------------------------------------------------
    // Interval → milliseconds
    // -------------------------------------------------------------------------

    private const INTERVAL_MS = [
        '1m' => 60_000,
        '5m' => 300_000,
        '15m' => 900_000,
        '1h' => 3_600_000,
        '4h' => 14_400_000,
        '1d' => 86_400_000,
    ];

    public function __construct(private readonly CandleGeneratorService $generator)
    {
        parent::__construct();
    }

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

    public function handle(): int
    {
        $symbols = $this->resolveSymbols();
        $intervals = $this->resolveIntervals();
        $count = max(1, (int) $this->option('count'));

        $this->info('chart:seed starting');
        $this->info('Symbols   : '.implode(', ', $symbols));
        $this->info('Intervals : '.implode(', ', $intervals));
        $this->info("Count     : {$count} candles per pair");

        // --fresh: wipe existing data first
        if ($this->option('fresh')) {
            if (! $this->confirm('--fresh will DELETE all candles. Continue?', false)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }

            TradingChartCandle::truncate();
            $this->warn('Table truncated.');
        }

        $totalPairs = count($symbols) * count($intervals);
        $bar = $this->output->createProgressBar($totalPairs);
        $bar->start();

        foreach ($symbols as $symbol) {
            foreach ($intervals as $interval) {
                $this->seedPair($symbol, $interval, $count);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Done. You can now run: php artisan chart:worker');

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Seed one pair
    // -------------------------------------------------------------------------

    private function seedPair(string $symbol, string $interval, int $count): void
    {
        $intervalMs = self::INTERVAL_MS[$interval] ?? 60_000;

        // End timestamp = floor of current time to interval boundary
        $nowMs = (int) (microtime(true) * 1000);
        $endTs = intdiv($nowMs, $intervalMs) * $intervalMs;

        // Seed price from config
        $initialPrice = $this->seedPrice($symbol);

        $inserted = $this->generator->seedHistoricalCandles(
            symbol: $symbol,
            interval: $interval,
            endTimestamp: $endTs,
            count: $count,
            initialPrice: $initialPrice,
            intervalMs: $intervalMs,
        );

        $this->line(
            "  <fg=green>✓</> {$symbol}/{$interval} — {$inserted} candles"
            .' (up to '.date('Y-m-d H:i', $endTs / 1000).')'
        );
    }

    // -------------------------------------------------------------------------
    // Config helpers
    // -------------------------------------------------------------------------

    private function resolveSymbols(): array
    {
        $opt = $this->option('symbols');

        return $opt
            ? array_map('trim', explode(',', $opt))
            : config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']);
    }

    private function resolveIntervals(): array
    {
        $opt = $this->option('intervals');

        return $opt
            ? array_map('trim', explode(',', $opt))
            : config('trading_chart.intervals', ['1m', '5m']);
    }

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

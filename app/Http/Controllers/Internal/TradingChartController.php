<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Internal\GetCandlesRequest;
use App\Http\Requests\Internal\RewriteCandleRangeRequest;
use App\Http\Requests\Internal\UpdateFutureDirectionRequest;
use App\Http\Responses\ApiResponse;
use App\Models\TradingChartCandle;
use App\Models\TradingChartGenerationRule;
use App\Services\TradingChart\CandleGeneratorService;
use App\Services\TradingChart\ChartBroadcastService;
use App\Support\ErrorCodes;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * TradingChartController
 *
 * Internal API endpoints for the fake chart data system.
 * All routes are protected by the internal middleware defined in routes/api.php.
 *
 * Endpoints:
 *   GET  /api/internal/chart/candles            → getCandles()
 *   POST /api/internal/chart/future-direction   → updateFutureDirection()
 *   POST /api/internal/chart/rewrite-range      → rewriteRange()
 */
class TradingChartController extends Controller
{
    /**
     * Maximum number of candles that may be rewritten in one request.
     * Prevents accidental full-table rewrites from a single API call.
     */
    private const MAX_REWRITE_COUNT = 1000;

    public function __construct(
        private readonly CandleGeneratorService $generator,
        private readonly ChartBroadcastService $broadcaster,
    ) {}

    // =========================================================================
    // API 1 — GET /api/internal/chart/candles
    // =========================================================================

    /**
     * Return a paginated list of candles for a symbol + interval.
     *
     * Query params: symbol, interval, from (ms), to (ms), limit (default 500, max 1000)
     * Results are ordered timestamp ASC — the order KLineCharts requires.
     *
     * No writes. Read-only.
     */
    public function getCandles(GetCandlesRequest $request): JsonResponse
    {
        $v = $request->validated();
        $symbol = $v['symbol'];
        $interval = $v['interval'];
        $limit = $request->resolvedLimit();
        $from = Carbon::now()->utc()->subDays(1)->timestamp * 1000;
        $to = Carbon::now()->utc()->timestamp * 1000;

        if (isset($v['from'])) { 
            $from = (int) $v['from'];
        }

        if (isset($v['to'])) {
            $to = (int) $v['to'];
        }

        // Fetch most recent candles first (DESC), then reverse to ASC for the chart.
        // Using chronological() + limit() would return the oldest N candles,
        // leaving a gap between the REST batch and the live WebSocket stream.
        $candles = TradingChartCandle::forPair($symbol, $interval)
            ->realtime()
            ->fromTimestamp($from)
            ->toTimestamp($to)
            ->latestFirst()
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        // Map to KLineCharts-compatible array — numeric types, not strings.
        $data = $candles->map(fn (TradingChartCandle $c) => $c->toChartArray())->values()->all();

        return ApiResponse::success(
            data: $data,
            code: ErrorCodes::CHART_CANDLES_FETCHED,
        );
    }

    // =========================================================================
    // API 2 — POST /api/internal/chart/future-direction
    // =========================================================================

    /**
     * Save a generation rule that biases future (and optionally existing) candles
     * in a given direction for a given symbol + interval + time window.
     *
     * If apply_to_existing = true, also rewrites candles that already exist in
     * the specified timestamp range using the same direction + bias.
     *
     * The running chart:worker picks up new rules automatically on its next tick
     * by reading from trading_chart_generation_rules — no restart needed.
     */
    public function updateFutureDirection(UpdateFutureDirectionRequest $request): JsonResponse
    {
        $v = $request->validated();

        try {
            DB::transaction(function () use ($v): void {
                // Deactivate any existing open-ended rule for this pair so the new
                // rule is unambiguously the active one. Scoped rules (with active_until)
                // are left intact — they expire naturally.
                if (empty($v['active_until'])) {
                    TradingChartGenerationRule::forPair($v['symbol'], $v['interval'])
                        ->active()
                        ->whereNull('active_until')
                        ->update(['is_active' => false]);
                }

                TradingChartGenerationRule::create([
                    'symbol' => $v['symbol'],
                    'interval' => $v['interval'],
                    'forced_direction' => $v['direction'],
                    'price_bias_percent' => $v['price_bias_percent'] ?? 0,
                    'active_from' => $v['from_timestamp'] ?? null,
                    'active_until' => $v['to_timestamp'] ?? null,
                    'apply_to_existing' => $v['apply_to_existing'] ?? false,
                    'is_active' => true,
                ]);

                // If requested, immediately rewrite existing candles in the range.
                if (! empty($v['apply_to_existing']) && isset($v['from_timestamp'], $v['to_timestamp'])) {
                    $this->rewriteCandlesInRange(
                        symbol: $v['symbol'],
                        interval: $v['interval'],
                        fromMs: (int) $v['from_timestamp'],
                        toMs: (int) $v['to_timestamp'],
                        direction: $v['direction'],
                        strength: 1.0,
                        broadcast: true,
                    );
                }
            });
        } catch (Throwable $e) {
            Log::error('[TradingChartController] updateFutureDirection failed', [
                'payload' => $v,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                code: ErrorCodes::CHART_INTERNAL_ERROR,
                statusCode: 500,
            );
        }

        return ApiResponse::success(
            data: [
                'symbol' => $v['symbol'],
                'interval' => $v['interval'],
                'direction' => $v['direction'],
                'apply_to_existing' => (bool) ($v['apply_to_existing'] ?? false),
            ],
            code: ErrorCodes::CHART_FUTURE_DIRECTION_UPDATED,
        );
    }

    // =========================================================================
    // API 3 — POST /api/internal/chart/rewrite-range
    // =========================================================================

    /**
     * Directly overwrite the OHLCV of candles in a given timestamp range.
     *
     * Enforces continuity:
     *   - First rewritten candle's open = close of the candle before the range.
     *   - Candle immediately after the range has its open patched to the last
     *     rewritten candle's close (if that candle exists and is still open).
     *
     * All rewritten candles are marked is_modified = true.
     * Updated candles are broadcast as candle.rewrite events.
     *
     * Limit: 1000 candles per request.
     */
    public function rewriteRange(RewriteCandleRangeRequest $request): JsonResponse
    {
        $v = $request->validated();
        $symbol = $v['symbol'];
        $interval = $v['interval'];
        $fromMs = (int) $v['from_timestamp'];
        $toMs = (int) $v['to_timestamp'];
        $direction = $v['direction'];
        $strength = $request->resolvedStrength();

        // Count before fetching to return an early error if over limit.
        $count = TradingChartCandle::forPair($symbol, $interval)
            ->inRange($fromMs, $toMs)
            ->count();

        if ($count > self::MAX_REWRITE_COUNT) {
            return ApiResponse::error(
                code: ErrorCodes::CHART_RANGE_TOO_LARGE,
                message: "Range contains {$count} candles. Maximum is ".self::MAX_REWRITE_COUNT.'.',
                statusCode: 422,
            );
        }

        if ($count === 0) {
            return ApiResponse::error(
                code: ErrorCodes::CHART_CANDLE_NOT_FOUND,
                message: 'No candles found in the specified range.',
                statusCode: 404,
            );
        }

        try {
            $updated = DB::transaction(function () use (
                $symbol, $interval, $fromMs, $toMs, $direction, $strength
            ): int {
                return $this->rewriteCandlesInRange(
                    symbol: $symbol,
                    interval: $interval,
                    fromMs: $fromMs,
                    toMs: $toMs,
                    direction: $direction,
                    strength: $strength,
                    broadcast: true,
                );
            });
        } catch (Throwable $e) {
            Log::error('[TradingChartController] rewriteRange failed', [
                'symbol' => $symbol,
                'interval' => $interval,
                'from' => $fromMs,
                'to' => $toMs,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::error(
                code: ErrorCodes::CHART_INTERNAL_ERROR,
                statusCode: 500,
            );
        }

        return ApiResponse::success(
            data: ['updated_count' => $updated],
            code: ErrorCodes::CHART_RANGE_REWRITTEN,
        );
    }

    // =========================================================================
    // Shared rewrite logic
    // =========================================================================

    /**
     * Core candle rewrite implementation shared by rewriteRange() and
     * updateFutureDirection() when apply_to_existing = true.
     *
     * Steps:
     *   1. Load the candle immediately before the range → anchor previousClose.
     *   2. Load all candles in the range ordered chronologically.
     *   3. Rewrite each candle via CandleGeneratorService::rewriteCandle().
     *   4. Patch the open price of the candle immediately after the range
     *      if it still exists and is still open (preserves forward continuity).
     *   5. Broadcast candle.rewrite events if $broadcast = true.
     *
     * @return int Number of candles rewritten
     */
    private function rewriteCandlesInRange(
        string $symbol,
        string $interval,
        int $fromMs,
        int $toMs,
        string $direction,
        float $strength,
        bool $broadcast,
    ): int {
        // Step 1 — anchor close price from the candle just before the range.
        $beforeCandle = TradingChartCandle::forPair($symbol, $interval)
            ->where('timestamp', '<', $fromMs)
            ->latestFirst()
            ->first();

        // If no preceding candle, use the first candle in the range's own open
        // as the starting anchor (self-referential — best we can do).
        $firstInRange = TradingChartCandle::forPair($symbol, $interval)
            ->fromTimestamp($fromMs)
            ->chronological()
            ->first();

        $previousClose = $beforeCandle
            ? (string) $beforeCandle->close
            : (string) $firstInRange->open;

        // Step 2 — fetch all candles in range.
        $candles = TradingChartCandle::forPair($symbol, $interval)
            ->inRange($fromMs, $toMs)
            ->chronological()
            ->get();

        // Step 3 — rewrite each candle in order, chaining close → next open.
        $rewritten = [];

        foreach ($candles as $candle) {
            $rewritten[] = $this->generator->rewriteCandle(
                candle: $candle,
                previousClose: $previousClose,
                direction: $direction,
                strength: $strength,
            );

            $previousClose = (string) $candle->fresh()->close;
        }

        // Step 4 — patch the candle immediately after the range if it is still open.
        // This preserves forward price continuity past the rewritten range.
        $afterCandle = TradingChartCandle::forPair($symbol, $interval)
            ->where('timestamp', '>', $toMs)
            ->chronological()
            ->first();

        if ($afterCandle && $afterCandle->status === TradingChartCandle::STATUS_OPEN) {
            $afterCandle->open = $previousClose;

            // Re-validate that open still sits within high/low bounds.
            if (bccomp($previousClose, (string) $afterCandle->high, 8) > 0) {
                $afterCandle->high = $previousClose;
            }
            if (bccomp($previousClose, (string) $afterCandle->low, 8) < 0) {
                $afterCandle->low = $previousClose;
            }

            $afterCandle->is_modified = true;
            $afterCandle->save();

            // Include the patched after-candle in broadcast so frontend syncs it.
            $rewritten[] = $afterCandle;
        }

        // Step 5 — broadcast.
        if ($broadcast) {
            $this->broadcaster->broadcastRewriteRange($rewritten);
        }

        // Return count of candles in original range only (not the after-candle patch).
        return count($candles);
    }
}

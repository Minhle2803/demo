<?php

namespace App\Services\TradingChart;

use App\Models\TradingChartCandle;
use App\Models\TradingChartGenerationRule;
use Illuminate\Support\Facades\Cache;

/**
 * ChartRuleService
 *
 * Reads the active generation rule for a given symbol + interval + timestamp
 * and returns the effective direction and bias to apply for that tick.
 *
 * Called by TradingChartWorker on every tick for every pair.
 * Results are cached with a short TTL so the worker does not issue a DB query
 * on every 5-second tick per pair — at 6 pairs × 12 ticks/min that would be
 * 72 queries/min. With a 5s cache TTL it becomes at most 12 queries/min total.
 *
 * Has NO knowledge of price generation, broadcasting, or scheduling.
 */
class ChartRuleService
{
    /**
     * How long to cache the active rule result per pair (seconds).
     * Must be <= worker tick interval to avoid serving stale rules for too long.
     */
    private const CACHE_TTL = 5;

    /**
     * Cache key prefix.
     */
    private const CACHE_PREFIX = 'chart_rule:';

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Return the effective [direction, biasPct] tuple for a given pair + timestamp.
     *
     * Looks up the highest-priority active rule that covers $timestampMs:
     *   - is_active = true
     *   - active_from <= $timestampMs  (or null → applies from the beginning)
     *   - active_until >= $timestampMs (or null → applies indefinitely)
     *
     * Priority: most recently created rule wins (highest id).
     *
     * Falls back to ['neutral', 0.0] when no rule matches.
     *
     * @param  string $symbol
     * @param  string $interval
     * @param  int    $timestampMs  Candle open time in milliseconds
     * @return array{0: string, 1: float}  [direction, biasPct]
     */
    public function getActiveDirectionAndBias(
        string $symbol,
        string $interval,
        int    $timestampMs,
    ): array {
        $cacheKey = self::CACHE_PREFIX . "{$symbol}:{$interval}";

        $rule = Cache::remember($cacheKey, self::CACHE_TTL, function () use (
            $symbol, $interval, $timestampMs
        ): ?TradingChartGenerationRule {
            return TradingChartGenerationRule::forPair($symbol, $interval)
                ->active()
                ->coveringTimestamp($timestampMs)
                ->orderByDesc('id')  // most recently created rule wins
                ->first();
        });

        if ($rule === null) {
            return [
                TradingChartCandle::DIRECTION_NEUTRAL,
                0.0,
            ];
        }

        return [
            $rule->forced_direction,
            (float) ($rule->price_bias_percent ?? 0.0),
        ];
    }

    /**
     * Explicitly bust the cache for a pair.
     *
     * Call this after creating or deactivating a rule so the worker picks up
     * the change on its very next tick rather than waiting for TTL expiry.
     *
     * Called by TradingChartController after saving a new future-direction rule.
     */
    public function bustCache(string $symbol, string $interval): void
    {
        Cache::forget(self::CACHE_PREFIX . "{$symbol}:{$interval}");
    }

    /**
     * Bust the cache for all configured pairs at once.
     * Useful after a bulk rule change or a seed operation.
     */
    public function bustAllCaches(): void
    {
        $symbols   = config('trading_chart.symbols', []);
        $intervals = config('trading_chart.intervals', []);

        foreach ($symbols as $symbol) {
            foreach ($intervals as $interval) {
                $this->bustCache($symbol, $interval);
            }
        }
    }
}
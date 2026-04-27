<?php

namespace App\Services\TradingChart;

use App\Models\TradingChartCandle;
use Illuminate\Support\Facades\Log;

/**
 * CandleGeneratorService
 *
 * Responsible for all price generation math.
 * Has NO knowledge of broadcasting, rules, or scheduling.
 * The worker command orchestrates this service — this class only computes and persists.
 *
 * All arithmetic uses bcmath to avoid float precision loss on decimal(24,8) fields.
 */
class CandleGeneratorService
{
    // -------------------------------------------------------------------------
    // Generation parameters
    // Tune these to make the fake data look realistic for testing purposes.
    // -------------------------------------------------------------------------

    /**
     * Base volatility as a fraction of price per tick.
     * e.g. 0.002 = up to 0.2% price movement per tick.
     */
    private const BASE_VOLATILITY = 0.002;

    /**
     * Directional bias added on top of base volatility.
     * e.g. 0.001 = +0.1% extra push in the trend direction per tick.
     */
    private const DIRECTION_BIAS = 0.001;

    /**
     * Max upper/lower wick as a fraction of the candle body size.
     * e.g. 0.5 = wick can be up to 50% of body size.
     */
    private const MAX_WICK_RATIO = 0.5;

    /**
     * Minimum wick size as a fraction of price.
     * Prevents zero-wick candles which look artificial.
     */
    private const MIN_WICK_FRACTION = 0.0002;

    /**
     * Volume range: [min, max] multiplier applied to a base volume.
     */
    private const VOLUME_MIN_MULTIPLIER = 0.5;
    private const VOLUME_MAX_MULTIPLIER = 3.0;

    /**
     * Base volume per symbol. Keyed by symbol.
     */
    private const BASE_VOLUMES = [
        'BTC_USDT' => 500.0,
        'ETH_USDT' => 5000.0,
        'SOL_USDT' => 50000.0,
    ];

    /**
     * Default base volume when symbol is not in BASE_VOLUMES.
     */
    private const DEFAULT_BASE_VOLUME = 1000.0;

    /**
     * bcmath scale — decimal places for all intermediate calculations.
     */
    private const BC_SCALE = 10;

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Generate a brand new OPEN candle for the given symbol + interval + timestamp.
     *
     * Called when:
     *   - The current interval boundary has passed and we need a new candle.
     *   - Seeding historical data.
     *
     * @param  string      $symbol        e.g. 'BTC_USDT'
     * @param  string      $interval      e.g. '1m'
     * @param  int         $timestamp     Candle open time in milliseconds
     * @param  string      $previousClose Previous candle's close price (bcmath string)
     * @param  string      $direction     'up' | 'down' | 'neutral'
     * @param  float       $biasPct       Extra directional bias in percent (e.g. 1.5 = +1.5%)
     *                                    Pass negative value to amplify 'down'.
     */
    public function generateOpenCandle(
        string $symbol,
        string $interval,
        int    $timestamp,
        string $previousClose,
        string $direction = TradingChartCandle::DIRECTION_NEUTRAL,
        float  $biasPct = 0.0,
    ): TradingChartCandle {
        $ohlcv = $this->computeOhlcv($previousClose, $direction, $biasPct, $symbol);

        $candle = new TradingChartCandle();
        $candle->symbol       = $symbol;
        $candle->interval     = $interval;
        $candle->timestamp    = $timestamp;
        $candle->open         = $ohlcv['open'];
        $candle->high         = $ohlcv['high'];
        $candle->low          = $ohlcv['low'];
        $candle->close        = $ohlcv['close'];
        $candle->volume       = $ohlcv['volume'];
        $candle->direction    = $direction;
        $candle->status       = TradingChartCandle::STATUS_OPEN;
        $candle->is_generated = true;
        $candle->is_modified  = false;

        $candle->save();

        return $candle;
    }

    /**
     * Tick an existing OPEN candle — simulates price movement within the interval.
     *
     * Called repeatedly (e.g. every 5 seconds) while the candle is still open.
     * Updates close, and expands high/low if the new close breaks outside them.
     * Open price NEVER changes once set — it is the anchor for the interval.
     *
     * @param  TradingChartCandle $candle    The current open candle (Eloquent instance)
     * @param  string             $direction Active direction for this tick
     * @param  float              $biasPct   Active bias percent for this tick
     */
    public function tickOpenCandle(
        TradingChartCandle $candle,
        string $direction = TradingChartCandle::DIRECTION_NEUTRAL,
        float  $biasPct = 0.0,
    ): TradingChartCandle {
        // Compute a new close price from the candle's current close.
        $newClose = $this->computeNewClose((string) $candle->close, $direction, $biasPct);

        // Expand high if new close is above current high.
        $newHigh = bccomp($newClose, (string) $candle->high, self::BC_SCALE) > 0
            ? $this->addWick($newClose, true, (string) $candle->open)
            : (string) $candle->high;

        // Expand low if new close is below current low.
        $newLow = bccomp($newClose, (string) $candle->low, self::BC_SCALE) < 0
            ? $this->addWick($newClose, false, (string) $candle->open)
            : (string) $candle->low;

        // Re-validate and correct just in case of floating edge cases.
        [$newHigh, $newLow] = $this->ensureHighLowBounds(
            (string) $candle->open,
            $newClose,
            $newHigh,
            $newLow,
        );

        $candle->close  = $newClose;
        $candle->high   = $newHigh;
        $candle->low    = $newLow;
        $candle->volume = bcadd(
            (string) $candle->volume,
            $this->generateVolumeDelta($candle->symbol),
            self::BC_SCALE,
        );

        $candle->save();

        return $candle;
    }

    /**
     * Close an open candle — marks it as finalized.
     *
     * Called when the candle's interval boundary has passed.
     * No price values change here — close() only changes status.
     */
    public function closeCandle(TradingChartCandle $candle): TradingChartCandle
    {
        $candle->status = TradingChartCandle::STATUS_CLOSED;
        $candle->save();

        return $candle;
    }

    /**
     * Rewrite a single candle's OHLCV using a new direction + strength.
     *
     * Used by the rewrite-range API to overwrite historical candles.
     * Open is anchored to the provided $previousClose for continuity.
     * Marks the candle as is_modified = true.
     *
     * @param  TradingChartCandle $candle        Candle to rewrite
     * @param  string             $previousClose Close price of the preceding candle
     * @param  string             $direction     'up' | 'down' | 'neutral'
     * @param  float              $strength      Multiplier for movement size (e.g. 2.0 = 2× normal)
     */
    public function rewriteCandle(
        TradingChartCandle $candle,
        string $previousClose,
        string $direction,
        float  $strength = 1.0,
    ): TradingChartCandle {
        $biasPct = $this->strengthToBias($strength, $direction);
        $ohlcv   = $this->computeOhlcv($previousClose, $direction, $biasPct, $candle->symbol);

        $candle->open        = $ohlcv['open'];
        $candle->high        = $ohlcv['high'];
        $candle->low         = $ohlcv['low'];
        $candle->close       = $ohlcv['close'];
        $candle->volume      = $ohlcv['volume'];
        $candle->direction   = $direction;
        $candle->is_modified = true;

        $candle->save();

        return $candle;
    }

    /**
     * Seed a batch of historical closed candles for a given symbol + interval.
     *
     * Generates $count candles going BACKWARD from $endTimestamp.
     * Intended for use by the chart:seed Artisan command.
     *
     * @param  string $symbol
     * @param  string $interval
     * @param  int    $endTimestamp    Last candle timestamp in ms (most recent)
     * @param  int    $count           Number of candles to generate
     * @param  string $initialPrice    Starting price for the very first candle
     * @param  int    $intervalMs      Interval duration in milliseconds
     * @return int                     Number of candles inserted
     */
    public function seedHistoricalCandles(
        string $symbol,
        string $interval,
        int    $endTimestamp,
        int    $count,
        string $initialPrice,
        int    $intervalMs,
    ): int {
        // Build timestamps from oldest to newest.
        $timestamps = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $timestamps[] = $endTimestamp - ($i * $intervalMs);
        }

        $previousClose = $initialPrice;
        $inserted      = 0;

        foreach ($timestamps as $timestamp) {
            // Random direction for historical seed — creates natural-looking chart.
            $direction = $this->randomDirection();
            $ohlcv     = $this->computeOhlcv($previousClose, $direction, 0.0, $symbol);

            // Use upsert to safely re-run the seed command without duplicates.
            TradingChartCandle::upsert(
                [
                    'symbol'       => $symbol,
                    'interval'     => $interval,
                    'timestamp'    => $timestamp,
                    'open'         => $ohlcv['open'],
                    'high'         => $ohlcv['high'],
                    'low'          => $ohlcv['low'],
                    'close'        => $ohlcv['close'],
                    'volume'       => $ohlcv['volume'],
                    'direction'    => $direction,
                    'status'       => TradingChartCandle::STATUS_CLOSED,
                    'is_generated' => true,
                    'is_modified'  => false,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                uniqueBy: ['symbol', 'interval', 'timestamp'],
                update: ['open', 'high', 'low', 'close', 'volume', 'direction', 'updated_at'],
            );

            $previousClose = $ohlcv['close'];
            $inserted++;
        }

        return $inserted;
    }

    // -------------------------------------------------------------------------
    // Core OHLCV computation
    // -------------------------------------------------------------------------

    /**
     * Compute a full OHLCV set for one candle.
     *
     * The formula:
     *   open  = previousClose  (strict continuity rule)
     *   close = open + directional movement
     *   high  = max(open, close) + upper wick
     *   low   = min(open, close) - lower wick
     *   volume = randomised around symbol base volume
     *
     * All values validated before return:
     *   low <= open <= high
     *   low <= close <= high
     *
     * @return array{open: string, high: string, low: string, close: string, volume: string}
     */
    private function computeOhlcv(
        string $previousClose,
        string $direction,
        float  $biasPct,
        string $symbol,
    ): array {
        $open  = $previousClose; // candle open is always previous close
        $close = $this->computeNewClose($open, $direction, $biasPct);

        $upperRef = bccomp($open, $close, self::BC_SCALE) >= 0 ? $open : $close;
        $lowerRef = bccomp($open, $close, self::BC_SCALE) <= 0 ? $open : $close;

        $high   = $this->addWick($upperRef, true, $open);
        $low    = $this->addWick($lowerRef, false, $open);
        $volume = $this->generateVolume($symbol);

        // Final validation pass — correctness guaranteed before any DB write.
        [$high, $low] = $this->ensureHighLowBounds($open, $close, $high, $low);

        return [
            'open'   => $this->format($open),
            'high'   => $this->format($high),
            'low'    => $this->format($low),
            'close'  => $this->format($close),
            'volume' => $this->format($volume),
        ];
    }

    /**
     * Compute a new close price from a base price, applying directional bias.
     *
     * Movement formula:
     *   movement = basePrice × (volatility ± directionBias ± biasPct)
     *   newClose  = basePrice + movement
     *
     * Direction:
     *   up      → bias is always positive
     *   down    → bias is always negative
     *   neutral → bias is random ±
     */
    private function computeNewClose(
        string $basePrice,
        string $direction,
        float  $biasPct,
    ): string {
        // Random component — always present regardless of direction.
        $randomFraction = $this->randomFraction(self::BASE_VOLATILITY);

        // Directional component.
        $directionFraction = match ($direction) {
            TradingChartCandle::DIRECTION_UP      =>  self::DIRECTION_BIAS,
            TradingChartCandle::DIRECTION_DOWN    => -self::DIRECTION_BIAS,
            default                               => $this->randomSign() * self::DIRECTION_BIAS,
        };

        // External bias from generation rule (percent → fraction).
        $externalBias = $biasPct / 100.0;

        // Total movement fraction.
        $totalFraction = $randomFraction + $directionFraction + $externalBias;

        // Apply to base price using bcmath.
        // MUST use sprintf — PHP (string) cast on float can produce scientific
        // notation like "1.5E-5" which bcmath cannot parse → ValueError.
        $movement = bcmul($basePrice, sprintf('%.10f', $totalFraction), self::BC_SCALE);
        $newClose  = bcadd($basePrice, $movement, self::BC_SCALE);

        // Price must never go below 0.00000001 (crypto minimum tick).
        if (bccomp($newClose, '0.00000001', self::BC_SCALE) < 0) {
            $newClose = '0.00000001';
        }

        return $newClose;
    }

    // -------------------------------------------------------------------------
    // Wick computation
    // -------------------------------------------------------------------------

    /**
     * Add a wick above or below a reference price.
     *
     * Wick size is a random fraction of the body size, with a minimum
     * guaranteed size so candles never look like doji-only flat lines.
     *
     * @param  string $referencePrice  The high (for upper wick) or low (for lower wick) body edge
     * @param  bool   $isUpper         True = add upper wick, False = add lower wick
     * @param  string $openPrice       Used to compute body size for wick scaling
     * @return string                  New high or low with wick applied
     */
    private function addWick(string $referencePrice, bool $isUpper, string $openPrice): string
    {
        $bodySize = bcabs(bcsub($referencePrice, $openPrice, self::BC_SCALE));

        // Wick = random fraction of body, minimum guaranteed size.
        $wickFromBody = bcmul(
            $bodySize,
            sprintf('%.10f', mt_rand(0, (int)(self::MAX_WICK_RATIO * 100)) / 100),
            self::BC_SCALE,
        );

        $minWick = bcmul($referencePrice, sprintf('%.10f', self::MIN_WICK_FRACTION), self::BC_SCALE);
        $wick    = bccomp($wickFromBody, $minWick, self::BC_SCALE) >= 0 ? $wickFromBody : $minWick;

        return $isUpper
            ? bcadd($referencePrice, $wick, self::BC_SCALE)
            : bcsub($referencePrice, $wick, self::BC_SCALE);
    }

    // -------------------------------------------------------------------------
    // Validation / correction
    // -------------------------------------------------------------------------

    /**
     * Ensure high >= max(open, close) and low <= min(open, close).
     *
     * This is the hard correctness guarantee called after every computation.
     * If any edge case in bcmath produces a value that violates the candlestick
     * invariant, this method corrects it before the value reaches the DB.
     *
     * Invariants enforced:
     *   high >= open
     *   high >= close
     *   low  <= open
     *   low  <= close
     *   high >= low
     *
     * @return array{0: string, 1: string}  [corrected_high, corrected_low]
     */
    private function ensureHighLowBounds(
        string $open,
        string $close,
        string $high,
        string $low,
    ): array {
        // High must be >= both open and close.
        $minHigh = bccomp($open, $close, self::BC_SCALE) >= 0 ? $open : $close;
        if (bccomp($high, $minHigh, self::BC_SCALE) < 0) {
            $high = $minHigh;
        }

        // Low must be <= both open and close.
        $maxLow = bccomp($open, $close, self::BC_SCALE) <= 0 ? $open : $close;
        if (bccomp($low, $maxLow, self::BC_SCALE) > 0) {
            $low = $maxLow;
        }

        // Final sanity: high must be >= low (can only fail if price = 0, edge case).
        if (bccomp($high, $low, self::BC_SCALE) < 0) {
            $high = $low;
        }

        return [$high, $low];
    }

    // -------------------------------------------------------------------------
    // Volume generation
    // -------------------------------------------------------------------------

    /**
     * Generate a full candle volume.
     */
    private function generateVolume(string $symbol): string
    {
        $base       = self::BASE_VOLUMES[$symbol] ?? self::DEFAULT_BASE_VOLUME;
        $multiplier = self::VOLUME_MIN_MULTIPLIER
            + (mt_rand() / mt_getrandmax())
            * (self::VOLUME_MAX_MULTIPLIER - self::VOLUME_MIN_MULTIPLIER);

        return bcmul(sprintf('%.10f', $base), sprintf('%.10f', $multiplier), self::BC_SCALE);
    }

    /**
     * Generate a volume increment for ticking an open candle.
     * Smaller than a full-candle volume — represents one trade batch.
     */
    private function generateVolumeDelta(string $symbol): string
    {
        $base = self::BASE_VOLUMES[$symbol] ?? self::DEFAULT_BASE_VOLUME;
        // Delta = ~5–15% of base volume per tick
        $pct  = mt_rand(5, 15) / 100;

        return bcmul(sprintf('%.10f', $base), sprintf('%.10f', $pct), self::BC_SCALE);
    }

    // -------------------------------------------------------------------------
    // Strength → bias conversion (for rewrite API)
    // -------------------------------------------------------------------------

    /**
     * Convert a user-supplied strength multiplier to a bias percent.
     *
     * strength 1.0 = normal movement
     * strength 2.0 = double movement
     * strength 0.5 = half movement
     *
     * For 'down' direction the bias is returned as negative.
     */
    private function strengthToBias(float $strength, string $direction): float
    {
        $baseBias = self::DIRECTION_BIAS * 100; // convert to percent
        $biased   = $baseBias * $strength;

        return $direction === TradingChartCandle::DIRECTION_DOWN ? -$biased : $biased;
    }

    // -------------------------------------------------------------------------
    // Randomness helpers
    // -------------------------------------------------------------------------

    /**
     * Random fraction between -$maxAbsolute and +$maxAbsolute.
     */
    private function randomFraction(float $maxAbsolute): float
    {
        return (mt_rand() / mt_getrandmax() * 2 - 1) * $maxAbsolute;
    }

    /**
     * Random +1 or -1.
     */
    private function randomSign(): int
    {
        return mt_rand(0, 1) === 1 ? 1 : -1;
    }

    /**
     * Pick a random direction — used during historical seed.
     */
    private function randomDirection(): string
    {
        $directions = TradingChartCandle::DIRECTIONS;

        return $directions[array_rand($directions)];
    }

    // -------------------------------------------------------------------------
    // bcmath helpers
    // -------------------------------------------------------------------------

    /**
     * Format a bcmath string to 8 decimal places for storage.
     */
    private function format(string $value): string
    {
        return number_format((float) $value, 8, '.', '');
    }
}

// ---------------------------------------------------------------------------
// bcmath does not have a built-in abs() — polyfill it at file scope.
// Only defined if not already present (e.g. another file loaded it first).
// ---------------------------------------------------------------------------
if (! function_exists('bcabs')) {
    function bcabs(string $value, int $scale = 10): string
    {
        return bccomp($value, '0', $scale) < 0
            ? bcsub('0', $value, $scale)
            : $value;
    }
}
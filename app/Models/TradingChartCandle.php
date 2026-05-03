<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradingChartCandle extends Model
{
    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------

    protected $table = 'trading_chart_candles';

    // -------------------------------------------------------------------------
    // Mass-assignment whitelist
    // -------------------------------------------------------------------------

    protected $fillable = [
        'symbol',
        'interval',
        'timestamp',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'direction',
        'status',
        'is_generated',
        'is_modified',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            // Millisecond timestamp — kept as integer, never cast to Carbon
            // to avoid any timezone mutation and stay KLineCharts-compatible
            'timestamp' => 'integer',

            // Price & volume — cast to string to preserve full decimal(24,8)
            // precision without PHP float rounding errors.
            // Use bcmath or brick/money for any arithmetic on these values.
            'open' => 'decimal:8',
            'high' => 'decimal:8',
            'low' => 'decimal:8',
            'close' => 'decimal:8',
            'volume' => 'decimal:8',

            // Enum fields — cast to string (enum values: up/down/neutral, open/closed)
            'direction' => 'string',
            'status' => 'string',

            // Flags
            'is_generated' => 'boolean',
            'is_modified' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Constants — single source of truth for allowed values
    // -------------------------------------------------------------------------

    // Direction values
    const DIRECTION_UP = 'up';

    const DIRECTION_DOWN = 'down';

    const DIRECTION_NEUTRAL = 'neutral';

    const DIRECTIONS = [
        self::DIRECTION_UP,
        self::DIRECTION_DOWN,
        self::DIRECTION_NEUTRAL,
    ];

    // Status values
    const STATUS_OPEN = 'open';

    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter by symbol and interval — used in almost every query.
     */
    public function scopeForPair(Builder $query, string $symbol, string $interval): Builder
    {
        return $query->where('symbol', $symbol)->where('interval', $interval);
    }

    /**
     * Only closed (finalized) candles.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Only the current open (live) candle.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Candles within a millisecond timestamp range (inclusive).
     */
    public function scopeInRange(Builder $query, int $from, int $to): Builder
    {
        return $query->whereBetween('timestamp', [$from, $to]);
    }

    /**
     * Candles from a millisecond timestamp onward.
     */
    public function scopeFromTimestamp(Builder $query, int $from): Builder
    {
        return $query->where('timestamp', '>=', $from);
    }

    /**
     * Candles up to a millisecond timestamp.
     */
    public function scopeToTimestamp(Builder $query, int $to): Builder
    {
        return $query->where('timestamp', '<=', $to);
    }

    /**
     * Chronological order — used for API responses and chart rendering.
     */
    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('timestamp', 'asc');
    }

    /**
     * Latest first — used by the generator to resume from last candle.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('timestamp', 'desc');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Fetch the latest candle for a symbol+interval.
     * Used by the generator worker on boot to safely resume after a crash.
     */
    public static function latestFor(string $symbol, string $interval): ?self
    {
        return static::forPair($symbol, $interval)
            ->latestFirst()
            ->first();
    }

    /**
     * Fetch the current open candle for a symbol+interval.
     */
    public static function currentOpenFor(string $symbol, string $interval): ?self
    {
        return static::forPair($symbol, $interval)
            ->open()
            ->first();
    }

    /**
     * Whether all OHLCV values satisfy candlestick validity rules:
     *   low <= open <= high
     *   low <= close <= high
     *   high >= low
     */
    public function isValid(): bool
    {
        $o = (float) $this->open;
        $h = (float) $this->high;
        $l = (float) $this->low;
        $c = (float) $this->close;

        return $l <= $o
            && $o <= $h
            && $l <= $c
            && $c <= $h
            && $h >= $l;
    }

    /**
     * Return a KLineCharts-compatible array for API responses and WebSocket payloads.
     * All numeric fields are cast to float so JSON does not encode them as strings.
     */
    public function toChartArray(): array
    {
        return [
            'timestamp' => (int) $this->timestamp,
            'open' => (float) $this->open,
            'high' => (float) $this->high,
            'low' => (float) $this->low,
            'close' => (float) $this->close,
            'volume' => (float) $this->volume,
        ];
    }
}

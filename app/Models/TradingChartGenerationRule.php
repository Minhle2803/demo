<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradingChartGenerationRule extends Model
{
    protected $table = 'trading_chart_generation_rules';

    protected $fillable = [
        'symbol',
        'interval',
        'forced_direction',
        'price_bias_percent',
        'active_from',
        'active_until',
        'apply_to_existing',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_bias_percent' => 'decimal:4',
            'active_from' => 'integer',
            'active_until' => 'integer',
            'apply_to_existing' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPair(Builder $query, string $symbol, string $interval): Builder
    {
        return $query->where('symbol', $symbol)->where('interval', $interval);
    }

    /**
     * Rules whose time window covers the given millisecond timestamp.
     */
    public function scopeCoveringTimestamp(Builder $query, int $timestampMs): Builder
    {
        return $query->where(function (Builder $q) use ($timestampMs): void {
            $q->whereNull('active_from')->orWhere('active_from', '<=', $timestampMs);
        })->where(function (Builder $q) use ($timestampMs): void {
            $q->whereNull('active_until')->orWhere('active_until', '>=', $timestampMs);
        });
    }
}

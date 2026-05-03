<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradingSession extends Model
{
    protected $fillable = [
        'symbol',
        'interval',
        'start_time',
        'lock_time',
        'end_time',
        'status',
        'open_price',
        'close_price',
        'candle_timestamp',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'lock_time' => 'datetime',
        'end_time' => 'datetime',
        'open_price' => 'decimal:8',
        'close_price' => 'decimal:8',
        'candle_timestamp' => 'integer',
    ];

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && now()->lt($this->lock_time);
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked' || now()->gte($this->lock_time);
    }
}

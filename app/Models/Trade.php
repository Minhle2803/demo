<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'type',
        'amount',
        'status',
        'payout',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payout' => 'decimal:2',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TradingSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }
}

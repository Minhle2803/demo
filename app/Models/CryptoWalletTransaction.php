<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'crypto_wallet_transactions';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'asset',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'locked_before',
        'locked_after',
        'reference_type',
        'reference_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:18',
            'balance_before' => 'decimal:18',
            'balance_after' => 'decimal:18',
            'locked_before' => 'decimal:18',
            'locked_after' => 'decimal:18',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }
}

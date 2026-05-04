<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoOrder extends Model
{
    use HasFactory;

    protected $table = 'crypto_orders';

    protected $fillable = [
        'user_id',
        'symbol',
        'base_asset',
        'quote_asset',
        'side',
        'type',
        'price',
        'quantity',
        'filled_quantity',
        'remaining_quantity',
        'total_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:18',
            'quantity' => 'decimal:18',
            'filled_quantity' => 'decimal:18',
            'remaining_quantity' => 'decimal:18',
            'total_amount' => 'decimal:18',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }

    public function trades(): HasMany
    {
        return $this->hasMany(CryptoTrade::class, 'buy_order_id')
            ->orWhere('sell_order_id', $this->id);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'partially_filled']);
    }
}

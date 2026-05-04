<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoTrade extends Model
{
    use HasFactory;

    protected $table = 'crypto_trades';

    public $timestamps = false;

    protected $fillable = [
        'symbol',
        'buy_order_id',
        'sell_order_id',
        'buyer_user_id',
        'seller_user_id',
        'admin_user_id',
        'source',
        'price',
        'quantity',
        'total',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:18',
            'quantity' => 'decimal:18',
            'total' => 'decimal:18',
            'created_at' => 'datetime',
        ];
    }

    public function buyOrder(): BelongsTo
    {
        return $this->belongsTo(CryptoOrder::class, 'buy_order_id');
    }

    public function sellOrder(): BelongsTo
    {
        return $this->belongsTo(CryptoOrder::class, 'sell_order_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'buyer_user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'seller_user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoAsset extends Model
{
    protected $table = 'crypto_assets';

    protected $fillable = [
        'symbol',
        'name',
        'icon_url',
        'base_asset',
        'quote_asset',
        'price',
        'price_precision',
        'quantity_precision',
        'min_quantity',
        'min_notional',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:18',
            'price_precision' => 'integer',
            'quantity_precision' => 'integer',
            'min_quantity' => 'decimal:18',
            'min_notional' => 'decimal:18',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

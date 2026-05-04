<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoWallet extends Model
{
    use HasFactory;

    protected $table = 'crypto_wallets';

    protected $fillable = [
        'user_id',
        'asset',
        'available_balance',
        'locked_balance',
    ];

    protected function casts(): array
    {
        return [
            'available_balance' => 'decimal:18',
            'locked_balance' => 'decimal:18',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CryptoWalletTransaction::class, 'user_id', 'user_id')
            ->where('asset', $this->asset);
    }
}

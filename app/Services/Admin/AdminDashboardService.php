<?php

namespace App\Services\Admin;

use App\Models\CryptoOrder;
use App\Models\Trade;

class AdminDashboardService
{
    public function getStats(): array
    {
        return [
            'total_spot_buy_orders' => (float) CryptoOrder::where('side', 'buy')->sum('total_amount'),
            'total_spot_sell_orders' => (float) CryptoOrder::where('side', 'sell')->sum('total_amount'),
            'total_trading_sell_orders' => (float) Trade::where('type', 'sell')->sum('amount'),
            'total_trading_buy_orders' => (float) Trade::where('type', 'buy')->sum('amount'),
            'revenue' => $this->calculateRevenue(),
        ];
    }

    protected function calculateRevenue(): float
    {
        $loseAmount = (float) Trade::where('status', 'lose')->sum('amount');
        $winAmount = (float) Trade::where('status', 'win')->sum('amount');

        return $loseAmount - $winAmount;
    }

    public function getRecentSpotBuyOrders(int $limit = 10, ?string $symbol = null): array
    {
        $query = CryptoOrder::with('user')
            ->where('side', 'buy')
            ->latest();

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        return $query->take($limit)->get()->toArray();
    }

    public function getRecentSpotSellOrders(int $limit = 10, ?string $symbol = null): array
    {
        $query = CryptoOrder::with('user')
            ->where('side', 'sell')
            ->latest();

        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        return $query->take($limit)->get()->toArray();
    }

    public function getSymbols(): array
    {
        return CryptoOrder::distinct()->pluck('symbol')->toArray();
    }
}

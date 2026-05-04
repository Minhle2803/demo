<?php

namespace App\Services\SpotTrading;

use App\Events\SpotOrderBookEvent;
use App\Models\CryptoOrder;

class OrderBookService
{
    /**
     * Get aggregated order book for a symbol.
     *
     * Bids: buy orders grouped by price, sorted DESC (highest bid first).
     * Asks: sell orders grouped by price, sorted ASC (lowest ask first).
     */
    public function snapshot(string $symbol): array
    {
        $bids = CryptoOrder::where('symbol', $symbol)
            ->where('side', 'buy')
            ->whereIn('status', ['open', 'partially_filled'])
            ->selectRaw('price, SUM(remaining_quantity) as total_quantity, COUNT(*) as order_count')
            ->groupBy('price')
            ->orderBy('price', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'price' => (string) $row->price,
                'total_quantity' => (string) $row->total_quantity,
                'order_count' => (int) $row->order_count,
            ])
            ->toArray();

        $asks = CryptoOrder::where('symbol', $symbol)
            ->where('side', 'sell')
            ->whereIn('status', ['open', 'partially_filled'])
            ->selectRaw('price, SUM(remaining_quantity) as total_quantity, COUNT(*) as order_count')
            ->groupBy('price')
            ->orderBy('price', 'asc')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'price' => (string) $row->price,
                'total_quantity' => (string) $row->total_quantity,
                'order_count' => (int) $row->order_count,
            ])
            ->toArray();

        return ['bids' => $bids, 'asks' => $asks];
    }

    /**
     * Broadcast order book update for a symbol.
     */
    public function broadcast(string $symbol): void
    {
        $data = $this->snapshot($symbol);
        event(new SpotOrderBookEvent($symbol, $data['bids'], $data['asks']));
    }
}

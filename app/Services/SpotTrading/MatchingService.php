<?php

namespace App\Services\SpotTrading;

use App\Models\CryptoOrder;
use App\Models\CryptoTrade;
use Illuminate\Support\Facades\DB;

class MatchingService
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    /**
     * Attempt to auto-match a newly created order against existing open orders.
     *
     * Execution price uses the maker (existing resting order) price:
     *   - For a new buy: matches against sells at sell price
     *   - For a new sell: matches against buys at buy price
     */
    public function attemptMatch(CryptoOrder $takerOrder): void
    {
        if (! $takerOrder->isOpen()) {
            return;
        }

        if ($takerOrder->side === 'buy') {
            $this->matchBuyAgainstSells($takerOrder);
        } else {
            $this->matchSellAgainstBuys($takerOrder);
        }
    }

    private function matchBuyAgainstSells(CryptoOrder $buyOrder): void
    {
        while (bccomp($buyOrder->remaining_quantity, '0', 18) > 0 && $buyOrder->isOpen()) {
            $matchingSell = CryptoOrder::where('symbol', $buyOrder->symbol)
                ->where('side', 'sell')
                ->whereIn('status', ['open', 'partially_filled'])
                ->where('user_id', '!=', $buyOrder->user_id)
                ->where('price', '<=', $buyOrder->price)
                ->orderBy('price')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (! $matchingSell) {
                break;
            }

            $this->executeMatch(
                buyOrder: $buyOrder,
                sellOrder: $matchingSell,
                executionPrice: (string) $matchingSell->price,
            );
        }
    }

    private function matchSellAgainstBuys(CryptoOrder $sellOrder): void
    {
        while (bccomp($sellOrder->remaining_quantity, '0', 18) > 0 && $sellOrder->isOpen()) {
            $matchingBuy = CryptoOrder::where('symbol', $sellOrder->symbol)
                ->where('side', 'buy')
                ->whereIn('status', ['open', 'partially_filled'])
                ->where('user_id', '!=', $sellOrder->user_id)
                ->where('price', '>=', $sellOrder->price)
                ->orderBy('price', 'desc')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (! $matchingBuy) {
                break;
            }

            $this->executeMatch(
                buyOrder: $matchingBuy,
                sellOrder: $sellOrder,
                executionPrice: (string) $matchingBuy->price,
            );
        }
    }

    /**
     * Execute a match between a buy order and a sell order.
     */
    private function executeMatch(
        CryptoOrder $buyOrder,
        CryptoOrder $sellOrder,
        string $executionPrice,
    ): void {
        $matchedQty = bccomp($buyOrder->remaining_quantity, $sellOrder->remaining_quantity, 18) <= 0
            ? $buyOrder->remaining_quantity
            : $sellOrder->remaining_quantity;

        $total = bcmul($executionPrice, $matchedQty, 18);

        DB::transaction(function () use ($buyOrder, $sellOrder, $executionPrice) {
            // Re-fetch with lock
            $buy = CryptoOrder::where('id', $buyOrder->id)->lockForUpdate()->firstOrFail();
            $sell = CryptoOrder::where('id', $sellOrder->id)->lockForUpdate()->firstOrFail();

            if (! $buy->isOpen() || ! $sell->isOpen()) {
                return;
            }

            $actualQty = bccomp($buy->remaining_quantity, $sell->remaining_quantity, 18) <= 0
                ? $buy->remaining_quantity
                : $sell->remaining_quantity;

            $actualTotal = bcmul($executionPrice, $actualQty, 18);

            // Buyer settlement
            $lockedQuoteTotal = bcmul($buy->price, $actualQty, 18);
            $this->walletService->settleBuyer(
                userId: $buy->user_id,
                baseAsset: $buy->base_asset,
                quoteAsset: $buy->quote_asset,
                matchedQty: $actualQty,
                executionPrice: $executionPrice,
                lockedPrice: (string) $buy->price,
                lockedTotalQuote: $lockedQuoteTotal,
            );

            // Seller settlement
            $this->walletService->settleSeller(
                userId: $sell->user_id,
                baseAsset: $sell->base_asset,
                quoteAsset: $sell->quote_asset,
                matchedQty: $actualQty,
                executionPrice: $executionPrice,
            );

            // Update buy order
            $buyFilled = bcadd((string) $buy->filled_quantity, $actualQty, 18);
            $buyRemaining = bcsub($buy->remaining_quantity, $actualQty, 18);
            $buy->update([
                'filled_quantity' => $buyFilled,
                'remaining_quantity' => $buyRemaining,
                'status' => bccomp($buyRemaining, '0', 18) === 0 ? 'filled' : 'partially_filled',
            ]);

            // Update sell order
            $sellFilled = bcadd((string) $sell->filled_quantity, $actualQty, 18);
            $sellRemaining = bcsub($sell->remaining_quantity, $actualQty, 18);
            $sell->update([
                'filled_quantity' => $sellFilled,
                'remaining_quantity' => $sellRemaining,
                'status' => bccomp($sellRemaining, '0', 18) === 0 ? 'filled' : 'partially_filled',
            ]);

            // Record trade
            CryptoTrade::create([
                'symbol' => $buy->symbol,
                'buy_order_id' => $buy->id,
                'sell_order_id' => $sell->id,
                'buyer_user_id' => $buy->user_id,
                'seller_user_id' => $sell->user_id,
                'source' => 'auto_match',
                'price' => $executionPrice,
                'quantity' => $actualQty,
                'total' => $actualTotal,
                'created_at' => now(),
            ]);

            $buyOrder->refresh();
            $sellOrder->refresh();
        });
    }
}

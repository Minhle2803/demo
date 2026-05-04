<?php

namespace App\Services\SpotTrading;

use App\Models\CryptoOrder;
use App\Models\CryptoTrade;
use App\Models\User;
use App\Support\ErrorCodes;
use Illuminate\Support\Facades\DB;

class AdminManualMatchService
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    /**
     * Admin manually matches an open order against the system.
     *
     * If it's a buy order: user receives base_asset, pays quote_asset from locked.
     * If it's a sell order: user pays base_asset from locked, receives quote_asset.
     */
    public function execute(CryptoOrder $order, string $price, string $quantity, User $admin): array
    {
        if (! $order->isOpen()) {
            if ($order->status === 'cancelled') {
                return ['code' => ErrorCodes::SPOT_ORDER_ALREADY_CANCELLED];
            }

            return ['code' => ErrorCodes::SPOT_ORDER_ALREADY_FILLED];
        }

        if (bccomp($quantity, (string) $order->remaining_quantity, 18) > 0) {
            return ['code' => ErrorCodes::SPOT_ADMIN_MATCH_FAILED];
        }

        if (bccomp($price, '0', 18) <= 0) {
            return ['code' => ErrorCodes::SPOT_INVALID_PRICE];
        }

        $total = bcmul($price, $quantity, 18);

        return DB::transaction(function () use ($order, $price, $quantity, $total, $admin) {
            $order = CryptoOrder::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if (! $order->isOpen()) {
                return ['code' => ErrorCodes::SPOT_ADMIN_MATCH_FAILED];
            }

            if (bccomp($quantity, (string) $order->remaining_quantity, 18) > 0) {
                return ['code' => ErrorCodes::SPOT_ADMIN_MATCH_FAILED];
            }

            if ($order->side === 'buy') {
                $lockedQuoteTotal = bcmul((string) $order->price, $quantity, 18);

                $this->walletService->settleBuyer(
                    userId: $order->user_id,
                    baseAsset: $order->base_asset,
                    quoteAsset: $order->quote_asset,
                    matchedQty: $quantity,
                    executionPrice: $price,
                    lockedPrice: (string) $order->price,
                    lockedTotalQuote: $lockedQuoteTotal,
                );

                CryptoTrade::create([
                    'symbol' => $order->symbol,
                    'buy_order_id' => $order->id,
                    'buyer_user_id' => $order->user_id,
                    'admin_user_id' => $admin->id,
                    'source' => 'admin_manual',
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                    'created_at' => now(),
                ]);
            } else {
                $this->walletService->settleSeller(
                    userId: $order->user_id,
                    baseAsset: $order->base_asset,
                    quoteAsset: $order->quote_asset,
                    matchedQty: $quantity,
                    executionPrice: $price,
                );

                CryptoTrade::create([
                    'symbol' => $order->symbol,
                    'sell_order_id' => $order->id,
                    'seller_user_id' => $order->user_id,
                    'admin_user_id' => $admin->id,
                    'source' => 'admin_manual',
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                    'created_at' => now(),
                ]);
            }

            $newFilled = bcadd((string) $order->filled_quantity, $quantity, 18);
            $newRemaining = bcsub((string) $order->remaining_quantity, $quantity, 18);
            $order->update([
                'filled_quantity' => $newFilled,
                'remaining_quantity' => $newRemaining,
                'status' => bccomp($newRemaining, '0', 18) === 0 ? 'filled' : 'partially_filled',
            ]);

            return [
                'code' => ErrorCodes::SPOT_ADMIN_MATCH_SUCCESS,
                'order' => $order->fresh(),
            ];
        });
    }
}

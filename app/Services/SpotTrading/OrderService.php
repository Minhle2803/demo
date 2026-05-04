<?php

namespace App\Services\SpotTrading;

use App\Models\ClientUser;
use App\Models\CryptoOrder;
use App\Support\ErrorCodes;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly MatchingService $matchingService,
    ) {}

    /**
     * Create a buy limit order.
     *
     * Locks the required quote asset (e.g. USDT) based on price × quantity.
     */
    public function createBuy(ClientUser $user, array $symbolConfig, string $price, string $quantity): array
    {
        $totalAmount = bcmul($price, $quantity, 18);
        $quoteAsset = $symbolConfig['quote_asset'];
        $baseAsset = $symbolConfig['base_asset'];
        $symbol = $this->symbolKey($symbolConfig);

        // Validate balance
        $quoteWallet = $this->walletService->getOrCreate($user->id, $quoteAsset);

        if (bccomp((string) $quoteWallet->available_balance, $totalAmount, 18) < 0) {
            return ['code' => ErrorCodes::SPOT_INSUFFICIENT_BALANCE];
        }

        return DB::transaction(function () use ($user, $symbol, $baseAsset, $quoteAsset, $price, $quantity, $totalAmount) {
            $this->walletService->lock($user->id, $quoteAsset, $totalAmount);

            $order = CryptoOrder::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'base_asset' => $baseAsset,
                'quote_asset' => $quoteAsset,
                'side' => 'buy',
                'type' => 'limit',
                'price' => $price,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'total_amount' => $totalAmount,
                'status' => 'open',
            ]);

            $this->matchingService->attemptMatch($order);

            return [
                'code' => ErrorCodes::SPOT_ORDER_CREATED,
                'order' => $order->fresh(),
            ];
        });
    }

    /**
     * Create a sell limit order.
     *
     * Locks the base asset (e.g. BTC) based on quantity.
     */
    public function createSell(ClientUser $user, array $symbolConfig, string $price, string $quantity): array
    {
        $baseAsset = $symbolConfig['base_asset'];
        $quoteAsset = $symbolConfig['quote_asset'];
        $symbol = $this->symbolKey($symbolConfig);

        // Validate balance
        $baseWallet = $this->walletService->getOrCreate($user->id, $baseAsset);

        if (bccomp((string) $baseWallet->available_balance, $quantity, 18) < 0) {
            return ['code' => ErrorCodes::SPOT_INSUFFICIENT_BALANCE];
        }

        return DB::transaction(function () use ($user, $symbol, $baseAsset, $quoteAsset, $price, $quantity) {
            $this->walletService->lock($user->id, $baseAsset, $quantity);

            $totalAmount = bcmul($price, $quantity, 18);

            $order = CryptoOrder::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'base_asset' => $baseAsset,
                'quote_asset' => $quoteAsset,
                'side' => 'sell',
                'type' => 'limit',
                'price' => $price,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'total_amount' => $totalAmount,
                'status' => 'open',
            ]);

            $this->matchingService->attemptMatch($order);

            return [
                'code' => ErrorCodes::SPOT_ORDER_CREATED,
                'order' => $order->fresh(),
            ];
        });
    }

    /**
     * Cancel an open order and unlock reserved assets.
     */
    public function cancel(ClientUser $user, CryptoOrder $order): array
    {
        if (! $order->isOpen()) {
            if ($order->status === 'cancelled') {
                return ['code' => ErrorCodes::SPOT_ORDER_ALREADY_CANCELLED];
            }

            return ['code' => ErrorCodes::SPOT_ORDER_ALREADY_FILLED];
        }

        if ($order->user_id !== $user->id) {
            return ['code' => ErrorCodes::SPOT_UNAUTHORIZED];
        }

        DB::transaction(function () use ($order) {
            $unlockAmount = $order->side === 'buy'
                ? bcmul($order->price, $order->remaining_quantity, 18)
                : $order->remaining_quantity;

            $unlockAsset = $order->side === 'buy' ? $order->quote_asset : $order->base_asset;

            $this->walletService->unlock($order->user_id, $unlockAsset, $unlockAmount);

            $order->update([
                'status' => 'cancelled',
                'remaining_quantity' => '0',
            ]);
        });

        return ['code' => ErrorCodes::SPOT_ORDER_CANCELLED, 'order' => $order->fresh()];
    }

    private function symbolKey(array $config): string
    {
        return $config['base_asset'].'_'.$config['quote_asset'];
    }
}

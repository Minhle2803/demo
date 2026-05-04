<?php

namespace App\Services\SpotTrading;

use App\Models\CryptoWallet;
use App\Models\CryptoWalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreate(int $userId, string $asset): CryptoWallet
    {
        $demoBalances = config('spot_trading.demo_balances', []);
        $demoAmount = $demoBalances[$asset] ?? '0';

        $wallet = CryptoWallet::firstOrCreate(
            ['user_id' => $userId, 'asset' => $asset],
            ['available_balance' => $demoAmount, 'locked_balance' => '0'],
        );

        if ($wallet->wasRecentlyCreated && bccomp($demoAmount, '0', 18) > 0) {
            $this->recordTransaction(
                userId: $userId,
                asset: $asset,
                type: 'credit',
                amount: $demoAmount,
                balanceBefore: '0',
                balanceAfter: $demoAmount,
                lockedBefore: '0',
                lockedAfter: '0',
                referenceType: 'demo_seed',
            );
        }

        return $wallet;
    }

    /**
     * Ensure demo wallets exist for all configured assets.
     */
    public function seedDemoWallets(int $userId): void
    {
        foreach (config('spot_trading.demo_balances', []) as $asset => $amount) {
            $this->getOrCreate($userId, $asset);
        }
    }

    /**
     * Lock available balance into locked balance for an open order.
     */
    public function lock(int $userId, string $asset, string $amount): CryptoWallet
    {
        return DB::transaction(function () use ($userId, $asset, $amount) {
            $wallet = CryptoWallet::where('user_id', $userId)
                ->where('asset', $asset)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeAvailable = $wallet->available_balance;
            $beforeLocked = $wallet->locked_balance;

            $wallet->available_balance = bcsub($beforeAvailable, $amount, 18);
            $wallet->locked_balance = bcadd($beforeLocked, $amount, 18);

            if (bccomp($wallet->available_balance, '0', 18) < 0) {
                throw new \RuntimeException('SPOT_NEGATIVE_BALANCE_BLOCKED');
            }

            $wallet->save();

            $this->recordTransaction(
                userId: $userId,
                asset: $asset,
                type: 'lock',
                amount: $amount,
                balanceBefore: $beforeAvailable,
                balanceAfter: $wallet->available_balance,
                lockedBefore: $beforeLocked,
                lockedAfter: $wallet->locked_balance,
            );

            return $wallet;
        });
    }

    /**
     * Unlock balance back to available (e.g. order cancellation).
     */
    public function unlock(int $userId, string $asset, string $amount): CryptoWallet
    {
        return DB::transaction(function () use ($userId, $asset, $amount) {
            $wallet = CryptoWallet::where('user_id', $userId)
                ->where('asset', $asset)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeAvailable = $wallet->available_balance;
            $beforeLocked = $wallet->locked_balance;

            $wallet->locked_balance = bcsub($beforeLocked, $amount, 18);
            $wallet->available_balance = bcadd($beforeAvailable, $amount, 18);

            if (bccomp($wallet->locked_balance, '0', 18) < 0) {
                throw new \RuntimeException('SPOT_NEGATIVE_BALANCE_BLOCKED');
            }

            $wallet->save();

            $this->recordTransaction(
                userId: $userId,
                asset: $asset,
                type: 'cancel_unlock',
                amount: $amount,
                balanceBefore: $beforeAvailable,
                balanceAfter: $wallet->available_balance,
                lockedBefore: $beforeLocked,
                lockedAfter: $wallet->locked_balance,
            );

            return $wallet;
        });
    }

    /**
     * Deduct from locked balance when order is matched.
     */
    public function debitLocked(int $userId, string $asset, string $amount): CryptoWallet
    {
        return DB::transaction(function () use ($userId, $asset, $amount) {
            $wallet = CryptoWallet::where('user_id', $userId)
                ->where('asset', $asset)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeLocked = $wallet->locked_balance;

            $wallet->locked_balance = bcsub($beforeLocked, $amount, 18);

            if (bccomp($wallet->locked_balance, '0', 18) < 0) {
                throw new \RuntimeException('SPOT_NEGATIVE_BALANCE_BLOCKED');
            }

            $wallet->save();

            $this->recordTransaction(
                userId: $userId,
                asset: $asset,
                type: 'debit',
                amount: $amount,
                balanceBefore: $wallet->available_balance,
                balanceAfter: $wallet->available_balance,
                lockedBefore: $beforeLocked,
                lockedAfter: $wallet->locked_balance,
            );

            return $wallet;
        });
    }

    /**
     * Credit available balance.
     */
    public function credit(int $userId, string $asset, string $amount): CryptoWallet
    {
        return DB::transaction(function () use ($userId, $asset, $amount) {
            $wallet = $this->getOrCreate($userId, $asset);

            $wallet = CryptoWallet::where('id', $wallet->id)->lockForUpdate()->firstOrFail();

            $beforeAvailable = $wallet->available_balance;

            $wallet->available_balance = bcadd($beforeAvailable, $amount, 18);
            $wallet->save();

            $this->recordTransaction(
                userId: $userId,
                asset: $asset,
                type: 'credit',
                amount: $amount,
                balanceBefore: $beforeAvailable,
                balanceAfter: $wallet->available_balance,
                lockedBefore: $wallet->locked_balance,
                lockedAfter: $wallet->locked_balance,
            );

            return $wallet;
        });
    }

    /**
     * Full settlement for a buyer after match.
     *
     * Buyer: receives base_asset, pays quote_asset at execution price.
     * If locked quote > required at execution price, refund the difference.
     */
    public function settleBuyer(
        int $userId,
        string $baseAsset,
        string $quoteAsset,
        string $matchedQty,
        string $executionPrice,
        string $lockedPrice,
        string $lockedTotalQuote,
    ): void {
        DB::transaction(function () use (
            $userId, $baseAsset, $quoteAsset, $matchedQty,
            $executionPrice, $lockedTotalQuote
        ) {
            $executionTotal = bcmul($executionPrice, $matchedQty, 18);

            // Deduct used quote from locked
            $this->debitLocked($userId, $quoteAsset, $executionTotal);

            // Refund quote difference if buy price > execution price
            $refund = bcsub($lockedTotalQuote, $executionTotal, 18);
            if (bccomp($refund, '0', 18) > 0) {
                $this->unlock($userId, $quoteAsset, $refund);
            }

            // Credit base asset to buyer
            $this->credit($userId, $baseAsset, $matchedQty);
        });
    }

    /**
     * Full settlement for a seller after match.
     *
     * Seller: pays base_asset from locked, receives quote_asset at execution price.
     */
    public function settleSeller(
        int $userId,
        string $baseAsset,
        string $quoteAsset,
        string $matchedQty,
        string $executionPrice,
    ): void {
        DB::transaction(function () use (
            $userId, $baseAsset, $quoteAsset, $matchedQty, $executionPrice
        ) {
            $executionTotal = bcmul($executionPrice, $matchedQty, 18);

            // Deduct base asset from locked
            $this->debitLocked($userId, $baseAsset, $matchedQty);

            // Credit quote asset to seller
            $this->credit($userId, $quoteAsset, $executionTotal);
        });
    }

    private function recordTransaction(
        int $userId,
        string $asset,
        string $type,
        string $amount,
        string $balanceBefore,
        string $balanceAfter,
        string $lockedBefore,
        string $lockedAfter,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): void {
        CryptoWalletTransaction::create([
            'user_id' => $userId,
            'asset' => $asset,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'locked_before' => $lockedBefore,
            'locked_after' => $lockedAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_at' => now(),
        ]);
    }
}

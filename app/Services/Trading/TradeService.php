<?php

namespace App\Services\Trading;

use App\Models\ClientUser;
use App\Models\Trade;
use App\Models\TradingSession;
use App\Support\ErrorCodes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TradeService
{
    /**
     * Validate and place a trade.
     *
     * @throws \Exception with ErrorCodes constant as message
     */
    public function placeTrade(ClientUser $user, TradingSession $session, string $type, float $amount): Trade
    {
        $this->assertUserFullyVerified($user);
        $this->assertSessionAcceptingTrades($session);
        $this->assertNoExistingTrade($user, $session);
        $this->assertSufficientBalance($user, $amount);

        return DB::transaction(function () use ($user, $session, $type, $amount) {
            // Re-check balance inside transaction with lock
            $freshUser = ClientUser::lockForUpdate()->find($user->id);

            if ($freshUser->balance < $amount) {
                throw new \Exception(ErrorCodes::TRADE_INSUFFICIENT_BALANCE);
            }

            // Re-check session inside transaction
            $freshSession = TradingSession::lockForUpdate()->find($session->id);

            if (! $freshSession->isOpen()) {
                throw new \Exception(ErrorCodes::TRADE_SESSION_LOCKED);
            }

            // Deduct from balance
            $freshUser->decrement('balance', $amount);

            // Create trade
            return Trade::create([
                'user_id' => $freshUser->id,
                'session_id' => $freshSession->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'pending',
                'payout' => 0,
            ]);
        });
    }

    protected function assertUserFullyVerified(ClientUser $user): void
    {
        $kycComplete = ! empty($user->kyc_front_url) && ! empty($user->kyc_back_url);

        if (! $user->is_verified || ! $kycComplete) {
            throw new \Exception(ErrorCodes::USER_NOT_FULLY_VERIFIED);
        }
    }

    protected function assertSessionAcceptingTrades(TradingSession $session): void
    {
        if ($session->status !== 'open') {
            throw new \Exception(ErrorCodes::TRADE_SESSION_NOT_OPEN);
        }

        // Server-side lock enforcement — do not trust frontend timer
        if (now()->gte(Carbon::parse($session->lock_time))) {
            throw new \Exception(ErrorCodes::TRADE_SESSION_LOCKED);
        }
    }

    protected function assertNoExistingTrade(ClientUser $user, TradingSession $session): void
    {
        $exists = Trade::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->exists();

        if ($exists) {
            throw new \Exception(ErrorCodes::TRADE_ALREADY_PLACED);
        }
    }

    protected function assertSufficientBalance(ClientUser $user, float $amount): void
    {
        if ($user->balance < $amount) {
            throw new \Exception(ErrorCodes::TRADE_INSUFFICIENT_BALANCE);
        }
    }
}

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
    public function __construct(
        protected TradingFeeService $feeService,
    ) {}

    /**
     * Validate and place a trade.
     *
     * @throws \Exception with ErrorCodes constant as message
     */
    public function placeTrade(ClientUser $user, TradingSession $session, string $type, float $amount): Trade
    {
        $this->assertSessionAcceptingTrades($session);
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

            // Create trade — fee is calculated and stored at settlement time.
            return Trade::create([
                'user_id' => $freshUser->id,
                'session_id' => $freshSession->id,
                'type' => $type,
                'amount' => $amount,
                'status' => 'pending',
                'payout' => 0,
                'trading_fee' => 0,
            ]);
        });
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

    protected function assertSufficientBalance(ClientUser $user, float $amount): void
    {
        if ($user->balance < $amount) {
            throw new \Exception(ErrorCodes::TRADE_INSUFFICIENT_BALANCE);
        }
    }
}

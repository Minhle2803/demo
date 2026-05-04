<?php

namespace App\Services\Trading;

use App\Events\TradingSessionResult;
use App\Events\TradingSessionUpdated;
use App\Models\TradingChartCandle;
use App\Models\TradingSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradingSessionService
{
    // Symbol and interval used for session-candle sync.
    // Must match the candle worker configuration.
    protected string $symbol = 'BTC_USDT';

    protected string $interval = '1m';

    /**
     * Return the current open session, or create one aligned to the current candle.
     */
    public function getCurrentSession(): ?TradingSession
    {
        $session = TradingSession::where('status', 'open')->latest('start_time')->first();

        if ($session) {
            return $session;
        }

        // Try to create one aligned to current candle
        return $this->createSessionFromCurrentCandle();
    }

    /**
     * Creates a session mapped exactly to the current (open) candle.
     * session.start_time = candle open time
     * session.end_time   = candle open time + 60s
     * session.lock_time  = end_time - 10s
     */
    public function createSessionFromCurrentCandle(): ?TradingSession
    {
        $candle = TradingChartCandle::where('symbol', $this->symbol)
            ->where('interval', $this->interval)
            ->where('status', 'open')
            ->orderByDesc('timestamp')
            ->first();

        if (! $candle) {
            Log::warning('TradingSessionService: No open candle found to create session.');

            return null;
        }

        // Prevent duplicate session for same candle
        $exists = TradingSession::where('candle_timestamp', $candle->timestamp)->first();
        if ($exists) {
            return $exists;
        }

        $startTime = Carbon::createFromTimestampMs($candle->timestamp);
        $endTime = $startTime->copy()->addSeconds(60);
        $lockTime = $endTime->copy()->subSeconds(10);

        return DB::transaction(function () use ($candle, $startTime, $lockTime, $endTime) {
            return TradingSession::create([
                'symbol' => $this->symbol,
                'interval' => $this->interval,
                'start_time' => $startTime,
                'lock_time' => $lockTime,
                'end_time' => $endTime,
                'status' => 'open',
                'open_price' => $candle->open,
                'close_price' => null,
                'candle_timestamp' => $candle->timestamp,
            ]);
        });
    }

    /**
     * Lock the session (called at 50s mark).
     */
    public function lockSession(TradingSession $session): void
    {
        DB::transaction(function () use ($session) {
            $session->update(['status' => 'locked']);
        });

        broadcast(new TradingSessionUpdated($session));
    }

    /**
     * Close the session and settle results from candle close price.
     */
    public function closeSession(TradingSession $session): void
    {
        $candle = TradingChartCandle::where('symbol', $this->symbol)
            ->where('interval', $this->interval)
            ->where('timestamp', $session->candle_timestamp)
            ->first();

        if (! $candle || $candle->status !== 'closed') {
            Log::warning("TradingSessionService: Candle not closed yet for session {$session->id}");

            return;
        }

        DB::transaction(function () use ($session, $candle) {
            $session->update([
                'status' => 'closed',
                'close_price' => $candle->close,
            ]);

            $this->settleTradesForSession($session, $candle->open, $candle->close);
        });

        broadcast(new TradingSessionUpdated($session->fresh()));
        broadcast(new TradingSessionResult($session->fresh()));
    }

    /**
     * Settle all pending trades for a closed session.
     */
    protected function settleTradesForSession(TradingSession $session, string $openPrice, string $closePrice): void
    {
        $trades = $session->trades()->where('status', 'pending')->get();

        foreach ($trades as $trade) {
            $win = match ($trade->type) {
                'buy' => $closePrice > $openPrice,
                'sell' => $closePrice < $openPrice,
                default => false,
            };

            $payout = $win ? $trade->amount * 2 : 0;

            $trade->update([
                'status' => $win ? 'win' : 'lose',
                'payout' => $payout,
            ]);

            // Credit winnings back to user balance
            if ($win) {
                $trade->user()->lockForUpdate()->first()
                    ->increment('balance', $payout);
            }
        }
    }
}

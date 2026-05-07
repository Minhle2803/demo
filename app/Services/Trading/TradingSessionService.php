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
     * Return the current open session for client trading.
     * Sessions flow: future → open → locked → closed (managed by worker).
     */
    public function getCurrentSession(): ?TradingSession
    {
        $session = TradingSession::where('status', 'open')->latest('start_time')->first();

        if ($session) {
            return $session;
        }

        // No open session — try activating the next future session if its time has come.
        $now = now();
        $nextSession = TradingSession::where('status', 'future')
            ->where('start_time', '<=', $now)
            ->orderBy('start_time')
            ->first();

        if ($nextSession) {
            $this->activateSession($nextSession);
            $session = $nextSession->fresh();
            broadcast(new TradingSessionUpdated($session));

            return $session;
        }

        return null;
    }

    /**
     * Activate a future session → open, syncing open_price from the realtime candle.
     */
    public function activateSession(TradingSession $session): void
    {
        $realtimeCandle = TradingChartCandle::forPair($session->symbol, $session->interval)
            ->realtime()
            ->where('timestamp', $session->candle_timestamp)
            ->first();

        $data = ['status' => 'open'];

        if ($realtimeCandle) {
            $data['open_price'] = $realtimeCandle->open;
        }

        $session->update($data);
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

        if (! $candle) {
            // Candle was deleted — close with open_price as fallback.
            Log::warning("TradingSessionService: Candle missing for session {$session->id}, closing with open_price fallback.");
            DB::transaction(function () use ($session) {
                $session->update([
                    'status' => 'closed',
                    'close_price' => $session->open_price ?? '0',
                ]);
            });

            return;
        }

        if ($candle->status !== 'closed') {
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
     * Create sessions for future candles that don't have sessions yet.
     * Called on each tick of the session worker to keep future sessions populated.
     */
    public function syncFutureSessions(): void
    {
        $offset = (int) config('trading_chart.future_session_offset', 10);

        // Fetch future candles that don't have a corresponding session yet.
        $futureCandles = TradingChartCandle::forPair($this->symbol, $this->interval)
            ->future()
            ->whereDoesntHaveSession()
            ->orderBy('timestamp')
            ->limit($offset)
            ->get();

        foreach ($futureCandles as $candle) {
            $startTime = Carbon::createFromTimestampMs($candle->timestamp);
            $endTime = $startTime->copy()->addSeconds(60);
            $lockTime = $endTime->copy()->subSeconds(10);

            DB::transaction(function () use ($candle, $startTime, $lockTime, $endTime) {
                // Guard against duplicate sessions from concurrent worker instances.
                $exists = TradingSession::where('symbol', $this->symbol)
                    ->where('interval', $this->interval)
                    ->where('candle_timestamp', $candle->timestamp)
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    return;
                }

                TradingSession::create([
                    'symbol' => $this->symbol,
                    'interval' => $this->interval,
                    'start_time' => $startTime,
                    'lock_time' => $lockTime,
                    'end_time' => $endTime,
                    'status' => 'future',
                    'open_price' => $candle->open,
                    'close_price' => $candle->close,
                    'candle_timestamp' => $candle->timestamp,
                ]);
            });
        }
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

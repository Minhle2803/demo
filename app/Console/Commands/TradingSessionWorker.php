<?php

namespace App\Console\Commands;

use App\Events\TradingSessionUpdated;
use App\Models\TradingChartCandle;
use App\Models\TradingSession;
use App\Services\Trading\TradingSessionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TradingSessionWorker extends Command
{
    protected $signature = 'trading:session-worker';

    protected $description = 'Manages trading session lifecycle: future → open → lock → close → repeat';

    public function handle(TradingSessionService $service): void
    {
        $this->info('Trading session worker started.');

        while (true) {
            try {
                $this->tick($service);
            } catch (\Throwable $e) {
                $this->error('Session worker error: '.$e->getMessage());
                \Log::error('TradingSessionWorker: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }

            sleep(1);
        }
    }

    protected function tick(TradingSessionService $service): void
    {
        $now = now();
        $nowMs = (int) (microtime(true) * 1000);

        // Step 0: Convert future candles at the current interval to realtime open.
        // Only the candle whose interval boundary has just arrived — not ALL past futures.
        $intervalMsMap = ['1m' => 60_000, '5m' => 300_000, '15m' => 900_000, '1h' => 3_600_000, '4h' => 14_400_000, '1d' => 86_400_000];
        $symbols = config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']);
        $intervals = config('trading_chart.intervals', ['1m', '5m']);

        foreach ($symbols as $symbol) {
            foreach ($intervals as $interval) {
                $intervalMs = $intervalMsMap[$interval];
                $currentIntervalTs = intdiv($nowMs, $intervalMs) * $intervalMs;

                $candle = TradingChartCandle::forPair($symbol, $interval)
                    ->where('timeline_type', TradingChartCandle::TIMELINE_FUTURE)
                    ->where('timestamp', $currentIntervalTs)
                    ->first();

                if ($candle) {
                    $candle->timeline_type = TradingChartCandle::TIMELINE_REALTIME;
                    $candle->status = TradingChartCandle::STATUS_OPEN;
                    $candle->save();
                }
            }
        }

        // Step 0b: Activate future sessions whose start time has arrived.
        $readySessions = TradingSession::where('status', 'future')
            ->where('start_time', '<=', $now)
            ->get();

        foreach ($readySessions as $ready) {
            $service->activateSession($ready);
            broadcast(new TradingSessionUpdated($ready->fresh()));
        }

        // Step 1: Lock an open session when lock time arrives.
        $openSession = TradingSession::where('status', 'open')->latest('start_time')->first();

        if ($openSession && $now->gte(Carbon::parse($openSession->lock_time))) {
            $this->info("Locking session #{$openSession->id}");
            $service->lockSession($openSession);
            $service->syncFutureSessions();

            return;
        }

        // Step 2: Close a locked session when end time arrives.
        $lockedSession = TradingSession::where('status', 'locked')->latest('start_time')->first();

        if ($lockedSession && $now->gte(Carbon::parse($lockedSession->end_time))) {
            $this->info("Closing session #{$lockedSession->id}");
            $service->closeSession($lockedSession);
            $service->syncFutureSessions();

            return;
        }

        // Step 3: Ensure future sessions exist for future candles.
        $service->syncFutureSessions();
    }
}

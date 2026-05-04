<?php

namespace App\Console\Commands;

use App\Events\TradingSessionUpdated;
use App\Models\TradingSession;
use App\Services\Trading\TradingSessionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TradingSessionWorker extends Command
{
    protected $signature = 'trading:session-worker';

    protected $description = 'Manages trading session lifecycle: open → lock → close → repeat';

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

        // Handle open session state transitions
        $openSession = TradingSession::where('status', 'open')->latest('start_time')->first();

        if ($openSession) {
            if ($now->gte(Carbon::parse($openSession->lock_time))) {
                $this->info("Locking session #{$openSession->id}");
                $service->lockSession($openSession);
            }

            return;
        }

        // Handle locked session → close when candle is closed
        $lockedSession = TradingSession::where('status', 'locked')->latest('start_time')->first();

        if ($lockedSession) {
            if ($now->gte(Carbon::parse($lockedSession->end_time))) {
                $this->info("Closing session #{$lockedSession->id}");
                $service->closeSession($lockedSession);
            }

            return;
        }

        // No open or locked session — create new one from current candle
        $this->info('Creating new session from current candle...');
        $session = $service->createSessionFromCurrentCandle();

        if ($session) {
            $this->info("New session #{$session->id} created.");
            broadcast(new TradingSessionUpdated($session));
        }
    }
}

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

        // Step 0: Activate future sessions whose start time has arrived.
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

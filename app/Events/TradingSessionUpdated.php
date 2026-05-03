<?php

namespace App\Events;

use App\Models\TradingSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class TradingSessionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public TradingSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel('trading.session')];
    }

    public function broadcastAs(): string
    {
        return 'session.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->session->id,
            'status' => $this->session->status,
            'start_time' => $this->session->start_time->toIso8601String(),
            'lock_time' => $this->session->lock_time->toIso8601String(),
            'end_time' => $this->session->end_time->toIso8601String(),
            'open_price' => $this->session->open_price,
            'close_price' => $this->session->close_price,
            'server_time' => now()->toIso8601String(),
        ];
    }
}

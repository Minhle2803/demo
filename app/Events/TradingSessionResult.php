<?php

namespace App\Events;

use App\Models\TradingSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class TradingSessionResult implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public TradingSession $session) {}

    public function broadcastOn(): array
    {
        return [new Channel("trading.result.{$this->session->id}")];
    }

    public function broadcastAs(): string
    {
        return 'session.result';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'open_price' => $this->session->open_price,
            'close_price' => $this->session->close_price,
            'status' => $this->session->status,
        ];
    }
}

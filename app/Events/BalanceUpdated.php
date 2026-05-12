<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BalanceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public float $newBalance,
        public string $changeType,
        public ?float $amount = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('user.'.$this->userId.'.balance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'balance.updated';
    }
}

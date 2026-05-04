<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpotOrderBookEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $symbol;

    public array $bids;

    public array $asks;

    public function __construct(string $symbol, array $bids, array $asks)
    {
        $this->symbol = $symbol;
        $this->bids = $bids;
        $this->asks = $asks;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("spot-orderbook.{$this->symbol}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'orderbook.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'symbol' => $this->symbol,
            'bids' => $this->bids,
            'asks' => $this->asks,
        ];
    }
}

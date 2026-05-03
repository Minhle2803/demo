<?php

namespace App\Events;

use App\Models\TradingChartCandle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TradingChartCandleEvent
 *
 * Single broadcast event class for all three candle event types.
 * Event name is resolved at runtime via broadcastAs() based on $eventType.
 *
 * Using one class instead of three avoids duplicating channel + payload logic
 * while keeping each event name distinct on the frontend.
 *
 * Channel:  chart.{symbol}.{interval}   e.g. chart.BTC_USDT.1m
 * Events:   candle.update | candle.close | candle.rewrite
 */
class TradingChartCandleEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // -------------------------------------------------------------------------
    // Event type constants — used by ChartBroadcastService and frontend
    // -------------------------------------------------------------------------

    const TYPE_UPDATE = 'candle.update';

    const TYPE_CLOSE = 'candle.close';

    const TYPE_REWRITE = 'candle.rewrite';

    // -------------------------------------------------------------------------
    // Public properties are automatically included in the broadcast payload.
    // -------------------------------------------------------------------------

    public string $event;

    public string $symbol;

    public string $interval;

    public array $data;

    /**
     * @param  TradingChartCandle  $candle  The candle being broadcast
     * @param  string  $eventType  One of the TYPE_* constants
     */
    public function __construct(TradingChartCandle $candle, string $eventType)
    {
        $this->event = $eventType;
        $this->symbol = $candle->symbol;
        $this->interval = $candle->interval;
        $this->data = $this->buildPayload($candle, $eventType);
    }

    // -------------------------------------------------------------------------
    // Channel
    // -------------------------------------------------------------------------

    /**
     * Broadcast on a public channel so the demo frontend can connect without auth.
     *
     * Switch to PrivateChannel and add a route in channels.php if you want
     * to restrict access to authenticated client users.
     *
     * Channel name: chart.BTC_USDT.1m
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("chart.{$this->symbol}.{$this->interval}"),
        ];
    }

    // -------------------------------------------------------------------------
    // Event name
    // -------------------------------------------------------------------------

    /**
     * The event name the frontend listens for.
     * Overrides the default Laravel class-name-derived event name.
     *
     * Returns: 'candle.update' | 'candle.close' | 'candle.rewrite'
     */
    public function broadcastAs(): string
    {
        return $this->event;
    }

    // -------------------------------------------------------------------------
    // Payload
    // -------------------------------------------------------------------------

    /**
     * Broadcast payload.
     *
     * All public properties are broadcast automatically by Laravel, but we also
     * define broadcastWith() explicitly for clarity and to control field order.
     */
    public function broadcastWith(): array
    {
        return [
            'event' => $this->event,
            'symbol' => $this->symbol,
            'interval' => $this->interval,
            'data' => $this->data,
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build the data payload for a candle event.
     *
     * candle.update  → full OHLCV + status (open candle snapshot)
     * candle.close   → full OHLCV + status (finalized candle)
     * candle.rewrite → full OHLCV + is_modified flag (overwritten candle)
     */
    private function buildPayload(TradingChartCandle $candle, string $eventType): array
    {
        $base = $candle->toChartArray(); // timestamp, open, high, low, close, volume

        return match ($eventType) {
            self::TYPE_UPDATE => array_merge($base, [
                'status' => TradingChartCandle::STATUS_OPEN,
            ]),

            self::TYPE_CLOSE => array_merge($base, [
                'status' => TradingChartCandle::STATUS_CLOSED,
            ]),

            self::TYPE_REWRITE => array_merge($base, [
                'status' => $candle->status,
                'is_modified' => true,
            ]),

            default => $base,
        };
    }
}

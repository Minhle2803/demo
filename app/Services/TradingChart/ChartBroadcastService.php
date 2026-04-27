<?php

namespace App\Services\TradingChart;

use App\Events\TradingChartCandleEvent;
use App\Models\TradingChartCandle;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ChartBroadcastService
 *
 * Single point of contact for all WebSocket broadcasting in the chart system.
 * Wraps event dispatch so the worker and controllers never touch broadcast
 * infrastructure directly.
 *
 * Responsibilities:
 *   - Fire the correct TradingChartCandleEvent type.
 *   - Swallow and log broadcast failures so a Reverb hiccup never crashes the worker.
 *   - Support bulk rewrite broadcasting (one event per candle, or a range summary).
 *
 * Does NOT know about price generation, DB writes, or rules.
 */
class ChartBroadcastService
{
    // -------------------------------------------------------------------------
    // Public API — one method per event type
    // -------------------------------------------------------------------------

    /**
     * Broadcast a candle.update event.
     *
     * Called by the worker on every tick for the current open candle.
     * Frontend: calls chart.updateData(candle) to update the live candle in place.
     */
    public function broadcastUpdate(TradingChartCandle $candle): void
    {
        $this->dispatch($candle, TradingChartCandleEvent::TYPE_UPDATE);
    }

    /**
     * Broadcast a candle.close event.
     *
     * Called by the worker when a candle interval ends and the candle is finalized.
     * Frontend: calls chart.updateData(candle) — same as update but signals finality.
     */
    public function broadcastClose(TradingChartCandle $candle): void
    {
        $this->dispatch($candle, TradingChartCandleEvent::TYPE_CLOSE);
    }

    /**
     * Broadcast a candle.rewrite event for a single candle.
     *
     * Called by the rewrite-range API after overwriting a candle.
     * Frontend: on candle.rewrite, reload the affected range from the REST API
     * rather than applying a single updateData — rewritten candles break the
     * assumed price continuity of incremental updates.
     */
    public function broadcastRewrite(TradingChartCandle $candle): void
    {
        $this->dispatch($candle, TradingChartCandleEvent::TYPE_REWRITE);
    }

    /**
     * Broadcast candle.rewrite for a collection of candles.
     *
     * Called by the rewrite-range API after a bulk rewrite.
     *
     * Strategy:
     *   - If count <= INDIVIDUAL_REWRITE_THRESHOLD: fire one event per candle.
     *   - If count > threshold: fire a single range summary event.
     *     Frontend receives from_timestamp + to_timestamp and does a full range reload.
     *
     * This prevents flooding the WebSocket channel with 1000 events for a large rewrite.
     *
     * @param  TradingChartCandle[] $candles   Ordered collection of rewritten candles
     */
    public function broadcastRewriteRange(array $candles): void
    {
        if (empty($candles)) {
            return;
        }

        if (count($candles) <= self::INDIVIDUAL_REWRITE_THRESHOLD) {
            foreach ($candles as $candle) {
                $this->broadcastRewrite($candle);
            }
            return;
        }

        // Too many candles — send a single range summary event instead.
        $this->broadcastRangeSummary($candles);
    }

    // -------------------------------------------------------------------------
    // Threshold
    // -------------------------------------------------------------------------

    /**
     * Maximum number of individual candle.rewrite events to broadcast.
     * Above this, a single range summary event is sent instead.
     */
    private const INDIVIDUAL_REWRITE_THRESHOLD = 50;

    // -------------------------------------------------------------------------
    // Core dispatcher
    // -------------------------------------------------------------------------

    /**
     * Fire the broadcast event, catching any Reverb / queue failure gracefully.
     *
     * Broadcasting failures (Reverb down, queue driver unavailable) must NEVER
     * crash the worker or block an API response. The candle is already saved
     * to DB — the frontend can recover by polling the REST API.
     */
    private function dispatch(TradingChartCandle $candle, string $eventType): void
    {
        try {
            broadcast(new TradingChartCandleEvent($candle, $eventType));
        } catch (Throwable $e) {
            Log::warning('[ChartBroadcast] Failed to broadcast event', [
                'event'     => $eventType,
                'symbol'    => $candle->symbol,
                'interval'  => $candle->interval,
                'timestamp' => $candle->timestamp,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Fire a single range summary event when the rewrite batch is too large
     * for individual events.
     *
     * This is a synthetic candle.rewrite payload that carries the range
     * boundaries instead of individual candle data. The frontend detects
     * the presence of from_timestamp/to_timestamp and triggers a range reload.
     *
     * @param  TradingChartCandle[] $candles
     */
    private function broadcastRangeSummary(array $candles): void
    {
        // Use first and last candle to get the range boundaries.
        $first  = $candles[0];
        $last   = $candles[array_key_last($candles)];
        $count  = count($candles);

        try {
            // Build a synthetic event directly on the first candle's channel.
            // We override broadcastWith() by dispatching a customised event.
            broadcast(new class($first, $last, $count) extends TradingChartCandleEvent
            {
                private TradingChartCandle $lastCandle;
                private int $count;

                public function __construct(
                    TradingChartCandle $first,
                    TradingChartCandle $last,
                    int $count,
                ) {
                    // Skip parent constructor — we build payload manually.
                    $this->event    = TradingChartCandleEvent::TYPE_REWRITE;
                    $this->symbol   = $first->symbol;
                    $this->interval = $first->interval;
                    $this->data     = []; // overridden in broadcastWith()

                    $this->lastCandle = $last;
                    $this->count      = $count;
                }

                public function broadcastWith(): array
                {
                    return [
                        'event'          => $this->event,
                        'symbol'         => $this->symbol,
                        'interval'       => $this->interval,
                        'data'           => [
                            'type'           => 'range',
                            'from_timestamp' => (int) $this->data['timestamp'] ?? 0,
                            'to_timestamp'   => (int) $this->lastCandle->timestamp,
                            'count'          => $this->count,
                        ],
                    ];
                }
            });
        } catch (Throwable $e) {
            Log::warning('[ChartBroadcast] Failed to broadcast range summary', [
                'symbol'   => $first->symbol,
                'interval' => $first->interval,
                'count'    => $count,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}

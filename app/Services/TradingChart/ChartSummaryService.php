<?php

namespace App\Services\TradingChart;

use App\Models\TradingChartCandle;
use App\Models\TradingChartSummary;
use Illuminate\Support\Facades\DB;

class ChartSummaryService
{
    private array $ranges = [
        '1H' => 3600,
        '7D' => 604800,
        '1M' => 2592000,
        '1Y' => 31536000,
    ];

    public function refreshAll(): void
    {
        TradingChartSummary::query()->delete();

        $combinations = TradingChartCandle::query()
            ->select('symbol', 'interval')
            ->distinct()
            ->get();

        foreach ($combinations as $combo) {
            $latestCandle = TradingChartCandle::query()
                ->where('symbol', $combo->symbol)
                ->where('interval', $combo->interval)
                ->orderByDesc('timestamp')
                ->first();

            if (! $latestCandle) {
                continue;
            }

            foreach ($this->ranges as $range => $seconds) {
                $to = (int) $latestCandle->timestamp;
                $from = $to - $seconds;

                $this->createSummaryFromRange($latestCandle, $range, $from, $to);
            }
        }
    }

    public function applyCandle(TradingChartCandle $candle): void
    {
        foreach ($this->ranges as $range => $seconds) {
            $this->applyCandleToRange($candle, $range, $seconds);
        }
    }

    private function applyCandleToRange(
        TradingChartCandle $candle,
        string $range,
        int $rangeSeconds
    ): void {
        DB::transaction(function () use ($candle, $range, $rangeSeconds) {
            $to = (int) $candle->timestamp;
            $from = $to - $rangeSeconds;

            $summary = TradingChartSummary::query()
                ->where('symbol', $candle->symbol)
                ->where('interval', $candle->interval)
                ->where('range', $range)
                ->lockForUpdate()
                ->first();

            if (! $summary) {
                $this->createSummaryFromRange($candle, $range, $from, $to);

                return;
            }

            // Nếu candle hiện tại đã xử lý rồi thì không cộng volume lại nữa
            if ((int) $summary->last_candle_timestamp === (int) $candle->timestamp) {
                $this->updateCurrentCandleOnly($summary, $candle, $from, $to);

                return;
            }

            $this->removeExpiredCandles($summary, $from);

            $summary->current_price = $candle->close;
            $summary->to_timestamp = $to;
            $summary->from_timestamp = $from;
            $summary->last_candle_timestamp = $candle->timestamp;

            if ((float) $candle->high >= (float) $summary->high) {
                $summary->high = $candle->high;
                $summary->high_timestamp = $candle->timestamp;
            }

            if ((float) $summary->low == 0 || (float) $candle->low <= (float) $summary->low) {
                $summary->low = $candle->low;
                $summary->low_timestamp = $candle->timestamp;
            }

            $summary->market_volume = (float) $summary->market_volume + (float) $candle->volume;

            $summary->change_percent = $this->calcChangePercent(
                (float) $summary->open_price,
                (float) $summary->current_price
            );

            $summary->save();
        });
    }

    private function createSummaryFromRange(
        TradingChartCandle $candle,
        string $range,
        int $from,
        int $to
    ): void {
        $query = TradingChartCandle::query()
            ->where('symbol', $candle->symbol)
            ->where('interval', $candle->interval)
            ->whereBetween('timestamp', [$from, $to]);

        $first = (clone $query)->orderBy('timestamp')->first();
        $last = (clone $query)->orderByDesc('timestamp')->first();

        if (! $first || ! $last) {
            return;
        }

        $highCandle = (clone $query)->orderByDesc('high')->first();
        $lowCandle = (clone $query)->orderBy('low')->first();

        TradingChartSummary::updateOrCreate(
            [
                'symbol' => $candle->symbol,
                'interval' => $candle->interval,
                'range' => $range,
            ],
            [
                'open_price' => $first->open,
                'current_price' => $last->close,
                'high' => $highCandle->high,
                'low' => $lowCandle->low,
                'market_volume' => (clone $query)->sum('volume'),
                'change_percent' => $this->calcChangePercent(
                    (float) $first->open,
                    (float) $last->close
                ),
                'from_timestamp' => $from,
                'to_timestamp' => $to,
                'open_timestamp' => $first->timestamp,
                'high_timestamp' => $highCandle->timestamp,
                'low_timestamp' => $lowCandle->timestamp,
                'last_candle_timestamp' => $last->timestamp,
            ]
        );
    }

    private function updateCurrentCandleOnly(
        TradingChartSummary $summary,
        TradingChartCandle $candle,
        int $from,
        int $to
    ): void {
        $summary->current_price = $candle->close;
        $summary->to_timestamp = $to;
        $summary->from_timestamp = $from;

        if ((float) $candle->high >= (float) $summary->high) {
            $summary->high = $candle->high;
            $summary->high_timestamp = $candle->timestamp;
        }

        if ((float) $candle->low <= (float) $summary->low) {
            $summary->low = $candle->low;
            $summary->low_timestamp = $candle->timestamp;
        }

        $summary->change_percent = $this->calcChangePercent(
            (float) $summary->open_price,
            (float) $summary->current_price
        );

        $summary->save();
    }

    private function removeExpiredCandles(TradingChartSummary $summary, int $from): void
    {
        if ($summary->open_timestamp !== null && $summary->open_timestamp < $from) {
            $newOpen = TradingChartCandle::query()
                ->where('symbol', $summary->symbol)
                ->where('interval', $summary->interval)
                ->where('timestamp', '>=', $from)
                ->orderBy('timestamp')
                ->first();

            if ($newOpen) {
                $summary->open_price = $newOpen->open;
                $summary->open_timestamp = $newOpen->timestamp;
            }
        }

        if ($summary->high_timestamp !== null && $summary->high_timestamp < $from) {
            $newHigh = TradingChartCandle::query()
                ->where('symbol', $summary->symbol)
                ->where('interval', $summary->interval)
                ->where('timestamp', '>=', $from)
                ->orderByDesc('high')
                ->first();

            if ($newHigh) {
                $summary->high = $newHigh->high;
                $summary->high_timestamp = $newHigh->timestamp;
            }
        }

        if ($summary->low_timestamp !== null && $summary->low_timestamp < $from) {
            $newLow = TradingChartCandle::query()
                ->where('symbol', $summary->symbol)
                ->where('interval', $summary->interval)
                ->where('timestamp', '>=', $from)
                ->orderBy('low')
                ->first();

            if ($newLow) {
                $summary->low = $newLow->low;
                $summary->low_timestamp = $newLow->timestamp;
            }
        }

        $expiredVolume = TradingChartCandle::query()
            ->where('symbol', $summary->symbol)
            ->where('interval', $summary->interval)
            ->where('timestamp', '<', $from)
            ->where('timestamp', '>=', $summary->from_timestamp ?? 0)
            ->sum('volume');

        if ($expiredVolume > 0) {
            $summary->market_volume = max(
                0,
                (float) $summary->market_volume - (float) $expiredVolume
            );
        }
    }

    private function calcChangePercent(float $open, float $current): float
    {
        if ($open <= 0) {
            return 0;
        }

        return (($current - $open) / $open) * 100;
    }
}

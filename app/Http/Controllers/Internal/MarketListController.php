<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\TradingChartSummary;
use Illuminate\Http\Request;

class MarketListController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'interval' => ['nullable', 'string'],
            'range' => ['nullable', 'in:1H,7D,1M,1Y'],
        ]);

        $interval = $request->input('interval', '1m');
        $range = $request->input('range', '1H');

        $coinMeta = config('trading_chart.coins', []);

        $summaries = TradingChartSummary::query()
            ->where('interval', $interval)
            ->where('range', $range)
            ->orderBy('symbol')
            ->get();

        $data = $summaries->map(function ($summary) use ($coinMeta) {
            $symbol = $summary->symbol;

            $currentPrice = (float) $summary->current_price;
            $openPrice = (float) $summary->open_price;

            $changeValue = $currentPrice - $openPrice;

            $meta = $coinMeta[$symbol] ?? [];

            return [
                'symbol' => $symbol,
                'base_symbol' => str_replace('_USDT', '', $symbol),
                'name' => $meta['name'] ?? $symbol,
                'icon' => $meta['icon'] ?? null,

                'price' => round($currentPrice, 8),
                'open_price' => round($openPrice, 8),
                'change_value' => round($changeValue, 8),
                'change_percent' => round((float) $summary->change_percent, 2),

                'market_cap' => $this->resolveMarketCap($currentPrice, $meta),
                'market_volume' => round((float) $summary->market_volume, 8),

                'updated_at' => $summary->updated_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'code' => 'MARKET_LIST_FETCHED',
            'data' => $data,
        ]);
    }

    private function resolveMarketCap(float $currentPrice, array $meta): float
    {
        if (isset($meta['market_cap'])) {
            return (float) $meta['market_cap'];
        }

        if (isset($meta['total_supply'])) {
            return round($currentPrice * (float) $meta['total_supply'], 2);
        }

        return 0;
    }
}

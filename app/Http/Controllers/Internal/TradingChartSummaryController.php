<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\TradingChartSummary;
use Illuminate\Http\Request;

class TradingChartSummaryController extends Controller
{
    public function show(Request $request)
    {
        $request->validate([
            'symbol' => ['required', 'string'],
            'interval' => ['required', 'string'],
            'range' => ['nullable', 'in:1H,7D,1M,1Y'],
        ]);

        $summary = TradingChartSummary::query()
            ->where('symbol', $request->symbol)
            ->where('interval', $request->interval)
            ->where('range', $request->input('range', '1H'))
            ->first();

        if (! $summary) {
            return response()->json([
                'success' => false,
                'code' => 'CHART_SUMMARY_NOT_FOUND',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 'CHART_SUMMARY_FETCHED',
            'data' => [
                'symbol' => $summary->symbol,
                'interval' => $summary->interval,
                'range' => $summary->range,
                'current_price' => (float) $summary->current_price,
                'open_price' => (float) $summary->open_price,
                'high' => (float) $summary->high,
                'low' => (float) $summary->low,
                'market_volume' => (float) $summary->market_volume,
                'change_percent' => round((float) $summary->change_percent, 2),
                'from_timestamp' => $summary->from_timestamp,
                'to_timestamp' => $summary->to_timestamp,
                'updated_at' => $summary->updated_at?->toDateTimeString(),
            ],
        ]);
    }
}

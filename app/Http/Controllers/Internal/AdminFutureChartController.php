<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\TradingChartCandle;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminFutureChartController extends Controller
{
    /**
     * Return future candles for the admin dashboard chart.
     * Only future timeline candles are returned — no realtime data.
     */
    public function candles(Request $request): JsonResponse
    {
        $request->validate([
            'symbol' => ['required', 'string'],
            'interval' => ['required', 'string'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:1000'],
        ]);

        $symbol = $request->input('symbol');
        $interval = $request->input('interval');
        $limit = min((int) $request->input('limit', 500), 1000);

        $candles = TradingChartCandle::forPair($symbol, $interval)
            ->future()
            ->latestFirst()
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        $data = $candles->map(fn (TradingChartCandle $c) => $c->toChartArray())->values()->all();

        return response()->json([
            'success' => true,
            'code' => ErrorCodes::CHART_CANDLES_FETCHED,
            'data' => $data,
        ]);
    }
}

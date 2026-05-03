<?php

namespace App\Http\Controllers;

use App\Models\Trade;

class TraddingController extends Controller
{
    public function index()
    {
        $trades = Trade::query()
            ->join('trading_sessions', 'trades.session_id', '=', 'trading_sessions.id')
            ->select([
                'trades.id',
                'trades.user_id',
                'trades.session_id',
                'trades.type',
                'trades.amount',
                'trades.status',
                'trades.payout',
                'trades.created_at',
                'trading_sessions.symbol as session_symbol',
                'trading_sessions.open_price as session_open_price',
                'trading_sessions.close_price as session_close_price',
            ])
            ->orderByDesc('trades.session_id')
            ->orderByDesc('trades.id')
            ->paginate(20);

        return view('pages.tradding', compact('trades'));
    }
}

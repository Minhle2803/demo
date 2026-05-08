<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingSession;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = TradingSession::query()->orderByDesc('start_time');

        if ($request->input('session_type') === 'realtime') {
            return $this->realtimeIndex($request);
        }

        if ($request->input('session_type') === 'future') {
            $query->future();
        } elseif ($request->input('session_type') === 'all') {
            // no status filter — show everything
        } else {
            // Default: show all active sessions (future, open, locked) except closed.
            $query->whereIn('status', ['future', 'open', 'locked']);
        }

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->input('symbol'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->where('start_time', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('start_time', '<=', $request->input('to'));
        }

        $sessions = $query->paginate(25)->withQueryString();
        $symbols = TradingSession::distinct()->pluck('symbol')->toArray();

        return view('pages.admin.sessions.index', compact('sessions', 'symbols'));
    }

    protected function realtimeIndex(Request $request)
    {
        $query = TradingSession::query()->realtime()->orderByDesc('start_time');

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->input('symbol'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->where('start_time', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('start_time', '<=', $request->input('to'));
        }

        $sessions = $query->paginate(25)->withQueryString();
        $symbols = TradingSession::distinct()->pluck('symbol')->toArray();

        return view('pages.admin.sessions.realtime', compact('sessions', 'symbols'));
    }

    public function show(int $id)
    {
        $session = TradingSession::findOrFail($id);

        $trades = $session->trades()
            ->with('user')
            ->orderByDesc('id')
            ->paginate(25);

        return view('pages.admin.sessions.show', compact('session', 'trades'));
    }
}

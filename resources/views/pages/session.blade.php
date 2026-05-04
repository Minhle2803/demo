@extends('layouts.app')

@section('title', 'Trading')

@section('content')
<div class="trading-container">
    <div class="session-info">
        <span>Session: <strong id="session-id">—</strong></span>
        <span>Status: <strong id="session-status">Loading...</strong></span>
        <span>Time left: <strong id="session-countdown">—</strong></span>
    </div>

    <div class="trade-form">
        <input
            type="number"
            id="trade-amount"
            placeholder="Amount"
            min="1"
            step="1"
        />

        <button id="btn-buy" disabled>BUY ↑</button>
        <button id="btn-sell" disabled>SELL ↓</button>
    </div>

    <div id="trade-result-popup" class="hidden"></div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/trading-session/main.js')
@endpush

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chart Demo — Internal</title>

    {{-- Vite compiled entry --}}
    @vite(['resources/js/chart-demo.js'])

    {{--
        Inject Laravel server-side config into window.CHART_CONFIG.
        chart-demo.js reads from this object.
    --}}
    <script>
        window.CHART_CONFIG = {
            apiBase:         '{{ url("/api/internal/chart") }}',
            reverbHost:      '{{ env('REVERB_HOST', '0.0.0.0') }}',
            reverbPort:      {{ (int) env("REVERB_PORT", 8080) }},
            reverbKey:       '{{ env("REVERB_APP_KEY", "") }}',
            reverbScheme:    '{{ env("REVERB_SCHEME", "http") }}',
            defaultSymbol:   '{{ collect(config("trading_chart.symbols", ["BTC_USDT"]))->first() }}',
            defaultInterval: '{{ collect(config("trading_chart.intervals", ["1m"]))->first() }}',
        };
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #080c12; --surface: #0d1420; --surface-2: #111b2b;
            --border: #1c2d44; --border-bright: #243b58;
            --text: #c8d8ea; --text-dim: #4e6680; --text-muted: #2a3f58;
            --accent: #00c9ff; --accent-dim: #0a4a66;
            --green: #00e676; --green-dim: #003d1e;
            --red: #ff3d5a; --red-dim: #3d0013; --yellow: #ffc107;
            --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
            --font-ui: 'IBM Plex Sans', system-ui, sans-serif;
        }
        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: var(--font-ui); font-size: 13px; overflow: hidden; }
        .shell { display: grid; grid-template-rows: 48px 1fr 28px; height: 100vh; }
        .topbar { display: flex; align-items: center; border-bottom: 1px solid var(--border); background: var(--surface); padding: 0 16px; }
        .topbar-brand { font-family: var(--font-mono); font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--accent); margin-right: 20px; }
        .topbar-brand span { color: var(--text-dim); font-weight: 400; }
        .symbol-tabs { display: flex; gap: 2px; }
        .symbol-tab { padding: 0 14px; height: 48px; display: flex; align-items: center; gap: 7px; font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--text-dim); cursor: pointer; border: none; background: none; border-bottom: 2px solid transparent; transition: color .15s, border-color .15s; }
        .symbol-tab:hover { color: var(--text); }
        .symbol-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
        .symbol-tab .price { font-size: 10px; font-weight: 400; opacity: .7; }
        .change { font-size: 9px; padding: 1px 4px; border-radius: 2px; }
        .change.up { background: var(--green-dim); color: var(--green); }
        .change.down { background: var(--red-dim); color: var(--red); }
        .topbar-gap { flex: 1; }
        .interval-pills { display: flex; gap: 2px; }
        .interval-pill { padding: 4px 10px; font-family: var(--font-mono); font-size: 10px; font-weight: 600; letter-spacing: .06em; color: var(--text-dim); cursor: pointer; border: 1px solid transparent; border-radius: 3px; background: none; transition: all .12s; }
        .interval-pill:hover { color: var(--text); border-color: var(--border); }
        .interval-pill.active { color: var(--accent); background: var(--accent-dim); border-color: var(--accent-dim); }
        .ws-status { display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-left: 16px; }
        .ws-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--text-muted); transition: background .3s; }
        .ws-dot.connected { background: var(--green); box-shadow: 0 0 6px var(--green); }
        .ws-dot.connecting { background: var(--yellow); animation: pulse 1s infinite; }
        .ws-dot.disconnected { background: var(--red); }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
        .main { display: grid; grid-template-columns: 1fr 220px; overflow: hidden; height: 100%; }
        .chart-pane { display: flex; flex-direction: column; overflow: hidden; border-right: 1px solid var(--border); position: relative; height: 100%; }
        .ohlcv-bar { display: flex; align-items: center; gap: 16px; padding: 6px 14px; border-bottom: 1px solid var(--border); background: var(--surface); font-family: var(--font-mono); font-size: 11px; flex-shrink: 0; }
        .ohlcv-label { color: var(--text-muted); font-size: 9px; letter-spacing: .08em; text-transform: uppercase; margin-right: 2px; }
        .ohlcv-val { color: var(--text); }
        .ohlcv-val.up { color: var(--green); }
        .ohlcv-val.down { color: var(--red); }
        #chart-container { flex: 1; min-height: 0; position: relative; width: 100%; height: 100%; }
        .loading-overlay { position: absolute; inset: 0; background: var(--bg); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; z-index: 5; transition: opacity .3s; }
        .loading-overlay.hidden { opacity: 0; pointer-events: none; }
        .spinner { width: 28px; height: 28px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-family: var(--font-mono); font-size: 10px; letter-spacing: .1em; color: var(--text-dim); text-transform: uppercase; }
        .sidebar { display: flex; flex-direction: column; background: var(--surface); overflow: hidden; }
        .sidebar-section { padding: 10px 12px; border-bottom: 1px solid var(--border); }
        .sidebar-title { font-family: var(--font-mono); font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
        .live-candle { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .lc-item { display: flex; flex-direction: column; gap: 2px; }
        .lc-label { font-family: var(--font-mono); font-size: 9px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; }
        .lc-val { font-family: var(--font-mono); font-size: 12px; color: var(--text); transition: color .2s; }
        .lc-val.flash-up { color: var(--green); }
        .lc-val.flash-down { color: var(--red); }
        .event-log { flex: 1; overflow-y: auto; padding: 6px 12px; display: flex; flex-direction: column; gap: 3px; }
        .event-log::-webkit-scrollbar { width: 3px; }
        .event-log::-webkit-scrollbar-thumb { background: var(--border); }
        .log-entry { display: flex; gap: 6px; align-items: flex-start; padding: 3px 0; border-bottom: 1px solid var(--border); animation: slideIn .15s ease; }
        @keyframes slideIn { from{opacity:0;transform:translateX(6px)} to{opacity:1;transform:translateX(0)} }
        .log-time { font-family: var(--font-mono); font-size: 9px; color: var(--text-muted); flex-shrink: 0; margin-top: 1px; }
        .log-badge { font-family: var(--font-mono); font-size: 8px; font-weight: 700; letter-spacing: .06em; padding: 1px 5px; border-radius: 2px; flex-shrink: 0; text-transform: uppercase; margin-top: 1px; }
        .badge-update  { background: var(--accent-dim); color: var(--accent); }
        .badge-close   { background: var(--green-dim);  color: var(--green); }
        .badge-rewrite { background: var(--red-dim);    color: var(--red); }
        .badge-error   { background: var(--red-dim);    color: var(--red); }
        .badge-info    { background: var(--surface-2);  color: var(--text-dim); }
        .log-msg { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); word-break: break-all; line-height: 1.5; }
        .statusbar { display: flex; align-items: center; gap: 12px; padding: 0 14px; border-top: 1px solid var(--border); background: var(--surface); font-family: var(--font-mono); font-size: 10px; color: var(--text-muted); }
        .statusbar-sep { color: var(--border); }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <div class="topbar-brand">CHART<span>/INTERNAL</span></div>
        <div class="symbol-tabs" id="symbol-tabs">
            @foreach(config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']) as $sym)
            <button class="symbol-tab {{ $loop->first ? 'active' : '' }}" data-symbol="{{ $sym }}">
                {{ str_replace('_', '/', $sym) }}
                <span class="price" id="price-{{ $sym }}">—</span>
                <span class="change up" id="change-{{ $sym }}">—</span>
            </button>
            @endforeach
        </div>
        <div class="topbar-gap"></div>
        <div class="interval-pills" id="interval-pills">
            @foreach(config('trading_chart.intervals', ['1m', '5m']) as $iv)
            <button class="interval-pill {{ $loop->first ? 'active' : '' }}" data-interval="{{ $iv }}">{{ $iv }}</button>
            @endforeach
        </div>
        <div class="ws-status">
            <div class="ws-dot" id="ws-dot"></div>
            <span id="ws-label">OFFLINE</span>
        </div>
    </header>

    <div class="main">
        <div class="chart-pane">
            <div class="ohlcv-bar">
                <span><span class="ohlcv-label">O</span><span class="ohlcv-val" id="ov-open">—</span></span>
                <span><span class="ohlcv-label">H</span><span class="ohlcv-val" id="ov-high">—</span></span>
                <span><span class="ohlcv-label">L</span><span class="ohlcv-val" id="ov-low">—</span></span>
                <span><span class="ohlcv-label">C</span><span class="ohlcv-val" id="ov-close">—</span></span>
                <span><span class="ohlcv-label">V</span><span class="ohlcv-val" id="ov-volume">—</span></span>
                <span style="margin-left:auto;font-size:9px;color:var(--text-muted)" id="ov-ts">—</span>
            </div>
            <div id="chart-container">
                <div class="loading-overlay" id="loading">
                    <div class="spinner"></div>
                    <div class="loading-text" id="loading-text">Loading candles…</div>
                </div>
            </div>
        </div>
        <aside class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title">Live Candle</div>
                <div class="live-candle">
                    <div class="lc-item"><span class="lc-label">Open</span><span class="lc-val" id="lc-open">—</span></div>
                    <div class="lc-item"><span class="lc-label">Close</span><span class="lc-val" id="lc-close">—</span></div>
                    <div class="lc-item"><span class="lc-label">High</span><span class="lc-val" id="lc-high">—</span></div>
                    <div class="lc-item"><span class="lc-label">Low</span><span class="lc-val" id="lc-low">—</span></div>
                    <div class="lc-item" style="grid-column:span 2"><span class="lc-label">Volume</span><span class="lc-val" id="lc-vol">—</span></div>
                    <div class="lc-item" style="grid-column:span 2"><span class="lc-label">Status</span><span class="lc-val" id="lc-status">—</span></div>
                </div>
            </div>
            <div class="sidebar-section" style="padding-bottom:4px">
                <div class="sidebar-title">Event Log</div>
            </div>
            <div class="event-log" id="event-log"></div>
        </aside>
    </div>

    <footer class="statusbar">
        <span>INTERNAL DEMO</span>
        <span class="statusbar-sep">|</span>
        <span id="status-symbol">—</span>
        <span class="statusbar-sep">/</span>
        <span id="status-interval">—</span>
        <span class="statusbar-sep">|</span>
        <span>CANDLES: <span id="status-candle-count">0</span></span>
        <span class="statusbar-sep">|</span>
        <span>LAST: <span id="status-last-event">—</span></span>
    </footer>
</div>
</body>
</html>
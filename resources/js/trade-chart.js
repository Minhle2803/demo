/**
 * resources/js/chart-demo.js
 *
 * Entry point for the chart demo page.
 * Wires together TradingChart + TradingChartSocket + TradingChartApi.
 *
 * Registered in vite.config.js as an input:
 *   input: ['resources/js/app.js', 'resources/js/chart-demo.js']
 *
 * Blade usage:
 *   @vite(['resources/js/chart-demo.js'])
 */

import TradingChart       from './trading-chart/TradingChart.js';
import TradingChartSocket from './trading-chart/TradingChartSocket.js';
import TradingChartApi    from './trading-chart/TradingChartApi.js';
import { initMarketSummary, stopMarketSummary } from './trading-chart/market-summary.js';
import { initMarketList } from './trading-chart/market-list'
// ── State ─────────────────────────────────────────────────────────────────────

const state = {
    symbol:      window.CHART_CONFIG?.defaultSymbol   ?? 'BTC_USDT',
    interval:    window.CHART_CONFIG?.defaultInterval ?? '1m',
    candleCount: 0,
    lastClose:   null,
};

// ── Instantiate services ───────────────────────────────────────────────────────

const chart = new TradingChart('chart_container', {
    onCrosshairChange: (candle) => updateOhlcvBar(candle),
});

const api = new TradingChartApi(
    window.CHART_CONFIG?.apiBase ?? '/api/internal/chart'
);

const socket = new TradingChartSocket({
    host:   window.CHART_CONFIG?.reverbHost   ?? 'localhost',
    port:   window.CHART_CONFIG?.reverbPort   ?? 8080,
    key:    window.CHART_CONFIG?.reverbKey    ?? '',
    scheme: window.CHART_CONFIG?.reverbScheme ?? 'http',
});

// ── Socket callbacks ──────────────────────────────────────────────────────────

socket.onUpdate = (candle) => {
    chart.updateCandle(candle);
    updateLiveCandle(candle);
    updatePriceBadge(candle);
    window.__currentPrice = candle.close;
};

socket.onClose = (candle) => {
    chart.updateCandle(candle);
    state.candleCount++;
    updateLiveCandle(candle);
    updatePriceBadge(candle);
    updateStatusBar();
    window.__currentPrice = candle.close;
};

socket.onRewrite = async (data) => {
    if (data.type === 'range') {
        // Bulk rewrite — reload affected range from API
        const candles = await api.getCandlesInRange(
            state.symbol, state.interval,
            data.from_timestamp, data.to_timestamp
        );
        candles.forEach(c => chart.updateCandle(c));
        appendLog('rewrite', `Range reloaded: ${candles.length} candles`);
    } else {
        chart.updateCandle(data);
        appendLog('rewrite', `Rewritten @ ${new Date(data.timestamp).toLocaleTimeString()}`);
    }
    setLastEvent('candle.rewrite');
};

socket.onStatusChange = (status, label) => {
    const dot = document.getElementById('ws-dot');
    const lbl = document.getElementById('ws-label');
    if (dot) dot.className = `ws-dot ${status}`;
    if (lbl) lbl.textContent = label;
};

socket.onLog = (type, msg) => appendLog(type, msg);

// ── Load + connect ────────────────────────────────────────────────────────────

async function loadCandles() {
    showLoading('Loading candles…');

    try {
        const candles = await api.getCandles(state.symbol, state.interval, { limit: 500 });

        if (!candles.length) {
            showLoading('No data — is chart:worker running?');
            appendLog('info', 'No candles returned.');
            return;
        }

        chart.applyCandles(candles);

        state.candleCount = candles.length;
        state.lastClose   = candles.at(-1).close;

        updateOhlcvBar(candles.at(-1));
        updateLiveCandle(candles.at(-1));
        updateStatusBar();
        hideLoading();
        appendLog('info', `Loaded ${candles.length} candles via REST`);

    } catch (err) {
        showLoading(`Error: ${err.message}`);
        appendLog('error', err.message);
    }
}

async function switchPair(symbol, interval) {
    socket.unsubscribe();

    state.symbol   = symbol;
    state.interval = interval;
    state.candleCount = 0;

    await loadCandles();
    socket.subscribe(state.symbol, state.interval);
    updateStatusBar();
}

// ── UI helpers ────────────────────────────────────────────────────────────────

function updateOhlcvBar(c) {
    if (!c) return;
    setText('ov-open',   fmt(c.open));
    setText('ov-high',   fmt(c.high));
    setText('ov-low',    fmt(c.low));
    setText('ov-close',  fmt(c.close));
    setText('ov-volume', fmtVol(c.volume));
    setText('ov-ts',     new Date(c.timestamp).toLocaleTimeString());

    const el = document.getElementById('ov-close');
    if (el) el.className = `ohlcv-val ${c.close >= c.open ? 'up' : 'down'}`;
}

function updateLiveCandle(c) {
    setText('lc-open',   fmt(c.open));
    setText('lc-high',   fmt(c.high));
    setText('lc-low',    fmt(c.low));
    setText('lc-vol',    fmtVol(c.volume));
    setText('lc-status', c.status ?? 'open');

    const closeEl = document.getElementById('lc-close');
    if (!closeEl) return;

    const prev    = parseFloat(closeEl.textContent.replace(/,/g, '')) || 0;
    const newVal  = fmt(c.close);

    if (closeEl.textContent !== newVal) {
        closeEl.textContent = newVal;
        const dir = c.close > prev ? 'up' : 'down';
        closeEl.classList.remove('flash-up', 'flash-down');
        void closeEl.offsetWidth;
        closeEl.classList.add(`flash-${dir}`);
        setTimeout(() => closeEl.classList.remove('flash-up', 'flash-down'), 600);
    }

    state.lastClose = c.close;
}

function updatePriceBadge(c) {
    const priceEl = document.getElementById(`price-${state.symbol}`);
    const chgEl   = document.getElementById(`change-${state.symbol}`);
    if (!priceEl || !chgEl || state.lastClose === null) return;

    priceEl.textContent = fmt(c.close);
    const pct  = (c.close - state.lastClose) / state.lastClose * 100;
    chgEl.textContent   = `${pct >= 0 ? '+' : ''}${pct.toFixed(2)}%`;
    chgEl.className     = `change ${pct >= 0 ? 'up' : 'down'}`;
}

function updateStatusBar() {
    setText('status-symbol',       state.symbol);
    setText('status-interval',     state.interval);
    setText('status-candle-count', state.candleCount);
}

function setLastEvent(name) {
    setText('status-last-event', `${name} ${nowTime()}`);
}

function showLoading(text) {
    const el = document.getElementById('loading');
    if (el) el.classList.remove('hidden');
    setText('loading-text', text);
}

function hideLoading() {
    document.getElementById('loading')?.classList.add('hidden');
}

const LOG_LIMIT = 120;

function appendLog(type, msg) {
    const container = document.getElementById('event-log');
    if (!container) return;

    const entry = document.createElement('div');
    entry.className = 'log-entry';
    entry.innerHTML = `
        <span class="log-time">${nowTime()}</span>
        <span class="log-badge badge-${type}">${type}</span>
        <span class="log-msg">${msg}</span>
    `;

    container.prepend(entry);
    while (container.children.length > LOG_LIMIT) {
        container.removeChild(container.lastChild);
    }

    setLastEvent(type);
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function fmt(v) {
    const n = parseFloat(v);
    if (isNaN(n)) return '—';
    if (n >= 1000) return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (n >= 1)    return n.toFixed(4);
    return n.toFixed(8);
}

function fmtVol(v) {
    const n = parseFloat(v);
    if (isNaN(n)) return '—';
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(2) + 'M';
    if (n >= 1_000)     return (n / 1_000).toFixed(2) + 'K';
    return n.toFixed(2);
}

function nowTime() {
    return new Date().toLocaleTimeString('en-US', { hour12: false });
}

// ── Bootstrap on DOM ready ────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', async () => {

    chart.init();

    // Symbol tabs
    document.getElementById('symbol-tabs')?.addEventListener('click', (e) => {
        const tab = e.target.closest('.symbol-tab');
        if (!tab) return;
        document.querySelectorAll('.symbol-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        switchPair(tab.dataset.symbol, state.interval);
    });

    // Interval pills
    document.getElementById('interval-pills')?.addEventListener('click', (e) => {
        const pill = e.target.closest('.interval-pill');
        if (!pill) return;
        document.querySelectorAll('.interval-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        switchPair(state.symbol, pill.dataset.interval);
    });

    window.addEventListener('resize', () => chart.resize());

    await loadCandles();
    socket.connect();
    socket.subscribe(state.symbol, state.interval);

    // Initialize market summary with auto-refresh
    initMarketSummary({
        apiBase: '/api/internal/chart',
        symbol: 'BTC_USDT',
        interval: '1m',
        range: '1H',
    })
    // Initialize market list with auto-refresh
    initMarketList({
        apiBase: '/api/internal/chart',
        interval: '1m',
        range: '1H',
    })
    // // ── Debug helpers (remove in production) ─────────────────────────────
    // window.__chart  = chart;
    // window.__socket = socket;
    // window.__api    = api;
    // window.__state  = state;

    function formatDateTime(date = new Date()) {
      const pad = (n) => String(n).padStart(2, "0");

      const day = pad(date.getDate());
      const month = pad(date.getMonth() + 1);
      const year = date.getFullYear();

      const hours = pad(date.getHours());
      const minutes = pad(date.getMinutes());
      const seconds = pad(date.getSeconds());

      return {
        date: `${day}-${month}-${year}`,                 // dd-MM-yyyy
        dateTime: `${day}-${month}-${year} ${hours}:${minutes}:${seconds}` // full
      };
    }

    var ses = 25346;
    setInterval(() => {
          const elTime = document.getElementById("time");
          const elDate = document.getElementById("date");
          
          const now = formatDateTime();
          if (elTime) elTime.innerText = now.dateTime;
          if (elDate) elDate.innerText = now.date;
      }, 1000);


      let seconds = 0;

      const buyBtn = document.getElementById("buyBtn");
      const sellBtn = document.getElementById("sellBtn");

      function pad(n) {
        return String(n).padStart(2, "0");
      }
});
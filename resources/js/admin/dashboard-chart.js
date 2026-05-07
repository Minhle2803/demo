/**
 * Admin dashboard chart — supports realtime and future timeline toggle.
 */
import TradingChart       from '../trading-chart/TradingChart.js';
import TradingChartSocket from '../trading-chart/TradingChartSocket.js';
import TradingChartApi    from '../trading-chart/TradingChartApi.js';

const state = {
    symbol:      window.CHART_CONFIG?.defaultSymbol   ?? 'BTC_USDT',
    interval:    window.CHART_CONFIG?.defaultInterval ?? '1m',
    timeline:    'realtime',
};

const container = document.getElementById('chart_container');

if (container) {
    const chart = new TradingChart('chart_container', {});

    const realtimeApi = new TradingChartApi(
        window.CHART_CONFIG?.apiBase ?? '/api/internal/chart'
    );

    const futureApi = new TradingChartApi(
        window.CHART_CONFIG?.futureApiBase ?? '/api/admin/future-chart'
    );

    const socket = new TradingChartSocket({
        host:   window.CHART_CONFIG?.reverbHost   ?? 'localhost',
        port:   window.CHART_CONFIG?.reverbPort   ?? 8080,
        key:    window.CHART_CONFIG?.reverbKey    ?? '',
        scheme: window.CHART_CONFIG?.reverbScheme ?? 'http',
    });

    socket.onUpdate = (candle) => {
        if (state.timeline === 'realtime') {
            chart.updateCandle(candle);
        }
    };

    socket.onClose = (candle) => {
        if (state.timeline === 'realtime') {
            chart.updateCandle(candle);
        }
    };

    socket.onError = () => {};

    function getApi() {
        return state.timeline === 'future' ? futureApi : realtimeApi;
    }

    function loadChart(type) {
        state.timeline = type;
        chart.clear();

        const api = getApi();
        api.fetchCandles = api.getCandles;

        api.getCandles(state.symbol, state.interval)
            .then((candles) => {
                if (candles && candles.length) {
                    chart.setCandles(candles);
                }
            })
            .catch(() => {});
    }

    // Initial load
    loadChart('realtime');
    socket.subscribe(state.symbol, state.interval);

    // Timeline toggle handler
    const toggle = document.getElementById('timelineToggle');
    if (toggle) {
        toggle.addEventListener('change', () => {
            loadChart(toggle.value);
        });
    }
}

/**
 * Admin dashboard chart — reuses existing TradingChart components.
 */
import TradingChart       from '../trading-chart/TradingChart.js';
import TradingChartSocket from '../trading-chart/TradingChartSocket.js';
import TradingChartApi    from '../trading-chart/TradingChartApi.js';

const state = {
    symbol:      window.CHART_CONFIG?.defaultSymbol   ?? 'BTC_USDT',
    interval:    window.CHART_CONFIG?.defaultInterval ?? '1m',
};

const container = document.getElementById('chart_container');

if (container) {
    const chart = new TradingChart('chart_container', {});

    const api = new TradingChartApi(
        window.CHART_CONFIG?.apiBase ?? '/api/internal/chart'
    );

    const socket = new TradingChartSocket({
        host:   window.CHART_CONFIG?.reverbHost   ?? 'localhost',
        port:   window.CHART_CONFIG?.reverbPort   ?? 8080,
        key:    window.CHART_CONFIG?.reverbKey    ?? '',
        scheme: window.CHART_CONFIG?.reverbScheme ?? 'http',
    });

    socket.onUpdate = (candle) => {
        chart.updateCandle(candle);
    };

    socket.onClose = (candle) => {
        chart.updateCandle(candle);
    };

    socket.onError = () => {};

    api.fetchCandles(state.symbol, state.interval)
        .then((candles) => {
            if (candles && candles.length) {
                chart.setCandles(candles);
            }
        })
        .catch(() => {});

    socket.subscribe(state.symbol, state.interval);
}

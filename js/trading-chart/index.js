/**
 * resources/js/trading-chart/index.js
 *
 * Barrel export — import anything from '@/trading-chart'
 *
 * Examples:
 *   import { TradingChart, TradingChartSocket, TradingChartApi } from '@/trading-chart';
 *   import TradingChart from '@/trading-chart/TradingChart';
 */

export { default as TradingChart }       from './TradingChart.js';
export { default as TradingChartSocket } from './TradingChartSocket.js';
export { default as TradingChartApi }    from './TradingChartApi.js';
export { CHART_STYLES, CHART_COLORS }   from './chart-config.js';

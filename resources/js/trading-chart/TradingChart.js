/**
 * resources/js/trading-chart/TradingChart.js
 *
 * KLineCharts wrapper as an ES module.
 * Requires klinecharts to be available globally (loaded via CDN in Blade)
 * or imported directly if using npm:
 *   import * as klinecharts from 'klinecharts';
 *
 * Usage:
 *   import TradingChart from '@/trading-chart/TradingChart';
 *
 *   const chart = new TradingChart('container-id', {
 *     onCrosshairChange: (candle) => console.log(candle),
 *   });
 *   chart.init();
 *   chart.applyCandles(candles);
 *   chart.updateCandle(candle);
 *   chart.dispose();
 */

import { init, dispose } from 'klinecharts';
import { CHART_STYLES } from './chart-config.js';

export default class TradingChart {
    /**
     * @param {string}   containerId
     * @param {object}   [options]
     * @param {function} [options.onCrosshairChange]
     */
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.options     = options;
        this._chart      = null;
        this._resizeObs  = null;
    }

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    init() {
        this.dispose();

        this._chart = init(this.containerId, {
            styles: CHART_STYLES,
        });

        if (!this._chart) {
            console.error(`[TradingChart] init failed — check container "#${this.containerId}"`);
            return this;
        }

        this._subscribeCrosshair();
        this._watchResize();

        // Resize after layout pass so canvas dimensions are correct
        requestAnimationFrame(() => this.resize());

        return this;
    }

    dispose() {
        this._resizeObs?.disconnect();
        this._resizeObs = null;

        if (this._chart) {
            try { dispose(this.containerId); } catch (_) {}
            this._chart = null;
        }

        return this;
    }

    resize() {
        this._chart?.resize();
        return this;
    }

    // -------------------------------------------------------------------------
    // Data
    // -------------------------------------------------------------------------

    /**
     * Load a full set of candles — replaces all existing data.
     * Array must be sorted timestamp ASC.
     *
     * @param {object[]} candles
     */
    applyCandles(candles) {
        if (!this._chart || !candles?.length) return this;

        // Resize synchronously first — ensures canvas has real dimensions
        // before data is applied. Then a second resize+apply in rAF handles
        // any layout shift that happens after the first paint.
        this._chart.resize();

        requestAnimationFrame(() => {
            try {
                this._chart.resize();
                this._chart.applyNewData(candles);
                console.log(`[TradingChart] applyNewData OK — ${candles.length} candles`);
            } catch (e) {
                console.error('[TradingChart] applyNewData error:', e);
            }
        });

        return this;
    }

    /**
     * Update or append a single candle (live tick or close).
     *
     * @param {object} candle
     */
    updateCandle(candle) {
        if (!this._chart || !candle) return this;

        try {
            this._chart.updateData(candle);
        } catch (e) {
            console.warn('[TradingChart] updateData error:', e.message);
        }

        return this;
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    _subscribeCrosshair() {
        if (typeof this.options.onCrosshairChange !== 'function') return;

        try {
            const type = 'onCrosshairChange';
            this._chart.subscribeAction(type, ({ kLineData } = {}) => {
                if (kLineData) this.options.onCrosshairChange(kLineData);
            });
        } catch (e) {
            console.warn('[TradingChart] subscribeAction error:', e.message);
        }
    }

    _watchResize() {
        const el = document.getElementById(this.containerId);
        if (!el || !window.ResizeObserver) return;

        this._resizeObs = new ResizeObserver(() => this.resize());
        this._resizeObs.observe(el);
    }
}
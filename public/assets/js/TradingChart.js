/**
 * TradingChart.js
 *
 * Wraps KLineCharts into a clean class.
 * Handles: init, resize, applyNewData, updateData, dispose.
 *
 * Usage:
 *   const chart = new TradingChart('my-container');
 *   await chart.init();
 *   chart.applyCandles(candleArray);
 *   chart.updateCandle(candleObject);
 */
class TradingChart {
    /**
     * @param {string}   containerId  DOM element ID to render the chart into
     * @param {object}   [options]
     * @param {function} [options.onCrosshairChange]  Callback fired when crosshair moves
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

    /**
     * Initialise the KLineCharts instance inside the container.
     * Safe to call multiple times — disposes previous instance first.
     */
    init() {
        this.dispose();

        if (!window.klinecharts) {
            console.error('[TradingChart] klinecharts not loaded — include the CDN script first.');
            return this;
        }

        this._chart = klinecharts.init(this.containerId, {
            styles: this._buildStyles(),
        });

        if (!this._chart) {
            console.error(`[TradingChart] klinecharts.init failed — is "#${this.containerId}" in the DOM?`);
            return this;
        }

        // Register crosshair callback if provided
        if (typeof this.options.onCrosshairChange === 'function') {
            try {
                const actionType = klinecharts.ActionType
                    ? klinecharts.ActionType.OnCrosshairChange
                    : 'onCrosshairChange';
                this._chart.subscribeAction(actionType, (data) => {
                    if (data && data.kLineData) {
                        this.options.onCrosshairChange(data.kLineData);
                    }
                });
            } catch (e) {
                console.warn('[TradingChart] subscribeAction error:', e.message);
            }
        }

        // Auto-resize when container dimensions change
        this._watchResize();

        // Initial resize after layout pass
        requestAnimationFrame(() => this.resize());

        return this;
    }

    dispose() {
        if (this._resizeObs) {
            this._resizeObs.disconnect();
            this._resizeObs = null;
        }
        if (this._chart) {
            try { klinecharts.dispose(this.containerId); } catch (_) {}
            this._chart = null;
        }
    }

    resize() {
        if (this._chart) this._chart.resize();
        return this;
    }

    // -------------------------------------------------------------------------
    // Data API
    // -------------------------------------------------------------------------

    /**
     * Load a full set of historical candles.
     * Clears any existing data first.
     * Expects array sorted timestamp ASC (oldest → newest).
     *
     * @param {object[]} candles  KLineCharts-compatible candle objects
     */
    applyCandles(candles) {
        if (!this._chart) return this;
        if (!Array.isArray(candles) || candles.length === 0) return this;

        requestAnimationFrame(() => {
            try {
                this._chart.resize();
                this._chart.applyNewData(candles);
                console.log(`[TradingChart] applyNewData — ${candles.length} candles, first:`, candles[0]);
            } catch (e) {
                console.error('[TradingChart] applyNewData failed:', e);
            }
        });

        return this;
    }

    /**
     * Update or append a single candle (live tick or close event).
     * KLineCharts will update the last candle if timestamp matches,
     * or append a new candle if it's a new timestamp.
     *
     * @param {object} candle  Single candle object
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
    // Internal helpers
    // -------------------------------------------------------------------------

    _watchResize() {
        const el = document.getElementById(this.containerId);
        if (!el || !window.ResizeObserver) return;

        this._resizeObs = new ResizeObserver(() => this.resize());
        this._resizeObs.observe(el);
    }

    _buildStyles() {
        return {
            grid: {
                horizontal: { color: '#1c2d44', style: 'dashed', dashedValue: [2, 2] },
                vertical:   { color: '#1c2d44', style: 'dashed', dashedValue: [2, 2] },
            },
            candle: {
                bar: {
                    upColor:            '#00e676',
                    downColor:          '#ff3d5a',
                    noChangeColor:      '#4e6680',
                    upBorderColor:      '#00e676',
                    downBorderColor:    '#ff3d5a',
                    noChangeBorderColor:'#4e6680',
                    upWickColor:        '#00e676',
                    downWickColor:      '#ff3d5a',
                    noChangeWickColor:  '#4e6680',
                },
                priceMark: {
                    last: {
                        upColor:      '#00e676',
                        downColor:    '#ff3d5a',
                        noChangeColor:'#4e6680',
                        line: { show: true, style: 'dashed', dashedValue: [4, 4], size: 1 },
                        text: { show: true, size: 11 },
                    },
                },
            },
            xAxis: { tickText: { color: '#4e6680', size: 10 } },
            yAxis: { tickText: { color: '#4e6680', size: 10 } },
            crosshair: {
                horizontal: {
                    line: { color: '#4e6680', style: 'dashed', dashedValue: [4, 4] },
                    text: { backgroundColor: '#0d1420', borderColor: '#243b58', color: '#c8d8ea', size: 11 },
                },
                vertical: {
                    line: { color: '#4e6680', style: 'dashed', dashedValue: [4, 4] },
                    text: { backgroundColor: '#0d1420', borderColor: '#243b58', color: '#c8d8ea', size: 11 },
                },
            },
        };
    }
}
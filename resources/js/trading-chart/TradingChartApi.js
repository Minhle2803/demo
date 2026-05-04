/**
 * resources/js/trading-chart/TradingChartApi.js
 *
 * All REST API calls for the trading chart system.
 *
 * Usage:
 *   import TradingChartApi from '@/trading-chart/TradingChartApi';
 *
 *   const api = new TradingChartApi('/api/internal/chart');
 *
 *   const candles = await api.getCandles('BTC_USDT', '1m', { limit: 500 });
 *   await api.updateFutureDirection({ symbol: 'BTC_USDT', interval: '1m', direction: 'up' });
 *   await api.rewriteRange({ symbol: 'BTC_USDT', interval: '1m', from_timestamp: ..., to_timestamp: ..., direction: 'up' });
 */

export default class TradingChartApi {
    /**
     * @param {string} baseUrl   e.g. '/api/internal/chart'
     * @param {string} [csrfToken]  Laravel CSRF token (required for POST)
     */
    constructor(baseUrl, csrfToken = null) {
        this.baseUrl   = baseUrl.replace(/\/$/, '');
        this.csrfToken = csrfToken || this._readCsrfFromMeta();
    }

    // -------------------------------------------------------------------------
    // GET /candles
    // -------------------------------------------------------------------------

    /**
     * Fetch candles for a symbol × interval pair.
     *
     * @param {string} symbol    e.g. 'BTC_USDT'
     * @param {string} interval  e.g. '1m'
     * @param {object} [params]
     * @param {number} [params.limit]  Max candles (default 500, max 1000)
     * @param {number} [params.from]   Start timestamp ms (inclusive)
     * @param {number} [params.to]     End timestamp ms (inclusive)
     *
     * @returns {Promise<object[]>}  Array of KLineCharts-compatible candles
     */
    async getCandles(symbol, interval, params = {}) {
        const url = new URL(`${this.baseUrl}/candles`, window.location.origin);
        url.searchParams.set('symbol',   symbol);
        url.searchParams.set('interval', interval);

        if (params.limit !== undefined) url.searchParams.set('limit', params.limit);
        if (params.from  !== undefined) url.searchParams.set('from',  params.from);
        if (params.to    !== undefined) url.searchParams.set('to',    params.to);

        const res  = await this._get(url);
        return res.data ?? [];
    }

    /**
     * Reload candles within a specific timestamp range.
     * Used after a candle.rewrite event to sync the affected range.
     *
     * @param {string} symbol
     * @param {string} interval
     * @param {number} fromMs
     * @param {number} toMs
     * @returns {Promise<object[]>}
     */
    async getCandlesInRange(symbol, interval, fromMs, toMs) {
        return this.getCandles(symbol, interval, {
            from:  fromMs,
            to:    toMs,
            limit: 1000,
        });
    }

    // -------------------------------------------------------------------------
    // POST /future-direction
    // -------------------------------------------------------------------------

    /**
     * Set a future direction rule for a pair.
     *
     * @param {object} payload
     * @param {string}  payload.symbol
     * @param {string}  payload.interval
     * @param {string}  payload.direction          'up' | 'down' | 'neutral'
     * @param {number}  [payload.price_bias_percent]
     * @param {number}  [payload.from_timestamp]
     * @param {number}  [payload.to_timestamp]
     * @param {boolean} [payload.apply_to_existing]
     *
     * @returns {Promise<object>}  API response data
     */
    async updateFutureDirection(payload) {
        const res = await this._post(`${this.baseUrl}/future-direction`, payload);
        return res.data;
    }

    // -------------------------------------------------------------------------
    // POST /rewrite-range
    // -------------------------------------------------------------------------

    /**
     * Rewrite historical candles in a timestamp range.
     *
     * @param {object} payload
     * @param {string} payload.symbol
     * @param {string} payload.interval
     * @param {number} payload.from_timestamp
     * @param {number} payload.to_timestamp
     * @param {string} payload.direction      'up' | 'down' | 'neutral'
     * @param {number} [payload.strength]     0.1 – 10 (default 1.0)
     *
     * @returns {Promise<object>}  { updated_count: number }
     */
    async rewriteRange(payload) {
        const res = await this._post(`${this.baseUrl}/rewrite-range`, payload);
        return res.data;
    }

    // -------------------------------------------------------------------------
    // Private HTTP helpers
    // -------------------------------------------------------------------------

    async _get(url) {
        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        return this._handleResponse(res);
    }

    async _post(url, body) {
        const res = await fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': this.csrfToken || '',
            },
            body: JSON.stringify(body),
        });

        return this._handleResponse(res);
    }

    async _handleResponse(res) {
        let json;

        try {
            json = await res.json();
        } catch {
            throw new Error(`[TradingChartApi] Non-JSON response (${res.status})`);
        }

        if (!json.success) {
            const message = json.message || json.code || 'API error';
            throw new Error(`[TradingChartApi] ${message} (${res.status})`);
        }

        return json;
    }

    _readCsrfFromMeta() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
}

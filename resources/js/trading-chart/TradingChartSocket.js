/**
 * resources/js/trading-chart/TradingChartSocket.js
 *
 * Reverb WebSocket connection manager as an ES module.
 * Handles: connect, ping/pong, subscribe/unsubscribe, auto-reconnect.
 *
 * Usage:
 *   import TradingChartSocket from '@/trading-chart/TradingChartSocket';
 *
 *   const socket = new TradingChartSocket({
 *     host:   import.meta.env.VITE_REVERB_HOST,
 *     port:   import.meta.env.VITE_REVERB_PORT,
 *     key:    import.meta.env.VITE_REVERB_APP_KEY,
 *     scheme: import.meta.env.VITE_REVERB_SCHEME,
 *   });
 *
 *   socket.onUpdate       = (candle) => chart.updateCandle(candle);
 *   socket.onClose        = (candle) => chart.updateCandle(candle);
 *   socket.onRewrite      = (data)   => handleRewrite(data);
 *   socket.onStatusChange = (status, label) => updateStatusDot(status, label);
 *   socket.onLog          = (type, msg)     => appendLog(type, msg);
 *
 *   socket.connect();
 *   socket.subscribe('BTC_USDT', '1m');
 *
 *   // Before switching pair:
 *   socket.unsubscribe();
 *   socket.subscribe('ETH_USDT', '5m');
 *
 *   // Full teardown:
 *   socket.disconnect();
 */

export default class TradingChartSocket {
    constructor(config = {}) {
        this.host           = config.host           || 'localhost';
        this.port           = config.port           || 8080;
        this.key            = config.key            || '';
        this.scheme         = config.scheme         || 'http';
        this.reconnectDelay = config.reconnectDelay || 3000;

        this._symbol          = null;
        this._interval        = null;
        this._ws              = null;
        this._reconnectTimer  = null;
        this._intentionalClose = false;

        // ── Public callbacks ──────────────────────────────────────────────

        /** @type {function(object): void} */
        this.onUpdate = null;

        /** @type {function(object): void} */
        this.onClose = null;

        /** @type {function(object): void} */
        this.onRewrite = null;

        /** @type {function(string, string): void} status: 'connecting'|'connected'|'disconnected' */
        this.onStatusChange = null;

        /** @type {function(string, string): void} type: 'info'|'update'|'close'|'rewrite'|'error' */
        this.onLog = null;
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    connect() {
        this._intentionalClose = false;
        this._setStatus('connecting', 'CONNECTING');

        const scheme = this.scheme === 'https' ? 'wss' : 'ws';
        const url    = `${scheme}://${this.host}:${this.port}/app/${this.key}`;

        // Detach old socket cleanly before creating new one
        if (this._ws) {
            this._ws.onclose = null;
            this._ws.close();
        }

        this._ws = new WebSocket(url);
        this._ws.onopen    = ()  => this._onOpen();
        this._ws.onmessage = (e) => this._onMessage(e);
        this._ws.onerror   = ()  => this._onError();
        this._ws.onclose   = ()  => this._onClose();

        return this;
    }

    /**
     * Subscribe to chart.{symbol}.{interval}.
     * Stores pair so reconnect can re-subscribe automatically.
     */
    subscribe(symbol, interval) {
        this._symbol   = symbol;
        this._interval = interval;
        this._sendSubscribe();
        return this;
    }

    /** Unsubscribe from current channel. Call before switching pair. */
    unsubscribe() {
        if (this._symbol && this._interval) {
            this._send('pusher:unsubscribe', { channel: this._channel() });
        }
        this._symbol   = null;
        this._interval = null;
        return this;
    }

    /** Full teardown — closes connection and cancels auto-reconnect. */
    disconnect() {
        this._intentionalClose = true;
        clearTimeout(this._reconnectTimer);

        if (this._ws) {
            this._ws.onclose = null;
            this._ws.close();
            this._ws = null;
        }

        this._setStatus('disconnected', 'OFFLINE');
        return this;
    }

    get isConnected() {
        return this._ws?.readyState === WebSocket.OPEN;
    }

    // -------------------------------------------------------------------------
    // Private — WS handlers
    // -------------------------------------------------------------------------

    _onOpen() {
        this._setStatus('connected', 'LIVE');
        this._log('info', `Connected → ${this.host}:${this.port}`);

        // Re-subscribe automatically after reconnect
        if (this._symbol && this._interval) {
            this._sendSubscribe();
        }
    }

    _onMessage({ data: raw }) {
        let msg;
        try { msg = JSON.parse(raw); } catch { return; }

        const event = msg.event || '';

        // ── Pusher protocol ───────────────────────────────────────────────
        if (event === 'pusher:connection_established') {
            this._log('info', 'Reverb handshake OK');
            return;
        }

        if (event === 'pusher:ping') {
            // Must reply or Reverb disconnects after activity_timeout (~30s)
            this._send('pusher:pong', {});
            return;
        }

        if (event === 'pusher_internal:subscription_succeeded') {
            this._log('info', `Subscribed: ${msg.channel}`);
            return;
        }

        // Silently ignore all other pusher: system events
        if (event.startsWith('pusher')) return;

        // ── Chart data events ─────────────────────────────────────────────
        let payload;
        try {
            payload = typeof msg.data === 'string' ? JSON.parse(msg.data) : msg.data;
        } catch { return; }

        if (!payload?.data) return;

        this._dispatch(event.replace(/^\./, ''), payload.data);
    }

    _onError() {
        this._setStatus('disconnected', 'ERROR');
        this._log('error', 'WebSocket error');
    }

    _onClose() {
        this._setStatus('disconnected', 'OFFLINE');
        if (this._intentionalClose) return;

        this._log('info', `Disconnected — retrying in ${this.reconnectDelay}ms…`);
        clearTimeout(this._reconnectTimer);
        this._reconnectTimer = setTimeout(() => this.connect(), this.reconnectDelay);
    }

    // -------------------------------------------------------------------------
    // Private — event dispatch
    // -------------------------------------------------------------------------

    _dispatch(eventName, candle) {
        console.log(`[TradingChartSocket] Event: ${eventName}`, candle);
        switch (eventName) {
            case 'candle.update':
                this.onUpdate?.(candle);
                this._log('update', this._fmt(candle.close));
                break;

            case 'candle.close':
                this.onClose?.(candle);
                this._log('close', `Closed @ ${this._fmt(candle.close)}`);
                break;

            case 'candle.rewrite':
                this.onRewrite?.(candle);
                this._log('rewrite', candle.type === 'range'
                    ? `Range rewrite: ${candle.count} candles`
                    : `Rewritten @ ${new Date(candle.timestamp).toLocaleTimeString()}`
                );
                break;
        }
    }

    // -------------------------------------------------------------------------
    // Private — helpers
    // -------------------------------------------------------------------------

    _sendSubscribe() {
        if (!this._symbol || !this._interval) return;
        this._send('pusher:subscribe', { channel: this._channel() });
    }

    _send(event, data) {
        if (!this.isConnected) return;
        try {
            this._ws.send(JSON.stringify({ event, data }));
        } catch (e) {
            console.warn('[TradingChartSocket] send error:', e.message);
        }
    }

    _channel() {
        return `chart.${this._symbol}.${this._interval}`;
    }

    _setStatus(status, label) {
        this.onStatusChange?.(status, label);
    }

    _log(type, msg) {
        this.onLog?.(type, msg);
    }

    _fmt(v) {
        const n = parseFloat(v);
        if (isNaN(n)) return '—';
        if (n >= 1000) return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (n >= 1)    return n.toFixed(4);
        return n.toFixed(8);
    }
}

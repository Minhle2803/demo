import { getOrderBook } from './api.js';

let currentSymbol = 'BTC_USDT';
let reverbWs = null;
let pollInterval = null;

function connectReverb() {
    const cfg = window.SPOT_CONFIG || {};
    const scheme = cfg.reverbScheme === 'https' ? 'wss' : 'ws';
    const url = `${scheme}://${cfg.reverbHost || 'localhost'}:${cfg.reverbPort || 8080}/app/${cfg.reverbKey || ''}`;

    if (reverbWs) {
        reverbWs.onclose = null;
        reverbWs.close();
    }

    reverbWs = new WebSocket(url);
    reverbWs.onopen = () => {
        subscribe(currentSymbol);
    };
    reverbWs.onmessage = (e) => {
        let msg;
        try { msg = JSON.parse(e.data); } catch { return; }
        if (msg.event === 'pusher:connection_established') return;
        if (msg.event === 'pusher:ping') {
            reverbWs.send(JSON.stringify({ event: 'pusher:pong', data: {} }));
            return;
        }
        if (msg.event && msg.event.startsWith('pusher')) return;

        let payload;
        try {
            payload = typeof msg.data === 'string' ? JSON.parse(msg.data) : msg.data;
        } catch { return; }

        if (msg.event === 'orderbook.updated' || msg.event === '.orderbook.updated') {
            renderOrderBook(payload);
        }
    };
    reverbWs.onclose = () => {
        setTimeout(connectReverb, 5000);
    };
}

function subscribe(symbol) {
    if (reverbWs && reverbWs.readyState === WebSocket.OPEN) {
        if (currentSymbol) {
            reverbWs.send(JSON.stringify({
                event: 'pusher:unsubscribe',
                data: { channel: `spot-orderbook.${currentSymbol}` },
            }));
        }
        currentSymbol = symbol;
        reverbWs.send(JSON.stringify({
            event: 'pusher:subscribe',
            data: { channel: `spot-orderbook.${currentSymbol}` },
        }));
    }
}

function renderOrderBook(data) {
    if (!data) return;

    renderAsks(data.asks || []);
    renderBids(data.bids || []);
    renderSpread(data.asks, data.bids);
    renderMidPrice(data.asks, data.bids);
}

function renderAsks(asks) {
    const tbody = document.getElementById('orderBookAsks');
    if (!tbody) return;

    if (!asks.length) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-2">No asks</td></tr>';
        return;
    }

    const maxQty = Math.max(...asks.map(a => parseFloat(a.total_quantity)), 1);

    tbody.innerHTML = asks.map(a => {
        const qty = parseFloat(a.total_quantity).toFixed(8);
        const pct = (parseFloat(a.total_quantity) / maxQty * 100).toFixed(0);
        return `
            <tr class="orderbook-row orderbook-row--ask">
                <td class="ps-3 text-danger">${parseFloat(a.price).toFixed(2)}</td>
                <td class="text-end">
                    <span class="orderbook-bar-wrapper">
                        <span class="orderbook-bar orderbook-bar--ask" style="width:${pct}%"></span>
                    </span>
                    ${qty}
                </td>
                <td class="text-end pe-3 text-muted small">${a.order_count}</td>
            </tr>
        `;
    }).join('');
}

function renderBids(bids) {
    const tbody = document.getElementById('orderBookBids');
    if (!tbody) return;

    if (!bids.length) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-2">No bids</td></tr>';
        return;
    }

    const maxQty = Math.max(...bids.map(b => parseFloat(b.total_quantity)), 1);

    tbody.innerHTML = bids.map(b => {
        const qty = parseFloat(b.total_quantity).toFixed(8);
        const pct = (parseFloat(b.total_quantity) / maxQty * 100).toFixed(0);
        return `
            <tr class="orderbook-row orderbook-row--bid">
                <td class="ps-3 text-success">${parseFloat(b.price).toFixed(2)}</td>
                <td class="text-end">
                    <span class="orderbook-bar-wrapper">
                        <span class="orderbook-bar orderbook-bar--bid" style="width:${pct}%"></span>
                    </span>
                    ${qty}
                </td>
                <td class="text-end pe-3 text-muted small">${b.order_count}</td>
            </tr>
        `;
    }).join('');
}

function renderSpread(asks, bids) {
    const el = document.getElementById('orderBookMidSpread');
    if (!el) return;

    if (asks.length && bids.length) {
        const bestAsk = Math.min(...asks.map(a => parseFloat(a.price)));
        const bestBid = Math.max(...bids.map(b => parseFloat(b.price)));
        const spread = (bestAsk - bestBid).toFixed(2);
        const pct = (spread / bestAsk * 100).toFixed(3);
        el.textContent = `Spread: ${spread} (${pct}%)`;
    } else {
        el.textContent = '---';
    }
}

function renderMidPrice(asks, bids) {
    const el = document.getElementById('orderBookMidPrice');
    if (!el) return;

    if (asks.length && bids.length) {
        const bestAsk = Math.min(...asks.map(a => parseFloat(a.price)));
        const bestBid = Math.max(...bids.map(b => parseFloat(b.price)));
        el.textContent = ((bestAsk + bestBid) / 2).toFixed(2);
    } else if (asks.length) {
        el.textContent = Math.min(...asks.map(a => parseFloat(a.price))).toFixed(2);
    } else if (bids.length) {
        el.textContent = Math.max(...bids.map(b => parseFloat(b.price))).toFixed(2);
    } else {
        el.textContent = '---';
    }
}

async function fetchAndRender() {
    const data = await getOrderBook(currentSymbol);
    if (data.success && data.data) {
        renderOrderBook(data.data);
    }
}

export function initOrderBook() {
    const select = document.getElementById('orderBookSymbol');
    if (select) {
        select.addEventListener('change', () => {
            subscribe(select.value);
            fetchAndRender();
        });
        currentSymbol = select.value || 'BTC_USDT';
    }

    fetchAndRender();
    connectReverb();

    // Poll every 30s as fallback
    pollInterval = setInterval(fetchAndRender, 30000);
}

export function stopOrderBook() {
    if (pollInterval) clearInterval(pollInterval);
    if (reverbWs) {
        reverbWs.onclose = null;
        reverbWs.close();
        reverbWs = null;
    }
}

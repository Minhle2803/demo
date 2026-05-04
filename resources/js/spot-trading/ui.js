import { getOrders, getTrades, getWallet } from './state.js';
import { loadAndRenderWallets } from './wallets.js';
import { loadAndRenderOrders, handleCancel } from './orders.js';
import { getMyTrades } from './api.js';
import { setTrades } from './state.js';

function statusBadge(status) {
    const map = {
        open: 'bg-info',
        partially_filled: 'bg-warning',
        filled: 'bg-success',
        cancelled: 'bg-secondary',
    };
    return `<span class="badge ${map[status] || 'bg-secondary'}">${status}</span>`;
}

export function showError(side, msg) {
    const el = document.getElementById(side === 'buy' ? 'buyError' : 'sellError');
    if (el) {
        el.textContent = msg;
        el.style.display = 'block';
    }
}

export function showSuccess(side, msg) {
    const el = document.getElementById(side === 'buy' ? 'buyError' : 'sellError');
    if (el) {
        el.textContent = msg;
        el.className = 'alert alert-success py-2';
        el.style.display = 'block';
        setTimeout(() => {
            el.style.display = 'none';
            el.className = 'alert alert-danger py-2';
        }, 3000);
    }
}

export function renderWalletBalances() {
    const buyAvail = document.getElementById('buyAvailable');
    const sellAvail = document.getElementById('sellAvailable');
    const sellAssetLabel = document.getElementById('sellAssetLabel');

    const buySym = document.getElementById('spotSymbol')?.value || 'BTC_USDT';
    const sellSym = document.getElementById('spotSellSymbol')?.value || 'BTC_USDT';
    const cfgBuy = window.SPOT_CONFIG?.symbols?.[buySym];
    const cfgSell = window.SPOT_CONFIG?.symbols?.[sellSym];

    if (cfgBuy) {
        const w = getWallet(cfgBuy.quote_asset);
        if (buyAvail && w) buyAvail.textContent = parseFloat(w.available_balance).toFixed(2);
    }

    if (cfgSell) {
        const w = getWallet(cfgSell.base_asset);
        if (sellAvail && w) sellAvail.textContent = parseFloat(w.available_balance).toFixed(8);
        if (sellAssetLabel) sellAssetLabel.textContent = cfgSell.base_asset;
    }
}

export function renderOpenOrders() {
    const tbody = document.getElementById('openOrdersBody');
    if (!tbody) return;

    const orders = getOrders();
    const openOrders = orders.filter(o => o.status === 'open' || o.status === 'partially_filled');

    if (openOrders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No open orders.</td></tr>';
        return;
    }

    tbody.innerHTML = openOrders.map(o => `
        <tr>
            <td>${o.symbol}</td>
            <td><span class="badge ${o.side === 'buy' ? 'bg-success' : 'bg-danger'}">${o.side}</span></td>
            <td>${parseFloat(o.price).toFixed(2)}</td>
            <td>${parseFloat(o.quantity).toFixed(8)}</td>
            <td>${parseFloat(o.filled_quantity).toFixed(8)}</td>
            <td>${statusBadge(o.status)}</td>
            <td>${new Date(o.created_at).toLocaleString('vi-VN')}</td>
            <td><button class="btn btn-sm btn-outline-danger cancel-order-btn" data-id="${o.id}">Hủy</button></td>
        </tr>
    `).join('');

    tbody.querySelectorAll('.cancel-order-btn').forEach(btn => {
        btn.addEventListener('click', () => handleCancel(btn.dataset.id));
    });
}

export async function loadAndRenderTrades() {
    const sym = document.getElementById('spotSymbol')?.value;
    const data = await getMyTrades(sym);
    const tbody = document.getElementById('tradeHistoryBody');
    if (!tbody) return;

    if (data.success && data.data) {
        const trades = data.data.data || data.data;
        setTrades(Array.isArray(trades) ? trades : []);

        if (trades.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No trades.</td></tr>';
            return;
        }

        tbody.innerHTML = trades.map(t => {
            const isBuyer = t.buyer_user_id && !t.seller_user_id;
            const side = t.buy_order_id && t.sell_order_id ? 'match' : (isBuyer ? 'buy' : 'sell');
            const sideBadge = side === 'buy' ? 'bg-success' : (side === 'sell' ? 'bg-danger' : 'bg-info');
            return `
            <tr>
                <td>${t.symbol}</td>
                <td><span class="badge ${sideBadge}">${side}</span></td>
                <td>${parseFloat(t.price).toFixed(2)}</td>
                <td>${parseFloat(t.quantity).toFixed(8)}</td>
                <td>${parseFloat(t.total).toFixed(2)}</td>
                <td><span class="badge bg-light text-dark">${t.source}</span></td>
                <td>${new Date(t.created_at).toLocaleString('vi-VN')}</td>
            </tr>`;
        }).join('');
    }
}

export function refreshWallets() {
    return loadAndRenderWallets();
}

// Update buy total display when inputs change
export function initTotalCalculations() {
    function updateBuyTotal() {
        const p = parseFloat(document.getElementById('buyPrice')?.value) || 0;
        const q = parseFloat(document.getElementById('buyQuantity')?.value) || 0;
        const el = document.getElementById('buyTotal');
        if (el) el.textContent = (p * q).toFixed(2) + ' USDT';
    }

    function updateSellTotal() {
        const p = parseFloat(document.getElementById('sellPrice')?.value) || 0;
        const q = parseFloat(document.getElementById('sellQuantity')?.value) || 0;
        const el = document.getElementById('sellTotal');
        if (el) el.textContent = (p * q).toFixed(2) + ' USDT';
    }

    const buyPrice = document.getElementById('buyPrice');
    const buyQty = document.getElementById('buyQuantity');
    const sellPrice = document.getElementById('sellPrice');
    const sellQty = document.getElementById('sellQuantity');

    if (buyPrice) buyPrice.addEventListener('input', updateBuyTotal);
    if (buyQty) buyQty.addEventListener('input', updateBuyTotal);
    if (sellPrice) sellPrice.addEventListener('input', updateSellTotal);
    if (sellQty) sellQty.addEventListener('input', updateSellTotal);

    // Update wallet display when symbol changes
    const buySymSelect = document.getElementById('spotSymbol');
    const sellSymSelect = document.getElementById('spotSellSymbol');
    if (buySymSelect) buySymSelect.addEventListener('change', renderWalletBalances);
    if (sellSymSelect) sellSymSelect.addEventListener('change', renderWalletBalances);
}

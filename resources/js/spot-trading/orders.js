import { createBuyOrder, createSellOrder, cancelOrder, getMyOrders } from './api.js';
import { getSymbol, setOrders, getWallet } from './state.js';
import { validateOrder, validateBalance } from './validation.js';
import { renderOpenOrders, showError, showSuccess, refreshWallets } from './ui.js';

function getSymbolConfig(sym) {
    return window.SPOT_CONFIG?.symbols?.[sym] || null;
}

export async function submitBuy() {
    const price = document.getElementById('buyPrice')?.value;
    const quantity = document.getElementById('buyQuantity')?.value;
    const sym = document.getElementById('spotSymbol')?.value || getSymbol();
    const cfg = getSymbolConfig(sym);

    const errors = validateOrder(price, quantity, cfg);
    if (errors.length > 0) {
        showError('buy', errors[0]);
        return;
    }

    const wallet = getWallet(cfg.quote_asset);
    const balErr = validateBalance('buy', price, quantity, wallet, cfg);
    if (balErr) {
        showError('buy', balErr);
        return;
    }

    const data = await createBuyOrder(sym, price, quantity);
    if (data.success) {
        showSuccess('buy', 'Đặt lệnh mua thành công.');
        document.getElementById('buyPrice').value = '';
        document.getElementById('buyQuantity').value = '';
        document.getElementById('buyTotal').textContent = '0.00 USDT';
        await loadAndRenderOrders();
        await refreshWallets();
    } else {
        showError('buy', data.message || 'Lỗi đặt lệnh.');
    }
}

export async function submitSell() {
    const price = document.getElementById('sellPrice')?.value;
    const quantity = document.getElementById('sellQuantity')?.value;
    const sym = document.getElementById('spotSellSymbol')?.value || getSymbol();
    const cfg = getSymbolConfig(sym);

    const errors = validateOrder(price, quantity, cfg);
    if (errors.length > 0) {
        showError('sell', errors[0]);
        return;
    }

    const wallet = getWallet(cfg.base_asset);
    const balErr = validateBalance('sell', price, quantity, wallet, cfg);
    if (balErr) {
        showError('sell', balErr);
        return;
    }

    const data = await createSellOrder(sym, price, quantity);
    if (data.success) {
        showSuccess('sell', 'Đặt lệnh bán thành công.');
        document.getElementById('sellPrice').value = '';
        document.getElementById('sellQuantity').value = '';
        document.getElementById('sellTotal').textContent = '0.00 USDT';
        await loadAndRenderOrders();
        await refreshWallets();
    } else {
        showError('sell', data.message || 'Lỗi đặt lệnh.');
    }
}

export async function handleCancel(id) {
    const data = await cancelOrder(id);
    if (data.success) {
        showSuccess('buy', 'Hủy lệnh thành công.');
        await loadAndRenderOrders();
        await refreshWallets();
    }
}

export async function loadAndRenderOrders() {
    const data = await getMyOrders();
    if (data.success && data.data) {
        const orders = data.data.data || data.data;
        setOrders(Array.isArray(orders) ? orders : []);
        renderOpenOrders();
    }
}

// Auto-load every 15s
let ordersInterval = null;
export function startOrderPolling() {
    loadAndRenderOrders();
    ordersInterval = setInterval(loadAndRenderOrders, 15000);
}
export function stopOrderPolling() {
    if (ordersInterval) clearInterval(ordersInterval);
}

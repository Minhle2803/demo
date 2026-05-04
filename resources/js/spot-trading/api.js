const BASE_URL = window.SPOT_CONFIG?.baseUrl || '/api/spot';

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function getAuthToken() {
    return localStorage.getItem('token');
}

async function request(url, options = {}) {
    const token = getAuthToken();
    const headers = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        ...(token && { Authorization: `Bearer ${token}` }),
        ...(options.headers || {}),
    };

    if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    return fetch(url, { ...options, headers });
}

export async function createBuyOrder(symbol, price, quantity) {
    const res = await request(`${BASE_URL}/orders/buy`, {
        method: 'POST',
        body: JSON.stringify({ symbol, price, quantity }),
    });
    return res.json();
}

export async function createSellOrder(symbol, price, quantity) {
    const res = await request(`${BASE_URL}/orders/sell`, {
        method: 'POST',
        body: JSON.stringify({ symbol, price, quantity }),
    });
    return res.json();
}

export async function cancelOrder(id) {
    const res = await request(`${BASE_URL}/orders/${id}/cancel`, { method: 'POST' });
    return res.json();
}

export async function getMyOrders() {
    const res = await request(`${BASE_URL}/orders`);
    return res.json();
}

export async function getMyTrades(symbol) {
    const url = symbol ? `${BASE_URL}/trades?symbol=${symbol}` : `${BASE_URL}/trades`;
    const res = await request(url);
    return res.json();
}

export async function getMyWallets() {
    const res = await request(`${BASE_URL}/wallets`);
    return res.json();
}

export async function getOrderBook(symbol) {
    const url = `${BASE_URL}/orderbook?symbol=${encodeURIComponent(symbol)}`;
    const res = await request(url);
    return res.json();
}

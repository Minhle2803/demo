const BASE = '/api/trade';

async function request(method, path, body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
    };
    if (body) opts.body = JSON.stringify(body);

    const res = await fetch(`${BASE}${path}`, opts);
    const json = await res.json();
    return { status: res.status, ...json };
}

export async function fetchCurrentSession() {
    return request('GET', '/session/current');
}

export async function fetchSessionResult(sessionId) {
    return request('GET', `/session/${sessionId}/result`);
}

export async function placeBuy(amount) {
    return request('POST', '/buy', { amount });
}

export async function placeSell(amount) {
    return request('POST', '/sell', { amount });
}

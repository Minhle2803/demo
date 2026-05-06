import { state, setSession, getServerNow } from './state.js';
import { fetchCurrentSession, fetchSessionResult, placeBuy, placeSell } from './api.js';
import { startTimer, stopTimer } from './timer.js';
import { setCountdown, setStatus, setSessionId, enableTrading, disableTrading, showResultPopup, showError } from './ui.js';
import { validateAmount } from './validation.js';
import { updateTrades, initTradesTable  } from './service.js';

let echo = null;
let pendingTrade = null;

async function init() {
    await loadSession();
    initTradesTable();
    updateTrades();
    bindButtons();
    initReverb();
    initConfirmModal();
}

async function loadSession() {
    const res = await fetchCurrentSession();

    if (!res.success || !res.data?.session) {
        setStatus('No active session');
        return;
    }

    applySession(res.data.session, res.data.server_time);
}

function applySession(session, serverTime) {
    setSession(session, serverTime ?? new Date().toISOString());

    setSessionId(session.id);
    setStatus(session.status);
    disableTrading();

    const now    = getServerNow();
    const lockAt = new Date(session.lock_time).getTime();

    if (session.status === 'open' && now < lockAt) {
        enableTrading();
    }

    startTimer(
        (remaining, isLocked) => {
            setCountdown(remaining);
            if (isLocked) {
                disableTrading();
                setStatus('locked');
            }
        },
        () => {
            disableTrading();
            setStatus('locked');
        },
        () => {
            setStatus('closed');
            scheduleResultFetch(session.id);
        }
    );
}

function scheduleResultFetch(sessionId) {
    // Small delay to let backend settle the result
    setTimeout(() => fetchResult(sessionId), 5000);
}

async function fetchResult(sessionId) {
    if (state.resultFetched) return;
    state.resultFetched = true;

    const res = await fetchSessionResult(sessionId);
    if (res.success) {
        showResultPopup(res.data.trade, res.data.session);
        // Load next session
        setTimeout(loadSession, 3000);
        await updateTrades();
    }
}

function bindButtons() {
    document.getElementById('buyBtn')?.addEventListener('click', () => handleTrade('buy'));
    document.getElementById('sellBtn')?.addEventListener('click', () => handleTrade('sell'));
}

function initConfirmModal() {
    const modal = document.getElementById('tradeConfirmModal');
    if (!modal) return;

    document.getElementById('confirmTradeBtn')?.addEventListener('click', () => {
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal?.hide();
        if (pendingTrade) {
            executeTrade(pendingTrade.type, pendingTrade.amount);
        }
    });

    modal.addEventListener('hidden.bs.modal', () => {
        if (pendingTrade) {
            const now    = getServerNow();
            const lockAt = state.session ? new Date(state.session.lock_time).getTime() : 0;
            if (now < lockAt) enableTrading();
            pendingTrade = null;
        }
    });
}

function showTradeConfirm(type, amount) {
    const priceEl = document.getElementById('market-price');
    const symbolEl = document.getElementById('market-symbol');
    const amountSelect = document.getElementById('trade-amount');

    const price = priceEl?.textContent ?? '—';
    const symbol = symbolEl?.textContent ?? '—';
    const amountLabel = amountSelect?.selectedOptions?.[0]?.textContent ?? amount;

    document.getElementById('confirm-type').textContent = type === 'buy' ? 'BUY' : 'SELL';
    document.getElementById('confirm-type').className = type === 'buy' ? 'text-success fw-bold' : 'text-danger fw-bold';
    document.getElementById('confirm-symbol').textContent = symbol;
    document.getElementById('confirm-price').textContent = price;
    document.getElementById('confirm-amount').textContent = amountLabel;

    const modal = new bootstrap.Modal(document.getElementById('tradeConfirmModal'));
    modal.show();
}

async function handleTrade(type) {
    if (state.tradePlaced) return;

    const raw = document.getElementById('trade-amount')?.value;
    const { valid, amount, error } = validateAmount(raw);

    if (!valid) {
        showError(error);
        return;
    }

    pendingTrade = { type, amount };
    showTradeConfirm(type, amount);
}

async function executeTrade(type, amount) {
    disableTrading();

    const res = type === 'buy' ? await placeBuy(amount) : await placeSell(amount);

    if (res.success) {
        state.tradePlaced = true;
        setStatus('Trade placed — waiting for result...');
        return;
    }

    // Handle specific error codes
    if (res.code === 'AUTH_UNAUTHORIZED') {
        window.location.href = '/signin';
        return;
    }

    if (res.code === 'USER_NOT_FULLY_VERIFIED') {
        if (confirm(res.message)) {
            window.location.href = '/profile';
        }
        return;
    }

    showError(res.message);

    // Re-enable if session still open
    const now    = getServerNow();
    const lockAt = state.session ? new Date(state.session.lock_time).getTime() : 0;
    if (now < lockAt) enableTrading();
}

function initReverb() {
    if (typeof window.Echo === 'undefined') return;

    echo = window.Echo;

    // Listen for session updates
    echo.channel('trading.session').listen('.session.updated', (data) => {
        if (data.status === 'open') {
            stopTimer();
            applySession(data, data.server_time);
        } else if (data.status === 'locked') {
            disableTrading();
            setStatus('locked');
        }
    });

    // Listen for result on current session
    listenForResult();
}

function listenForResult() {
    if (!state.session || !echo) return;

    echo.channel(`trading.result.${state.session.id}`).listen('.session.result', (data) => {
        if (!state.resultFetched) {
            state.resultFetched = true;
            fetchResult(data.session_id);
        }
    });
}

document.addEventListener('DOMContentLoaded', init);

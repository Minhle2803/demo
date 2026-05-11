import { state, setSession, getServerNow } from './state.js';
import { fetchCurrentSession, fetchSessionResult, placeBuy, placeSell, fetchFeeConfig } from './api.js';
import { startTimer, stopTimer } from './timer.js';
import { setCountdown, setStatus, setSessionId, enableTrading, disableTrading, showResultPopup, showError } from './ui.js';
import { validateAmount } from './validation.js';
import { updateTrades, initTradesTable, refreshSessionTrades } from './service.js';
import { tradeState } from './store';

let echo = null;
let pendingTrade = null;
let feePercent = 5;

async function init() {
    await loadFeeConfig();
    await loadSession();
    initTradesTable();
    await updateTrades();
    bindButtons();
    initReverb();
    initConfirmModal();
}

async function loadFeeConfig() {
    try {
        const res = await fetchFeeConfig();
        if (res.success && res.data?.fee_percent != null) {
            feePercent = parseFloat(res.data.fee_percent);
        }
    } catch (err) {
        console.error('Failed to load fee config:', err);
    }
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
        // if (session.id === tradeState.orderSessionId && tradeState.orderId) {
        //     console.log("==============================");
        //     console.log(tradeState.orderSessionId);
        //     console.log(tradeState.orderId);
        //     // Optionally, highlight the user's trade in the table or show a pending status
        //     // This requires additional UI handling to mark the trade as pending until result is fetched
        //     setTimeout(async () => {
        //         await fetchResult(session.id);
        //     }, 2000);
        // }
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

    listenForResult();
}

function scheduleResultFetch(sessionId) {
    // Small delay to let backend settle trades, then fetch result and next session.
    setTimeout(async () => {
        //await fetchResult(sessionId);
        // Load next session immediately after result
        setTimeout(loadSession, 1000);
    }, 2000);
}

async function fetchResult(sessionId) {
    if (window.tradeTableConfig.orderId <= 0 || window.tradeTableConfig.orderSessionId <= 0) return;

    try {
        const res = await fetchSessionResult(sessionId);
        if (!res.success) {
            console.error('Fetch session result failed:', res.code, res.message);
            return;
        }
        
        if (res.data?.session.id === window.tradeTableConfig.orderSessionId && window.tradeTableConfig.orderId !== 0) {
            showResultPopup(res.data.summary, res.data.session);
        }
        setTimeout(loadSession, 3000);
        await refreshSessionTrades(sessionId);
        await updateTrades();
    } catch (err) {
        console.error('Fetch session result error:', err);
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

    const feeAmount = Math.round((amount * 2) * (feePercent / 100) * 100) / 100;
    const estimatedPayout = (amount * 2) - feeAmount;

    document.getElementById('confirm-type').textContent = type === 'buy' ? 'BUY' : 'SELL';
    document.getElementById('confirm-type').className = type === 'buy' ? 'text-success fw-bold' : 'text-danger fw-bold';
    document.getElementById('confirm-symbol').textContent = symbol;
    document.getElementById('confirm-price').textContent = price;
    document.getElementById('confirm-amount').textContent = amountLabel;
    document.getElementById('confirm-fee').textContent = feeAmount.toLocaleString() + ' VND';
    document.getElementById('confirm-estimated-payout').textContent = estimatedPayout.toLocaleString() + ' VND';

    const modal = new bootstrap.Modal(document.getElementById('tradeConfirmModal'));
    modal.show();
}

async function handleTrade(type) {
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
        setStatus('Trade placed — waiting for result...');
        await updateTrades();
        deductBalanceDisplay(amount);
        if (res.data?.trade) {
            window.tradeTableConfig.orderId = res.data.trade.id;
            window.tradeTableConfig.orderSessionId = res.data.trade.session_id;
        }
        return;
    }

    // Handle specific error codes
    if (res.code === 'AUTH_UNAUTHORIZED') {
        window.location.href = '/signin';
        return;
    }

    showError(res.message);

    // Re-enable if session still open
    const now    = getServerNow();
    const lockAt = state.session ? new Date(state.session.lock_time).getTime() : 0;
    if (now < lockAt) enableTrading();
}

function deductBalanceDisplay(amount) {
    document.querySelectorAll('.balance-display').forEach(el => {
        const current = parseFloat(el.getAttribute('data-amount')) || 0;
        const newBalance = current - amount;
        el.setAttribute('data-amount', newBalance);
        el.innerHTML = newBalance.toLocaleString('vi-VN') + ' VND';
    });
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

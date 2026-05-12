import { formatDateUTC } from './timer.js';

export function setCountdown(seconds) {
    const el = document.getElementById('session-countdown');
    if (el) el.textContent = `${seconds}s`;
}

export function setStatus(text) {
    const el = document.getElementById('session-status');
    if (el) el.textContent = text;
}

export function setSessionId(id) {
    const el = document.getElementById('session-id');
    if (el) el.textContent = `#${id}`;
}

export function enableTrading() {
    document.getElementById('buyBtn').disabled  = false;
    document.getElementById('sellBtn').disabled = false;
}

export function disableTrading() {
    document.getElementById('buyBtn').disabled  = true;
    document.getElementById('sellBtn').disabled = true;
}

export function showResultPopup(summary, session) {
    const net = summary?.net ?? 0;
    const modalId = net > 0 ? 'modal-win' : 'modal-lost';
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;

    const sessionIdEl = modalEl.querySelector('#session_id');
    if (sessionIdEl) {
        sessionIdEl.textContent = `#${session?.id ?? '—'}`;
    }

    const totalAmountEl = modalEl.querySelector('#summary-total-amount');
    if (totalAmountEl) {
        totalAmountEl.textContent = (summary?.total_amount ?? 0).toLocaleString() + ' VND';
    }

    const winEl = modalEl.querySelector('#summary-win');
    if (winEl) {
        winEl.textContent = (summary?.total_win ?? 0).toLocaleString() + ' VND';
    }

    const loseEl = modalEl.querySelector('#summary-lose');
    if (loseEl) {
        loseEl.textContent = '-' + (summary?.total_lose ?? 0).toLocaleString() + ' VND';
    }

    const netEl = modalEl.querySelector('#summary-net');
    if (netEl) {
        const prefix = net >= 0 ? '+' : '';
        netEl.textContent = prefix + net.toLocaleString() + ' VND';
        netEl.className = net >= 0 ? 'fw-bold text-end text-success' : 'fw-bold text-end text-danger';
    }

    const feeEl = modalEl.querySelector('#summary-fee');
    if (feeEl) {
        const totalFee = summary?.total_fee ?? 0;
        feeEl.textContent = '-' + totalFee.toLocaleString() + ' VND';
    }

    const countEl = modalEl.querySelector('#trade-count');
    if (countEl) {
        countEl.textContent = (summary?.trade_count ?? 0);
    }

    updateBalanceDisplay(summary?.total_win ?? 0);

    window.tradeTableConfig.orderId = 0;
    window.tradeTableConfig.orderSessionId = 0;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function updateBalanceDisplay(payout) {
    document.querySelectorAll('.balance-display').forEach(el => {
        const current = parseFloat(el.getAttribute('data-amount')) || 0;
        const newBalance = current + payout;
        el.setAttribute('data-amount', newBalance);
        el.innerHTML = newBalance.toLocaleString('vi-VN') + ' VND';
    });
}

export function showError(message) {
    // Replace with your project's toast/notification system
    alert(message);
}


export function buildTradeRow(trade, coinMeta) {
    const meta = coinMeta[trade.session_symbol] || {};
    const name = meta.name || trade.session_symbol;
    const icon = meta.icon || 'default';

    const typeClass = trade.type === 'sell' ? 'text-danger' : 'text-success';
    const statusClass = trade.status === 'lose' ? 'text-danger' : 'text-success';

    const amount = Number(trade.amount).toLocaleString();
    const feeDisplay = trade.trading_fee != null && trade.trading_fee > 0
        ? Number(trade.trading_fee).toLocaleString()
        : '—';

    let resultSymb = '';
    let resultAmount = '';
    if (trade.status === 'lose') {
        resultSymb = '-';
        resultAmount = amount;
    } else if (trade.status === 'win') {
        resultSymb = '+';
        resultAmount = Number(trade.payout || 0).toLocaleString();
    } else {
        resultAmount = amount;
    }

    return `
        <tr data-id="${trade.id}">
            <td>
                <img src="/assets/images/svg/crypto-icons/${icon}.svg" width="16">
                ${name}
            </td>
            <td>${trade.session_id}</td>
            <td class="${typeClass}">${trade.type.toUpperCase()}</td>
            <td>${trade.session_open_price ?? '-'}</td>
            <td>${trade.session_close_price ?? '-'}</td>
            <td class="${statusClass}">${trade.status.toUpperCase()}</td>
            <td>${amount}</td>
            <td>${feeDisplay}</td>
            <td>
                <h6 class="${statusClass} fs-13 mb-0">${resultSymb}${resultAmount}</h6>
            </td>
            <td>${formatDateUTC(trade.created_at)}</td>
        </tr>
    `;
}
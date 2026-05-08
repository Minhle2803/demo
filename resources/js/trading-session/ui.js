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

export function showResultPopup(trade, session) {
    const won = trade?.status === 'win';
    const modalId = won ? 'modal-win' : 'modal-lost';
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;

    const sessionIdEl = modalEl.querySelector('#session_id');
    if (sessionIdEl) {
        sessionIdEl.textContent = `#${trade?.session_id ?? session?.id ?? '—'}`;
    }

    const amountEl = modalEl.querySelector('#amount_id');
    if (amountEl && trade) {
        amountEl.textContent = Number(trade.amount).toLocaleString();
    }
    window.tradeTableConfig.orderId = 0;
    window.tradeTableConfig.orderSessionId = 0;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
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

    var amount =  Number(trade.amount).toLocaleString();
    var symb = "";
    if (trade.status === 'lose') {
        symb = "-";
    } else if(trade.status === 'win') {
        symb = "+";
    }
    return `
        <tr data-id="${trade.id}">
            <td>
                <img src="/assets/images/svg/crypto-icons/${icon}.svg" width="16">
                ${name}
            </td>
            <td>${trade.session_id}</td>
            <td class="${typeClass}">${trade.type}</td>
            <td>${trade.session_open_price ?? '-'}</td>
            <td>${trade.session_close_price ?? '-'}</td>
            <td class="${statusClass}">${trade.status}</td>
            <td>${Number(trade.amount).toLocaleString()}</td>
            <td>
                <h6 class="${statusClass} fs-13 mb-0">${symb}${amount}</h6> 
            </td>
            <td>${formatDateUTC(trade.created_at)}</td>
        </tr>
    `;
}
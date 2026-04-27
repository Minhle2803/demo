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
    const popup = document.getElementById('trade-result-popup');
    if (!popup) return;

    const won = trade?.status === 'win';

    popup.innerHTML = `
        <div class="result-inner ${won ? 'win' : 'lose'}">
            <h2>${won ? '🎉 WIN' : '❌ LOSE'}</h2>
            <p>Open: ${session.open_price} → Close: ${session.close_price}</p>
            ${trade ? `<p>Payout: ${trade.payout}</p>` : '<p>No trade placed.</p>'}
        </div>
    `;
    popup.classList.remove('hidden');

    setTimeout(() => popup.classList.add('hidden'), 6000);
}

export function showError(message) {
    // Replace with your project's toast/notification system
    alert(message);
}

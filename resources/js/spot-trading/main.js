import { submitBuy, submitSell, loadAndRenderOrders, startOrderPolling } from './orders.js';
import { loadAndRenderWallets } from './wallets.js';
import { loadAndRenderTrades, initTotalCalculations } from './ui.js';
import { initOrderBook } from './orderbook.js';

// Auto-fill price inputs from the latest chart candle close price.
function initPriceAutoFill() {
    setInterval(() => {
        const price = window.__currentPrice;
        if (!price) return;

        const buyPrice = document.getElementById('buyPrice');
        const sellPrice = document.getElementById('sellPrice');

        if (buyPrice && !buyPrice.value) {
            buyPrice.value = parseFloat(price).toFixed(2);
            buyPrice.dispatchEvent(new Event('input'));
        }
        if (sellPrice && !sellPrice.value) {
            sellPrice.value = parseFloat(price).toFixed(2);
            sellPrice.dispatchEvent(new Event('input'));
        }
    }, 500);
}

document.addEventListener('DOMContentLoaded', async () => {
    initTotalCalculations();
    initOrderBook();
    initPriceAutoFill();

    // Bind buy/sell buttons
    const buyBtn = document.getElementById('spotBuyBtn');
    const sellBtn = document.getElementById('spotSellBtn');
    if (buyBtn) buyBtn.addEventListener('click', submitBuy);
    if (sellBtn) sellBtn.addEventListener('click', submitSell);

    // Load wallets and orders on page load
    await loadAndRenderWallets();
    startOrderPolling();

    // Load trade history
    loadAndRenderTrades();

    // Refresh trade history when tab is clicked
    const tradeTab = document.querySelector('[data-bs-toggle="tab"][href="#tradeHistoryTab"]');
    if (tradeTab) {
        tradeTab.addEventListener('shown.bs.tab', loadAndRenderTrades);
    }
});

import { fetchLatestTrades } from './api';
import { buildTradeRow } from './ui';
import { tradeState } from './store';

export function initTradesTable() {
    tradeState.table = document.querySelector('#tradesTable');
    if (!tradeState.table) return;

    tradeState.tbody = tradeState.table.querySelector('tbody');
    tradeState.config = window.tradeTableConfig || {};

    tradeState.lastId =
        tradeState.tbody.querySelector('tr')?.dataset.id || 0;
}

let isUpdating = false;

export async function updateTrades() {
    if (isUpdating) return;
    isUpdating = true;

    try {
        const res = await fetchLatestTrades(tradeState.lastId);

        if (!res.status || !res.data?.length) return;

        res.data.reverse().forEach((trade) => {
            if (tradeState.table.querySelector(`tr[data-id="${trade.id}"]`)) {
                return;
            }

            tradeState.tbody.insertAdjacentHTML(
                'afterbegin',
                buildTradeRow(trade, tradeState.config.coinMeta)
            );

            if (trade.id > tradeState.lastId) {
                tradeState.lastId = trade.id;
            }
        });

        while (tradeState.tbody.children.length > tradeState.config.maxRows) {
            tradeState.tbody.removeChild(tradeState.tbody.lastElementChild);
        }

    } finally {
        isUpdating = false;
    }
}
import { fetchLatestTrades, fetchTradesBySession } from './api';
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

export function upsertTradeRow(trade) {
    const existingRow = tradeState.table.querySelector(`tr[data-id="${trade.id}"]`);
    if (existingRow) {
        existingRow.outerHTML = buildTradeRow(trade, tradeState.config.coinMeta);
    } else {
        tradeState.tbody.insertAdjacentHTML(
            'afterbegin',
            buildTradeRow(trade, tradeState.config.coinMeta)
        );
    }
    if (trade.id > tradeState.lastId) {
        tradeState.lastId = trade.id;
    }
}

export async function updateTrades() {

    if (isUpdating) return;
    isUpdating = true;

    try {
        const res = await fetchLatestTrades(tradeState.lastId);
        if (!res.success || !res.data?.length) return;

        res.data.reverse().forEach((trade) => {
            upsertTradeRow(trade);
        });

        while (tradeState.tbody.children.length > tradeState.config.maxRows) {
            tradeState.tbody.removeChild(tradeState.tbody.lastElementChild);
        }

    } finally {
        isUpdating = false;
    }
}

export async function refreshSessionTrades(sessionId) {
    try {
        const res = await fetchTradesBySession(sessionId);
        if (!res.success || !res.data?.length) return;

        res.data.forEach((trade) => {
            upsertTradeRow(trade);
        });
    } catch (err) {
        console.error('Failed to refresh session trades:', err);
    }
}
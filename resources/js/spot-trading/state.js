const state = {
    symbol: 'BTC_USDT',
    wallets: [],
    orders: [],
    trades: [],
};

export function getSymbol() {
    return state.symbol;
}

export function setSymbol(sym) {
    state.symbol = sym;
}

export function getWallet(asset) {
    return state.wallets.find(w => w.asset === asset);
}

export function setWallets(wallets) {
    state.wallets = wallets;
}

export function setOrders(orders) {
    state.orders = orders;
}

export function getOrders() {
    return state.orders;
}

export function setTrades(trades) {
    state.trades = trades;
}

export function getTrades() {
    return state.trades;
}

export default state;

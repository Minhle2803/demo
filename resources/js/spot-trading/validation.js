export function validateOrder(price, quantity, symbolConfig) {
    const errors = [];

    if (!price || isNaN(price) || parseFloat(price) <= 0) {
        errors.push('Giá phải lớn hơn 0.');
    }

    if (!quantity || isNaN(quantity) || parseFloat(quantity) <= 0) {
        errors.push('Số lượng phải lớn hơn 0.');
    }

    if (symbolConfig && price && quantity) {
        const notional = parseFloat(price) * parseFloat(quantity);
        if (notional < parseFloat(symbolConfig.min_notional)) {
            errors.push(`Giá trị lệnh tối thiểu là ${symbolConfig.min_notional} USDT.`);
        }
    }

    return errors;
}

export function validateBalance(side, price, quantity, wallet, symbolConfig) {
    if (!wallet || !symbolConfig) return 'Wallet not found.';

    if (side === 'buy') {
        const required = parseFloat(price) * parseFloat(quantity);
        if (parseFloat(wallet.available_balance) < required) {
            return `Số dư ${symbolConfig.quote_asset} không đủ.`;
        }
    } else {
        if (parseFloat(wallet.available_balance) < parseFloat(quantity)) {
            return `Số dư ${symbolConfig.base_asset} không đủ.`;
        }
    }

    return null;
}

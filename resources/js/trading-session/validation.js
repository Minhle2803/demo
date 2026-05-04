export function validateAmount(raw) {
    const amount = parseFloat(raw);
    if (isNaN(amount) || amount < 1) {
        return { valid: false, error: 'Amount must be at least 1.' };
    }
    return { valid: true, amount };
}

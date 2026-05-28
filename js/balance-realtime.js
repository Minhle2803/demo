function formatBalance(amount) {
    // if (amount >= 1e9) {
    //     return (amount / 1e9).toFixed(1) + 'B VND';
    // }
    // if (amount >= 1e6) {
    //     return (amount / 1e6).toFixed(1) + 'M VND';
    // }
    // if (amount >= 1e3) {
    //     return (amount / 1e3).toFixed(0) + 'K VND';
    // }
    return amount.toLocaleString('vi-VN') + ' VND';
}

function updateAllBalanceDisplays(newBalance) {
    document.querySelectorAll('.balance-display').forEach((el) => {
        el.setAttribute('data-amount', newBalance);
        el.textContent = formatBalance(newBalance);
    });

    const totalBalance = document.getElementById('totalBalance');
    if (totalBalance) {
        totalBalance.textContent = newBalance.toLocaleString('vi-VN') + ' VNĐ';
    }
}

function initBalanceRealtime() {
    const userId = window.BALANCE_CONFIG?.userId;
    if (!userId || typeof window.Echo === 'undefined') {
        return;
    }

    window.Echo.channel(`user.${userId}.balance`).listen('.balance.updated', (event) => {
        if (event.newBalance !== undefined) {
            updateAllBalanceDisplays(event.newBalance);
        }
    });
}

document.addEventListener('DOMContentLoaded', initBalanceRealtime);

/**
 * Admin management pages — shared utilities for users/deposits/withdraws.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Confirm reject buttons
    document.querySelectorAll('[data-confirm]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            if (!confirm(btn.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});

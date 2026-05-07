/**
 * Real-time session table updates via Reverb/Echo.
 * Listens for session.updated events and updates the DOM.
 */

function initSessionRealtime() {
    if (typeof window.Echo === 'undefined') return;

    window.Echo.channel('trading.session').listen('.session.updated', (data) => {
        updateSessionRow(data);
        updateSessionDetail(data);
    });
}

const STATUS_BADGES = {
    future:  '<span class="badge bg-info-subtle text-info">Future</span>',
    open:    '<span class="badge bg-success-subtle text-success">Open</span>',
    locked:  '<span class="badge bg-warning-subtle text-warning">Locked</span>',
    closed:  '<span class="badge bg-secondary-subtle text-secondary">Closed</span>',
};

function formatTime(iso) {
    if (!iso) return '—';
    const d = new Date(iso);
    // GMT+7
    const offset = 7 * 60;
    const local = new Date(d.getTime() + offset * 60000);
    const pad = (n) => String(n).padStart(2, '0');
    return `${local.getUTCFullYear()}-${pad(local.getUTCMonth() + 1)}-${pad(local.getUTCDate())} `
        + `${pad(local.getUTCHours())}:${pad(local.getUTCMinutes())}:${pad(local.getUTCSeconds())}`;
}

function formatPrice(price) {
    if (price == null) return '—';
    return Number(price).toLocaleString('en-US', { minimumFractionDigits: 8, maximumFractionDigits: 8 });
}

function resultBadge(openPrice, closePrice) {
    if (!openPrice || !closePrice) return '—';
    const o = parseFloat(openPrice);
    const c = parseFloat(closePrice);
    if (c > o) return '<span class="text-success fw-bold">Mua</span>';
    if (c < o) return '<span class="text-danger fw-bold">Bán</span>';
    return '—';
}

function updateSessionRow(data) {
    const existing = document.querySelector(`tr[data-session-id="${data.id}"]`);

    if (existing) {
        // Update status cell (index 3, 0-based)
        const cells = existing.querySelectorAll('td');
        if (cells.length >= 9) {
            cells[3].innerHTML = STATUS_BADGES[data.status] || data.status;

            // Update close_price if provided (index 8 for index view, 8 for realtime view)
            if (data.close_price != null) {
                cells[8].textContent = formatPrice(data.close_price);
                // Update result column (index 9)
                cells[9].innerHTML = resultBadge(data.open_price, data.close_price);
            }
        }
    } else {
        // New session — prepend to table body
        const tbody = document.querySelector('table.table tbody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.setAttribute('data-session-id', data.id);

        const url = new URL(window.location.origin + '/admin/sessions/' + data.id);
        const showUrl = url.pathname;

        row.innerHTML = `
            <td>${data.id}</td>
            <td>${data.symbol || '—'}</td>
            <td>${data.interval || '—'}</td>
            <td>${STATUS_BADGES[data.status] || data.status}</td>
            <td>${formatTime(data.start_time)}</td>
            <td>${formatTime(data.lock_time)}</td>
            <td>${formatTime(data.end_time)}</td>
            <td>${formatPrice(data.open_price)}</td>
            <td>${formatPrice(data.close_price)}</td>
            <td>${resultBadge(data.open_price, data.close_price)}</td>
            <td>
                <a href="${showUrl}" class="btn btn-sm btn-soft-info">
                    <i class="ri-eye-line align-bottom"></i>
                </a>
            </td>
        `;

        tbody.insertBefore(row, tbody.firstChild);
    }
}

function updateSessionDetail(data) {
    // Detail page
    const detailCard = document.querySelector('[data-session-detail]');
    if (!detailCard) return;

    const statusCell = document.querySelector('[data-field="status"]');
    if (statusCell) {
        statusCell.innerHTML = STATUS_BADGES[data.status] || data.status;
    }

    const closeCell = document.querySelector('[data-field="close_price"]');
    if (closeCell && data.close_price != null) {
        closeCell.textContent = formatPrice(data.close_price);
    }

    const resultCell = document.querySelector('[data-field="result"]');
    if (resultCell && data.close_price != null) {
        resultCell.innerHTML = resultBadge(data.open_price, data.close_price);
    }
}

document.addEventListener('DOMContentLoaded', initSessionRealtime);

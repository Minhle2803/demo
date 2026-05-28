function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function getAuthToken() {
    return localStorage.getItem('token');
}

async function fetchWithAuth(url, options = {}) {
    const token = getAuthToken();
    return fetch(url, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            ...(token && { Authorization: `Bearer ${token}` }),
            ...(options.headers || {}),
        },
    });
}

function showFlash(type, message) {
    const existing = document.querySelector('.alert-flash');
    if (existing) existing.remove();

    const cls = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${cls} alert-dismissible fade show alert-flash`;
    alert.role = 'alert';
    alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

    const container = document.querySelector('.tab-content');
    if (container) container.insertBefore(alert, container.firstChild);
}

// ---------------------------------------------------------------------------
// Tab activation from URL query param
// ---------------------------------------------------------------------------
function initTabFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (!tab) return;

    const tabMap = {
        profile: '#personalDetails',
        password: '#changePassword',
        deposit: '#experience',
    };

    const selector = tabMap[tab];
    if (!selector) return;

    const trigger = document.querySelector(`[data-bs-toggle="tab"][href="${selector}"]`);
    if (trigger && typeof bootstrap !== 'undefined') {
        new bootstrap.Tab(trigger).show();
    }
}

// ---------------------------------------------------------------------------
// Status badge renderer
// ---------------------------------------------------------------------------
function statusBadge(status) {
    const map = {
        pending: 'bg-warning',
        processing: 'bg-info',
        done: 'bg-success',
        rejected: 'bg-danger',
    };
    const cls = map[status] || 'bg-secondary';
    return `<span class="badge ${cls}">${status}</span>`;
}

// ---------------------------------------------------------------------------
// Deposit History
// ---------------------------------------------------------------------------
async function loadDepositHistory(page = 1) {
    const tbody = document.getElementById('depositHistoryBody');
    const pagination = document.getElementById('depositPagination');
    if (!tbody) return;

    const config = window.profileConfig || {};
    try {
        const res = await fetchWithAuth(`${config.depositHistoryUrl}?page=${page}`);
        const data = await res.json();

        if (data.success && data.data) {
            const items = data.data.data || [];
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No deposit history.</td></tr>';
                if (pagination) pagination.innerHTML = '';
                return;
            }

            tbody.innerHTML = items.map(d => `
                <tr>
                    <td>${Number(d.amount).toLocaleString()} VND</td>
                    <td>${statusBadge(d.status)}</td>
                    <td>${escapeHtml(d.admin_note || '-')}</td>
                    <td>${new Date(d.created_at).toLocaleDateString('vi-VN')}</td>
                </tr>
            `).join('');

            if (pagination && data.data.last_page > 1) {
                let html = '<ul class="pagination pagination-sm">';
                for (let i = 1; i <= data.data.last_page; i++) {
                    html += `<li class="page-item ${i === data.data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                html += '</ul>';
                pagination.innerHTML = html;
                pagination.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        loadDepositHistory(parseInt(link.dataset.page));
                    });
                });
            } else if (pagination) {
                pagination.innerHTML = '';
            }
        }
    } catch {
        if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load history.</td></tr>';
    }
}

// ---------------------------------------------------------------------------
// Withdraw History
// ---------------------------------------------------------------------------
async function loadWithdrawHistory(page = 1) {
    const tbody = document.getElementById('withdrawHistoryBody');
    const pagination = document.getElementById('withdrawPagination');
    if (!tbody) return;

    const config = window.profileConfig || {};
    try {
        const res = await fetchWithAuth(`${config.withdrawHistoryUrl}?page=${page}`);
        const data = await res.json();

        if (data.success && data.data) {
            const items = data.data.data || [];
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No withdraw history.</td></tr>';
                if (pagination) pagination.innerHTML = '';
                return;
            }

            tbody.innerHTML = items.map(d => `
                <tr>
                    <td>${Number(d.amount).toLocaleString()} VND</td>
                    <td>${statusBadge(d.status)}</td>
                    <td>${escapeHtml(d.admin_note || '-')}</td>
                    <td>${new Date(d.created_at).toLocaleDateString('vi-VN')}</td>
                </tr>
            `).join('');

            if (pagination && data.data.last_page > 1) {
                let html = '<ul class="pagination pagination-sm">';
                for (let i = 1; i <= data.data.last_page; i++) {
                    html += `<li class="page-item ${i === data.data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                html += '</ul>';
                pagination.innerHTML = html;
                pagination.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        loadWithdrawHistory(parseInt(link.dataset.page));
                    });
                });
            } else if (pagination) {
                pagination.innerHTML = '';
            }
        }
    } catch {
        if (tbody) tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load history.</td></tr>';
    }
}

// ---------------------------------------------------------------------------
// Deposit QR generation
// ---------------------------------------------------------------------------
function initDeposit() {
    const depositBtn = document.getElementById('depositButton');
    const amountInput = document.getElementById('depositAmount');
    const errorEl = document.getElementById('depositError');
    const qrCode = document.getElementById('qr-code');
    const payBtn = document.getElementById('btn-pay');

    if (!depositBtn) return;

    depositBtn.addEventListener('click', async () => {
        const amount = amountInput?.value?.trim();
        const min = window.profileConfig?.minDeposit || 300000;
        if (!amount || isNaN(amount) || Number(amount) < min) {
            if (errorEl) {
                errorEl.textContent = 'Số tiền tối thiểu là ' + Number(min).toLocaleString('vi-VN') + ' VND.';
                errorEl.style.display = 'block';
            }
            return;
        }
        if (errorEl) errorEl.style.display = 'none';

        try {
            const config = window.profileConfig || {};
            const res = await fetchWithAuth(config.depositQrUrl || '/profile/deposit/qr', {
                method: 'POST',
                body: JSON.stringify({ amount: Number(amount) }),
            });
            const data = await res.json();

            if (data.success && data.data) {
                const d = data.data;
                const qrUrl = `https://img.vietqr.io/image/${d.bank_id}-${d.account_no}-compact.png?amount=${d.amount}&addInfo=${encodeURIComponent(d.content)}`;

                if (qrCode) {
                    qrCode.innerHTML = `<img style="width: 60%; height: 60%;" src="${qrUrl}" alt="QR Code" />`;
                }

                const elAccountName = document.getElementById('accountName');
                const elBankName = document.getElementById('bankName');
                const elAccountNo = document.getElementById('accountNo');
                const elTransferAmount = document.getElementById('transferAmount');
                if (elAccountName) elAccountName.textContent = d.account_name;
                if (elBankName) elBankName.textContent = d.bank_name;
                if (elAccountNo) elAccountNo.textContent = d.account_no;
                if (elTransferAmount) elTransferAmount.textContent = Number(d.amount).toLocaleString();
                showFlash('success', data.message || 'QR code generated.');
                loadDepositHistory();
            } else {
                showFlash('error', data.message || 'Failed to generate QR.');
            }
        } catch {
            showFlash('error', 'Lỗi mạng. Vui lòng thử lại.');
        }
    });

    if (payBtn) {
        payBtn.addEventListener('click', () => {
            if (qrCode) qrCode.innerHTML = '';
            if (amountInput) amountInput.value = '';
            if (errorEl) errorEl.style.display = 'none';
        });
    }
}

// ---------------------------------------------------------------------------
// Withdraw form
// ---------------------------------------------------------------------------
function initWithdraw() {
    const form = document.getElementById('withdrawForm');
    if (!form) return;

    const errorEl = document.getElementById('withdrawError');

    function showWithdrawError(msg, isKycError) {
        if (errorEl) {
            if (isKycError) {
                errorEl.innerHTML = `${escapeHtml(msg)} <a href="?tab=kyc" class="alert-link">Go to KYC Verification tab</a>.`;
            } else {
                errorEl.textContent = msg;
            }
            errorEl.style.display = 'block';
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const amount = document.getElementById('withdrawAmount')?.value;

        if (!amount || isNaN(amount) || Number(amount) < 10000) {
            showWithdrawError('Số tiền rút tối thiểu là 10,000 VND.', false);
            return;
        }
        if (errorEl) errorEl.style.display = 'none';

        const config = window.profileConfig || {};
        try {
            const res = await fetchWithAuth(config.withdrawSubmitUrl || '/profile/withdraw', {
                method: 'POST',
                body: JSON.stringify({ amount: Number(amount) }),
            });
            const data = await res.json();

            if (data.success) {
                if (errorEl) errorEl.style.display = 'none';
                showFlash('success', data.message || 'Gửi yêu cầu rút tiền thành công.');
                form.reset();
                loadWithdrawHistory();
                const totalBalance = document.getElementById('totalBalance');
                if (totalBalance && data.data?.new_balance !== undefined) {
                    totalBalance.textContent = Number(data.data.new_balance).toLocaleString();
                }
            } else {
                const isKycError = data.code === 'USER_NOT_FULLY_VERIFIED';
                showWithdrawError(data.message || 'Yêu cầu rút tiền thất bại.', isKycError);
            }
        } catch {
            showWithdrawError('Lỗi mạng.', false);
        }
    });
}

// ---------------------------------------------------------------------------
// KYC file previews
// ---------------------------------------------------------------------------
function initKycPreviews() {
    function previewFile(input, previewEl) {
        if (!input || !previewEl) return;

        const wrapper = input.closest('.custom-file-input-wrapper');
        const label = wrapper?.querySelector('.custom-file-label');
        const defaultText = label?.getAttribute('data-default') || 'Chọn file';

        input.addEventListener('change', () => {
            previewEl.innerHTML = '';
            const file = input.files[0];
            if (!file) {
                if (label) {
                    label.textContent = defaultText;
                    label.classList.remove('has-file');
                }
                return;
            }
            if (label) {
                label.textContent = file.name;
                label.classList.add('has-file');
            }
            const reader = new FileReader();
            reader.onload = (ev) => {
                previewEl.innerHTML = `<img src="${ev.target.result}" class="img-thumbnail mt-2" style="max-height: 200px;" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        });
    }

    previewFile(document.getElementById('kycFrontInput'), document.getElementById('kycFrontPreview'));
    previewFile(document.getElementById('kycBackInput'), document.getElementById('kycBackPreview'));
}

// ---------------------------------------------------------------------------
// AJAX form handling for profile update and password change
// ---------------------------------------------------------------------------
function initForms() {
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');

    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const config = window.profileConfig || {};
            const formData = new FormData(profileForm);

            try {
                const res = await fetch(config.updateProfileUrl || '/profile/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${getAuthToken()}`,
                    },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    showFlash('success', data.message || 'Cập nhật thông tin thành công.');
                } else {
                    showFlash('error', data.message || 'Cập nhật thất bại.');
                }
            } catch {
                showFlash('error', 'Network error.');
            }
        });
    }

    if (passwordForm) {
        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const config = window.profileConfig || {};
            try {
                const res = await fetchWithAuth(config.updatePasswordUrl || '/profile/password', {
                    method: 'POST',
                    body: JSON.stringify({
                        current_password: document.getElementById('currentPasswordInput')?.value,
                        new_password: document.getElementById('newPasswordInput')?.value,
                        new_password_confirmation: document.getElementById('confirmPasswordInput')?.value,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    showFlash('success', data.message || 'Mật khẩu đã được thay đổi.');
                    passwordForm.reset();
                } else {
                    showFlash('error', data.message || 'Thay đổi mật khẩu thất bại.');
                }
            } catch {
                showFlash('error', 'Lỗi mạng.');
            }
        });
    }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initTabFromUrl();
    initDeposit();
    initWithdraw();
    initKycPreviews();
    initForms();
    loadDepositHistory();
    loadWithdrawHistory();
});

function copyToClipboard(elementId) {
    const input = document.getElementById(elementId);
    if (!input) return;

    input.select();
    input.setSelectionRange(0, 99999);

    try {
        navigator.clipboard.writeText(input.value).then(() => {
            showToast('Copied to clipboard!');
        }).catch(() => {
            document.execCommand('copy');
            showToast('Copied to clipboard!');
        });
    } catch (err) {
        document.execCommand('copy');
        showToast('Copied to clipboard!');
    }
}

function copyInviteLink() {
    copyToClipboard('inviteLinkInput');
}

function copyClientInviteLink() {
    copyToClipboard('clientInviteLink');
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show align-items-center text-bg-success border-0">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Auto-fill referral code from URL ?ref= parameter on signup page
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const ref = params.get('ref');
    if (ref) {
        const input = document.getElementById('referral_code');
        if (input) {
            input.value = ref;
        }
    }
});

window.copyInviteLink = copyInviteLink;
window.copyClientInviteLink = copyClientInviteLink;

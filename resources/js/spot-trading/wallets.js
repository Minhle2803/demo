import { getMyWallets } from './api.js';
import { setWallets } from './state.js';
import { renderWalletBalances } from './ui.js';

export async function loadAndRenderWallets() {
    const data = await getMyWallets();
    if (data.success && data.data) {
        setWallets(data.data);
        renderWalletBalances();
    }
}

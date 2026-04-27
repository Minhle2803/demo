import { state, getServerNow } from './state.js';

let _interval = null;

export function startTimer(onTick, onLock, onExpire) {
    clearInterval(_interval);

    _interval = setInterval(() => {
        if (!state.session) return;

        const now        = getServerNow();
        const lockAt     = new Date(state.session.lock_time).getTime();
        const endAt      = new Date(state.session.end_time).getTime();
        const remaining  = Math.max(0, Math.ceil((endAt - now) / 1000));
        const isLocked   = now >= lockAt;
        const isExpired  = now >= endAt;

        onTick(remaining, isLocked);

        if (isExpired) {
            clearInterval(_interval);
            onExpire();
        } else if (isLocked) {
            onLock();
        }
    }, 500);
}

export function stopTimer() {
    clearInterval(_interval);
}

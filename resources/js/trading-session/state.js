export const state = {
    session: null,
    serverTimeDiff: 0, // ms offset between server and client clock
    tradePlaced: false,
    resultFetched: false,
};

export function setSession(session, serverTime) {
    state.session = session;
    state.serverTimeDiff = new Date(serverTime).getTime() - Date.now();
    state.tradePlaced = false;
    state.resultFetched = false;
}

export function getServerNow() {
    return Date.now() + state.serverTimeDiff;
}

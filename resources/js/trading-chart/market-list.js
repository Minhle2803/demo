

let marketListTimer = null

export function initMarketList(config) {
    fetchMarketList(config)

    marketListTimer = setInterval(() => {
        fetchMarketList(config)
    }, 60_000)
}

export function stopMarketList() {
    if (marketListTimer) {
        clearInterval(marketListTimer)
        marketListTimer = null
    }
}

async function fetchMarketList({ apiBase, interval = '1m', range = '1H' }) {
    const url = new URL(`${apiBase}/market-list`, window.location.origin)

    url.searchParams.set('interval', interval)
    url.searchParams.set('range', range)

    const res = await fetch(url, {
        headers: { Accept: 'application/json' },
    })

    const json = await res.json()

    if (!json.success) return

    renderMarketList(json.data)
}

function renderMarketList(items) {
    const el = document.getElementById('market-list')
    if (!el) return

    el.innerHTML = items.map(item => {
        const isUp = item.change_percent >= 0

        return `
            <li class="list-group-item d-flex align-items-center">
                        
                <div class="flex-shrink-0">
                    <img src="assets/images/svg/crypto-icons/${item.icon}.svg" class="avatar-xs" alt="">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">${item.name}</h6>
                    <p class="text-muted mb-0">${formatCompact(item.market_cap)}</p>
                </div>
                <div class="flex-shrink-0 text-end">
                    <h6 class="fs-14 mb-1">${formatMoney(item.price)}</h6>
                    <p class="text-${isUp ? 'success' : 'danger'} fs-12 mb-0">
                        ${isUp ? '+' : '-'}${formatMoney((item.change_value > 0)?item.change_value:item.change_value * -1)} (${isUp ? '+' : ''}${item.change_percent}%)
                    </p>
                </div>
            </li>
        `
    }).join('')
}

function formatMoney(value) {
    return `$${Number(value).toLocaleString('en-US', {
        minimumFractionDigits: 3,
        maximumFractionDigits: 3,
    })}`
}

function formatCompact(value) {
    return `$${Number(value).toLocaleString('en-US', {
        notation: 'compact',
        maximumFractionDigits: 2,
    })}`
}
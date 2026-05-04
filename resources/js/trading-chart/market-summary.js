
let summaryTimer = null

export function initMarketSummary(config) {
    fetchMarketSummary(config)

    summaryTimer = setInterval(() => {
        fetchMarketSummary(config)
    }, 60_000)
}

export function stopMarketSummary() {
    if (summaryTimer) {
        clearInterval(summaryTimer)
        summaryTimer = null
    }
}

export async function fetchMarketSummary({ apiBase, symbol, interval, range = '1H' }) {
    try {
        const url = new URL(`${apiBase}/summary`, window.location.origin)

        url.searchParams.set('symbol', symbol)
        url.searchParams.set('interval', interval)
        url.searchParams.set('range', range)

        const res = await fetch(url, {
            headers: {
                Accept: 'application/json',
            },
        })

        const json = await res.json()

        if (!json.success) {
            console.warn('Summary API error:', json.code)
            return
        }

        renderMarketSummary(json.data)
    } catch (error) {
        console.error('Failed to fetch market summary:', error)
    }
}

function renderMarketSummary(data) {
    setText('market-symbol', data.symbol.replace('_', '/'))
    setText('market-price', formatMoney(data.current_price))
    setText('market-high', formatMoney(data.high))
    setText('market-low', formatMoney(data.low))
    setText('market-volume', formatCompact(data.market_volume))

    const change = Number(data.change_percent)
    const changeText = `${change >= 0 ? '+' : ''}${change.toFixed(2)}%`

    setText('market-change', changeText)

    const changeEl = document.getElementById('market-change')
    const iconEl = document.getElementById('market-icon')
    const bgEl = document.getElementById('market-bg')

    if (iconEl) {
        iconEl.classList.remove('ri-arrow-right-up-line', 'ri-arrow-right-down-line')
        iconEl.classList.add(change >= 0 ? 'ri-arrow-right-up-line' : 'ri-arrow-right-down-line')
    }
    if (changeEl && iconEl && bgEl) {
        iconEl.classList.remove('ri-arrow-right-up-line', 'ri-arrow-right-down-line')
        iconEl.classList.add(change >= 0 ? 'ri-arrow-right-up-line' : 'ri-arrow-right-down-line')

        bgEl.classList.remove('bg-success-subtle', 'text-success', 'bg-danger-subtle', 'text-danger')
        bgEl.classList.add(change >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle')
        bgEl.classList.add(change >= 0 ? 'text-success' : 'text-danger')

        changeEl.classList.remove('up', 'down')
        changeEl.classList.add(change >= 0 ? 'up' : 'down')
    }
}

function setText(id, value) {
    const el = document.getElementById(id)
    if (el) el.textContent = value
}

function formatMoney(value) {
    return `$${Number(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`
}

function formatCompact(value) {
    return `$${Number(value).toLocaleString('en-US', {
        notation: 'compact',
        maximumFractionDigits: 2,
    })}`
}
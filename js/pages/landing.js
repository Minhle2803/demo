/**
 * resources/js/pages/landing.js
 * Binance Landing Page — Behaviour
 */

// ── MOCK DATA (fallback if API unavailable) ────────────────────────────────
const MOCK_MARKETS = [
    { symbol: 'BTC_USDT', name: 'Bitcoin',  icon: '₿', price: 60248.00, change: 2.41,  volume: 1842300000 },
    { symbol: 'ETH_USDT', name: 'Ethereum', icon: 'Ξ', price: 3124.50,  change: -1.08, volume: 622100000  },
    { symbol: 'SOL_USDT', name: 'Solana',   icon: '◎', price: 148.32,   change: 4.72,  volume: 98400000   },
];

// ── FORMAT HELPERS ─────────────────────────────────────────────────────────
function fmtPrice(n) {
    const num = parseFloat(n);
    if (num >= 1000) return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (num >= 1)    return '$' + num.toFixed(4);
    return '$' + num.toFixed(8);
}

function fmtChange(n) {
    const num = parseFloat(n);
    const sign = num >= 0 ? '+' : '';
    return `${sign}${num.toFixed(2)}%`;
}

function fmtVolume(n) {
    const num = parseFloat(n);
    if (num >= 1e9) return '$' + (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return '$' + (num / 1e6).toFixed(2) + 'M';
    if (num >= 1e3) return '$' + (num / 1e3).toFixed(2) + 'K';
    return '$' + num.toFixed(2);
}

// ── NAVBAR SCROLL ──────────────────────────────────────────────────────────
function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// ── HAMBURGER MENU ─────────────────────────────────────────────────────────
function initHamburger() {
    const btn   = document.getElementById('hamburger');
    const links = document.getElementById('nav-links');
    if (!btn || !links) return;

    btn.addEventListener('click', () => {
        const open = links.classList.toggle('open');
        btn.setAttribute('aria-expanded', open);
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !links.contains(e.target)) {
            links.classList.remove('open');
        }
    });
}

// ── COUNTER ANIMATION ──────────────────────────────────────────────────────
function animateCounters() {
    const counters = document.querySelectorAll('[data-count]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el    = entry.target;
            const end   = parseFloat(el.dataset.count);
            const isFloat = String(end).includes('.');
            const duration = 1500;
            const start = performance.now();

            const tick = (now) => {
                const elapsed  = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                const current  = end * eased;
                el.textContent = isFloat ? current.toFixed(1) : Math.floor(current).toLocaleString('en-US');
                if (progress < 1) requestAnimationFrame(tick);
            };

            requestAnimationFrame(tick);
            observer.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
}

// ── MARKET DATA ────────────────────────────────────────────────────────────
async function loadMarkets() {
    let markets = [];

    try {
        const res  = await fetch('/api/internal/market/list', {
            headers: { 'Accept': 'application/json' },
            signal: AbortSignal.timeout(4000),
        });
        const json = await res.json();
        if (json.success && Array.isArray(json.data) && json.data.length > 0) {
            markets = json.data;
        } else {
            markets = MOCK_MARKETS;
        }
    } catch {
        markets = MOCK_MARKETS;
    }

    renderMarketTable(markets);
    renderTicker(markets);
}

function renderMarketTable(markets) {
    const tbody = document.getElementById('market-body');
    if (!tbody) return;

    tbody.innerHTML = markets.map((m, i) => {
        const isUp = parseFloat(m.change) >= 0;
        return `
            <tr>
                <td><span style="color:var(--text-muted);font-family:var(--font-mono);font-size:13px">${i + 1}</span></td>
                <td>
                    <div class="market-pair">
                        <div class="pair-icon">${m.icon ?? '●'}</div>
                        <div>
                            <div class="pair-name">${m.symbol.replace('_', '/')}</div>
                            <div class="pair-sub">${m.name ?? ''}</div>
                        </div>
                    </div>
                </td>
                <td><span class="market-price">${fmtPrice(m.price)}</span></td>
                <td><span class="market-change ${isUp ? 'positive' : 'negative'}">${fmtChange(m.change)}</span></td>
                <td><span class="market-volume">${fmtVolume(m.volume)}</span></td>
                <td class="market-action">
                    <a href="/trade?symbol=${m.symbol}" class="btn-outline">Trade</a>
                </td>
            </tr>
        `;
    }).join('');
}

function renderTicker(markets) {
    const track = document.getElementById('ticker-track');
    if (!track) return;

    // Duplicate for seamless loop
    const items = [...markets, ...markets, ...markets, ...markets];

    track.innerHTML = items.map(m => {
        const isUp = parseFloat(m.change) >= 0;
        return `
            <div class="ticker-item">
                <span class="ticker-pair">${m.symbol.replace('_', '/')}</span>
                <span class="ticker-price">${fmtPrice(m.price)}</span>
                <span class="ticker-change ${isUp ? 'positive' : 'negative'}">${fmtChange(m.change)}</span>
            </div>
        `;
    }).join('');
}

// ── SCROLL REVEAL ──────────────────────────────────────────────────────────
function initReveal() {
    const targets = document.querySelectorAll(
        '.feature-card, .security-card, .section-header, .market-table-wrap, .mobile-inner > *'
    );

    targets.forEach(el => el.classList.add('reveal'));

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(el => observer.observe(el));
}

// ── SECURITY BAR ANIMATION ─────────────────────────────────────────────────
function initSecurityBars() {
    const bars = document.querySelectorAll('.security-bar-fill');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    bars.forEach(bar => observer.observe(bar));
}

// ── HERO BTC PRICE PULSE ───────────────────────────────────────────────────
function initHeroPrice() {
    const el = document.getElementById('hero-btc-price');
    if (!el) return;

    // Simulate live price micro-movements
    let base = 60248;
    setInterval(() => {
        base += (Math.random() - 0.5) * 20;
        el.textContent = '$' + base.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }, 3000);
}

// ── BOOT ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initHamburger();
    animateCounters();
    initReveal();
    initSecurityBars();
    initHeroPrice();
    loadMarkets();
});
/**
 * resources/js/pages/landing.js
 * CryptoX Landing Page — Binance-inspired
 */

// ── MOCK DATA ──────────────────────────────────────────────
const MOCK = [
    { sym:'BTC',  name:'Bitcoin',  icon:'₿', color:'#f7931a', price:60248.32, change:2.41,  vol:1842300000, cap:1182000000000 },
    { sym:'ETH',  name:'Ethereum', icon:'Ξ', color:'#627eea', price:3124.50,  change:-1.08, vol:622100000,  cap:374800000000  },
    { sym:'SOL',  name:'Solana',   icon:'◎', color:'#9945ff', price:148.32,   change:4.72,  vol:98400000,   cap:67000000000   },
    { sym:'BNB',  name:'BNB',      icon:'B', color:'#F0B90B', price:412.80,   change:0.85,  vol:245000000,  cap:63500000000   },
    { sym:'XRP',  name:'XRP',      icon:'X', color:'#2196f3', price:0.5842,   change:-2.14, vol:185000000,  cap:32000000000   },
    { sym:'ADA',  name:'Cardano',  icon:'A', color:'#0033ad', price:0.4421,   change:1.33,  vol:87000000,   cap:15600000000   },
    { sym:'DOGE', name:'Dogecoin', icon:'D', color:'#c2a633', price:0.1582,   change:6.94,  vol:420000000,  cap:22800000000   },
    { sym:'AVAX', name:'Avalanche',icon:'V', color:'#e84142', price:36.42,    change:-0.61, vol:92000000,   cap:14900000000   },
];

// ── FORMAT HELPERS ──────────────────────────────────────────
const fmtPrice = (n) => {
    const v = parseFloat(n);
    if (v >= 1000) return '$' + v.toLocaleString('en-US', { minimumFractionDigits:2, maximumFractionDigits:2 });
    if (v >= 1)    return '$' + v.toFixed(4);
    return '$' + v.toFixed(6);
};

const fmtChange = (n) => {
    const v = parseFloat(n);
    return (v >= 0 ? '+' : '') + v.toFixed(2) + '%';
};

const fmtVol = (n) => {
    if (n >= 1e9) return '$' + (n/1e9).toFixed(2) + 'B';
    if (n >= 1e6) return '$' + (n/1e6).toFixed(2) + 'M';
    if (n >= 1e3) return '$' + (n/1e3).toFixed(2) + 'K';
    return '$' + n.toFixed(2);
};

const fmtCap = fmtVol;

// ── LIVE PRICE SIMULATION ───────────────────────────────────
const livePrices = MOCK.map(m => ({ ...m }));
let priceInterval;

function simulatePrices() {
    livePrices.forEach(coin => {
        const drift = (Math.random() - 0.495) * 0.0015;
        coin.price = Math.max(0.00001, coin.price * (1 + drift));
        coin.change += (Math.random() - 0.5) * 0.08;
    });
}

// ── NAVBAR ──────────────────────────────────────────────────
function initNavbar() {
    const nb  = document.getElementById('navbar');
    const hmb = document.getElementById('hamburger');
    const menu= document.getElementById('nav-menu');
    if (!nb) return;

    window.addEventListener('scroll', () => {
        nb.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive:true });

    hmb?.addEventListener('click', () => {
        const open = menu.classList.toggle('open');
        hmb.setAttribute('aria-expanded', open);
        const spans = hmb.querySelectorAll('span');
        if (open) {
            spans[0].style.cssText = 'transform:rotate(45deg) translate(5px,5px)';
            spans[1].style.cssText = 'opacity:0';
            spans[2].style.cssText = 'transform:rotate(-45deg) translate(5px,-5px)';
        } else {
            spans.forEach(s => s.style.cssText = '');
        }
    });

    document.addEventListener('click', (e) => {
        if (!hmb?.contains(e.target) && !menu?.contains(e.target)) {
            menu?.classList.remove('open');
            hmb?.querySelectorAll('span').forEach(s => s.style.cssText = '');
        }
    });
}

// ── TICKER ──────────────────────────────────────────────────
function buildTicker() {
    const track = document.getElementById('ticker-track');
    if (!track) return;

    const render = () => {
        // Duplicate for seamless infinite scroll
        const items = [...livePrices, ...livePrices, ...livePrices];
        track.innerHTML = items.map(c => {
            const isUp = c.change >= 0;
            return `<span class="ticker-item">
                <span class="ti-sym">${c.sym}/USDT</span>
                <span class="ti-price">${fmtPrice(c.price)}</span>
                <span class="${isUp ? 'ti-chg-pos' : 'ti-chg-neg'}">${fmtChange(c.change)}</span>
            </span>`;
        }).join('');
    };

    render();
    setInterval(render, 5000);
}

// ── HERO MARKET ROWS ────────────────────────────────────────
function buildHeroRows() {
    const container = document.getElementById('hero-rows');
    if (!container) return;

    const render = () => {
        container.innerHTML = livePrices.slice(0, 5).map(c => {
            const isUp = c.change >= 0;
            return `<div class="hero-mkt-row" onclick="location.href='/trade?symbol=${c.sym}_USDT'">
                <div class="hero-row-pair">
                    <span class="hero-row-name">${c.sym}/USDT</span>
                    <span class="hero-row-vol">Vol ${fmtVol(c.vol)}</span>
                </div>
                <span class="hero-row-price">${fmtPrice(c.price)}</span>
                <span class="hero-row-change ${isUp ? 'green-txt' : 'red-txt'}">${fmtChange(c.change)}</span>
            </div>`;
        }).join('');
    };

    render();
    setInterval(render, 3000);
}

// Hero tabs
function initHeroTabs() {
    document.querySelectorAll('.hct').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.hct').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            // Shuffle order to simulate tab switching
            livePrices.sort(() => Math.random() - 0.5);
            buildHeroRows();
        });
    });
}

// ── MARKETS TABLE ────────────────────────────────────────────
function buildSparkline(isUp, containerId) {
    const points = Array.from({ length: 8 }, () => Math.random() * 40 + 20);
    // Force overall trend direction
    if (isUp) { points[0] = 55; points[7] = 15; }
    else       { points[0] = 15; points[7] = 55; }

    const w = 80, h = 32;
    const xStep = w / (points.length - 1);
    const minP = Math.min(...points);
    const maxP = Math.max(...points);
    const scaleY = (v) => h - ((v - minP) / (maxP - minP + 0.01)) * (h - 4) - 2;

    const pts = points.map((v, i) => `${i * xStep},${scaleY(v)}`).join(' ');
    const color = isUp ? '#0ecb81' : '#f6465d';

    return `<svg class="mkt-spark" viewBox="0 0 ${w} ${h}" preserveAspectRatio="none">
        <polyline points="${pts}" fill="none" stroke="${color}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>`;
}

function buildMarketsTable(data) {
    const tbody = document.getElementById('mkt-body');
    if (!tbody) return;

    tbody.innerHTML = data.map((c, i) => {
        const isUp = c.change >= 0;
        return `<tr onclick="location.href='/trade?symbol=${c.sym}_USDT'" style="cursor:pointer">
            <td>
                <div class="mkt-name-cell">
                    <div class="mkt-icon" style="background:${c.color}22;color:${c.color}">${c.icon}</div>
                    <div class="mkt-names">
                        <div class="mkt-sym">${c.sym}</div>
                        <div class="mkt-full">${c.name}</div>
                    </div>
                </div>
            </td>
            <td class="mkt-price">${fmtPrice(c.price)}</td>
            <td class="mkt-change-cell">
                <span class="mkt-change ${isUp ? 'pos' : 'neg'}">${fmtChange(c.change)}</span>
            </td>
            <td class="mkt-vol">${fmtVol(c.vol)}</td>
            <td class="mkt-cap">${fmtCap(c.cap)}</td>
            <td class="mkt-spark-cell">${buildSparkline(isUp)}</td>
            <td><a href="/trade?symbol=${c.sym}_USDT" class="mkt-trade-btn" onclick="event.stopPropagation()">Trade</a></td>
        </tr>`;
    }).join('');
}

function initMarketsTabs() {
    document.querySelectorAll('.mkt-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.mkt-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            // Sort differently per tab
            const tab = btn.dataset.mkt;
            let sorted = [...livePrices];
            if (tab === 'gainers') sorted.sort((a,b) => b.change - a.change);
            else if (tab === 'volume') sorted.sort((a,b) => b.vol - a.vol);
            else if (tab === 'new') sorted.sort(() => Math.random() - 0.5);
            buildMarketsTable(sorted);
        });
    });
}

// Live update markets table
function startLiveMarkets() {
    setInterval(() => {
        simulatePrices();
        buildMarketsTable(livePrices);
    }, 4000);
}

// ── FETCH FROM API OR USE MOCK ───────────────────────────────
async function loadMarkets() {
    try {
        const res  = await fetch('/api/internal/chart/candles?limit=1', {
            headers: { 'Accept':'application/json' },
            signal:  AbortSignal.timeout(3000),
        });
        const json = await res.json();
        // If API works, shape the data — otherwise fall to mock
        if (json.success && Array.isArray(json.data) && json.data.length > 0) {
            // Map API data onto mock structure (patch prices)
            livePrices[0].price = parseFloat(json.data[0]?.close ?? livePrices[0].price);
        }
    } catch {
        // API unavailable — mock data is already loaded
    }

    buildHeroRows();
    buildMarketsTable(livePrices);
    buildTicker();
    startLiveMarkets();
}

// ── HERO EMAIL → REGISTER ────────────────────────────────────
function initHeroCta() {
    const btn   = document.getElementById('hero-cta');
    const email = document.getElementById('hero-email');
    if (!btn || !email) return;

    btn.addEventListener('click', () => {
        const val = email.value.trim();
        if (val) {
            location.href = `/register?email=${encodeURIComponent(val)}`;
        } else {
            location.href = '/register';
        }
    });

    email.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') btn.click();
    });
}

// ── SCROLL REVEAL ────────────────────────────────────────────
function initReveal() {
    const els = document.querySelectorAll(
        '.why-card, .prod-card, .section-h2, .section-sub, .stat-block, .app-inner > *, .cta-final-inner > *'
    );

    els.forEach(el => el.classList.add('reveal'));

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('in'), i * 60);
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.1, rootMargin:'0px 0px -40px 0px' });

    els.forEach(el => io.observe(el));
}

// ── HERO TITLE ANIMATION ─────────────────────────────────────
function animateHero() {
    const spans = document.querySelectorAll('.hero-title span');
    spans.forEach((s, i) => {
        s.style.opacity = '0';
        s.style.transform = 'translateY(32px)';
        s.style.transition = `opacity .6s ease ${i * .15}s, transform .6s ease ${i * .15}s`;
        requestAnimationFrame(() => {
            setTimeout(() => {
                s.style.opacity = '1';
                s.style.transform = 'translateY(0)';
            }, 50);
        });
    });

    // Sub text
    const sub = document.querySelector('.hero-sub');
    if (sub) {
        sub.style.opacity = '0';
        sub.style.transform = 'translateY(20px)';
        sub.style.transition = 'opacity .6s ease .5s, transform .6s ease .5s';
        setTimeout(() => {
            sub.style.opacity = '1';
            sub.style.transform = 'translateY(0)';
        }, 50);
    }

    const form = document.querySelector('.hero-form');
    if (form) {
        form.style.opacity = '0';
        form.style.transform = 'translateY(16px)';
        form.style.transition = 'opacity .6s ease .7s, transform .6s ease .7s';
        setTimeout(() => {
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 50);
    }
}

// ── SMOOTH ANCHOR SCROLL ─────────────────────────────────────
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', (e) => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const top = target.getBoundingClientRect().top + window.scrollY - 72;
                window.scrollTo({ top, behavior:'smooth' });
            }
        });
    });
}

// ── BOOT ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    animateHero();
    initHeroCta();
    initHeroTabs();
    initMarketsTabs();
    initReveal();
    initSmoothScroll();
    loadMarkets();
});

// Expose for debug
window.__cryptox = { livePrices, fmtPrice, fmtChange };
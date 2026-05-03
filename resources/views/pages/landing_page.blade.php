<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional crypto spot trading. Real-time charts, secure wallet, KYC-verified accounts.">
    <title>TradeX — Crypto Trading Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:wght@300;400;500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite([
        'resources/css/landing.css',
        'resources/js/pages/landing.js'
    ])
</head>
<body>

{{-- ── NOISE OVERLAY ─────────────────────────────────────────────────────────── --}}
<div class="noise-overlay" aria-hidden="true"></div>

{{-- ══════════════════════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════════════════════ --}}
<header class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="/" class="logo">
            <img 
                src="{{ asset('assets/images/logo/tradex_logo.png') }}" 
                alt="TRADEX Logo"
                class="logo-img"
            >
            <span class="logo-text">TRADEX</span>
        </a>

        <nav class="nav-links" id="nav-links">
            <a href="#markets" class="nav-link">Markets</a>
            <a href="{{ route('tradding') }}" class="nav-link">Trade</a>
            <a href="{{ route('client.profile.show') }}" class="nav-link">Wallet</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('signin') }}" class="btn-ghost">Login</a>
            <a href="{{ route('signup') }}" class="btn-primary">Register</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-grid-bg" aria-hidden="true"></div>
    <div class="hero-glow" aria-hidden="true"></div>

    <div class="container hero-inner">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Live Trading Active
            </div>

            <h1 class="hero-headline">
                <span class="line-1">TRADE</span>
                <span class="line-2">CRYPTO</span>
                <span class="line-3">WITH <em>PRECISION</em></span>
            </h1>

            <p class="hero-sub">
                Institutional-grade spot trading. Real-time order matching,
                KYC-secured accounts, and non-custodial wallet protection.
            </p>

            <div class="hero-cta">
                <a href="/register" class="btn-primary btn-lg">
                    <span>Create Account</span>
                    <span class="btn-arrow">→</span>
                </a>
                <a href="#markets" class="btn-outline btn-lg">View Markets</a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-num" data-count="12400">0</span>
                        <span class="stat-suffix">+</span>
                    </div>
                    <span class="stat-label">Active Traders</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-prefix">$</span><span class="stat-num" data-count="48">0</span><span class="stat-suffix">M+</span>
                    </div>
                    <span class="stat-label">Volume 24h</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-num" data-count="99.9">0</span><span class="stat-suffix">%</span>
                    </div>
                    <span class="stat-label">Uptime</span>
                </div>
            </div>
        </div>

        <div class="hero-chart-preview" aria-hidden="true">
            <div class="chart-mock">
                <div class="chart-header">
                    <span class="chart-pair">BTC / USDT</span>
                    <span class="chart-price" id="hero-btc-price">$60,248.00</span>
                    <span class="chart-change positive">+2.41%</span>
                </div>
                <svg class="chart-svg" viewBox="0 0 400 160" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#F0B90B" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#F0B90B" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <path class="chart-area" d="M0,120 C30,110 50,90 80,85 C110,80 130,95 160,80 C190,65 210,50 240,45 C270,40 290,55 320,40 C350,25 370,20 400,10 L400,160 L0,160 Z" fill="url(#chartGrad)"/>
                    <path class="chart-line" d="M0,120 C30,110 50,90 80,85 C110,80 130,95 160,80 C190,65 210,50 240,45 C270,40 290,55 320,40 C350,25 370,20 400,10" fill="none" stroke="#F0B90B" stroke-width="2"/>
                    <circle class="chart-dot" cx="400" cy="10" r="4" fill="#F0B90B"/>
                </svg>
                <div class="chart-candles">
                    <div class="candle green"><div class="candle-body"></div><div class="candle-wick"></div></div>
                    <div class="candle red"><div class="candle-body"></div><div class="candle-wick"></div></div>
                    <div class="candle green"><div class="candle-body"></div><div class="candle-wick"></div></div>
                    <div class="candle green"><div class="candle-body"></div><div class="candle-wick"></div></div>
                    <div class="candle red"><div class="candle-body"></div><div class="candle-wick"></div></div>
                    <div class="candle green"><div class="candle-body"></div><div class="candle-wick"></div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ticker-bar">
        <div class="ticker-track" id="ticker-track">
            {{-- Populated by JS --}}
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     MARKETS
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="section markets-section" id="markets">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Live Markets</div>
            <h2 class="section-title">MARKET <span class="gold">OVERVIEW</span></h2>
        </div>

        <div class="market-table-wrap">
            <table class="market-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pair</th>
                        <th>Price</th>
                        <th>24h Change</th>
                        <th>Volume</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="market-body">
                    {{-- Populated by JS --}}
                    <tr class="market-skeleton"><td colspan="6"><div class="skeleton-row"></div></td></tr>
                    <tr class="market-skeleton"><td colspan="6"><div class="skeleton-row"></div></td></tr>
                    <tr class="market-skeleton"><td colspan="6"><div class="skeleton-row"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     FEATURES
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="section features-section" id="features">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Why TradeX</div>
            <h2 class="section-title">BUILT FOR <span class="gold">SERIOUS</span> TRADERS</h2>
        </div>

        <div class="features-grid">
            <div class="feature-card feature-card--large">
                <div class="feature-icon">⬡</div>
                <h3>Real-Time Charts</h3>
                <p>Professional K-line charts powered by WebSocket. Price updates every 5 seconds with full OHLCV data.</p>
                <div class="feature-visual chart-visual">
                    <div class="mini-bars">
                        <div class="bar" style="height:40%"></div>
                        <div class="bar" style="height:65%"></div>
                        <div class="bar up" style="height:50%"></div>
                        <div class="bar up" style="height:80%"></div>
                        <div class="bar" style="height:60%"></div>
                        <div class="bar up" style="height:90%"></div>
                        <div class="bar up" style="height:75%"></div>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">◎</div>
                <h3>Secure Wallet</h3>
                <p>Per-asset wallets with locked/available balance separation. Every transaction logged immutably.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">◈</div>
                <h3>KYC Verified</h3>
                <p>Two-step verification: email + phone OTP. ID document required before trading.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⟳</div>
                <h3>Fast Matching</h3>
                <p>Limit order auto-matching with partial fill support. Admin manual matching as fallback.</p>
            </div>

            <div class="feature-card feature-card--wide">
                <div class="feature-icon">◉</div>
                <h3>Spot Trading Engine</h3>
                <p>Full order book: Buy/Sell limit orders, cancellation, trade history, and wallet ledger — all in one platform.</p>
                <div class="orderbook-visual">
                    <div class="ob-row sell"><span>65,100</span><span>0.042</span></div>
                    <div class="ob-row sell"><span>65,050</span><span>0.128</span></div>
                    <div class="ob-mid">65,000 <span class="gold">▲</span></div>
                    <div class="ob-row buy"><span>64,950</span><span>0.215</span></div>
                    <div class="ob-row buy"><span>64,900</span><span>0.089</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     SECURITY
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="section security-section" id="security">
    <div class="security-bg" aria-hidden="true"></div>
    <div class="container">
        <div class="section-header">
            <div class="section-label">Security First</div>
            <h2 class="section-title">YOUR FUNDS <span class="gold">PROTECTED</span></h2>
        </div>

        <div class="security-grid">
            <div class="security-card">
                <div class="security-num">01</div>
                <h3>Identity Verified</h3>
                <p>Email + phone OTP + KYC document upload. No unverified accounts can trade.</p>
                <div class="security-bar"><div class="security-bar-fill" style="width:100%"></div></div>
            </div>
            <div class="security-card">
                <div class="security-num">02</div>
                <h3>Balance Protection</h3>
                <p>Row-level DB locking prevents race conditions. Negative balances are blocked at the database level.</p>
                <div class="security-bar"><div class="security-bar-fill" style="width:95%"></div></div>
            </div>
            <div class="security-card">
                <div class="security-num">03</div>
                <h3>Admin Oversight</h3>
                <p>All trades auditable. Admin manual matching recorded with full timestamp and actor ID.</p>
                <div class="security-bar"><div class="security-bar-fill" style="width:90%"></div></div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     MOBILE SECTION
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="section mobile-section">
    <div class="container mobile-inner">
        <div class="mobile-text">
            <div class="section-label">Mobile Ready</div>
            <h2 class="section-title">TRADE FROM <span class="gold">ANYWHERE</span></h2>
            <p>Fully responsive interface. Place orders, check your portfolio, and monitor live charts — from any device, any screen size.</p>
            <ul class="mobile-features">
                <li><span class="check">✓</span> Mobile-optimized chart view</li>
                <li><span class="check">✓</span> One-tap buy &amp; sell</li>
                <li><span class="check">✓</span> Real-time push notifications</li>
                <li><span class="check">✓</span> Instant wallet balance</li>
            </ul>
        </div>
        <div class="mobile-device">
            <div class="phone-frame">
                <div class="phone-screen">
                    <div class="phone-header">
                        <span class="phone-pair">BTC/USDT</span>
                        <span class="phone-price gold">60,248</span>
                        <span class="phone-change positive">+2.41%</span>
                    </div>
                    <div class="phone-chart">
                        <svg viewBox="0 0 200 80" preserveAspectRatio="none" style="width:100%;height:100%">
                            <path d="M0,60 C20,50 35,40 55,35 C75,30 85,45 105,30 C125,15 140,10 160,5 C175,2 185,8 200,5 L200,80 L0,80 Z" fill="rgba(240,185,11,0.15)"/>
                            <path d="M0,60 C20,50 35,40 55,35 C75,30 85,45 105,30 C125,15 140,10 160,5 C175,2 185,8 200,5" fill="none" stroke="#F0B90B" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="phone-buttons">
                        <button class="phone-btn-buy">BUY</button>
                        <button class="phone-btn-sell">SELL</button>
                    </div>
                    <div class="phone-balance">
                        <span class="balance-label">Balance</span>
                        <span class="balance-val">$12,450.<small>38</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     CTA
══════════════════════════════════════════════════════════════════════════════ --}}
<section class="section cta-section">
    <div class="cta-glow" aria-hidden="true"></div>
    <div class="container cta-inner">
        <h2 class="cta-title">READY TO <span class="gold">START</span> TRADING?</h2>
        <p class="cta-sub">Join thousands of verified traders on TradeX. Account setup takes under 5 minutes.</p>
        <div class="cta-buttons">
            <a href="/register" class="btn-primary btn-lg btn-glow">
                <span>Create Free Account</span>
                <span class="btn-arrow">→</span>
            </a>
            <a href="/login" class="btn-ghost btn-lg">Sign In</a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════════════════════ --}}
<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <span class="logo-icon">◈</span>
            <span class="logo-text">TRADEX</span>
        </div>
        <div class="footer-links">
            <a href="#markets">Markets</a>
            <a href="/trade">Trade</a>
            <a href="/wallet">Wallet</a>
            <a href="/login">Login</a>
            <a href="/register">Register</a>
        </div>
        <p class="footer-copy">© {{ date('Y') }} TradeX. All rights reserved. Trading involves risk.</p>
    </div>
</footer>
<style>
.stat-value {
    display: inline-flex;
    align-items: baseline;
    gap: 2px;
    white-space: nowrap; /* 🔥 fix xuống dòng */
}

.stat-label {
    display: block;
    color: #94a3b8;
    font-size: 13px;
}
.logo {
    display: flex;
    align-items: center;
}

.logo-img {
    height: 32px; /* desktop */
    width: auto;
    object-fit: contain;
}

/* hover nhẹ */
.logo-img:hover {
    transform: scale(1.05);
    transition: 0.2s ease;
}
@media (max-width: 768px) {
    .logo-img {
        height: 26px;
    }
}
</style>
</body>
</html>
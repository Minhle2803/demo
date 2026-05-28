<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional crypto spot trading. Real-time charts, secure wallet, KYC-verified accounts.">
    <title>HN Stock Exchange — Crypto Trading Platform</title>

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
                src="{{ $projectLogo }}" 
                alt="HN Stock Exchange Logo"
                class="logo-img"
                style="width: 64px;"
            >
        </a>

        <nav class="nav-links" id="nav-links">
            <a href="#markets" class="nav-link">{{ __('messages.nav.markets') }}</a>
            <a href="{{ route('spot.trading') }}" class="nav-link">{{ __('messages.nav.trade') }}</a>
            <a href="{{ route('client.profile.show') }}" class="nav-link">{{ __('messages.nav.wallet') }}</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('signin') }}" class="btn-ghost">{{ __('messages.nav.login') }}</a>
            <a href="{{ route('signup') }}" class="btn-primary">{{ __('messages.nav.register') }}</a>
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
                {{ __('messages.hero.live_trading') }}
            </div>

            <h1 class="hero-headline">
                <span class="line-1">{{ __('messages.hero.headline_line1') }}</span>
                <span class="line-2">{{ __('messages.hero.headline_line2') }}</span>
                <span class="line-3">{{ __('messages.hero.headline_line3') }}</span>
            </h1>

            <p class="hero-sub">
                {{ __('messages.hero.subtitle') }}
            </p>

            <div class="hero-cta">
                <a href="/register" class="btn-primary btn-lg">
                    <span>{{ __('messages.hero.create_account') }}</span>
                    <span class="btn-arrow">→</span>
                </a>
                <a href="#markets" class="btn-outline btn-lg">{{ __('messages.hero.view_markets') }}</a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-num" data-count="12400">0</span>
                        <span class="stat-suffix">+</span>
                    </div>
                    <span class="stat-label">{{ __('messages.hero.active_traders') }}</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-prefix">$</span><span class="stat-num" data-count="48">0</span><span class="stat-suffix">M+</span>
                    </div>
                    <span class="stat-label">{{ __('messages.hero.volume_24h') }}</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <div class="stat-value">
                        <span class="stat-num" data-count="99.9">0</span><span class="stat-suffix">%</span>
                    </div>
                    <span class="stat-label">{{ __('messages.hero.uptime') }}</span>
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
            <div class="section-label">{{ __('messages.markets.live_markets') }}</div>
            <h2 class="section-title">{{ __('messages.markets.market_overview') }}</h2>
        </div>

        <div class="market-table-wrap">
            <table class="market-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.markets.pair') }}</th>
                        <th>{{ __('messages.markets.price') }}</th>
                        <th>{{ __('messages.markets.change_24h') }}</th>
                        <th>{{ __('messages.markets.volume') }}</th>
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
            <div class="section-label">{{ __('messages.features.why_HN Stock Exchange') }}</div>
            <h2 class="section-title">{{ __('messages.features.built_for') }}</h2>
        </div>

        <div class="features-grid">
            <div class="feature-card feature-card--large">
                <div class="feature-icon">⬡</div>
                <h3>{{ __('messages.features.real_time_charts') }}</h3>
                <p>{{ __('messages.features.charts_desc') }}</p>
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
                <h3>{{ __('messages.features.secure_wallet') }}</h3>
                <p>{{ __('messages.features.wallet_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">◈</div>
                <h3>{{ __('messages.features.kyc_verified') }}</h3>
                <p>{{ __('messages.features.kyc_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⟳</div>
                <h3>{{ __('messages.features.fast_matching') }}</h3>
                <p>{{ __('messages.features.matching_desc') }}</p>
            </div>

            <div class="feature-card feature-card--wide">
                <div class="feature-icon">◉</div>
                <h3>{{ __('messages.features.spot_engine') }}</h3>
                <p>{{ __('messages.features.engine_desc') }}</p>
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
            <div class="section-label">{{ __('messages.security.security_first') }}</div>
            <h2 class="section-title">{{ __('messages.security.funds_protected') }}</h2>
        </div>

        <div class="security-grid">
            <div class="security-card">
                <div class="security-num">01</div>
                <h3>{{ __('messages.security.identity_verified') }}</h3>
                <p>{{ __('messages.security.identity_desc') }}</p>
                <div class="security-bar"><div class="security-bar-fill" style="width:100%"></div></div>
            </div>
            <div class="security-card">
                <div class="security-num">02</div>
                <h3>{{ __('messages.security.balance_protection') }}</h3>
                <p>{{ __('messages.security.balance_desc') }}</p>
                <div class="security-bar"><div class="security-bar-fill" style="width:95%"></div></div>
            </div>
            <div class="security-card">
                <div class="security-num">03</div>
                <h3>{{ __('messages.security.admin_oversight') }}</h3>
                <p>{{ __('messages.security.admin_desc') }}</p>
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
            <div class="section-label">{{ __('messages.mobile.mobile_ready') }}</div>
            <h2 class="section-title">{{ __('messages.mobile.trade_anywhere') }}</h2>
            <p>{{ __('messages.mobile.mobile_desc') }}</p>
            <ul class="mobile-features">
                <li><span class="check">✓</span> {{ __('messages.mobile.mobile_chart') }}</li>
                <li><span class="check">✓</span> {{ __('messages.mobile.one_tap') }}</li>
                <li><span class="check">✓</span> {{ __('messages.mobile.push_notifications') }}</li>
                <li><span class="check">✓</span> {{ __('messages.mobile.instant_wallet') }}</li>
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
                        <button class="phone-btn-buy">{{ __('messages.mobile.buy') }}</button>
                        <button class="phone-btn-sell">{{ __('messages.mobile.sell') }}</button>
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
        <h2 class="cta-title">{{ __('messages.cta.ready_to_start') }}</h2>
        <p class="cta-sub">{{ __('messages.cta.cta_subtitle') }}</p>
        <div class="cta-buttons">
            <a href="/register" class="btn-primary btn-lg btn-glow">
                <span>{{ __('messages.cta.create_free_account') }}</span>
                <span class="btn-arrow">→</span>
            </a>
            <a href="/login" class="btn-ghost btn-lg">{{ __('messages.cta.sign_in') }}</a>
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
            <span class="logo-text">HN Stock Exchange</span>
        </div>
        <div class="footer-links">
            <a href="#markets">Markets</a>
            <a href="/trade">Trade</a>
            <a href="/wallet">Wallet</a>
            <a href="/login">Login</a>
            <a href="/register">Register</a>
        </div>
        <p class="footer-copy">{{ __('messages.footer.copyright', ['year' => date('Y')]) }} {{ __('messages.footer.trading_risk') }}</p>
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
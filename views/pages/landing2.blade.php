<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('messages.landing2.hero_subtitle') }}">
    <title>HN Stock Exchange — {{ __('messages.landing2.world_leading') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/landing2.css',
        'resources/js/pages/landing2.js'
    ])
</head>
<body>

{{-- ══════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════ --}}
<header class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="/" class="logo">
            <img
                src="{{ $projectLogo }}"
                alt="HN Stock Exchange Logo"
                style="height:64px;width:auto;object-fit:contain;margin-right:8px;"
            >
            <span class="trust-stat">{{ __('messages.landing2.users_trust') }}</span>
        </a>

        <nav class="nav-links" id="nav-links">
            <a href="#markets">{{ __('messages.nav.markets') }}</a>
            <a href="{{ route('spot.trading') }}">{{ __('messages.nav.trade') }}</a>
            <a href="{{ route('client.profile.show') }}">{{ __('messages.nav.wallet') }}</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('signin') }}" class="btn-ghost">{{ __('messages.nav.login') }}</a>
            <a href="{{ route('signup') }}" class="btn-primary">{{ __('messages.nav.register') }}</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="hero">
    <div class="container">
        <div class="hero-stats">
            <div class="hero-stat-card">
                <div class="stat-label">{{ __('messages.landing2.total_assets') }}</div>
                <div class="stat-value">
                    <span class="stat-num" data-count="120">0</span><span>B+</span>
                </div>
            </div>
            <div class="hero-stat-card">
                <div class="stat-label">{{ __('messages.landing2.daily_volume') }}</div>
                <div class="stat-value">
                    <span class="stat-num" data-count="76">0</span><span>B+</span>
                </div>
            </div>
            <div class="hero-stat-card">
                <div class="stat-label">{{ __('messages.landing2.world_leading') }}</div>
                <div class="stat-value">
                    <span class="stat-num" data-count="250">0</span><span>M+</span>
                </div>
            </div>
        </div>

        <div class="hero-bonus">
            <span>🎁</span>
            <span>{{ __('messages.landing2.bonus_banner') }}</span>
            <a href="{{ route('signup') }}" class="btn-primary">{{ __('messages.landing2.sign_up') }}</a>
        </div>

        <h1 class="hero-headline">{{ __('messages.landing2.hero_title') }}</h1>
        <p class="hero-sub">{{ __('messages.landing2.hero_subtitle') }}</p>

        <div class="hero-cta">
            <a href="{{ route('signup') }}" class="btn-primary btn-lg">{{ __('messages.landing2.register_now') }}</a>
            <a href="#markets" class="btn-outline">{{ __('messages.landing2.view_all_coins') }}</a>
        </div>

        <div class="ticker-grid">
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#F7931A;color:#fff;">B</div>
                    <div>
                        <div class="coin-ticker">BTC</div>
                        <div class="coin-name">Bitcoin</div>
                    </div>
                </div>
                <div class="coin-price">$87,452.10</div>
                <div class="coin-change up">+3.24%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#627EEA;color:#fff;">E</div>
                    <div>
                        <div class="coin-ticker">ETH</div>
                        <div class="coin-name">Ethereum</div>
                    </div>
                </div>
                <div class="coin-price">$3,847.20</div>
                <div class="coin-change up">+2.11%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#F0B90B;color:#000;">B</div>
                    <div>
                        <div class="coin-ticker">BNB</div>
                        <div class="coin-name">BNB</div>
                    </div>
                </div>
                <div class="coin-price">$621.80</div>
                <div class="coin-change down">-0.87%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#23292F;color:#fff;">X</div>
                    <div>
                        <div class="coin-ticker">XRP</div>
                        <div class="coin-name">Ripple</div>
                    </div>
                </div>
                <div class="coin-price">$2.18</div>
                <div class="coin-change up">+5.62%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#9945FF;color:#fff;">S</div>
                    <div>
                        <div class="coin-ticker">SOL</div>
                        <div class="coin-name">Solana</div>
                    </div>
                </div>
                <div class="coin-price">$168.40</div>
                <div class="coin-change up">+1.93%</div>
            </div>
        </div>

        <a href="#" class="view-all-link">{{ __('messages.landing2.view_all_coins') }} →</a>

        <div class="listings-strip">
            <div class="listing-card">
                <div style="font-weight:600;font-size:14px;">ARB/USDT</div>
                <div style="font-size:12px;color:var(--text-secondary);">Arbitrum</div>
                <div style="font-size:12px;color:var(--green);margin-top:4px;">New</div>
            </div>
            <div class="listing-card">
                <div style="font-weight:600;font-size:14px;">APT/USDT</div>
                <div style="font-size:12px;color:var(--text-secondary);">Aptos</div>
                <div style="font-size:12px;color:var(--green);margin-top:4px;">New</div>
            </div>
            <div class="listing-card">
                <div style="font-weight:600;font-size:14px;">SEI/USDT</div>
                <div style="font-size:12px;color:var(--text-secondary);">Sei</div>
                <div style="font-size:12px;color:var(--green);margin-top:4px;">New</div>
            </div>
            <div class="listing-card">
                <div style="font-weight:600;font-size:14px;">TIA/USDT</div>
                <div style="font-size:12px;color:var(--text-secondary);">Celestia</div>
                <div style="font-size:12px;color:var(--green);margin-top:4px;">New</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     AWARDS
══════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>{{ __('messages.landing2.awards_title') }}</h2>
        </div>
        <div class="awards-grid">
            <div class="award-card">
                <div class="award-logo">Forbes</div>
                <div class="award-text">{{ __('messages.landing2.awards_forbes') }}</div>
            </div>
            <div class="award-card">
                <div class="award-logo">Fortune</div>
                <div class="award-text">{{ __('messages.landing2.awards_fortune') }}</div>
            </div>
            <div class="award-card">
                <div class="award-logo">CNBC</div>
                <div class="award-text">{{ __('messages.landing2.awards_cnbc') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     NEWS
══════════════════════════════════════════════════════════ --}}
<section class="section" style="background:var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2>{{ __('messages.landing2.news_title') }}</h2>
            <a href="#" style="color:var(--text-secondary);text-decoration:none;font-size:14px;">{{ __('messages.landing2.view_all_news') }} →</a>
        </div>
        <div class="news-grid">
            <div class="news-card">
                <div class="news-title">HN Stock Exchange Introduces New Trading Pairs for Q2 2026</div>
                <div class="news-date">2026-05-01</div>
            </div>
            <div class="news-card">
                <div class="news-title">Platform Upgrade: Enhanced Order Matching Engine Now Live</div>
                <div class="news-date">2026-04-28</div>
            </div>
            <div class="news-card">
                <div class="news-title">HN Stock Exchange Proof of Reserves Report — April 2026</div>
                <div class="news-date">2026-04-25</div>
            </div>
            <div class="news-card">
                <div class="news-title">Introducing Real-Time Order Book via WebSocket</div>
                <div class="news-date">2026-04-20</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     SAFU
══════════════════════════════════════════════════════════ --}}
<section class="safu-section">
    <div class="container">
        <div class="safu-content">
            <div class="safu-text">
                <h2>{{ __('messages.landing2.safu_title') }}</h2>
                <p>{{ __('messages.landing2.safu_description') }}</p>
                <p>{{ __('messages.landing2.safu_reserve') }}</p>
                <div class="safu-address">1BA42c...xK8f7D</div>
                <div class="safu-stats">
                    <div class="safu-stat">
                        <div class="safu-stat-val">2.4M+</div>
                        <div class="safu-stat-label">{{ __('messages.landing2.safu_users_helped') }}</div>
                    </div>
                    <div class="safu-stat">
                        <div class="safu-stat-val">$1.2B</div>
                        <div class="safu-stat-label">{{ __('messages.landing2.safu_funds_recovered') }}</div>
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <h4 style="font-size:16px;font-weight:600;">{{ __('messages.landing2.humans_of_HN Stock Exchange') }}</h4>
                    <div class="humans-grid">
                        <div class="human-avatar">👤</div>
                        <div class="human-avatar">👤</div>
                        <div class="human-avatar">👤</div>
                        <div class="human-avatar">👤</div>
                        <div class="human-avatar">👤</div>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;">
                <div style="background:var(--bg-card);border-radius:16px;padding:48px;text-align:center;">
                    <div style="font-size:48px;margin-bottom:16px;">🛡️</div>
                    <div style="font-size:20px;font-weight:700;color:var(--accent-gold);">$1,000,000,000+</div>
                    <div style="font-size:14px;color:var(--text-secondary);margin-top:8px;">SAFU Reserve Fund</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     DOWNLOAD
══════════════════════════════════════════════════════════ --}}
<section class="section download-section">
    <div class="container">
        <div class="download-inner">
            <div>
                <h2 style="font-size:32px;font-weight:700;margin-bottom:12px;">{{ __('messages.landing2.download_title') }}</h2>
                <div class="download-tabs">
                    <button class="download-tab active" data-platform="desktop">{{ __('messages.landing2.download_desktop') }}</button>
                    <button class="download-tab" data-platform="lite">{{ __('messages.landing2.download_lite') }}</button>
                    <button class="download-tab" data-platform="pro">{{ __('messages.landing2.download_pro') }}</button>
                </div>
                <div id="download-platform-info" style="color:var(--text-secondary);font-size:14px;margin-bottom:16px;">
                    {{ __('messages.landing2.download_desktop') }} download options
                </div>
                <div class="download-platforms">
                    <a href="#" class="platform-link">Windows</a>
                    <a href="#" class="platform-link">macOS</a>
                    <a href="#" class="platform-link">Linux</a>
                </div>
                <a href="#" style="display:inline-block;margin-top:16px;color:var(--text-secondary);font-size:14px;text-decoration:none;">
                    {{ __('messages.landing2.more_download_options') }} →
                </a>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;">
                <div class="qr-box">QR Code</div>
                <p style="color:var(--text-secondary);font-size:14px;margin-top:16px;">{{ __('messages.landing2.download_subtitle') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FAQ
══════════════════════════════════════════════════════════ --}}
<section class="section" style="background:var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2>{{ __('messages.landing2.faq_title') }}</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q1') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a1') }}</div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q2') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a2') }}</div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q3') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a3') }}</div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q4') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a4') }}</div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q5') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a5') }}</div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    {{ __('messages.landing2.faq_q6') }}
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">{{ __('messages.landing2.faq_a6') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     BOTTOM CTA
══════════════════════════════════════════════════════════ --}}
<section class="cta-section">
    <div class="container">
        <h2>{{ __('messages.landing2.bottom_cta_title') }}</h2>
        <p>{{ __('messages.landing2.hero_subtitle') }}</p>
        <a href="{{ route('signup') }}" class="btn-primary btn-lg">{{ __('messages.landing2.sign_up_now') }}</a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════ --}}
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_about') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_careers') }}</a>
                <a href="#">{{ __('messages.landing2.footer_announcements') }}</a>
                <a href="#">{{ __('messages.landing2.footer_news') }}</a>
                <a href="#">{{ __('messages.landing2.footer_press') }}</a>
                <a href="#">{{ __('messages.landing2.footer_legal') }}</a>
                <a href="#">{{ __('messages.landing2.footer_terms') }}</a>
                <a href="#">{{ __('messages.landing2.footer_privacy') }}</a>
                <a href="#">{{ __('messages.landing2.footer_building_trust') }}</a>
                <a href="#">{{ __('messages.landing2.footer_blog') }}</a>
                <a href="#">{{ __('messages.landing2.footer_community') }}</a>
                <a href="#">{{ __('messages.landing2.footer_risk_warning') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_products') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_exchange') }}</a>
                <a href="#">{{ __('messages.landing2.footer_buy_crypto') }}</a>
                <a href="#">{{ __('messages.landing2.footer_academy') }}</a>
                <a href="#">{{ __('messages.landing2.footer_gift_card') }}</a>
                <a href="#">{{ __('messages.landing2.footer_launchpool') }}</a>
                <a href="#">{{ __('messages.landing2.footer_research') }}</a>
                <a href="#">{{ __('messages.landing2.footer_charity') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_business') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_p2p') }}</a>
                <a href="#">{{ __('messages.landing2.footer_listing_application') }}</a>
                <a href="#">{{ __('messages.landing2.footer_institutional') }}</a>
                <a href="#">{{ __('messages.landing2.footer_labs') }}</a>
                <a href="#">{{ __('messages.landing2.footer_learn_earn') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_learn') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_browse_prices') }}</a>
                <a href="#">{{ __('messages.landing2.footer_bitcoin_price') }}</a>
                <a href="#">{{ __('messages.landing2.footer_ethereum_price') }}</a>
                <a href="#">{{ __('messages.landing2.footer_buy_bitcoin') }}</a>
                <a href="#">{{ __('messages.landing2.footer_buy_bnb') }}</a>
                <a href="#">{{ __('messages.landing2.footer_buy_xrp') }}</a>
                <a href="#">{{ __('messages.landing2.footer_buy_ethereum') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_service') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_affiliate') }}</a>
                <a href="#">{{ __('messages.landing2.footer_referral') }}</a>
                <a href="#">{{ __('messages.landing2.footer_historical_data') }}</a>
                <a href="#">{{ __('messages.landing2.footer_proof_of_reserves') }}</a>
                <a href="#">{{ __('messages.landing2.footer_downloads') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('messages.landing2.footer_support') }}</h4>
                <a href="#">{{ __('messages.landing2.footer_24_7_support') }}</a>
                <a href="#">{{ __('messages.landing2.footer_support_center') }}</a>
                <a href="#">{{ __('messages.landing2.footer_fees') }}</a>
                <a href="#">{{ __('messages.landing2.footer_apis') }}</a>
                <a href="#">{{ __('messages.landing2.footer_cookie_preferences') }}</a>
            </div>
        </div>

        <div class="footer-social">
            <a href="#" aria-label="Twitter">T</a>
            <a href="#" aria-label="Telegram">Tg</a>
            <a href="#" aria-label="Facebook">F</a>
            <a href="#" aria-label="YouTube">Y</a>
            <a href="#" aria-label="Discord">D</a>
        </div>

        <div class="footer-legal">
            <a href="#">{{ __('messages.landing2.footer_terms') }}</a>
            <a href="#">{{ __('messages.landing2.footer_privacy') }}</a>
            <a href="#">{{ __('messages.landing2.footer_risk_warning') }}</a>
        </div>

        <div class="footer-copy">
            <p style="margin-bottom:8px;">{{ __('messages.landing2.footer_risk_disclosure') }}</p>
            <p>{{ __('messages.landing2.copyright', ['year' => date('Y')]) }}</p>
        </div>
    </div>
</footer>

</body>
</html>

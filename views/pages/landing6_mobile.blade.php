<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="TradeX — Nền tảng giao dịch Crypto chuyên nghiệp">
    <title>TradeX — Thị Trường Crypto</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/landing6_mobile.css',
        'resources/js/pages/landing6_mobile.js'
    ])
</head>
<body>

{{-- ═══════════ HEADER ═══════════ --}}
<header id="mobile-header">
    <div class="header-top">
        <a href="/" class="logo-link">
            <img src="{{ $projectLogo }}" alt="TradeX" class="header-logo">
        </a>
        <div class="header-actions">
            @auth('client')
                <a href="{{ route('tradding') }}" class="btn-trade-mobile">Giao Dịch</a>
            @else
                <a href="{{ route('signin') }}" class="btn-login-mobile">Đăng Nhập</a>
                <a href="{{ route('signup') }}" class="btn-signup-mobile">Đăng Ký</a>
            @endauth
        </div>
    </div>

    {{-- Nav bar — horizontally scrollable --}}
    <nav class="mobile-nav">
        <a href="#section-indices" class="nav-item active">Thị Trường</a>
        <a href="#section-spot" class="nav-item">Spot</a>
        <a href="#section-futures" class="nav-item">Futures</a>
        <a href="#section-gainers" class="nav-item">Biến Động</a>
        <a href="#section-news" class="nav-item">Tin Tức</a>
        <a href="#section-about" class="nav-item">Giới Thiệu</a>
    </nav>
</header>

{{-- ═══════════ MAIN CONTENT ═══════════ --}}
<main id="mobile-main">

    {{-- Section: Indices --}}
    <section id="section-indices" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-indices">
            <span>Chỉ Số Thị Trường</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-indices">
            <div class="index-summary">
                <div class="index-row">
                    <span class="index-label">BTC Dominance</span>
                    <span class="index-value">52.4%</span>
                    <span class="triangle-up-sm"></span>
                    <span class="change-positive">+0.32 (0.62%)</span>
                </div>
                <div class="index-row">
                    <span class="index-label">Total Market Cap</span>
                    <span class="index-value">$3.82T</span>
                    <span class="triangle-up-sm"></span>
                    <span class="change-positive">+1.2%</span>
                </div>
                <div class="index-row">
                    <span class="index-label">ETH Dominance</span>
                    <span class="index-value">18.1%</span>
                    <span class="triangle-down-sm"></span>
                    <span class="change-negative">-0.18 (0.98%)</span>
                </div>
                <div class="index-row">
                    <span class="index-label">Fear & Greed</span>
                    <span class="index-value">72</span>
                    <span class="triangle-up-sm"></span>
                    <span class="change-positive">+5 (7.46%)</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Spot Market --}}
    <section id="section-spot" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-spot">
            <span>Thị Trường Spot</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-spot">
            <div class="table-scroll">
            <table class="mobile-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Giá (USD)</th>
                        <th>KL Giao Dịch</th>
                        <th>Thay Đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="coin-code">BTC</td>
                        <td>$102,450</td>
                        <td>24.8B</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+2.35%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">ETH</td>
                        <td>$5,280</td>
                        <td>12.6B</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+1.80%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">SOL</td>
                        <td>$245.80</td>
                        <td>8.4B</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+12.40%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">BNB</td>
                        <td>$682.30</td>
                        <td>4.2B</td>
                        <td><span class="triangle-down-sm"></span> <span class="change-negative">-0.85%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">XRP</td>
                        <td>$0.892</td>
                        <td>3.1B</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+3.20%</span></td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="section-footer-link">
                <a href="{{ route('tradding') }}">Bảng giá trực tuyến →</a>
            </div>
        </div>
    </section>

    {{-- Section: Futures --}}
    <section id="section-futures" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-futures">
            <span>Thị Trường Phái Sinh</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-futures">
            <div class="table-scroll">
            <table class="mobile-table">
                <thead>
                    <tr>
                        <th>Hợp Đồng</th>
                        <th>KL (HĐ)</th>
                        <th>GT (Triệu USD)</th>
                        <th>OI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="coin-code">BTC/USDT Perp</td>
                        <td>189,500</td>
                        <td>38,643</td>
                        <td>36,512</td>
                    </tr>
                    <tr>
                        <td class="coin-code">ETH/USDT Perp</td>
                        <td>95,200</td>
                        <td>18,270</td>
                        <td>22,840</td>
                    </tr>
                    <tr>
                        <td class="coin-code">SOL/USDT Perp</td>
                        <td>42,100</td>
                        <td>5,620</td>
                        <td>8,150</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="section-footer-link">
                <a href="{{ route('tradding') }}">Giao dịch Futures →</a>
            </div>
        </div>
    </section>

    {{-- Section: Top Gainers / Losers --}}
    <section id="section-gainers" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-gainers">
            <span>Top Biến Động 24h</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-gainers">
            <div class="table-scroll">
            <table class="mobile-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Giá (USD)</th>
                        <th>Thay Đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="coin-code">SOL</td>
                        <td>$245.80</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+12.40%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">AVAX</td>
                        <td>$58.32</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+8.75%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">LINK</td>
                        <td>$32.15</td>
                        <td><span class="triangle-up-sm"></span> <span class="change-positive">+6.20%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">DOGE</td>
                        <td>$0.185</td>
                        <td><span class="triangle-down-sm"></span> <span class="change-negative">-4.30%</span></td>
                    </tr>
                    <tr>
                        <td class="coin-code">ADA</td>
                        <td>$0.675</td>
                        <td><span class="triangle-down-sm"></span> <span class="change-negative">-3.15%</span></td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="section-footer-link">
                <a href="{{ route('tradding') }}">Xem thêm →</a>
            </div>
        </div>
    </section>

    {{-- Section: News --}}
    <section id="section-news" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-news">
            <span>Tin Tức — Sự Kiện</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-news">
            <div class="news-list">
                <a href="#" class="news-card">
                    <img src="/assets/images/landing6/main-BG-01.jpg" alt="News" class="news-thumb">
                    <div class="news-info">
                        <div class="news-card-title">Bitcoin vượt mốc 100,000 USD — Kỷ lục mới của thị trường crypto</div>
                        <div class="news-card-time">09:30, 18/05/2026</div>
                    </div>
                </a>
                <a href="#" class="news-card">
                    <img src="/assets/images/landing6/main-BG-03.jpg" alt="News" class="news-thumb">
                    <div class="news-info">
                        <div class="news-card-title">Ethereum Merge 2.0 — Hiệu suất mạng tăng 300%</div>
                        <div class="news-card-time">08:15, 18/05/2026</div>
                    </div>
                </a>
                <a href="#" class="news-card">
                    <img src="/assets/images/landing6/main-BG-01.jpg" alt="News" class="news-thumb">
                    <div class="news-info">
                        <div class="news-card-title">Solana dẫn đầu xu hướng DeFi với TVL tăng 45%</div>
                        <div class="news-card-time">07:45, 18/05/2026</div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Section: About --}}
    <section id="section-about" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-about">
            <span>Giới Thiệu TradeX</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-about">
            <div class="about-content">
                <p class="about-text">TradeX là nền tảng giao dịch tiền mã hóa hàng đầu, cung cấp dịch vụ giao dịch Spot, Futures và các sản phẩm tài chính số. Với công nghệ tiên tiến và đội ngũ chuyên gia giàu kinh nghiệm, TradeX cam kết mang đến trải nghiệm giao dịch an toàn, minh bạch và hiệu quả.</p>

                <div class="about-stats">
                    <div class="stat-item">
                        <span class="stat-number">5M+</span>
                        <span class="stat-label">Người Dùng</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">$50B+</span>
                        <span class="stat-label">KL Giao Dịch/Ngày</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">150+</span>
                        <span class="stat-label">Quốc Gia</span>
                    </div>
                </div>

                <div class="about-timeline">
                    <div class="timeline-title">Lịch Sử Phát Triển</div>
                    <div class="timeline-scroll">
                        <div class="timeline-item">
                            <span class="timeline-year">2022</span>
                            <span class="timeline-desc">Thành lập TradeX, ra mắt nền tảng giao dịch Spot</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-year">2023</span>
                            <span class="timeline-desc">Ra mắt Futures, đạt 1M người dùng</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-year">2024</span>
                            <span class="timeline-desc">Mở rộng toàn cầu, tích hợp DeFi & Staking</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-year">2025</span>
                            <span class="timeline-desc">Top 5 sàn giao dịch crypto toàn cầu</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-year">2026</span>
                            <span class="timeline-desc">AI Trading, Social Trading, 5M+ người dùng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Contact --}}
    <section id="section-contact" class="content-section">
        <div class="section-header accordion-toggle" data-target="accordion-contact">
            <span>Liên Hệ</span>
            <span class="accordion-arrow">▼</span>
        </div>
        <div class="accordion-body" id="accordion-contact">
            <div class="contact-info">
                <div class="contact-row"><strong>Địa chỉ:</strong> Tầng 20, Tòa nhà Bitexco, Q.1, TP. Hồ Chí Minh</div>
                <div class="contact-row"><strong>Điện thoại:</strong> (84-28) 3823 4567</div>
                <div class="contact-row"><strong>Email:</strong> support@tradex.io</div>
            </div>
            <form class="contact-form">
                <input type="email" class="contact-input" placeholder="Email của bạn">
                <input type="text" class="contact-input" placeholder="Tiêu đề">
                <textarea class="contact-input contact-textarea" placeholder="Nội dung"></textarea>
                <button type="submit" class="contact-submit">Gửi</button>
            </form>
        </div>
    </section>

</main>

{{-- ═══════════ FOOTER ═══════════ --}}
<footer id="mobile-footer">
    <div class="footer-menu-toggle" id="footerMenuToggle">
        <span>Menu</span>
        <span class="footer-menu-icon">☰</span>
    </div>
    <div class="footer-menu" id="footerMenu">
        <a href="/" class="footer-menu-item">Trang Chủ</a>
        <a href="{{ route('tradding') }}" class="footer-menu-item">Giao Dịch Spot</a>
        <a href="{{ route('tradding') }}" class="footer-menu-item">Giao Dịch Futures</a>
        <a href="#" class="footer-menu-item">Tin Tức</a>
        <a href="#" class="footer-menu-item">Liên Hệ</a>
        <a href="#" class="footer-menu-item sub-item">Phiên Bản Desktop</a>
    </div>
    <div class="footer-bottom">
        <img src="{{ $projectLogo }}" alt="TradeX" class="footer-logo">
        <div class="footer-copyright">© 2026 Bản quyền thuộc TradeX. Giao dịch tiền mã hóa tiềm ẩn rủi ro cao.</div>
    </div>
</footer>

</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Giao dịch crypto an toàn, biểu đồ thời gian thực, thanh khoản sâu, bảo mật cấp ngân hàng.">
    <title>TradeX — Sàn Giao Dịch Tiền Mã Hóa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/landing5.css',
        'resources/js/pages/landing5.js'
    ])
</head>
<body>

{{-- ══════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════ --}}
<header class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="/" class="logo">
            <img src="{{ $projectLogo }}" alt="TradeX Logo">
            <span>TradeX</span>
        </a>

        <nav class="nav-links" id="nav-links">
            <a href="#features">Tính Năng</a>
            <a href="{{ route('tradding') }}">Giao Dịch</a>
            <a href="#markets">Thị Trường</a>
            <a href="#security">Bảo Mật</a>
        </nav>

        <div class="nav-actions">
            <a href="{{ route('signin') }}" class="btn-outline btn-sm">Đăng Nhập</a>
            <a href="{{ route('signup') }}" class="btn-primary btn-sm">Đăng Ký</a>
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
        <div class="hero-badge">
            <span class="dot"></span> Được 250M+ người dùng toàn cầu tin tưởng
        </div>

        <h1 class="hero-headline">
            Mua, Giao Dịch & Nắm Giữ <span class="highlight">Crypto</span> An Toàn
        </h1>
        <p class="hero-sub">
            Sàn giao dịch tiền mã hóa hàng đầu thế giới. Giao dịch Bitcoin, Ethereum và hơn 350+ altcoin với phí siêu thấp và thanh khoản sâu.
        </p>

        <div class="hero-cta">
            <a href="{{ route('signup') }}" class="btn-primary btn-lg">Bắt Đầu Miễn Phí</a>
            <a href="#markets" class="btn-outline btn-lg">Khám Phá Thị Trường</a>
        </div>

        <div class="stats-strip">
            <div class="stat-card">
                <div class="stat-value">$<span class="stat-num" data-count="120">0</span>B+</div>
                <div class="stat-label">Khối Lượng Giao Dịch 24h</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><span class="stat-num" data-count="350">0</span>+</div>
                <div class="stat-label">Cặp Giao Dịch</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><span class="stat-num" data-count="0">0</span>.1%</div>
                <div class="stat-label">Phí Giao Dịch Thấp Nhất</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><span class="stat-num" data-count="250">0</span>M+</div>
                <div class="stat-label">Người Dùng Đã Đăng Ký</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     LIVE TICKERS
══════════════════════════════════════════════════════════ --}}
<section class="ticker-section">
    <div class="container">
        <div class="section-label">Giá Thị Trường Trực Tuyến</div>
        <div class="ticker-grid">
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#F7931A;color:#fff;">B</div>
                    <div>
                        <div class="coin-ticker">BTC/USDT</div>
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
                        <div class="coin-ticker">ETH/USDT</div>
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
                        <div class="coin-ticker">BNB/USDT</div>
                        <div class="coin-name">BNB</div>
                    </div>
                </div>
                <div class="coin-price">$621.80</div>
                <div class="coin-change down">-0.87%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#9945FF;color:#fff;">S</div>
                    <div>
                        <div class="coin-ticker">SOL/USDT</div>
                        <div class="coin-name">Solana</div>
                    </div>
                </div>
                <div class="coin-price">$168.40</div>
                <div class="coin-change up">+1.93%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#23292F;color:#fff;">X</div>
                    <div>
                        <div class="coin-ticker">XRP/USDT</div>
                        <div class="coin-name">Ripple</div>
                    </div>
                </div>
                <div class="coin-price">$2.18</div>
                <div class="coin-change up">+5.62%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#E84142;color:#fff;">A</div>
                    <div>
                        <div class="coin-ticker">AVAX/USDT</div>
                        <div class="coin-name">Avalanche</div>
                    </div>
                </div>
                <div class="coin-price">$38.75</div>
                <div class="coin-change down">-1.42%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#26A17B;color:#fff;">U</div>
                    <div>
                        <div class="coin-ticker">USDC/USDT</div>
                        <div class="coin-name">USD Coin</div>
                    </div>
                </div>
                <div class="coin-price">$1.00</div>
                <div class="coin-change up">+0.01%</div>
            </div>
            <div class="coin-card">
                <div class="coin-header">
                    <div class="coin-icon" style="background:#8247E5;color:#fff;">D</div>
                    <div>
                        <div class="coin-ticker">DOGE/USDT</div>
                        <div class="coin-name">Dogecoin</div>
                    </div>
                </div>
                <div class="coin-price">$0.182</div>
                <div class="coin-change up">+8.15%</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FEATURES
══════════════════════════════════════════════════════════ --}}
<section class="section" id="features" style="background:var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2>Tại Sao Chọn TradeX</h2>
            <p>Xây dựng cho mọi cấp độ — từ người mới đến nhà đầu tư tổ chức</p>
            <div class="accent-line"></div>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Khớp Lệnh Tức Thì</h3>
                <p>Hệ thống khớp lệnh có khả năng xử lý 1.4 triệu lệnh mỗi giây. Giao dịch của bạn được thực hiện ngay lập tức với độ trượt giá tối thiểu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Phí Cực Thấp</h3>
                <p>Chỉ từ 0.1% mỗi giao dịch. Nắm giữ token nền tảng để được giảm thêm 25% trên tất cả phí giao dịch.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Bảo Mật Cấp Ngân Hàng</h3>
                <p>Lưu trữ ví lạnh đa lớp, xác thực 2FA, mã chống phishing và giám sát thời gian thực bảo vệ tài sản của bạn 24/7.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     TRADING TOOLS
══════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Công Cụ Giao Dịch Chuyên Nghiệp</h2>
            <p>Mọi thứ bạn cần để phân tích, giao dịch và phát triển danh mục đầu tư</p>
            <div class="accent-line"></div>
        </div>
        <div class="tools-grid">
            <div class="tool-card">
                <div class="tool-icon">📊</div>
                <div>
                    <h3>Biểu Đồ Nâng Cao</h3>
                    <p>Biểu đồ nến thời gian thực với TradingView. Hơn 100 chỉ báo, phân tích đa khung thời gian và công cụ vẽ tích hợp sẵn.</p>
                </div>
            </div>
            <div class="tool-card">
                <div class="tool-icon">🤖</div>
                <div>
                    <h3>Giao Dịch Spot & Futures</h3>
                    <p>Giao dịch spot với thanh toán tức thì hoặc futures với đòn bẩy lên đến 125x. Các loại lệnh nâng cao: OCO, trailing stop, TWAP.</p>
                </div>
            </div>
            <div class="tool-card">
                <div class="tool-icon">🔗</div>
                <div>
                    <h3>WebSocket API</h3>
                    <p>Truyền dữ liệu order book, giao dịch và số dư theo thời gian thực. Xây dựng bot giao dịch riêng với REST và WebSocket API.</p>
                </div>
            </div>
            <div class="tool-card">
                <div class="tool-icon">📱</div>
                <div>
                    <h3>Giao Dịch Di Động</h3>
                    <p>Giao dịch mọi lúc mọi nơi với ứng dụng di động. Đầy đủ chức năng — nạp, giao dịch, rút và theo dõi thị trường từ điện thoại.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     LIVE MARKETS TABLE
══════════════════════════════════════════════════════════ --}}
<section class="section" id="markets" style="background:var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2>Thị Trường Phổ Biến</h2>
            <p>Các cặp giao dịch hàng đầu theo khối lượng 24h</p>
            <div class="accent-line"></div>
        </div>
        <div class="markets-table-wrap">
            <table class="markets-table">
                <thead>
                    <tr>
                        <th>Cặp</th>
                        <th>Giá</th>
                        <th>Thay Đổi 24h</th>
                        <th>Khối Lượng 24h</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="pair-name">BTC/USDT</div>
                            <div class="pair-full">Bitcoin</div>
                        </td>
                        <td>$87,452.10</td>
                        <td style="color:var(--green);">+3.24%</td>
                        <td>$28.4B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pair-name">ETH/USDT</div>
                            <div class="pair-full">Ethereum</div>
                        </td>
                        <td>$3,847.20</td>
                        <td style="color:var(--green);">+2.11%</td>
                        <td>$14.2B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pair-name">SOL/USDT</div>
                            <div class="pair-full">Solana</div>
                        </td>
                        <td>$168.40</td>
                        <td style="color:var(--green);">+1.93%</td>
                        <td>$5.8B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pair-name">BNB/USDT</div>
                            <div class="pair-full">BNB</div>
                        </td>
                        <td>$621.80</td>
                        <td style="color:var(--red);">-0.87%</td>
                        <td>$3.9B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pair-name">XRP/USDT</div>
                            <div class="pair-full">Ripple</div>
                        </td>
                        <td>$2.18</td>
                        <td style="color:var(--green);">+5.62%</td>
                        <td>$3.1B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pair-name">DOGE/USDT</div>
                            <div class="pair-full">Dogecoin</div>
                        </td>
                        <td>$0.182</td>
                        <td style="color:var(--green);">+8.15%</td>
                        <td>$2.4B</td>
                        <td class="market-actions"><a href="{{ route('spot.trading') }}">Giao Dịch</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     HOW TO GET STARTED
══════════════════════════════════════════════════════════ --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Bắt Đầu Giao Dịch Trong Vài Phút</h2>
            <p>Các bước đơn giản để bắt đầu hành trình crypto của bạn</p>
            <div class="accent-line"></div>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Tạo Tài Khoản</h3>
                <p>Đăng ký bằng email hoặc số điện thoại. Hoàn thành xác minh danh tính KYC trong vòng chưa đầy 5 phút.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Nạp Tiền</h3>
                <p>Nạp tiền qua chuyển khoản ngân hàng, thẻ tín dụng hoặc gửi crypto từ ví bên ngoài.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Bắt Đầu Giao Dịch</h3>
                <p>Mua và bán crypto trên thị trường spot hoặc giao dịch đòn bẩy với nền tảng futures.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Rút Tiền Mọi Lúc</h3>
                <p>Rút tiền về tài khoản ngân hàng hoặc ví bên ngoài — nhanh chóng, đơn giản, an toàn.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     SECURITY
══════════════════════════════════════════════════════════ --}}
<section class="section security-section" id="security">
    <div class="container">
        <div class="security-banner">
            <div class="security-text">
                <h2>Tài Sản Của Bạn, <span class="gold">Luôn Được Bảo Vệ</span></h2>
                <p>
                    Bảo mật là ưu tiên hàng đầu. Chúng tôi sử dụng kiến trúc đa tầng để đảm bảo tài sản số của bạn an toàn trước các mối đe dọa từ bên ngoài và rủi ro nội bộ.
                </p>
                <ul class="security-checklist">
                    <li>Lưu trữ ví lạnh đa chữ ký</li>
                    <li>Phát hiện & giám sát bất thường thời gian thực</li>
                    <li>Proof of Reserves — xác minh trên blockchain</li>
                    <li>Bảo vệ DDoS & kiểm tra bảo mật định kỳ</li>
                    <li>Bắt buộc 2FA cho mọi lệnh rút tiền</li>
                    <li>Quỹ bảo hiểm khẩn cấp SAFU</li>
                </ul>
            </div>
            <div class="security-visual">
                <div class="shield">🛡️</div>
                <div class="fund-value">$1,000,000,000+</div>
                <div class="fund-label">Quỹ Dự Phòng SAFU</div>
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
            <h2>Câu Hỏi Thường Gặp</h2>
            <p>Giải đáp những thắc mắc phổ biến về giao dịch trên TradeX</p>
            <div class="accent-line"></div>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question">
                    Làm thế nào để bắt đầu giao dịch tiền mã hóa?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Tạo tài khoản, hoàn thành xác minh danh tính, nạp tiền qua chuyển khoản ngân hàng hoặc crypto, sau đó vào trang giao dịch để đặt lệnh đầu tiên. Nền tảng hỗ trợ lệnh market, limit và stop-limit.
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Phí giao dịch trên TradeX là bao nhiêu?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Phí giao dịch tiêu chuẩn chỉ 0.1% cho cả maker và taker. Bạn có thể giảm thêm bằng cách nắm giữ token nền tảng để được giảm 25% trên tất cả phí.
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Tiền mã hóa của tôi có an toàn trên TradeX không?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Tuyệt đối an toàn. Phần lớn tiền của người dùng được lưu trữ trong ví lạnh với bảo vệ đa chữ ký. Chúng tôi cũng duy trì quỹ dự phòng SAFU trị giá hơn $1B để bảo vệ người dùng trong trường hợp xảy ra sự cố bảo mật.
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Tôi có thể giao dịch những loại tiền mã hóa nào?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Chúng tôi hỗ trợ hơn 350 cặp giao dịch bao gồm Bitcoin (BTC), Ethereum (ETH), Solana (SOL), BNB, XRP, Dogecoin (DOGE), Avalanche (AVAX) và nhiều loại khác. Token mới được thêm thường xuyên.
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    TradeX có hỗ trợ giao dịch đòn bẩy không?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Có. Nền tảng futures hỗ trợ đòn bẩy lên đến 125x trên một số cặp giao dịch. Lưu ý giao dịch đòn bẩy có rủi ro cao — hãy giao dịch có trách nhiệm và quản lý rủi ro phù hợp.
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    Rút tiền mất bao lâu?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        Rút crypto được xử lý tự động và thường hoàn tất trong vòng 5-30 phút tùy theo mức độ tắc nghẽn mạng. Rút tiền pháp định qua chuyển khoản ngân hàng thường mất 1-3 ngày làm việc.
                    </div>
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
        <h2>Sẵn Sàng Giao Dịch?</h2>
        <p>Tham gia cùng 250 triệu người dùng trên sàn giao dịch tiền mã hóa hàng đầu thế giới. Đăng ký ngay hôm nay và nhận thưởng chào mừng.</p>
        <a href="{{ route('signup') }}" class="btn-primary btn-lg">Tạo Tài Khoản Miễn Phí</a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════ --}}
<footer class="footer">
    <div class="container">
        <div class="footer-bottom" style="border-top:none;padding-top:0;justify-content:center;text-align:center;">
            <p>TradeX là nền tảng giao dịch tiền mã hóa. Giao dịch tài sản số có rủi ro đáng kể và có thể không phù hợp với tất cả nhà đầu tư. Giá trị tiền mã hóa có thể biến động rất mạnh. Hãy giao dịch có trách nhiệm.</p>
            <p style="margin-top:4px;">&copy; {{ date('Y') }} TradeX. Đã đăng ký bản quyền.</p>
        </div>
    </div>
</footer>

</body>
</html>

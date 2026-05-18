<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TradeX — Nền tảng giao dịch Crypto chuyên nghiệp. Cập nhật thị trường, biểu đồ thời gian thực.">
    <title>TradeX — Thị Trường Crypto</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite([
        'resources/css/landing6.css',
        'resources/js/pages/landing6.js'
    ])
</head>
<body>

{{-- ═══════════════════════ HEADER (White, HNX-style) ═══════ --}}
<div id="header">
    <div class="header-inner">
        <a href="/" class="hnx-logo">
            <img src="{{ $projectLogo }}" alt="TradeX Logo">
        </a>
        <div class="header-right">
            <div class="nav-menu">
                <ul>
                    <li><a href="{{ route('tradding') }}">TRADING</a></li>
                </ul>
            </div>

            <div class="auth-buttons">
                @auth('client')
                    <a href="{{ route('tradding') }}" class="btn-trading">Vào Giao Dịch</a>
                @else
                    <a href="{{ route('signin') }}" class="btn-login">Đăng Nhập</a>
                    <a href="{{ route('signup') }}" class="btn-signup">Đăng Ký</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Index Bar (light gray) --}}
    <div class="index-bar">
        <div class="index-bar-inner">
            <div class="market-indices" id="marketIndices">
                <div class="index-item" v-for="(item, idx) in indices" :key="idx">
                    <div class="index-name">@{{ item.name }}</div>
                    <div class="index-value">@{{ item.value }}</div>
                    <div :class="item.change >= 0 ? 'triangle-up' : 'triangle-down'"></div>
                    <div class="index-change">@{{ item.changeFormatted }}</div>
                    <div class="index-pct">(@{{ item.pct }}%)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ticker Bar (dark green) --}}
    <div class="ticker-bar">
        <div class="ticker-content" id="tickerContent">
            <div class="ticker-item" v-for="(coin, idx) in tickerCoins" :key="idx">
                <span>@{{ coin.symbol }}</span>
                <span>@{{ coin.price }}</span>
                <div :class="coin.change >= 0 ? 'triangle-up' : 'triangle-down'"></div>
                <span>@{{ coin.changeFormatted }}</span>
                <span>(@{{ coin.pct }}%)</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ MAIN PANEL ══════════════════════ --}}
<div id="pannel">
    <div class="content-main" id="contentMain">
        <div id="contentPanel">
            <div class="slideshow-container" id="slideshow">

                {{-- Slide 1: Market Overview --}}
                <div class="mySlides">
                    <div class="slide-inner">
                        <div class="slide-news">
                            <div>
                                <div class="news-title">Bitcoin vượt mốc 100,000 USD — Kỷ lục mới của thị trường crypto</div>
                                <div class="news-time">09:30, 18/05/2026</div>
                                <div class="news-desc">Bitcoin chính thức vượt qua cột mốc lịch sử $100,000 sau nhiều tháng tích lũy. Sự kiện này đánh dấu một chương mới cho thị trường tiền mã hóa toàn cầu.</div>
                                <div class="news-more">Xem Thêm</div>
                            </div>
                        </div>
                        <div class="slide-table">
                            <table class="market-table">
                                <tr class="table-header">
                                    <th style="width:200px">Thị trường</th>
                                    <th style="width:200px">Khối lượng giao dịch<br>(tỷ USD)</th>
                                    <th style="width:100px">Thay đổi</th>
                                </tr>
                                <tr>
                                    <td>Spot BTC/USDT</td>
                                    <td class="text-right pd-right-20">24.8</td>
                                    <td class="tr-center">
                                        <div class="triangle-up-lar"></div>
                                        <span>+3.25%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Spot ETH/USDT</td>
                                    <td class="text-right pd-right-20">12.6</td>
                                    <td class="tr-center">
                                        <div class="triangle-up-lar"></div>
                                        <span>+2.10%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Futures BTC</td>
                                    <td class="text-right pd-right-20">38.2</td>
                                    <td class="tr-center">
                                        <div class="triangle-down-lar"></div>
                                        <span>-1.45%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Futures ETH</td>
                                    <td class="text-right pd-right-20">18.9</td>
                                    <td class="tr-center">
                                        <div class="triangle-up-lar"></div>
                                        <span>+0.87%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ETF Bitcoin</td>
                                    <td class="text-right pd-right-20">5.4</td>
                                    <td class="tr-center">
                                        <div class="triangle-up-lar"></div>
                                        <span>+5.62%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="table-footer">Cập nhật ngày 18/05/2026</td>
                                </tr>
                            </table>
                            <div class="slide-links">
                                <a href="{{ route('tradding') }}" class="form-text">Bảng giá trực tuyến</a>
                                <span class="form-text">Danh sách tin mới nhất</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2: Market Cap Distribution --}}
                <div class="mySlides">
                    <div class="slide-inner">
                        <div class="slide-news">
                            <div>
                                <div class="news-title">Ethereum Merge 2.0 — Hiệu suất mạng tăng 300%</div>
                                <div class="news-time">08:15, 18/05/2026</div>
                                <div class="news-desc">Bản nâng cấp Ethereum Merge 2.0 đã chính thức được triển khai, đưa tốc độ giao dịch lên đến 100,000 TPS.</div>
                                <div class="news-more">Xem Thêm</div>
                            </div>
                        </div>
                        <div class="slide-table">
                            <div class="chart-section">
                                <div class="chart-header">
                                    <span>Vốn hóa thị trường</span>
                                    <span class="chart-unit">(Tỷ trọng)</span>
                                </div>
                                <div class="bar-chart-container">
                                    <div class="bar-chart">
                                        <div class="bar-label">BTC</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:52%"></div></div>
                                        <div class="bar-value">52%</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="bar-label">ETH</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:18%"></div></div>
                                        <div class="bar-value">18%</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="bar-label">USDT</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:7%"></div></div>
                                        <div class="bar-value">7%</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="bar-label">BNB</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:4.5%"></div></div>
                                        <div class="bar-value">4.5%</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="bar-label">SOL</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:3.8%"></div></div>
                                        <div class="bar-value">3.8%</div>
                                    </div>
                                    <div class="bar-chart">
                                        <div class="bar-label">Khác</div>
                                        <div class="bar-track"><div class="bar-fill" style="width:14.7%"></div></div>
                                        <div class="bar-value">14.7%</div>
                                    </div>
                                </div>
                                <div class="table-footer">Cập nhật ngày 18/05/2026</div>
                            </div>
                            <div class="slide-links">
                                <a href="{{ route('tradding') }}" class="form-text">Bảng giá trực tuyến</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3: Top Gainers / Losers --}}
                <div class="mySlides">
                    <div class="slide-inner">
                        <div class="slide-news">
                            <div>
                                <div class="news-title">Solana dẫn đầu xu hướng DeFi với TVL tăng 45%</div>
                                <div class="news-time">07:45, 18/05/2026</div>
                                <div class="news-desc">Hệ sinh thái DeFi trên Solana đang chứng kiến sự bùng nổ. TVL đã tăng 45% chỉ trong 30 ngày qua, thu hút lượng lớn nhà đầu tư tổ chức.</div>
                                <div class="news-more">Xem Thêm</div>
                            </div>
                        </div>
                        <div class="slide-table">
                            <table class="market-table">
                                <tr class="table-header">
                                    <th style="width:200px">Top biến động 24h</th>
                                    <th style="width:200px">Giá (USD)</th>
                                    <th style="width:100px">Thay đổi</th>
                                </tr>
                                <tr>
                                    <td>SOL</td>
                                    <td class="text-right pd-right-20">245.80</td>
                                    <td class="tr-center">
                                        <div class="triangle-up-lar"></div>
                                        <span>+12.40%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>AVAX</td>
                                    <td class="text-right pd-right-20">58.32</td>
                                    <td class="tr-center"><div class="triangle-up-lar"></div><span>+8.75%</span></td>
                                </tr>
                                <tr>
                                    <td>LINK</td>
                                    <td class="text-right pd-right-20">32.15</td>
                                    <td class="tr-center"><div class="triangle-up-lar"></div><span>+6.20%</span></td>
                                </tr>
                                <tr>
                                    <td>DOGE</td>
                                    <td class="text-right pd-right-20">0.185</td>
                                    <td class="tr-center"><div class="triangle-down-lar"></div><span>-4.30%</span></td>
                                </tr>
                                <tr>
                                    <td>ADA</td>
                                    <td class="text-right pd-right-20">0.675</td>
                                    <td class="tr-center"><div class="triangle-down-lar"></div><span>-3.15%</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="table-footer">Cập nhật ngày 18/05/2026</td>
                                </tr>
                            </table>
                            <div class="slide-links">
                                <a href="{{ route('tradding') }}" class="form-text">Bảng giá trực tuyến</a>
                                <span class="form-text">Xem thêm biến động</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 4: Derivatives --}}
                <div class="mySlides">
                    <div class="slide-inner">
                        <div class="slide-news">
                            <div>
                                <div class="news-title">Open Interest Bitcoin Futures đạt ATH mới</div>
                                <div class="news-time">06:00, 18/05/2026</div>
                                <div class="news-desc">Hợp đồng tương lai Bitcoin trên các sàn giao dịch lớn đã đạt Open Interest kỷ lục 48 tỷ USD.</div>
                                <div class="news-more">Xem Thêm</div>
                            </div>
                        </div>
                        <div class="slide-table">
                            <table class="market-table">
                                <tr class="table-header">
                                    <th style="width:200px">Sản phẩm Futures</th>
                                    <th style="width:200px">Khối lượng (tỷ USD)</th>
                                    <th style="width:100px">Open Interest</th>
                                </tr>
                                <tr>
                                    <td>BTC/USDT Perpetual</td>
                                    <td class="text-right pd-right-20">52.30</td>
                                    <td class="text-right pd-right-20">48.2B</td>
                                </tr>
                                <tr>
                                    <td>ETH/USDT Perpetual</td>
                                    <td class="text-right pd-right-20">28.15</td>
                                    <td class="text-right pd-right-20">22.8B</td>
                                </tr>
                                <tr>
                                    <td>SOL/USDT Perpetual</td>
                                    <td class="text-right pd-right-20">8.40</td>
                                    <td class="text-right pd-right-20">5.6B</td>
                                </tr>
                                <tr>
                                    <td>BNB/USDT Perpetual</td>
                                    <td class="text-right pd-right-20">4.20</td>
                                    <td class="text-right pd-right-20">3.1B</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="table-footer">Cập nhật ngày 18/05/2026</td>
                                </tr>
                            </table>
                            <div class="slide-links">
                                <a href="{{ route('tradding') }}" class="form-text">Giao dịch Futures</a>
                                <a href="{{ route('tradding') }}" class="form-text">Bảng giá trực tuyến</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation arrows — same SVG, .next gets rotateY(180deg) via CSS --}}
                <a class="prev" onclick="plusSlides(-1)">
                    <svg width="50" height="50" viewBox="0 0 50 50"><polygon points="30,10 15,25 30,40" /></svg>
                </a>
                <a class="next" onclick="plusSlides(1)">
                    <svg width="50" height="50" viewBox="0 0 50 50"><polygon points="30,10 15,25 30,40" /></svg>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ FOOTER ══════════════════════════ --}}
<div id="footer">
    <div class="footer-inner">
        <div class="footer-copyright">© 2026 Bản quyền thuộc TradeX. Giao dịch tiền mã hóa tiềm ẩn rủi ro cao.</div>
        <div class="footer-links">
            <a href="#">Dịch vụ</a>
            <span>|</span>
            <a href="#">RSS</a>
            <span>|</span>
            <a href="#">Liên kết</a>
            <span>|</span>
            <a href="#">Liên hệ</a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    window.__LANDING6_DATA__ = {
        indices: [
            { name: 'BTC Dominance', value: '52.4%', change: 0.32, changeFormatted: '+0.32', pct: '0.62' },
            { name: 'Total Market Cap', value: '$3.82T', change: 0.15, changeFormatted: '+0.15', pct: '0.42' },
            { name: 'ETH Dominance', value: '18.1%', change: -0.18, changeFormatted: '-0.18', pct: '0.98' },
            { name: 'Fear & Greed', value: '72', change: 5, changeFormatted: '+5', pct: '7.46' },
        ],
        tickerCoins: [
            { symbol: 'BTC', price: '$102,450', change: 2.35, changeFormatted: '+2,350', pct: '2.35' },
            { symbol: 'ETH', price: '$5,280', change: 1.80, changeFormatted: '+93.5', pct: '1.80' },
            { symbol: 'SOL', price: '$245.80', change: 12.40, changeFormatted: '+27.1', pct: '12.40' },
            { symbol: 'BNB', price: '$682.30', change: -0.85, changeFormatted: '-5.85', pct: '0.85' },
            { symbol: 'XRP', price: '$0.892', change: 3.20, changeFormatted: '+0.027', pct: '3.20' },
            { symbol: 'ADA', price: '$0.675', change: -3.15, changeFormatted: '-0.022', pct: '3.15' },
            { symbol: 'AVAX', price: '$58.32', change: 8.75, changeFormatted: '+4.70', pct: '8.75' },
            { symbol: 'DOGE', price: '$0.185', change: -4.30, changeFormatted: '-0.008', pct: '4.30' },
            { symbol: 'DOT', price: '$8.42', change: -2.88, changeFormatted: '-0.25', pct: '2.88' },
            { symbol: 'LINK', price: '$32.15', change: 6.20, changeFormatted: '+1.88', pct: '6.20' },
        ]
    };
</script>

</body>
</html>

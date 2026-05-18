# TRADEX — Crypto Trading Platform

Hệ thống giao dịch crypto spot & session trading với WebSocket real-time, matching engine, và quản trị admin.

## Yêu cầu hệ thống

| Thành phần | Phiên bản |
|---|---|
| PHP | 8.4+ |
| MySQL | 8.0+ |
| Node.js | 18+ |
| Composer | 2.x |
| npm | 9+ |

**PHP Extensions cần thiết:**

```
bcmath, ctype, curl, dom, fileinfo, filter, gd, hash, json, mbstring,
mysqlnd, openssl, pcre, pdo, pdo_mysql, session, tokenizer, xml, zip
```

```bash
# Ubuntu/Debian
sudo apt install php8.4 php8.4-cli php8.4-fpm php8.4-bcmath php8.4-curl \
  php8.4-dom php8.4-fileinfo php8.4-gd php8.4-mbstring php8.4-mysql \
  php8.4-xml php8.4-zip php8.4-opcache

# Sau đó
composer install
```

---

## Cài đặt

### 1. Clone dự án

```bash
git clone <repo-url>
cd demo
```

### 2. Cài dependencies

```bash
composer install
npm install
```

### 3. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh sửa `.env` với thông tin thực tế:

```ini
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trading_app
DB_USERNAME=root
DB_PASSWORD=

# Queue (database driver)
QUEUE_CONNECTION=database

# Session & Cache (database driver)
SESSION_DRIVER=database
CACHE_STORE=database

# Broadcasting (Reverb)
BROADCAST_CONNECTION=reverb

# Reverb WebSocket Server
REVERB_APP_ID=353480
REVERB_APP_KEY=<your-app-key>
REVERB_APP_SECRET=<your-app-secret>
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite Reverb (phải trùng với REVERB_*)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 4. Tạo database & chạy migration

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS trading_app"
php artisan migrate --seed
```

### 5. Tạo tài khoản Admin

```bash
php artisan admin:create
```

```
Tên admin [Admin]:           ← nhập tên hoặc Enter để lấy mặc định "Admin"
Email:                        ← nhập email admin
Mật khẩu:                     ← nhập mật khẩu (ký tự bị ẩn)
```

### 6. Build frontend

```bash
# Production
npm run build

# Development (HMR)
npm run dev
```

### 7. Tạo storage link

```bash
php artisan storage:link
```

---

## Các Service / Process

Dự án cần **5 process** chạy liên tục để hoạt động đầy đủ:

| Process | Command | Mô tả |
|---|---|---|
| **Reverb** | `php artisan reverb:start` | WebSocket server (port 8080), xử lý real-time chart + orderbook |
| **Queue Worker** | `php artisan queue:work --sleep=3 --tries=3` | Xử lý job trong bảng `jobs` (database queue) |
| **Chart Worker** | `php artisan chart:worker` | Sinh dữ liệu nến K-line giả lập, broadcast qua Reverb |
| **Session Worker** | `php artisan trading:session-worker` | Quản lý vòng đời phiên giao dịch (mở → khóa → đóng) |
| **Scheduler** | `php artisan schedule:work` | Chạy `chart:summary-refresh` mỗi phút |

> `chart:worker` và `trading:session-worker` là các tiến trình chạy vô hạn (`while(true)`), cần Supervisor để quản lý.

---

## Cấu hình Supervisor

Cài Supervisor trên Linux:

```bash
sudo apt install supervisor
```

Tạo file cấu hình `/etc/supervisor/conf.d/tradex.conf`:

```ini
[group:tradex]
programs=reverb,queue,chart-worker,session-worker,scheduler

; ── Reverb WebSocket Server ──────────────────────────────────────────
[program:reverb]
command=php /var/www/demo/artisan reverb:start
directory=/var/www/demo
user=www-data
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=/var/www/demo/storage/logs/supervisor-reverb.log
stderr_logfile=/var/www/demo/storage/logs/supervisor-reverb-error.log

; ── Queue Worker (database queue) ────────────────────────────────────
[program:queue]
command=php /var/www/demo/artisan queue:work --sleep=3 --tries=3
directory=/var/www/demo
user=www-data
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=/var/www/demo/storage/logs/supervisor-queue.log
stderr_logfile=/var/www/demo/storage/logs/supervisor-queue-error.log

; ── Chart Worker (K-line generator) ──────────────────────────────────
[program:chart-worker]
command=php /var/www/demo/artisan chart:worker
directory=/var/www/demo
user=www-data
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=/var/www/demo/storage/logs/supervisor-chart-worker.log
stderr_logfile=/var/www/demo/storage/logs/supervisor-chart-worker-error.log

; ── Trading Session Worker ───────────────────────────────────────────
[program:session-worker]
command=php /var/www/demo/artisan trading:session-worker
directory=/var/www/demo
user=www-data
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=/var/www/demo/storage/logs/supervisor-session-worker.log
stderr_logfile=/var/www/demo/storage/logs/supervisor-session-worker-error.log

; ── Scheduler (thay thế cron) ────────────────────────────────────────
[program:scheduler]
command=php /var/www/demo/artisan schedule:work
directory=/var/www/demo
user=www-data
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=/var/www/demo/storage/logs/supervisor-scheduler.log
stderr_logfile=/var/www/demo/storage/logs/supervisor-scheduler-error.log
```

> Thay `/var/www/demo` bằng đường dẫn thực tế tới thư mục dự án.

### Áp dụng cấu hình Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tradex:*
```

### Các lệnh Supervisor thường dùng

```bash
sudo supervisorctl status              # Xem trạng thái tất cả process
sudo supervisorctl restart tradex:*    # Restart tất cả process nhóm tradex
sudo supervisorctl stop tradex:*       # Dừng tất cả process
sudo supervisorctl tail -f tradex:chart-worker  # Xem log chart worker
```

---

## Web Server (Nginx)

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name tradex.local;
    root /var/www/demo/public;
    client_max_body_size 100M;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

> Thay `tradex.local`, `/var/www/demo`, và `php8.4-fpm` theo môi trường thực tế.

---

## Kiến trúc dự án

```
app/
├── Console/Commands/        # Artisan commands (chart:worker, trading:session-worker, admin:create, ...)
├── Events/                  # Broadcast events (chart candles, orderbook, session, balance)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Admin dashboard controllers (web Blade)
│   │   └── Api/Admin/       # Admin API (token auth)
│   ├── Middleware/
│   │   ├── AdminMiddleware.php  # Kiểm tra is_admin
│   │   └── SetLocale.php        # EN/VI language switcher
│   └── Requests/            # FormRequest validation
├── Models/                  # Eloquent models
└── Services/
    ├── Admin/               # Dashboard stats, deposit/withdraw approval
    ├── SpotTrading/         # Order matching, orderbook, wallet
    ├── Trading/             # Session lifecycle, trade settlement, fee
    └── TradingChart/        # Candle generation, chart broadcasting

routes/
├── admin.php                # Admin web routes (protected)
├── api.php                  # Trading API + Admin Spot API routes
├── channels.php             # WebSocket channel authorization
├── client_auth.php          # Client auth routes (signin, signup, logout)
└── client_profile.php       # Client profile routes (deposit, withdraw, KYC)
```

### Guards / Auth

| Guard | Model | Table | Dùng cho |
|---|---|---|---|
| `web` | `User` | `users` | Admin (session-based, `is_admin` flag) |
| `client` | `ClientUser` | `client_users` | Client/User (session-based) |
| `sanctum` | token | `personal_access_tokens` | API (token-based) |

### Channels WebSocket

| Channel | Sự kiện | Mô tả |
|---|---|---|
| `chart.{symbol}.{interval}` | `candle.update`, `candle.close`, `candle.rewrite` | Dữ liệu nến K-line real-time |
| `spot-orderbook.{symbol}` | `orderbook.updated` | Orderbook spot trading |
| `trading.session` | `session.updated` | Trạng thái phiên giao dịch |
| `trading.result.{session_id}` | `session.result` | Kết quả phiên giao dịch |
| `user.{id}.balance` | `balance.updated` | Cập nhật số dư real-time |

---

## Artisan Commands

| Command | Mô tả |
|---|---|
| `admin:create` | Tạo tài khoản admin mới |
| `chart:worker` | Sinh nến K-line, broadcast real-time. Options: `--symbols`, `--intervals`, `--tick` |
| `chart:seed` | Seed dữ liệu nến lịch sử. Options: `--fresh`, `--count=500` |
| `chart:summary-refresh` | Refresh bảng `trading_chart_summaries` (chạy mỗi phút) |
| `trading:session-worker` | Quản lý phiên giao dịch (open/lock/close) |

---

## Cron thay thế (nếu không dùng `schedule:work`)

```cron
* * * * * php /var/www/demo/artisan schedule:run >> /dev/null 2>&1
```

---

## Các lưu ý khác

- Session và Cache dùng `database` driver — đảm bảo đã chạy `php artisan migrate` để tạo bảng `sessions` và `cache`.
- Reverb chạy trên port 8080 — cần mở port nếu có firewall hoặc dùng Nginx reverse proxy.
- Upload logo admin được lưu vào `storage/app/public/logo`.
- Ngôn ngữ: `en` (mặc định), `vi` — chuyển đổi qua route `/lang/{locale}` hoặc topbar dropdown.
- KYC upload hỗ trợ ảnh JPEG/PNG, file input đã được custom hiển thị tiếng Việt.
- Real-time balance update hoạt động qua kênh `user.{id}.balance`, tự động cập nhật số dư trên topbar khi nạp/rút tiền.

## Lưu ý quan trọng: 

- Trước khi chạy, đảm bảo domain đã được trỏ DNS (A record) về IP server — Certbot cần verify qua HTTP nên domain phải resolve về đúng server mới lấy được cert.

#!/bin/bash
# =============================================================================
#  TRADEX — Crypto Trading Platform — Ubuntu Setup Script
#  Requires: Ubuntu 20.04+ | Nginx | PHP 8.4+ | MySQL 8.0+
# =============================================================================

set -e  # Exit on any error

# ── Colors ────────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

log()    { echo -e "${GREEN}[✔] $1${NC}"; }
warn()   { echo -e "${YELLOW}[!] $1${NC}"; }
error()  { echo -e "${RED}[✘] $1${NC}"; exit 1; }
header() { echo -e "\n${CYAN}══════════════════════════════════════════${NC}"; echo -e "${CYAN}  $1${NC}"; echo -e "${CYAN}══════════════════════════════════════════${NC}"; }

# ── Config — Edit these before running ───────────────────────────────────────
APP_DIR="/var/www/tradex"           # Deployment path on server
APP_DOMAIN="tradex.local"          # Nginx server_name / domain
DB_NAME="trading_app"
DB_USER="trade"
DB_PASS="Trade1243"
REPO_URL=""                        # Your git repo URL (leave empty to skip clone)
PHP_VERSION="8.4"
NODE_VERSION="20"                  # Node.js major version (LTS)
WEB_USER="ubuntu"

# ── Root check ────────────────────────────────────────────────────────────────
if [ "$(id -u)" -ne 0 ]; then
  error "Please run this script as root: sudo bash setup.sh"
fi

# =============================================================================
header "STEP 1 — System update & base packages"
# =============================================================================
apt-get update -qq && apt-get upgrade -y -qq
apt-get install -y -qq \
  curl wget git unzip zip supervisor ufw \
  software-properties-common apt-transport-https ca-certificates gnupg lsb-release
log "Base packages installed"

# =============================================================================
header "STEP 2 — PHP ${PHP_VERSION}"
# =============================================================================
add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
apt-get update -qq
apt-get install -y -qq \
  php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-fpm \
  php${PHP_VERSION}-bcmath php${PHP_VERSION}-curl php${PHP_VERSION}-dom \
  php${PHP_VERSION}-fileinfo php${PHP_VERSION}-gd php${PHP_VERSION}-mbstring \
  php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-zip \
  php${PHP_VERSION}-opcache php${PHP_VERSION}-ctype php${PHP_VERSION}-tokenizer

PHP_FPM_SOCK="/run/php/php${PHP_VERSION}-fpm.sock"

# Configure php-fpm pool to run as ubuntu user
PHP_FPM_POOL="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
if [ -f "${PHP_FPM_POOL}" ]; then
  sed -i "s|^user = .*|user = ubuntu|"   "${PHP_FPM_POOL}"
  sed -i "s|^group = .*|group = ubuntu|" "${PHP_FPM_POOL}"
  log "php-fpm pool set to run as ubuntu"
fi

systemctl enable php${PHP_VERSION}-fpm
systemctl start  php${PHP_VERSION}-fpm
log "PHP ${PHP_VERSION} installed — FPM socket: ${PHP_FPM_SOCK}"

# =============================================================================
header "STEP 3 — Composer"
# =============================================================================
if ! command -v composer &> /dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
log "Composer $(composer --version --no-ansi 2>/dev/null | head -1)"

# =============================================================================
header "STEP 4 — Node.js ${NODE_VERSION} & npm"
# =============================================================================
if ! command -v node &> /dev/null; then
  curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - > /dev/null 2>&1
  apt-get install -y -qq nodejs
fi
log "Node $(node -v) / npm $(npm -v)"

# =============================================================================
header "STEP 5 — MySQL 8"
# =============================================================================
apt-get install -y -qq mysql-server
systemctl enable mysql
systemctl start  mysql

# Create DB & user with remote access
mysql -u root <<EOF
-- Create application database
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Create user 'trade' accessible from anywhere
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';

-- Grant full privileges on all databases from anywhere
GRANT ALL PRIVILEGES ON *.* TO '${DB_USER}'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;
EOF

# Allow MySQL to accept remote connections (bind to all interfaces)
MYSQL_CNF=$(find /etc/mysql -name "mysqld.cnf" 2>/dev/null | head -1)
if [ -n "${MYSQL_CNF}" ]; then
  if grep -q "^bind-address" "${MYSQL_CNF}"; then
    sed -i "s|^bind-address\s*=.*|bind-address = 0.0.0.0|" "${MYSQL_CNF}"
  else
    echo "bind-address = 0.0.0.0" >> "${MYSQL_CNF}"
  fi
  systemctl restart mysql
  log "MySQL bind-address set to 0.0.0.0 — remote connections enabled"
fi

# Open MySQL port 3306 in UFW
ufw allow 3306/tcp > /dev/null
log "MySQL: database '${DB_NAME}' created, user '${DB_USER}'@'%' granted full access from anywhere"

# =============================================================================
header "STEP 6 — Nginx"
# =============================================================================
apt-get install -y -qq nginx
systemctl enable nginx
systemctl start  nginx

# Write Nginx vhost
cat > /etc/nginx/sites-available/tradex <<NGINX
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${APP_DOMAIN};
    root ${APP_DIR}/public;
    client_max_body_size 100M;

    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Reverb WebSocket reverse proxy — Pusher /app path
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$host;
        proxy_read_timeout 60;
        proxy_cache_bypass \$http_upgrade;
    }

    # Reverb WebSocket reverse proxy — /apps path
    location /apps {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$host;
        proxy_read_timeout 60;
        proxy_cache_bypass \$http_upgrade;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    access_log /var/log/nginx/tradex_access.log;
    error_log  /var/log/nginx/tradex_error.log;
}
NGINX

ln -sf /etc/nginx/sites-available/tradex /etc/nginx/sites-enabled/tradex
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
log "Nginx vhost configured for ${APP_DOMAIN}"

# =============================================================================
header "STEP 7 — Clone / set up application"
# =============================================================================
mkdir -p "${APP_DIR}"

if [ -n "${REPO_URL}" ]; then
  if [ -d "${APP_DIR}/.git" ]; then
    warn "Repo already exists — pulling latest"
    git -C "${APP_DIR}" pull
  else
    git clone "${REPO_URL}" "${APP_DIR}"
  fi
  log "Source code ready at ${APP_DIR}"
else
  warn "REPO_URL is empty — skipping git clone. Copy your source to ${APP_DIR} manually."
fi

cd "${APP_DIR}" || error "Cannot cd into ${APP_DIR}"

# Install PHP dependencies
log "Running composer install..."
chown -R ${WEB_USER}:${WEB_USER} "${APP_DIR}"
sudo -u ${WEB_USER} composer install --optimize-autoloader --no-interaction --no-scripts 2>/dev/null

# Run only safe post-install scripts manually
sudo -u ${WEB_USER} php artisan vendor:publish --tag=laravel-assets --force 2>/dev/null || true
sudo -u ${WEB_USER} php artisan package:discover --ansi 2>/dev/null || true

# Install Node dependencies only (build happens AFTER .env is configured)
log "Running npm install..."
sudo -u ${WEB_USER} npm install --silent

# =============================================================================
header "STEP 8 — Laravel environment setup"
# =============================================================================
if [ ! -f "${APP_DIR}/.env" ]; then
  cp .env.example .env

  # Generate Reverb keys
  REVERB_KEY="$(openssl rand -hex 16)"
  REVERB_SECRET="$(openssl rand -hex 32)"

  # Patch .env
  sed -i "s|^APP_ENV=.*|APP_ENV=production|"     .env
  sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|"       .env
  sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|"           .env
  sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|"           .env
  sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|"           .env
  sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|"   .env
  sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=database|"       .env
  sed -i "s|^CACHE_STORE=.*|CACHE_STORE=database|"             .env
  sed -i "s|^BROADCAST_CONNECTION=.*|BROADCAST_CONNECTION=reverb|" .env
  sed -i "s|^REVERB_APP_KEY=.*|REVERB_APP_KEY=${REVERB_KEY}|"      .env
  sed -i "s|^REVERB_APP_SECRET=.*|REVERB_APP_SECRET=${REVERB_SECRET}|" .env
  # Reverb server listens internally on 127.0.0.1:8080
  sed -i "s|^REVERB_HOST=.*|REVERB_HOST=127.0.0.1|"                .env
  sed -i "s|^REVERB_PORT=.*|REVERB_PORT=8080|"                     .env
  sed -i "s|^REVERB_SCHEME=.*|REVERB_SCHEME=http|"                 .env
  # VITE_REVERB_* = what the BROWSER uses to connect (via Nginx proxy on port 80)
  sed -i "s|^VITE_REVERB_APP_KEY=.*|VITE_REVERB_APP_KEY=${REVERB_KEY}|" .env
  sed -i "s|^VITE_REVERB_HOST=.*|VITE_REVERB_HOST=${APP_DOMAIN}|"  .env
  sed -i "s|^VITE_REVERB_PORT=.*|VITE_REVERB_PORT=80|"             .env
  sed -i "s|^VITE_REVERB_SCHEME=.*|VITE_REVERB_SCHEME=http|"       .env

  log ".env configured"
else
  warn ".env already exists — skipping auto-config (check DB credentials manually)"
fi

# =============================================================================
header "STEP 9 — Storage & permissions (before artisan runs)"
# =============================================================================
# Create all required Laravel storage subdirectories
mkdir -p "${APP_DIR}/storage/framework/sessions"
mkdir -p "${APP_DIR}/storage/framework/views"
mkdir -p "${APP_DIR}/storage/framework/cache/data"
mkdir -p "${APP_DIR}/storage/logs"
mkdir -p "${APP_DIR}/bootstrap/cache"

# Set ownership & permissions FIRST so artisan never writes root-owned files
chown -R ${WEB_USER}:${WEB_USER} "${APP_DIR}"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"

# Run artisan as www-data from here on to avoid root-owned temp/cache files
sudo -u ${WEB_USER} php artisan key:generate --force
sudo -u ${WEB_USER} php artisan storage:link
log "Ownership set to ${WEB_USER}, storage link created"

# =============================================================================
header "STEP 10 — Database migration & seeding"
# =============================================================================
sudo -u ${WEB_USER} php artisan migrate --seed --force
sudo -u ${WEB_USER} php artisan config:clear
sudo -u ${WEB_USER} php artisan cache:clear
sudo -u ${WEB_USER} php artisan view:clear
log "Migrations & seeders done"

# =============================================================================
header "STEP 10b — Build frontend (after .env VITE_REVERB_* is ready)"
# =============================================================================
rm -rf "${APP_DIR}/public/build"
mkdir -p "${APP_DIR}/public/build"
chown -R ${WEB_USER}:${WEB_USER} "${APP_DIR}/public"
chmod -R +x "${APP_DIR}/node_modules/.bin"
cd "${APP_DIR}"
sudo -u ${WEB_USER} npx vite build
log "Frontend built with correct VITE_REVERB_HOST=${APP_DOMAIN} PORT=80"

# =============================================================================
header "STEP 11 — Supervisor (5 workers)"
# =============================================================================
cat > /etc/supervisor/conf.d/tradex.conf <<SUPERVISOR
[group:tradex]
programs=reverb,queue,chart-worker,session-worker,scheduler

; ── Reverb WebSocket Server ────────────────────────────────────────────
[program:reverb]
command=php ${APP_DIR}/artisan reverb:start
directory=${APP_DIR}
user=${WEB_USER}
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=${APP_DIR}/storage/logs/supervisor-reverb.log
stderr_logfile=${APP_DIR}/storage/logs/supervisor-reverb-error.log

; ── Queue Worker ───────────────────────────────────────────────────────
[program:queue]
command=php ${APP_DIR}/artisan queue:work --sleep=3 --tries=3
directory=${APP_DIR}
user=${WEB_USER}
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=${APP_DIR}/storage/logs/supervisor-queue.log
stderr_logfile=${APP_DIR}/storage/logs/supervisor-queue-error.log

; ── Chart Worker (K-line generator) ───────────────────────────────────
[program:chart-worker]
command=php ${APP_DIR}/artisan chart:worker
directory=${APP_DIR}
user=${WEB_USER}
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=${APP_DIR}/storage/logs/supervisor-chart-worker.log
stderr_logfile=${APP_DIR}/storage/logs/supervisor-chart-worker-error.log

; ── Trading Session Worker ─────────────────────────────────────────────
[program:session-worker]
command=php ${APP_DIR}/artisan trading:session-worker
directory=${APP_DIR}
user=${WEB_USER}
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=${APP_DIR}/storage/logs/supervisor-session-worker.log
stderr_logfile=${APP_DIR}/storage/logs/supervisor-session-worker-error.log

; ── Scheduler (replaces cron) ─────────────────────────────────────────
[program:scheduler]
command=php ${APP_DIR}/artisan schedule:work
directory=${APP_DIR}
user=${WEB_USER}
autostart=true
autorestart=true
numprocs=1
stopwaitsecs=10
stdout_logfile=${APP_DIR}/storage/logs/supervisor-scheduler.log
stderr_logfile=${APP_DIR}/storage/logs/supervisor-scheduler-error.log
SUPERVISOR

supervisorctl reread
supervisorctl update
supervisorctl start tradex:* 2>/dev/null || warn "Supervisor start failed — run 'sudo supervisorctl start tradex:*' after app is in ${APP_DIR}"
log "Supervisor configured & workers started"

# =============================================================================
header "STEP 12 — Firewall (UFW)"
# =============================================================================
ufw allow OpenSSH      > /dev/null
ufw allow 'Nginx HTTP' > /dev/null
ufw allow 'Nginx HTTPS' > /dev/null
ufw --force enable     > /dev/null
log "UFW rules applied (SSH + HTTP + HTTPS open)"

# =============================================================================
header "STEP 13 — SSL miễn phí với Certbot (Let's Encrypt)"
# =============================================================================
# Certbot chỉ hoạt động với domain thật (không phải IP hay localhost)
if echo "${APP_DOMAIN}" | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$'; then
  warn "APP_DOMAIN là địa chỉ IP (${APP_DOMAIN}) — bỏ qua cài SSL."
  warn "Để cài SSL, hãy trỏ domain về server rồi chạy:"
  warn "  sudo certbot --nginx -d your-domain.com --non-interactive --agree-tos -m admin@your-domain.com"
else
  # Cài Certbot + plugin Nginx
  apt-get install -y -qq certbot python3-certbot-nginx

  # Lấy cert & tự động cấu hình Nginx
  certbot --nginx \
    -d "${APP_DOMAIN}" \
    --non-interactive \
    --agree-tos \
    --redirect \
    --email "admin@${APP_DOMAIN}" \
    --no-eff-email || warn "Certbot thất bại — kiểm tra domain đã trỏ DNS về server chưa"

  # Cấu hình tự động gia hạn qua systemd timer (mặc định certbot đã cài)
  systemctl enable  certbot.timer 2>/dev/null || true
  systemctl start   certbot.timer 2>/dev/null || true

  # Kiểm tra timer gia hạn
  if systemctl is-active --quiet certbot.timer; then
    log "Certbot systemd timer đang chạy — cert sẽ tự gia hạn"
  else
    # Fallback: dùng cron nếu systemd timer không có
    CRON_JOB="0 3 * * * certbot renew --quiet --nginx --post-hook 'systemctl reload nginx'"
    ( crontab -l 2>/dev/null | grep -v 'certbot renew'; echo "${CRON_JOB}" ) | crontab -
    log "Cron job gia hạn SSL đã được thêm (chạy lúc 3:00 AM mỗi ngày)"
  fi

  # Sau khi có SSL, cập nhật VITE_REVERB_SCHEME = https và rebuild
  if [ -f "${APP_DIR}/.env" ]; then
    sed -i "s|^VITE_REVERB_SCHEME=.*|VITE_REVERB_SCHEME=https|"   "${APP_DIR}/.env"
    sed -i "s|^VITE_REVERB_PORT=.*|VITE_REVERB_PORT=443|"         "${APP_DIR}/.env"
    sed -i "s|^APP_URL=.*|APP_URL=https://${APP_DOMAIN}|"         "${APP_DIR}/.env"
    log ".env cập nhật VITE_REVERB_SCHEME=https, PORT=443"

    # Rebuild frontend với config https
    cd "${APP_DIR}"
    rm -rf public/build
    mkdir -p public/build
    chown -R ${WEB_USER}:${WEB_USER} public
    chmod -R +x node_modules/.bin
    sudo -u ${WEB_USER} npx vite build
    sudo -u ${WEB_USER} php artisan config:clear
    sudo -u ${WEB_USER} php artisan config:cache
    log "Frontend rebuild với HTTPS hoàn tất"
  fi

  # Cập nhật Nginx location /app & /apps dùng wss (port 443)
  systemctl reload nginx
  log "SSL (Let's Encrypt) cài đặt xong cho ${APP_DOMAIN}"
fi

# =============================================================================
header "STEP 14 — Create Admin account"
# =============================================================================
echo ""
warn "Now creating the TRADEX admin account..."
sudo -u ${WEB_USER} php artisan admin:create

# =============================================================================
header "✅  Setup complete!"
# =============================================================================
echo ""
echo -e "${GREEN}  App directory : ${APP_DIR}${NC}"
echo -e "${GREEN}  Site URL      : https://${APP_DOMAIN}${NC}"
echo -e "${GREEN}  DB name       : ${DB_NAME}${NC}"
echo -e "${GREEN}  DB user       : ${DB_USER}${NC}"
echo -e "${GREEN}  DB password   : ${DB_PASS}${NC}"
echo ""
echo -e "${YELLOW}  ⚠  Save the DB password above — it won't be shown again!${NC}"
echo ""
echo -e "${CYAN}  SSL & gia hạn:${NC}"
echo "    sudo certbot certificates          # Xem cert hiện tại"
echo "    sudo certbot renew --dry-run       # Test gia hạn thử"
echo "    systemctl status certbot.timer     # Trạng thái auto-renew"
echo ""
echo -e "${CYAN}  Supervisor status:${NC}"
supervisorctl status tradex:* 2>/dev/null || true
echo ""
echo -e "${CYAN}  Useful commands:${NC}"
echo "    sudo supervisorctl status           # All workers"
echo "    sudo supervisorctl restart tradex:* # Restart all"
echo "    sudo supervisorctl tail -f tradex:chart-worker"
echo "    sudo nginx -t && sudo systemctl reload nginx"
echo ""

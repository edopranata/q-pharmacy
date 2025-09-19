# Backend Deployment Documentation

## Overview

Panduan lengkap untuk deployment aplikasi backend Q-Pharmacy menggunakan Laravel 12 di berbagai environment.

## Prerequisites

### System Requirements

- **PHP**: >= 8.2
- **Composer**: >= 2.0
- **MySQL**: >= 8.0 atau MariaDB >= 10.4
- **Redis**: >= 6.0
- **Node.js**: >= 18.0 (untuk asset compilation)
- **Nginx**: >= 1.20 atau Apache >= 2.4

### Server Specifications

#### Development Environment
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+ / macOS

#### Production Environment
- **CPU**: 4+ cores
- **RAM**: 8GB+
- **Storage**: 100GB+ SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+

## Environment Setup

### 1. Development Environment

#### Using Laravel Sail (Docker)

```bash
# Clone repository
git clone https://github.com/your-org/q-pharmacy-backend.git
cd q-pharmacy-backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Start Sail
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database
./vendor/bin/sail artisan db:seed
```

#### Manual Setup

```bash
# Install PHP and extensions
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation

# Install Redis
sudo apt install redis-server

# Clone and setup project
git clone https://github.com/your-org/q-pharmacy-backend.git
cd q-pharmacy-backend
composer install
cp .env.example .env
php artisan key:generate

# Configure database
mysql -u root -p
CREATE DATABASE q_pharmacy;
CREATE USER 'q_pharmacy_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON q_pharmacy.* TO 'q_pharmacy_user'@'localhost';
FLUSH PRIVILEGES;

# Run migrations
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### 2. Staging Environment

#### Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server redis-server \
    php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd \
    supervisor git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Create application user
sudo useradd -m -s /bin/bash qpharmacy
sudo usermod -aG www-data qpharmacy
```

#### Application Deployment

```bash
# Switch to application user
sudo su - qpharmacy

# Clone repository
git clone https://github.com/your-org/q-pharmacy-backend.git /home/qpharmacy/app
cd /home/qpharmacy/app

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R qpharmacy:www-data /home/qpharmacy/app
sudo chmod -R 755 /home/qpharmacy/app
sudo chmod -R 775 /home/qpharmacy/app/storage
sudo chmod -R 775 /home/qpharmacy/app/bootstrap/cache
```

#### Environment Configuration

```bash
# Create environment file
cp .env.staging .env

# Generate application key
php artisan key:generate

# Configure environment variables
vim .env
```

**Staging .env Configuration:**

```env
APP_NAME="Q-Pharmacy API"
APP_ENV=staging
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://staging-api.q-pharmacy.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=q_pharmacy_staging
DB_USERNAME=q_pharmacy_user
DB_PASSWORD=secure_staging_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# JWT Configuration
JWT_SECRET=your_jwt_secret_key
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60
RATE_LIMIT_LOGIN_PER_MINUTE=5

# File Upload
MAX_UPLOAD_SIZE=10240
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif
```

### 3. Production Environment

#### Infrastructure Setup

**Load Balancer Configuration (Nginx):**

```nginx
# /etc/nginx/sites-available/q-pharmacy-lb
upstream q_pharmacy_backend {
    least_conn;
    server 10.0.1.10:80 weight=3 max_fails=3 fail_timeout=30s;
    server 10.0.1.11:80 weight=3 max_fails=3 fail_timeout=30s;
    server 10.0.1.12:80 weight=2 max_fails=3 fail_timeout=30s backup;
}

server {
    listen 80;
    listen [::]:80;
    server_name api.q-pharmacy.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.q-pharmacy.com;

    ssl_certificate /etc/letsencrypt/live/api.q-pharmacy.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.q-pharmacy.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req zone=api burst=20 nodelay;

    location / {
        proxy_pass http://q_pharmacy_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_connect_timeout 30s;
        proxy_send_timeout 30s;
        proxy_read_timeout 30s;
    }

    # Health check endpoint
    location /health {
        access_log off;
        proxy_pass http://q_pharmacy_backend/api/health;
    }
}
```

**Application Server Configuration (Nginx + PHP-FPM):**

```nginx
# /etc/nginx/sites-available/q-pharmacy-app
server {
    listen 80;
    server_name _;
    root /home/qpharmacy/app/public;
    index index.php;

    # Security
    server_tokens off;
    client_max_body_size 10M;

    # Logging
    access_log /var/log/nginx/q-pharmacy-access.log;
    error_log /var/log/nginx/q-pharmacy-error.log;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location /storage {
        alias /home/qpharmacy/app/storage/app/public;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

#### Database Setup

**MySQL Master-Slave Configuration:**

```sql
-- Master server configuration
-- /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
server-id = 1
log-bin = mysql-bin
binlog-format = ROW
binlog-do-db = q_pharmacy_production

-- Create replication user
CREATE USER 'replication'@'%' IDENTIFIED BY 'secure_replication_password';
GRANT REPLICATION SLAVE ON *.* TO 'replication'@'%';
FLUSH PRIVILEGES;

-- Show master status
SHOW MASTER STATUS;
```

```sql
-- Slave server configuration
-- /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
server-id = 2
relay-log = mysql-relay-bin
log-slave-updates = 1
read-only = 1

-- Configure slave
CHANGE MASTER TO
    MASTER_HOST='master_ip_address',
    MASTER_USER='replication',
    MASTER_PASSWORD='secure_replication_password',
    MASTER_LOG_FILE='mysql-bin.000001',
    MASTER_LOG_POS=154;

START SLAVE;
SHOW SLAVE STATUS\G;
```

#### Redis Cluster Setup

```bash
# Redis cluster configuration
# /etc/redis/redis-cluster.conf
port 7000
cluster-enabled yes
cluster-config-file nodes-7000.conf
cluster-node-timeout 5000
appendonly yes
bind 0.0.0.0
protected-mode no

# Start cluster
redis-cli --cluster create \
    10.0.1.20:7000 10.0.1.21:7000 10.0.1.22:7000 \
    10.0.1.20:7001 10.0.1.21:7001 10.0.1.22:7001 \
    --cluster-replicas 1
```

## Deployment Process

### Automated Deployment with GitHub Actions

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, dom, filter, gd, json
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run tests
      run: php artisan test
      
    - name: Deploy to servers
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /home/qpharmacy/app
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan queue:restart
          sudo systemctl reload php8.2-fpm
          sudo systemctl reload nginx
```

### Manual Deployment Steps

```bash
#!/bin/bash
# deploy.sh

set -e

echo "Starting deployment..."

# Backup current version
cp -r /home/qpharmacy/app /home/qpharmacy/app-backup-$(date +%Y%m%d-%H%M%S)

# Pull latest code
cd /home/qpharmacy/app
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Clear and cache configurations
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Restart queue workers
php artisan queue:restart

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

echo "Deployment completed successfully!"
```

## Monitoring & Health Checks

### Application Health Check

```php
// routes/api.php
Route::get('/health', function () {
    $checks = [
        'database' => checkDatabase(),
        'redis' => checkRedis(),
        'storage' => checkStorage(),
        'queue' => checkQueue(),
    ];
    
    $healthy = array_reduce($checks, function ($carry, $check) {
        return $carry && $check['status'] === 'ok';
    }, true);
    
    return response()->json([
        'status' => $healthy ? 'healthy' : 'unhealthy',
        'timestamp' => now()->toISOString(),
        'checks' => $checks,
    ], $healthy ? 200 : 503);
});

function checkDatabase() {
    try {
        DB::connection()->getPdo();
        return ['status' => 'ok', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Database connection failed'];
    }
}

function checkRedis() {
    try {
        Redis::ping();
        return ['status' => 'ok', 'message' => 'Redis connection successful'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Redis connection failed'];
    }
}
```

### Supervisor Configuration

```ini
# /etc/supervisor/conf.d/q-pharmacy-worker.conf
[program:q-pharmacy-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/qpharmacy/app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=qpharmacy
numprocs=4
redirect_stderr=true
stdout_logfile=/home/qpharmacy/app/storage/logs/worker.log
stopwaitsecs=3600
```

### Log Rotation

```bash
# /etc/logrotate.d/q-pharmacy
/home/qpharmacy/app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 qpharmacy qpharmacy
    postrotate
        /bin/kill -USR1 $(cat /var/run/nginx.pid 2>/dev/null) 2>/dev/null || true
    endscript
}
```

## Security Considerations

### SSL/TLS Configuration

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d api.q-pharmacy.com

# Auto-renewal
sudo crontab -e
0 12 * * * /usr/bin/certbot renew --quiet
```

### Firewall Configuration

```bash
# UFW configuration
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow from 10.0.1.0/24 to any port 3306  # MySQL
sudo ufw allow from 10.0.1.0/24 to any port 6379  # Redis
sudo ufw enable
```

### File Permissions

```bash
# Set proper permissions
sudo chown -R qpharmacy:www-data /home/qpharmacy/app
sudo find /home/qpharmacy/app -type f -exec chmod 644 {} \;
sudo find /home/qpharmacy/app -type d -exec chmod 755 {} \;
sudo chmod -R 775 /home/qpharmacy/app/storage
sudo chmod -R 775 /home/qpharmacy/app/bootstrap/cache
sudo chmod 600 /home/qpharmacy/app/.env
```

## Backup & Recovery

### Database Backup

```bash
#!/bin/bash
# backup-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/qpharmacy/backups"
DB_NAME="q_pharmacy_production"
DB_USER="backup_user"
DB_PASS="backup_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Upload to S3 (optional)
aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql.gz s3://q-pharmacy-backups/database/

# Clean old backups (keep last 7 days)
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

### Application Backup

```bash
#!/bin/bash
# backup-app.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/qpharmacy/backups"
APP_DIR="/home/qpharmacy/app"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create application backup (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='.git' \
    -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C /home/qpharmacy app

# Upload to S3 (optional)
aws s3 cp $BACKUP_DIR/app_backup_$DATE.tar.gz s3://q-pharmacy-backups/application/

# Clean old backups (keep last 7 days)
find $BACKUP_DIR -name "app_backup_*.tar.gz" -mtime +7 -delete

echo "Application backup completed: app_backup_$DATE.tar.gz"
```

### Automated Backup Schedule

```bash
# Add to crontab
sudo crontab -e

# Database backup every 6 hours
0 */6 * * * /home/qpharmacy/scripts/backup-db.sh

# Application backup daily at 2 AM
0 2 * * * /home/qpharmacy/scripts/backup-app.sh

# Storage backup daily at 3 AM
0 3 * * * rsync -av /home/qpharmacy/app/storage/app/public/ /home/qpharmacy/backups/storage/
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R qpharmacy:www-data /home/qpharmacy/app
   sudo chmod -R 775 /home/qpharmacy/app/storage
   ```

2. **Database Connection Issues**
   ```bash
   # Check MySQL status
   sudo systemctl status mysql
   
   # Check connection
   mysql -u username -p -h hostname
   ```

3. **Redis Connection Issues**
   ```bash
   # Check Redis status
   sudo systemctl status redis
   
   # Test connection
   redis-cli ping
   ```

4. **Queue Not Processing**
   ```bash
   # Restart queue workers
   php artisan queue:restart
   
   # Check supervisor status
   sudo supervisorctl status
   ```

### Performance Issues

1. **Slow Database Queries**
   ```sql
   -- Enable slow query log
   SET GLOBAL slow_query_log = 'ON';
   SET GLOBAL long_query_time = 2;
   
   -- Check slow queries
   SHOW VARIABLES LIKE 'slow_query_log%';
   ```

2. **High Memory Usage**
   ```bash
   # Check memory usage
   free -h
   
   # Check PHP-FPM processes
   ps aux | grep php-fpm
   
   # Optimize PHP-FPM configuration
   sudo vim /etc/php/8.2/fpm/pool.d/www.conf
   ```

3. **High CPU Usage**
   ```bash
   # Check top processes
   top
   
   # Check Laravel logs
   tail -f /home/qpharmacy/app/storage/logs/laravel.log
   ```

## Conclusion

Dokumentasi deployment ini mencakup semua aspek yang diperlukan untuk menjalankan aplikasi Q-Pharmacy di berbagai environment. Pastikan untuk mengikuti best practices keamanan dan melakukan testing menyeluruh sebelum deployment ke production.

Untuk pertanyaan atau masalah deployment, hubungi tim DevOps atau buat issue di repository project.
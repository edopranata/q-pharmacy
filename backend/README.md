# Q-Pharmacy Backend - Laravel API

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![Redis](https://img.shields.io/badge/Redis-7.0+-red.svg)

**RESTful API Backend untuk Sistem Manajemen Apotek Q-Pharmacy**

</div>

## 📋 Daftar Isi

- [Tentang Backend](#tentang-backend)
- [Arsitektur](#arsitektur)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Database](#database)
- [Authentication & Authorization](#authentication--authorization)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Performance](#performance)
- [Security](#security)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

## 🏗 Tentang Backend

Backend Q-Pharmacy dibangun menggunakan Laravel 12 dengan arsitektur RESTful API yang modern dan scalable. Sistem ini dirancang untuk menangani operasional apotek dengan performa tinggi dan keamanan yang robust.

### 🎯 Fitur Utama

- **RESTful API**: Endpoint yang konsisten dan well-documented
- **Authentication**: Laravel Sanctum untuk token-based auth
- **Authorization**: Spatie Laravel Permission dengan Super-Admin role
- **Database**: Eloquent ORM dengan relationship yang optimal
- **Caching**: Redis untuk performa maksimal
- **Queue System**: Background job processing
- **File Storage**: Local dan cloud storage support
- **API Documentation**: OpenAPI/Swagger integration

## 🏛 Arsitektur

### Struktur Direktori

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── Auth/
│   │   │   │   │   ├── Products/
│   │   │   │   │   ├── Sales/
│   │   │   │   │   ├── Users/
│   │   │   │   │   └── Dashboard/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Jobs/
│   ├── Events/
│   ├── Listeners/
│   └── Exceptions/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
└── storage/
```

### Design Patterns

- **Repository Pattern**: Abstraksi data layer
- **Service Pattern**: Business logic separation
- **Observer Pattern**: Model events handling
- **Factory Pattern**: Object creation
- **Strategy Pattern**: Payment methods

## 📋 Persyaratan Sistem

### Minimum Requirements

- **PHP**: 8.2 atau lebih tinggi
- **Composer**: 2.x
- **Database**: MySQL 8.0+ atau PostgreSQL 13+
- **Redis**: 7.0+ (untuk cache dan queue)
- **Memory**: 512MB minimum, 2GB recommended
- **Storage**: 5GB minimum

### PHP Extensions

```bash
# Required extensions
php-bcmath
php-ctype
php-curl
php-dom
php-fileinfo
php-json
php-mbstring
php-openssl
php-pcre
php-pdo
php-tokenizer
php-xml
php-zip

# Optional but recommended
php-redis
php-imagick
php-intl
```

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-username/q-pharmacy.git
cd q-pharmacy/backend
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install development dependencies (optional)
composer install --dev
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret (jika menggunakan JWT)
php artisan jwt:secret
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database dengan data sample
php artisan db:seed

# Atau seed specific seeder
php artisan db:seed --class=UserSeeder
```

### 5. Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 6. Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Atau dengan custom host dan port
php artisan serve --host=0.0.0.0 --port=8080
```

## ⚙️ Konfigurasi

### Environment Variables

```env
# Application
APP_NAME="Q-Pharmacy"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=q_pharmacy
DB_USERNAME=root
DB_PASSWORD=

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@q-pharmacy.com
MAIL_FROM_NAME="Q-Pharmacy"

# File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,::1
SESSION_DOMAIN=localhost

# API Configuration
API_VERSION=v1
API_PREFIX=api
API_RATE_LIMIT=60

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
```

### Cache Configuration

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🗄 Database

### Schema Overview

#### Core Tables

- **users**: User management dengan roles
- **products**: Master data produk farmasi
- **categories**: Kategori produk
- **suppliers**: Data supplier
- **product_batches**: Batch tracking dengan expired date
- **stock_movements**: Tracking pergerakan stok
- **sales**: Transaksi penjualan
- **sale_items**: Detail item penjualan

#### Permission Tables (Spatie)

- **roles**: Role definition
- **permissions**: Permission definition
- **model_has_permissions**: User permissions
- **model_has_roles**: User roles
- **role_has_permissions**: Role permissions

### Migrations

```bash
# Create new migration
php artisan make:migration create_products_table

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh --seed
```

### Seeders

```bash
# Create seeder
php artisan make:seeder ProductSeeder

# Run specific seeder
php artisan db:seed --class=ProductSeeder

# Run all seeders
php artisan db:seed
```

## 🔐 Authentication & Authorization

### Laravel Sanctum Setup

```bash
# Publish Sanctum configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run Sanctum migrations
php artisan migrate
```

### Super-Admin Implementation

```php
// app/Providers/AuthServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot()
{
    // Super-Admin bypass all permissions
    Gate::before(function ($user, $ability) {
        return $user->hasRole('Super-Admin') ? true : null;
    });
}
```

### Default Roles & Permissions

```php
// Database Seeder
$roles = [
    'Super-Admin' => ['*'], // All permissions
    'Admin' => [
        'users.view', 'users.create', 'users.edit',
        'products.view', 'products.create', 'products.edit',
        'sales.view', 'reports.view'
    ],
    'Kasir' => [
        'products.view', 'sales.create', 'sales.view'
    ],
    'Supervisor' => [
        'products.view', 'sales.view', 'reports.view'
    ]
];
```

### API Authentication

```php
// Login endpoint
POST /api/v1/auth/login
{
    "email": "admin@q-pharmacy.com",
    "password": "password"
}

// Response
{
    "success": true,
    "data": {
        "user": {...},
        "token": "1|abc123...",
        "expires_at": "2025-01-01T00:00:00.000000Z"
    }
}

// Protected endpoint usage
Authorization: Bearer 1|abc123...
```

## 🛣 API Endpoints

### Base URL

```
Development: http://localhost:8000/api/v1
Production: https://api.q-pharmacy.com/v1
```

### Authentication Endpoints

```http
POST   /auth/login           # User login
POST   /auth/logout          # User logout
POST   /auth/refresh         # Refresh token
GET    /auth/me              # Get current user
POST   /auth/forgot-password # Password reset request
POST   /auth/reset-password  # Password reset
```

### User Management

```http
GET    /users                # List users (paginated)
POST   /users                # Create user
GET    /users/{id}           # Get user details
PUT    /users/{id}           # Update user
DELETE /users/{id}           # Delete user
POST   /users/{id}/roles     # Assign roles
```

### Product Management

```http
GET    /products             # List products (paginated)
POST   /products             # Create product
GET    /products/{id}        # Get product details
PUT    /products/{id}        # Update product
DELETE /products/{id}        # Delete product
GET    /products/{id}/batches # Get product batches
POST   /products/{id}/batches # Add product batch
```

### Sales Management

```http
GET    /sales                # List sales (paginated)
POST   /sales                # Create sale
GET    /sales/{id}           # Get sale details
PUT    /sales/{id}           # Update sale
DELETE /sales/{id}           # Cancel sale
```

### Dashboard & Reports

```http
GET    /dashboard/stats      # Dashboard statistics
GET    /reports/sales        # Sales reports
GET    /reports/inventory    # Inventory reports
GET    /reports/financial    # Financial reports
```

### Query Parameters (Pagination, Search, Filter, Sort)

```http
GET /products?page=1&per_page=20&search=paracetamol&sort_by=name&sort_order=asc&filters[category_id]=1&filters[is_active]=true&date_from=2025-01-01&date_to=2025-12-31
```

## 🧪 Testing

### Setup Testing Environment

```bash
# Copy test environment
cp .env.testing.example .env.testing

# Create test database
php artisan migrate --env=testing
php artisan db:seed --env=testing
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run specific test method
php artisan test --filter=test_can_create_product

# Run tests in parallel
php artisan test --parallel
```

### Test Categories

#### Unit Tests
```bash
# Model tests
tests/Unit/Models/ProductTest.php
tests/Unit/Models/UserTest.php

# Service tests
tests/Unit/Services/ProductServiceTest.php
tests/Unit/Services/SalesServiceTest.php
```

#### Feature Tests
```bash
# API endpoint tests
tests/Feature/Api/AuthTest.php
tests/Feature/Api/ProductTest.php
tests/Feature/Api/SalesTest.php

# Integration tests
tests/Feature/Integration/PaymentTest.php
tests/Feature/Integration/InventoryTest.php
```

### Test Coverage Target

- **Overall Coverage**: 80% minimum
- **Controllers**: 90% minimum
- **Models**: 85% minimum
- **Services**: 90% minimum

## ⚡ Performance

### Database Optimization

```php
// Eager loading untuk menghindari N+1 queries
$products = Product::with(['category', 'supplier', 'batches'])->get();

// Database indexing
Schema::table('products', function (Blueprint $table) {
    $table->index(['category_id', 'is_active']);
    $table->index('barcode');
    $table->fullText(['name', 'description']);
});
```

### Caching Strategy

```php
// Cache frequently accessed data
Cache::remember('categories', 3600, function () {
    return Category::all();
});

// Cache user permissions
Cache::remember("user.{$userId}.permissions", 1800, function () use ($user) {
    return $user->getAllPermissions();
});
```

### Queue Jobs

```bash
# Start queue worker
php artisan queue:work

# Start queue worker with specific queue
php artisan queue:work --queue=high,default

# Process failed jobs
php artisan queue:retry all
```

### Performance Monitoring

```bash
# Install Laravel Telescope
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Access Telescope dashboard
http://localhost:8000/telescope
```

## 🔒 Security

### Security Headers

```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    
    return $response;
}
```

### Rate Limiting

```php
// routes/api.php
Route::middleware(['throttle:api'])->group(function () {
    // API routes
});

// Custom rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    // 60 requests per minute
});
```

### Input Validation

```php
// app/Http/Requests/CreateProductRequest.php
public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'barcode' => 'required|string|unique:products',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id'
    ];
}
```

## 🚀 Deployment

### Production Checklist

```bash
# 1. Environment setup
cp .env.production .env

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Database migration
php artisan migrate --force

# 5. Storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 6. Queue worker setup
sudo systemctl enable q-pharmacy-worker
sudo systemctl start q-pharmacy-worker
```

### Docker Deployment

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name api.q-pharmacy.com;
    root /var/www/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 🔧 Troubleshooting

### Common Issues

#### 1. Permission Denied Errors

```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

#### 2. Database Connection Issues

```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test specific connection
php artisan db:show
```

#### 3. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled views
php artisan view:clear
```

#### 4. Queue Job Failures

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Debug Mode

```php
// Enable debug mode in .env
APP_DEBUG=true
LOG_LEVEL=debug

// Use Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev
```

### Logging

```php
// Custom logging
Log::info('User logged in', ['user_id' => $user->id]);
Log::error('Payment failed', ['error' => $exception->getMessage()]);

// Log channels
Log::channel('sales')->info('Sale created', $saleData);
```

---

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [API Documentation](../docs/API_DOCUMENTATION.md)
- [Database Schema](../docs/DATABASE_SCHEMA.md)

---

<div align="center">

**Q-Pharmacy Backend - Built with ❤️ using Laravel**

</div>
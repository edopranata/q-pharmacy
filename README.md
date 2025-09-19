# Q-Pharmacy - Sistem Manajemen Apotek Modern

<div align="center">

![Q-Pharmacy Logo](https://via.placeholder.com/200x80/4CAF50/FFFFFF?text=Q-Pharmacy)

**Sistem manajemen apotek terintegrasi dengan teknologi modern**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![Quasar](https://img.shields.io/badge/Quasar-2.x-blue.svg)](https://quasar.dev)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](#)

</div>

## 📋 Daftar Isi

- [Tentang Q-Pharmacy](#-tentang-q-pharmacy)
  - [Tujuan Proyek](#-tujuan-proyek)
- [Fitur Utama](#-fitur-utama)
  - [Autentikasi & Otorisasi](#-autentikasi--otorisasi)
  - [Manajemen Produk](#-manajemen-produk)
  - [Manajemen Stok](#-manajemen-stok)
  - [Sistem Penjualan](#-sistem-penjualan)
  - [Dashboard & Laporan](#-dashboard--laporan)
  - [User Interface](#-user-interface)
- [Teknologi Stack](#-teknologi-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#️-konfigurasi)
  - [Environment Variables](#environment-variables)
  - [Frontend Configuration](#frontend-configuration)
- [Penggunaan](#-penggunaan)
  - [Default Login Credentials](#default-login-credentials)
  - [Quick Start Guide](#quick-start-guide)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)
- [Support](#-support)

## 🏥 Tentang Q-Pharmacy

Q-Pharmacy adalah sistem manajemen apotek modern yang dirancang untuk membantu apotek dalam mengelola operasional harian dengan efisien. Sistem ini dibangun dengan arsitektur client-server menggunakan Laravel 12 sebagai backend API dan Quasar Framework (Vue.js 3) sebagai frontend.

### 🎯 Tujuan Proyek

- **Efisiensi Operasional**: Mengotomatisasi proses manual apotek
- **Manajemen Stok**: Tracking real-time inventory dan batch obat
- **Kepatuhan Regulasi**: Memenuhi standar regulasi apotek Indonesia
- **User Experience**: Interface yang intuitif dan responsif
- **Skalabilitas**: Arsitektur yang dapat berkembang sesuai kebutuhan

## ✨ Fitur Utama

### 🔐 Autentikasi & Otorisasi
- **Multi-role System**: Admin, Kasir, Supervisor
- **Super-Admin Role**: Akses penuh dengan implementasi best practice
- **Laravel Sanctum**: Token-based authentication
- **Permission Management**: Granular access control

### 💊 Manajemen Produk
- **Master Data Obat**: Informasi lengkap produk farmasi
- **Batch Tracking**: Pelacakan nomor batch dan expired date
- **Barcode Support**: Scanning dan pencarian produk
- **Kategori & Supplier**: Organisasi data yang terstruktur

### 📦 Manajemen Stok
- **Real-time Inventory**: Update stok secara real-time
- **Stock Movement**: Tracking pergerakan stok masuk/keluar
- **Low Stock Alert**: Notifikasi stok menipis
- **Expired Date Monitoring**: Peringatan obat mendekati expired

### 💰 Sistem Penjualan
- **Point of Sale (POS)**: Interface kasir yang user-friendly
- **Multiple Payment Methods**: Cash, transfer, e-wallet
- **Receipt Generation**: Cetak struk otomatis
- **Sales Analytics**: Laporan penjualan komprehensif

### 📊 Dashboard & Laporan
- **Real-time Analytics**: Dashboard dengan data terkini
- **Sales Reports**: Laporan penjualan harian/bulanan
- **Inventory Reports**: Laporan stok dan pergerakan
- **Financial Reports**: Laporan keuangan dan profit

### 🎨 User Interface
- **Responsive Design**: Optimal di desktop, tablet, dan mobile
- **Dark/Light Theme**: Pilihan tema sesuai preferensi
- **Multi-language**: Dukungan Bahasa Indonesia dan Inggris
- **Progressive Web App**: Dapat diinstall seperti aplikasi native

## 🛠 Teknologi Stack

### Backend
- **Framework**: Laravel 12.x
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **API**: RESTful API dengan OpenAPI documentation
- **Testing**: PHPUnit, Pest PHP
- **Queue**: Redis/Database queue
- **Cache**: Redis/Memcached

### Frontend
- **Framework**: Vue.js 3.x
- **UI Framework**: Quasar Framework 2.x
- **State Management**: Pinia
- **HTTP Client**: Axios
- **Testing**: Vitest, Cypress
- **Build Tool**: Vite
- **PWA**: Workbox

### DevOps & Tools
- **Version Control**: Git
- **CI/CD**: GitHub Actions
- **Containerization**: Docker
- **Web Server**: Nginx
- **Process Manager**: PM2
- **Monitoring**: Laravel Telescope

## 📋 Persyaratan Sistem

### Server Requirements
- **PHP**: 8.2 atau lebih tinggi
- **Node.js**: 18.x atau lebih tinggi
- **Database**: MySQL 8.0+ atau PostgreSQL 13+
- **Web Server**: Nginx atau Apache
- **Memory**: Minimum 2GB RAM
- **Storage**: Minimum 10GB free space

### Development Requirements
- **Composer**: 2.x
- **NPM/Yarn**: Latest stable version
- **Git**: 2.x
- **Docker**: (Optional) untuk development environment

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/edopranata/q-pharmacy.git
cd q-pharmacy
```

### 2. Backend Setup

```bash
# Masuk ke direktori backend
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Install Laravel Passport (jika menggunakan)
php artisan passport:install

# Start development server
php artisan serve
```

### 3. Frontend Setup

```bash
# Masuk ke direktori frontend
cd frontend/web

# Install dependencies
npm install
# atau
yarn install

# Start development server
npm run dev
# atau
yarn dev
```

## ⚙️ Konfigurasi

### Environment Variables

Konfigurasi file `.env` untuk backend:

```env
# Application
APP_NAME="Q-Pharmacy"
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=q_pharmacy
DB_USERNAME=root
DB_PASSWORD=

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Frontend Configuration

Konfigurasi file `quasar.config.js`:

```javascript
module.exports = {
  build: {
    env: {
      API_BASE_URL: 'http://localhost:8000/api/v1'
    }
  }
}
```

## 📖 Penggunaan

### Default Login Credentials

**Super Admin:**
- Email: `superadmin@q-pharmacy.com`
- Password: `password`

**Admin:**
- Email: `admin@q-pharmacy.com`
- Password: `password`

**Kasir:**
- Email: `kasir@q-pharmacy.com`
- Password: `password`

### Quick Start Guide

1. **Login** menggunakan kredensial di atas
2. **Setup Master Data**: Tambahkan kategori, supplier, dan unit
3. **Input Produk**: Tambahkan data obat dan produk farmasi
4. **Manage Stock**: Input stok awal dan batch produk
5. **Start Selling**: Mulai transaksi penjualan di POS

## 📚 API Documentation

API documentation tersedia di:
- **Development**: http://localhost:8000/api/documentation
- **Postman Collection**: [Download](docs/postman_collection.json)
- **OpenAPI Spec**: [docs/api-spec.yaml](docs/api-spec.yaml)

Untuk detail lengkap, lihat [API Documentation](docs/API_DOCUMENTATION.md)

## 🧪 Testing

### Backend Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=ProductTest
```

### Frontend Testing

```bash
# Unit tests
npm run test:unit

# E2E tests
npm run test:e2e

# Coverage report
npm run test:coverage
```

Target coverage: **80%** minimum

Untuk detail lengkap, lihat [Testing Strategy](docs/TESTING.md)

## 🚀 Deployment

### Production Deployment

1. **Server Setup**
   ```bash
   # Install dependencies
   sudo apt update
   sudo apt install nginx mysql-server php8.2-fpm redis-server
   ```

2. **Application Deployment**
   ```bash
   # Clone and setup
   git clone https://github.com/edopranata/q-pharmacy.git
   cd q-pharmacy
   
   # Backend
   cd backend
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Frontend
   cd ../frontend/web
   npm ci
   npm run build
   ```

3. **Web Server Configuration**
   - Nginx configuration: [docs/nginx.conf](docs/nginx.conf)
   - SSL setup dengan Let's Encrypt
   - PM2 untuk process management

Untuk detail lengkap, lihat [Deployment Guide](docs/DEPLOYMENT.md)

## 🤝 Kontribusi

Kami menyambut kontribusi dari komunitas! Silakan baca [Contributing Guidelines](CONTRIBUTING.md) untuk informasi detail.

### Development Workflow

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Code Standards

- **Backend**: PSR-12, Laravel best practices
- **Frontend**: ESLint, Vue.js style guide
- **Testing**: Minimum 80% coverage
- **Documentation**: Update docs untuk setiap perubahan

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 🆘 Support

### Documentation

- [Architecture Guide](docs/ARCHITECTURE.md)
- [Database Schema](docs/DATABASE_SCHEMA.md)
- [Security Policy](docs/SECURITY.md)
- [Performance Guide](docs/PERFORMANCE.md)
- [Roadmap](docs/ROADMAP.md)

### Community

- **Issues**: [GitHub Issues](https://github.com/edopranata/q-pharmacy/issues)
- **Discussions**: [GitHub Discussions](https://github.com/edopranata/q-pharmacy/discussions)
- **Email**: support@q-pharmacy.com

### Commercial Support

Untuk dukungan komersial dan enterprise, hubungi: enterprise@q-pharmacy.com

---

<div align="center">

**Dibuat dengan ❤️ oleh Tim Q-Pharmacy**

[Website](https://q-pharmacy.com) • [Documentation](docs/) • [API](docs/API_DOCUMENTATION.md) • [Changelog](docs/CHANGELOG.md)

</div>
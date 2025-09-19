# Q-Pharmacy Documentation

## 📋 Daftar Isi

- [Overview](#overview)
- [🏗️ Arsitektur Sistem](#️-arsitektur-sistem)
- [📚 Struktur Dokumentasi](#-struktur-dokumentasi)
  - [📖 Dokumentasi Umum](#-dokumentasi-umum)
  - [🔧 Dokumentasi Backend](#-dokumentasi-backend)
  - [🎨 Dokumentasi Frontend](#-dokumentasi-frontend)
- [🚀 Quick Start](#-quick-start)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Docker Setup (Alternative)](#docker-setup-alternative)
- [🏢 Fitur Utama](#-fitur-utama)
  - [👥 Manajemen Pengguna](#-manajemen-pengguna)
  - [💊 Manajemen Produk](#-manajemen-produk)
  - [📦 Manajemen Inventori](#-manajemen-inventori)
  - [🛒 Point of Sale (POS)](#-point-of-sale-pos)
  - [📊 Pelaporan & Analytics](#-pelaporan--analytics)
  - [🔧 Administrasi](#-administrasi)
- [🛠️ Development Guidelines](#️-development-guidelines)
  - [Code Standards](#code-standards)
  - [Git Workflow](#git-workflow)
  - [Testing Strategy](#testing-strategy)
- [🔐 Security](#-security)
  - [Authentication](#authentication)
  - [Authorization](#authorization)
  - [Data Protection](#data-protection)
- [📈 Performance](#-performance)
  - [Backend Optimization](#backend-optimization)
  - [Frontend Optimization](#frontend-optimization)
- [🚀 Deployment](#-deployment)
  - [Environments](#environments)
  - [CI/CD Pipeline](#cicd-pipeline)
  - [Monitoring](#monitoring)
- [📞 Support & Maintenance](#-support--maintenance)
  - [Issue Tracking](#issue-tracking)
  - [Maintenance Schedule](#maintenance-schedule)
- [🤝 Contributing](#-contributing)
  - [Development Process](#development-process)
  - [Code Review Checklist](#code-review-checklist)
- [📋 API Reference](#-api-reference)
  - [Base Information](#base-information)
  - [Core Endpoints](#core-endpoints)
  - [Response Format](#response-format)
- [🔧 Configuration](#-configuration)
  - [Environment Variables](#environment-variables)
- [📊 Monitoring & Analytics](#-monitoring--analytics)
  - [Application Metrics](#application-metrics)
  - [Business Metrics](#business-metrics)
  - [Alerting](#alerting)
- [🔄 Backup & Recovery](#-backup--recovery)
  - [Backup Strategy](#backup-strategy)
  - [Recovery Procedures](#recovery-procedures)
- [📚 Learning Resources](#-learning-resources)
  - [Documentation](#documentation)
  - [Tutorials](#tutorials)
- [🆘 Troubleshooting](#-troubleshooting)
  - [Common Issues](#common-issues)
  - [Debug Tools](#debug-tools)
- [📞 Contact & Support](#-contact--support)
  - [Development Team](#development-team)
  - [Support Channels](#support-channels)
- [📄 License](#-license)

## Overview

Selamat datang di dokumentasi lengkap Q-Pharmacy - sistem manajemen apotek modern yang dibangun dengan teknologi terkini. Dokumentasi ini menyediakan panduan komprehensif untuk developer, administrator, dan pengguna sistem.

**Current Status**: Foundation Phase Complete (~40% Progress)  
**Last Updated**: 20 September 2025  
**Version**: 0.4.0

### 🎯 Current Development Status

#### ✅ Completed Features
- **Authentication System**: Laravel Sanctum dengan role-based access control
- **Master Data Management**: Categories, Suppliers, Units dengan CRUD lengkap
- **Frontend Foundation**: Quasar UI dengan responsive design
- **API Infrastructure**: RESTful API dengan pagination, search, filter, sort

#### 🔄 In Development
- **User Management**: CRUD untuk users dan roles management
- **Permission System**: Advanced permission assignment dan management

#### ⏳ Planned Features
- **Product Management**: Manajemen produk dengan barcode support
- **Inventory System**: Stock tracking dengan batch dan expiry management
- **Point of Sale**: Sistem POS untuk transaksi penjualan

## 🏗️ Arsitektur Sistem

Q-Pharmacy dibangun dengan arsitektur modern yang terdiri dari:

- **Backend**: Laravel 10.x dengan PHP 8.2+
- **Frontend**: Vue.js 3 dengan Quasar Framework
- **Database**: MySQL 8.0+ / PostgreSQL 14+
- **Cache**: Redis 6.x+
- **Queue**: Laravel Queue dengan Redis
- **Storage**: Local/S3 untuk file management

## 📚 Struktur Dokumentasi

### 📖 Dokumentasi Umum

| Dokumen | Deskripsi | Target Audience |
|---------|-----------|----------------|
| [ROADMAP.md](./ROADMAP.md) | Roadmap pengembangan dan implementasi | Project Manager, Developer |
| [DEVELOPMENT_WORKFLOW.md](./DEVELOPMENT_WORKFLOW.md) | Workflow pengembangan dan guidelines | Developer, Tech Lead |

### 🔧 Dokumentasi Backend

| Dokumen | Deskripsi | Target Audience |
|---------|-----------|----------------|
| [Backend README](../backend/docs/README.md) | Overview dan quick start backend | Developer |
| [API Documentation](../backend/docs/API.md) | Dokumentasi lengkap REST API | Frontend Developer, Integrator |
| [Architecture](../backend/docs/ARCHITECTURE.md) | Arsitektur dan design patterns backend | Senior Developer, Architect |
| [Deployment](../backend/docs/DEPLOYMENT.md) | Panduan deployment backend | DevOps, System Admin |
| [Testing](TESTING.md) | Strategi dan panduan testing komprehensif | Developer, QA |

### 🎨 Dokumentasi Frontend

| Dokumen | Deskripsi | Target Audience |
|---------|-----------|----------------|
| [Frontend README](../frontend/web/docs/README.md) | Overview dan quick start frontend | Frontend Developer |
| [Components](../frontend/web/docs/COMPONENTS.md) | Dokumentasi komponen Vue.js | Frontend Developer |
| [Architecture](../frontend/web/docs/ARCHITECTURE.md) | Arsitektur frontend dan patterns | Frontend Developer, Architect |
| [Deployment](../frontend/web/docs/DEPLOYMENT.md) | Panduan deployment frontend | DevOps, Frontend Developer |
| [Testing](../frontend/web/docs/TESTING.md) | Strategi testing frontend | Frontend Developer, QA |

## 🚀 Quick Start

### Prerequisites

- **PHP 8.2+** dengan extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Node.js 18.x+** dengan npm 9.x+
- **MySQL 8.0+** atau **PostgreSQL 14+**
- **Redis 6.x+**
- **Composer 2.x**
- **Git 2.x+**

### Installation

#### 1. Clone Repository

```bash
git clone https://github.com/edopranata/q-pharmacy.git
cd q-pharmacy
```

#### 2. Backend Setup

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

#### 3. Frontend Setup

```bash
cd frontend/web
npm install
cp .env.example .env.local
npm run dev
```

#### 4. Access Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api/documentation

### Docker Setup (Alternative)

```bash
# Start all services
docker-compose up -d

# Run initial setup
docker-compose exec backend php artisan migrate --seed
docker-compose exec frontend npm install
```

## 🏢 Fitur Utama

### 👥 Manajemen Pengguna
- Autentikasi dan otorisasi berbasis permission
- Manajemen profil pengguna
- Permission-based access control (PBAC)
- Activity logging dan audit trail

### 💊 Manajemen Produk
- Katalog produk obat dan alat kesehatan
- Kategori dan klasifikasi produk
- Manajemen harga dan diskon
- Barcode scanning
- Batch tracking dan expiry management

### 📦 Manajemen Inventori
- Real-time stock tracking
- Stock opname dan adjustment
- Automatic reorder points
- Stock movement history
- Low stock dan expiring alerts

### 🛒 Point of Sale (POS)
- Interface kasir yang intuitif
- Multiple payment methods
- Receipt printing
- Transaction history
- Real-time inventory update

### 📊 Pelaporan & Analytics
- Dashboard analytics real-time
- Sales reports dengan filtering
- Inventory reports
- Financial reports
- Export data ke Excel/PDF

### 🔧 Administrasi
- System configuration
- Master data management
- User activity monitoring
- System health checks
- Backup dan restore

## 🛠️ Development Guidelines

### Code Standards

- **Backend**: PSR-12, Laravel conventions
- **Frontend**: Vue.js 3 Composition API, Quasar guidelines
- **Database**: Snake_case naming, proper indexing
- **API**: RESTful design, consistent response format

### Git Workflow

- **Main Branch**: `main` (production)
- **Development Branch**: `develop`
- **Feature Branches**: `feature/feature-name`
- **Hotfix Branches**: `hotfix/issue-description`

### Testing Strategy

- **Unit Tests**: 80%+ coverage
- **Integration Tests**: Critical paths
- **E2E Tests**: Main user journeys
- **API Tests**: All endpoints

## 🔐 Security

### Authentication
- Laravel Sanctum untuk API authentication
- JWT tokens dengan refresh mechanism
- Multi-factor authentication (MFA)
- Session management

### Authorization
- Role-based permissions
- Resource-level access control
- API rate limiting
- CORS configuration

### Data Protection
- Input validation dan sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Data encryption at rest

## 📈 Performance

### Backend Optimization
- Database query optimization
- Redis caching strategy
- Queue processing
- API response caching

### Frontend Optimization
- Code splitting
- Lazy loading
- Image optimization
- Bundle size optimization

## 🚀 Deployment

### Environments

- **Development**: Local development
- **Staging**: Pre-production testing
- **Production**: Live application

### CI/CD Pipeline

- Automated testing
- Code quality checks
- Security scanning
- Automated deployment

### Monitoring

- Application performance monitoring
- Error tracking
- Log aggregation
- Health checks

## 📞 Support & Maintenance

### Issue Tracking

- **Bug Reports**: GitHub Issues
- **Feature Requests**: GitHub Discussions
- **Security Issues**: security@q-pharmacy.com

### Maintenance Schedule

- **Daily**: Automated backups
- **Weekly**: Security updates
- **Monthly**: Performance reviews
- **Quarterly**: Major updates

## 🤝 Contributing

### Development Process

1. Fork repository
2. Create feature branch
3. Implement changes
4. Write tests
5. Submit pull request
6. Code review
7. Merge to develop

### Code Review Checklist

- [ ] Code follows style guidelines
- [ ] Tests are included and passing
- [ ] Documentation is updated
- [ ] Security considerations addressed
- [ ] Performance impact assessed

## 📋 API Reference

### Base Information

- **Base URL**: `https://api.q-pharmacy.com/v1`
- **Authentication**: Bearer Token
- **Content-Type**: `application/json`
- **Rate Limit**: 1000 requests/hour

### Core Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/auth/login` | POST | User authentication |
| `/auth/logout` | POST | User logout |
| `/products` | GET | List products |
| `/products` | POST | Create product |
| `/inventory` | GET | Inventory status |
| `/sales` | POST | Process sale |
| `/reports` | GET | Generate reports |

### Response Format

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation successful",
  "meta": {
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "total_items": 100
    }
  }
}
```

## 🔧 Configuration

### Environment Variables

#### Backend (.env)

```env
# Application
APP_NAME="Q-Pharmacy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://q-pharmacy.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=q_pharmacy
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
```

#### Frontend (.env.local)

```env
# API Configuration
VITE_API_BASE_URL=https://api.q-pharmacy.com
VITE_APP_NAME="Q-Pharmacy"
VITE_APP_VERSION=1.0.0

# Features
VITE_ENABLE_PWA=true
VITE_ENABLE_ANALYTICS=true

# External Services
VITE_GOOGLE_MAPS_API_KEY=
VITE_SENTRY_DSN=
```

## 📊 Monitoring & Analytics

### Application Metrics

- Response times
- Error rates
- Throughput
- Database performance
- Cache hit rates

### Business Metrics

- Daily sales
- Inventory turnover
- User activity
- Popular products
- Revenue trends

### Alerting

- System downtime
- High error rates
- Performance degradation
- Security incidents
- Low inventory alerts

## 🔄 Backup & Recovery

### Backup Strategy

- **Database**: Daily automated backups
- **Files**: Incremental backups
- **Configuration**: Version controlled
- **Retention**: 30 days

### Recovery Procedures

1. Assess damage scope
2. Restore from latest backup
3. Verify data integrity
4. Test system functionality
5. Resume operations

## 📚 Learning Resources

### Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [Quasar Framework](https://quasar.dev/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Tutorials

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Best Practices](https://vuejs.org/style-guide/)
- [API Design Guidelines](https://github.com/microsoft/api-guidelines)

## 🆘 Troubleshooting

### Common Issues

#### Backend Issues

**Database Connection Error**
```bash
php artisan config:cache
php artisan migrate:status
```

**Permission Issues**
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

**Cache Issues**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

#### Frontend Issues

**Build Errors**
```bash
rm -rf node_modules package-lock.json
npm install
```

**Development Server Issues**
```bash
lsof -ti:3000
kill -9 $(lsof -ti:3000)
```

### Debug Tools

- **Backend**: Laravel Debugbar, Telescope
- **Frontend**: Vue DevTools, Browser DevTools
- **API**: Postman, Insomnia
- **Database**: phpMyAdmin, Adminer

## 📞 Contact & Support

### Development Team

- **Tech Lead**: tech-lead@q-pharmacy.com
- **Backend Team**: backend@q-pharmacy.com
- **Frontend Team**: frontend@q-pharmacy.com
- **DevOps Team**: devops@q-pharmacy.com

### Support Channels

- **Documentation**: [GitHub Wiki](https://github.com/edopranata/q-pharmacy/wiki)
- **Issues**: [GitHub Issues](https://github.com/edopranata/q-pharmacy/issues)
- **Discussions**: [GitHub Discussions](https://github.com/edopranata/q-pharmacy/discussions)
- **Slack**: #q-pharmacy-dev

## 📄 License

Q-Pharmacy is proprietary software. All rights reserved.

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Maintained by**: Q-Pharmacy Development Team

---

> 💡 **Tip**: Bookmark halaman ini dan gunakan sebagai starting point untuk navigasi dokumentasi. Semua link akan membawa Anda ke dokumentasi yang lebih detail sesuai kebutuhan.
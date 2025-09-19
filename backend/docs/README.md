# Backend Documentation

## Overview

Dokumentasi lengkap untuk backend Q-Pharmacy yang dibangun menggunakan Laravel 12 dan PHP 8.2+. Sistem ini menyediakan API RESTful untuk manajemen apotek yang komprehensif.

## Struktur Dokumentasi

```
backend/docs/
├── README.md                 # Dokumentasi utama (file ini)
├── API.md                   # Dokumentasi API endpoints
├── ARCHITECTURE.md          # Arsitektur sistem backend
├── DATABASE.md              # Skema dan migrasi database
├── AUTHENTICATION.md        # Sistem autentikasi dan otorisasi
├── DEPLOYMENT.md            # Panduan deployment dan konfigurasi
├── TESTING.md               # Panduan testing dan quality assurance
├── PERFORMANCE.md           # Optimasi performa dan monitoring
└── examples/                # Contoh implementasi
    ├── api-requests/        # Contoh request API
    ├── middleware/          # Contoh custom middleware
    └── services/            # Contoh service classes
```

## Quick Start

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL 8.0
- Redis (untuk caching)
- Node.js (untuk asset compilation)

### Installation

```bash
# Clone repository
git clone <repository-url>
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

## Core Features

### 1. Authentication & Authorization
- Laravel Sanctum untuk API authentication
- Spatie Laravel Permission untuk role-based access control
- Support untuk multiple user roles (Admin, Kasir)

### 2. Master Data Management
- Manajemen kategori produk
- Manajemen supplier
- Manajemen unit satuan
- Audit trail untuk semua perubahan data

### 3. Product Management
- CRUD produk dengan barcode support
- Manajemen varian produk
- Upload dan optimasi gambar produk
- Advanced search dan filtering

### 4. Inventory Management
- Real-time stock tracking
- Batch management dengan FIFO/LIFO
- Expiry date monitoring
- Automated low stock alerts

### 5. Point of Sale (POS)
- Transaction processing
- Multiple payment methods
- Tax calculation
- Discount management
- Receipt generation

### 6. Reporting & Analytics
- Sales reports
- Inventory reports
- Financial reports
- Dashboard analytics

## API Overview

API menggunakan RESTful conventions dengan JSON responses. Semua endpoints memerlukan authentication kecuali login/register.

### Base URL
```
Development: http://localhost:8000/api
Production: https://your-domain.com/api
```

### Authentication
```bash
# Login
POST /api/auth/login

# Logout
POST /api/auth/logout

# Get user profile
GET /api/auth/user
```

### Response Format
```json
{
  "success": true,
  "message": "Success message",
  "data": {
    // Response data
  },
  "meta": {
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "per_page": 15,
      "total": 150
    }
  }
}
```

## Development Guidelines

### Code Standards
- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Write comprehensive PHPDoc comments
- Implement proper error handling

### Testing
- Write unit tests for all business logic
- Create feature tests for API endpoints
- Maintain minimum 80% code coverage
- Use factories for test data generation

### Security
- Validate all input data
- Use Laravel's built-in CSRF protection
- Implement rate limiting for API endpoints
- Regular security audits

## Performance Considerations

### Database Optimization
- Use database indexes appropriately
- Implement query optimization
- Use eager loading to prevent N+1 queries
- Regular database maintenance

### Caching Strategy
- Redis for session and cache storage
- API response caching for static data
- Database query result caching
- File-based caching for configuration

### Monitoring
- Application performance monitoring
- Database query monitoring
- Error tracking and logging
- Resource usage monitoring

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in .env
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Denied Errors**
   - Check file permissions for storage/ and bootstrap/cache/
   - Run: `chmod -R 775 storage bootstrap/cache`

3. **Composer Dependencies**
   - Clear composer cache: `composer clear-cache`
   - Update dependencies: `composer update`

4. **Cache Issues**
   - Clear application cache: `php artisan cache:clear`
   - Clear config cache: `php artisan config:clear`
   - Clear route cache: `php artisan route:clear`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## Support

Untuk pertanyaan atau bantuan:
- Email: dev-team@q-pharmacy.com
- Slack: #backend-support
- Documentation: [Link to detailed docs]

## License

MIT License - see LICENSE file for details.
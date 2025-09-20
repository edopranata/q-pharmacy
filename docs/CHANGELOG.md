# Changelog

Semua perubahan penting pada proyek Q-Pharmacy akan didokumentasikan dalam file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned Features
- Manajemen produk obat dengan batch tracking
- Sistem penjualan dan transaksi (POS)
- Dashboard analitik dan laporan
- Manajemen stok dengan notifikasi expired
- Sistem backup dan restore data
- Multi-language support (ID/EN)
- Advanced reporting dan analytics

### In Development
- Manajemen produk obat dengan barcode support
- Sistem inventory tracking dengan batch management
- Product search dan filtering capabilities
- Basic POS interface foundation

## [0.5.0] - 2025-01-15

### Added
- **User & Role Management System**
  - Users CRUD dengan server-side pagination, search, filter, sort, order
  - Roles management dengan permission assignment
  - Enhanced user activity monitoring dan audit logging
  - User profile management dengan avatar upload/delete
  - Permission management UI components
  - Advanced user profile features

- **System Optimizations**
  - Notification system optimization (removed duplicate notifications)
  - Improved error handling dan debugging
  - Enhanced API security dan validation
  - Better user experience dengan consistent notifications

- **Frontend Enhancements**
  - User management interface dengan advanced features
  - Role management interface dengan permission assignment
  - Avatar management dengan upload/delete functionality
  - Optimized notification system across all components

### Changed
- Centralized notification handling di stores
- Improved user profile management
- Enhanced role-based access control
- Better error logging untuk debugging

### Fixed
- Duplicate notifications di semua management pages
- User profile update issues
- Avatar upload/delete functionality
- Permission assignment bugs

## [0.4.0] - 2025-09-20

### Added
- **Master Data Management System**
  - Categories CRUD dengan server-side pagination, search, filter, sort, order
  - Suppliers CRUD dengan server-side pagination, search, filter, sort, order
  - Units CRUD dengan server-side pagination, search, filter, sort, order
  - Comprehensive data validation dan error handling
  - Responsive UI components untuk semua master data

- **Enhanced Authentication**
  - Password reset functionality (admin/permission-based)
  - User activity tracking dan audit logging
  - Enhanced API middleware dan security

- **Frontend Improvements**
  - Advanced data tables dengan server-side features
  - Form validation dan error handling
  - Theme management system
  - Component-based architecture

### Changed
- Enhanced API endpoints dengan comprehensive pagination, search, filter, sort
- Improved user interface dengan modern Quasar components
- Optimized state management dengan Pinia stores
- Better error handling dan user feedback

### Security
- Enhanced API security dengan proper middleware
- Comprehensive input validation dan sanitization
- Audit logging untuk semua data changes
- Role-based access control implementation

## [0.3.0] - 2025-11-15

### Added
- **Authentication System**
  - Laravel Sanctum authentication implementation
  - User registration dan login API endpoints
  - Role-based access control dengan Spatie Laravel Permission
  - Route guards dan permission-based navigation

- **Frontend Foundation**
  - Login dan Register pages dengan Quasar UI
  - Authentication store implementation dengan Pinia
  - Router guards untuk protected routes
  - User profile management interface
  - Role-based navigation system

### Technical
- API rate limiting dan middleware implementation
- Database architecture dengan proper migrations
- Audit logging system (AuditLog model)
- User activity monitoring (UserActivity model)

### Security
- Laravel Sanctum untuk API authentication
- Role dan permission management
- Protected API endpoints
- Secure session management

## [1.0.0] - 2025-09-15

### Added
- Setup awal proyek Q-Pharmacy
- Instalasi Laravel 12 dengan Laravel Permission
- Instalasi Quasar Framework untuk frontend
- Konfigurasi database schema
- Struktur arsitektur sistem
- Setup development environment

### Technical
- Laravel 12.x dengan PHP 8.2+
- Quasar Framework 2.16.0 dengan Vue.js 3.5.20
- MySQL database dengan SQLite untuk development
- Spatie Laravel Permission untuk role management
- Laravel Sanctum untuk API authentication
- Axios untuk HTTP client
- Pinia untuk state management

### Infrastructure
- Docker containerization setup
- CI/CD pipeline configuration
- Testing environment setup
- Code quality tools integration

---

## Template untuk Release Baru

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Added
- Fitur baru yang ditambahkan

### Changed
- Perubahan pada fitur yang sudah ada

### Deprecated
- Fitur yang akan dihapus di versi mendatang

### Removed
- Fitur yang dihapus

### Fixed
- Bug fixes

### Security
- Perbaikan keamanan
```

## Konvensi Versioning

- **MAJOR** (X.0.0): Breaking changes yang tidak kompatibel dengan versi sebelumnya
- **MINOR** (0.X.0): Penambahan fitur baru yang backward compatible
- **PATCH** (0.0.X): Bug fixes yang backward compatible

## Kategori Perubahan

- **Added**: Fitur baru
- **Changed**: Perubahan pada fitur yang sudah ada
- **Deprecated**: Fitur yang akan dihapus di masa depan
- **Removed**: Fitur yang dihapus
- **Fixed**: Bug fixes
- **Security**: Perbaikan keamanan
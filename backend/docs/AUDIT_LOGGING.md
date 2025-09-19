# Audit Logging System

Sistem audit logging telah diimplementasikan untuk melacak semua perubahan data dan aktivitas autentikasi dalam aplikasi.

## Komponen yang Diimplementasikan

### 1. Database
- **Tabel**: `audit_logs`
- **Migration**: `2025_09_16_024132_create_audit_logs_table.php`
- **Kolom**: user_id, action, model_type, model_id, old_values, new_values, ip_address, user_agent, description, timestamps

### 2. Model dan Service
- **Model**: `App\Models\AuditLog`
- **Service**: `App\Services\AuditLogger`
- **Trait**: `App\Traits\Auditable`

### 3. Event Listeners
- **Listener**: `App\Listeners\AuditAuthListener`
- **Provider**: `App\Providers\EventServiceProvider`

### 4. Middleware
- **Middleware**: `App\Http\Middleware\AuditMiddleware`
- Melacak semua API requests yang terautentikasi

### 5. Controller dan Routes
- **Controller**: `App\Http\Controllers\Api\AuditLogController`
- **Routes**: `/api/audit-logs/*` (Admin only)

### 6. Console Command
- **Command**: `php artisan audit:clean`
- Membersihkan audit logs lama (default: 90 hari)

## Fitur Audit Logging

### Automatic Logging
- **Model Events**: Created, Updated, Deleted (melalui trait Auditable)
- **Authentication Events**: Login, Logout, Failed Login, Registration, dll
- **API Requests**: Semua request API yang terautentikasi

### Manual Logging
```php
use App\Services\AuditLogger;

// Log custom action
AuditLogger::log('custom_action', $model, $oldValues, $newValues, 'Description');

// Log authentication event
AuditLogger::logAuth('login', $user, 'User logged in successfully');
```

## API Endpoints (Admin Only)

### GET /api/audit-logs
Mendapatkan daftar audit logs dengan filtering:
- `action`: Filter berdasarkan aksi
- `user_id`: Filter berdasarkan user
- `model_type` & `model_id`: Filter berdasarkan model
- `start_date` & `end_date`: Filter berdasarkan tanggal
- `per_page`: Jumlah item per halaman

### GET /api/audit-logs/recent
Mendapatkan audit logs terbaru:
- `limit`: Jumlah maksimal logs (default: 50)

### GET /api/audit-logs/for-model
Mendapatkan audit logs untuk model tertentu:
- `model_type`: Tipe model (required)
- `model_id`: ID model (required)

### GET /api/audit-logs/{id}
Mendapatkan detail audit log tertentu

### DELETE /api/audit-logs/clean
Membersihkan audit logs lama:
- `days_to_keep`: Jumlah hari untuk disimpan (default: 90)

## Model yang Menggunakan Audit Logging

Semua model berikut telah menggunakan trait `Auditable`:
- User
- Category
- Supplier
- Unit

## Console Commands

### Membersihkan Audit Logs Lama
```bash
# Hapus logs lebih dari 90 hari (default)
php artisan audit:clean

# Hapus logs lebih dari 30 hari
php artisan audit:clean --days=30
```

## Security Features

- **IP Address Tracking**: Setiap audit log mencatat IP address
- **User Agent Tracking**: Browser/client information
- **Authentication Required**: API endpoints memerlukan autentikasi
- **Admin Only Access**: Hanya admin yang dapat mengakses audit logs
- **Automatic Cleanup**: Command untuk membersihkan logs lama

## Data yang Dicatat

### Model Changes
- Old values (nilai sebelum perubahan)
- New values (nilai setelah perubahan)
- Action type (created, updated, deleted)
- User yang melakukan perubahan
- Timestamp

### Authentication Events
- Login/logout activities
- Failed login attempts
- User registration
- Password resets
- Email verification

### API Requests
- Endpoint yang diakses
- HTTP method
- User yang mengakses
- IP address dan user agent
- Timestamp

## Monitoring dan Maintenance

1. **Regular Cleanup**: Jalankan `php artisan audit:clean` secara berkala
2. **Storage Monitoring**: Monitor ukuran tabel audit_logs
3. **Performance**: Index telah ditambahkan untuk query yang optimal
4. **Retention Policy**: Default 90 hari, dapat disesuaikan

## Troubleshooting

### Audit Logs Tidak Terbuat
1. Pastikan trait `Auditable` sudah ditambahkan ke model
2. Periksa apakah migration sudah dijalankan
3. Pastikan EventServiceProvider terdaftar di bootstrap/providers.php

### Performance Issues
1. Jalankan cleanup command secara berkala
2. Monitor ukuran tabel audit_logs
3. Pertimbangkan untuk mengurangi retention period
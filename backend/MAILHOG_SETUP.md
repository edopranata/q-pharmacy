# MailHog Integration Setup

## Overview
MailHog telah berhasil diintegrasikan dengan backend Laravel untuk testing email functionality dalam development environment.

## Configuration

### Environment Variables (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@q-potek.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### Laravel Mail Config (config/mail.php)
SMTP mailer sudah dikonfigurasi dengan encryption setting yang mendukung MailHog.

## Features Implemented

### 1. Password Reset Notification
- **Class**: `App\Notifications\PasswordResetNotification`
- **Method**: `UserController@resetPassword`
- **Route**: `POST /api/app/management/users/{user}/reset-password`

### 2. Test Commands

#### Email Test Command
```bash
php artisan mail:test [email]
```
Mengirim email test sederhana ke alamat yang ditentukan.

#### Password Reset Test Command
```bash
php artisan password:reset-test [email]
```
Melakukan reset password dan mengirim notifikasi email.

## Usage Examples

### 1. Test Email Functionality
```bash
# Test dengan email default
php artisan mail:test

# Test dengan email spesifik
php artisan mail:test user@example.com
```

### 2. Test Password Reset
```bash
# Reset password untuk admin
php artisan password:reset-test admin@apotek.com

# Reset password untuk user lain
php artisan password:reset-test kasir@apotek.com
```

### 3. API Endpoint Usage
```bash
curl -X POST "http://localhost:8000/api/app/management/users/1/reset-password" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"send_email": true}'
```

## MailHog Access
- **Web Interface**: http://localhost:8025
- **SMTP Server**: localhost:1025

## Email Templates
Password reset email includes:
- User greeting with name
- New password information
- Security reminder
- Professional formatting

## Security Notes
- MailHog hanya untuk development environment
- Jangan gunakan di production
- Password baru di-generate secara random (12 karakter)
- Email berisi password baru untuk kemudahan testing

## Troubleshooting

### Common Issues
1. **Connection refused**: Pastikan MailHog container berjalan di port 1025
2. **Email tidak terkirim**: Periksa log Laravel dan MailHog
3. **Permission denied**: Pastikan user memiliki permission `app.management.users.update`

### Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# MailHog logs (jika menggunakan Docker)
docker logs mailhog-container
```
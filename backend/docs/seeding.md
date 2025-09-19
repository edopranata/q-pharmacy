# Database Seeding Guide

Panduan lengkap untuk menggunakan database seeder dengan factory pattern yang fleksibel.

## Overview

Sistem seeding ini menggunakan Laravel Factory pattern untuk menghasilkan data dummy yang realistis dengan jumlah yang dapat dikonfigurasi melalui environment variables.

## Struktur Seeder

### 1. DatabaseSeeder (Main Seeder)
Seeder utama yang menjalankan semua seeder lainnya dalam urutan yang benar:
- RolePermissionSeeder
- MasterDataSeeder  
- UserRoleSeeder

### 2. RolePermissionSeeder
Membuat roles dan permissions untuk sistem:
- **admin**: Akses penuh ke semua fitur
- **manager**: Akses moderate untuk manajemen
- **kasir**: Akses terbatas untuk POS dan penjualan

### 3. MasterDataSeeder
Menggunakan factory untuk membuat data master:
- Categories (Kategori produk)
- Suppliers (Pemasok)
- Units (Satuan produk)

### 4. UserRoleSeeder
Membuat users dengan role assignment:
- 1 Admin user
- 2 Manager users
- 3 Kasir users
- 5 Users tanpa role khusus

## Konfigurasi Environment

Tambahkan variabel berikut ke file `.env` untuk mengatur jumlah data:

```env
# Jumlah data master yang akan dibuat
SEED_CATEGORY_COUNT=10
SEED_SUPPLIER_COUNT=8
SEED_UNIT_COUNT=10
```

### Default Values
- `SEED_CATEGORY_COUNT`: 10 categories
- `SEED_SUPPLIER_COUNT`: 8 suppliers
- `SEED_UNIT_COUNT`: 10 units

## Cara Menjalankan Seeder

### 1. Jalankan Semua Seeder
```bash
php artisan db:seed
```

### 2. Jalankan Seeder Tertentu
```bash
# Hanya role dan permission
php artisan db:seed --class=RolePermissionSeeder

# Hanya master data
php artisan db:seed --class=MasterDataSeeder

# Hanya users dengan roles
php artisan db:seed --class=UserRoleSeeder
```

### 3. Fresh Migration + Seeding
```bash
php artisan migrate:fresh --seed
```

## Factory Features

### CategoryFactory
- Menghasilkan nama kategori realistis dari daftar kategori farmasi
- Generate kode unik dengan format "CAT001", "CAT002", dst
- 85% kemungkinan status aktif
- State methods: `active()`, `inactive()`

### SupplierFactory
- Menghasilkan nama perusahaan farmasi Indonesia
- Data kontak lengkap (nama, telepon, email, alamat)
- Generate kode unik dengan format "SUP001", "SUP002", dst
- 90% kemungkinan status aktif
- State methods: `active()`, `inactive()`

### UnitFactory
- Menghasilkan satuan produk farmasi (tablet, kapsul, botol, dll)
- Symbol yang sesuai untuk setiap unit
- Generate kode unik dengan format "UNIT001", "UNIT002", dst
- 95% kemungkinan status aktif
- State methods: `active()`, `inactive()`

### UserFactory
- Generate nama dan email unik
- Password default: "password"
- Email terverifikasi secara default
- State methods: `unverified()`

## Data Esensial

Seeder akan selalu membuat data esensial berikut (tidak terpengaruh konfigurasi jumlah):

### Categories
- Obat Bebas (OTC)
- Obat Keras (ETH)
- Suplemen (SUP)

### Units
- Tablet (TAB)
- Kapsul (CAP)
- Botol (BTL)
- Box (BOX)

### Suppliers
- PT Kimia Farma (KF001)
- PT Kalbe Farma (KB001)

### Users
- Administrator (admin@apotek.com) dengan role admin

## Keamanan

### Production Environment
Seeder akan memberikan peringatan dan meminta konfirmasi saat dijalankan di environment production.

### Password Default
Semua user yang dibuat memiliki password default: `password`

## Contoh Penggunaan

### Development Environment
```bash
# Set jumlah data yang banyak untuk testing
echo "SEED_CATEGORY_COUNT=20" >> .env
echo "SEED_SUPPLIER_COUNT=15" >> .env
echo "SEED_UNIT_COUNT=15" >> .env

# Jalankan seeding
php artisan migrate:fresh --seed
```

### Testing Environment
```bash
# Set jumlah data minimal untuk testing cepat
echo "SEED_CATEGORY_COUNT=5" >> .env
echo "SEED_SUPPLIER_COUNT=3" >> .env
echo "SEED_UNIT_COUNT=5" >> .env

# Jalankan seeding
php artisan db:seed
```

### Production Environment
```bash
# Set jumlah data minimal untuk production
echo "SEED_CATEGORY_COUNT=3" >> .env
echo "SEED_SUPPLIER_COUNT=2" >> .env
echo "SEED_UNIT_COUNT=4" >> .env

# Jalankan dengan hati-hati
php artisan db:seed
```

## Troubleshooting

### Error: Duplicate Entry
Jika terjadi error duplicate entry, jalankan:
```bash
php artisan migrate:fresh --seed
```

### Error: Class Not Found
Pastikan semua factory sudah dibuat:
```bash
php artisan make:factory CategoryFactory
php artisan make:factory SupplierFactory
php artisan make:factory UnitFactory
```

### Error: Permission Denied
Pastikan Spatie Permission sudah terinstall:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

## Best Practices

1. **Selalu backup database** sebelum menjalankan seeder di production
2. **Gunakan environment variables** untuk mengatur jumlah data
3. **Test seeder di development** sebelum deploy ke production
4. **Monitor performa** saat seeding data dalam jumlah besar
5. **Gunakan transaction** untuk rollback jika terjadi error

## Maintenance

### Update Factory Data
Untuk menambah atau mengubah data dalam factory:
1. Edit file factory yang sesuai di `database/factories/`
2. Jalankan `php artisan migrate:fresh --seed` untuk testing

### Add New Seeder
```bash
# Buat seeder baru
php artisan make:seeder NewDataSeeder

# Tambahkan ke DatabaseSeeder
$this->call(NewDataSeeder::class);
```

### Performance Optimization
Untuk data dalam jumlah besar, gunakan chunk atau batch insert:
```php
// Contoh untuk data besar
Category::factory(1000)->create();
// atau
DB::transaction(function () {
    Category::factory(1000)->create();
});
```
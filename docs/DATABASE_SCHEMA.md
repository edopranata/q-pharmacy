# Database Schema Documentation

## Overview

Database Q-Pharmacy menggunakan MySQL untuk production dan SQLite untuk development. Schema dirancang untuk mendukung operasi apotek lengkap dengan manajemen stok, batch tracking, penjualan, dan sistem role-based access control.

## Entity Relationship Diagram

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     users       │────▶│ model_has_roles │◀────│      roles      │
│                 │     │                 │     │                 │
│ - id            │     │ - role_id       │     │ - id            │
│ - name          │     │ - model_type    │     │ - name          │
│ - email         │     │ - model_id      │     │ - guard_name    │
│ - password      │     └─────────────────┘     │ - created_at    │
│ - created_at    │                             │ - updated_at    │
│ - updated_at    │                             └─────────────────┘
└─────────────────┘                                       │
         │                                                │
         │                                                ▼
         │                                   ┌─────────────────┐
         │                                   │ role_has_perms  │
         │                                   │                 │
         │                                   │ - permission_id │
         │                                   │ - role_id       │
         │                                   └─────────────────┘
         │                                                 │
         │                                                 ▼
         │                                   ┌─────────────────┐
         │                                   │  permissions    │
         │                                   │                 │
         │                                   │ - id            │
         │                                   │ - name          │
         │                                   │ - guard_name    │
         │                                   │ - created_at    │
         │                                   │ - updated_at    │
         │                                   └─────────────────┘
         │
         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     sales       │────▶│   sale_items    │────▶│    products     │
│                 │     │                 │     │                 │
│ - id            │     │ - id            │     │ - id            │
│ - user_id       │     │ - sale_id       │     │ - name          │
│ - total_amount  │     │ - product_id    │     │ - description   │
│ - tax_amount    │     │ - batch_id      │     │ - category_id   │
│ - discount      │     │ - quantity      │     │ - supplier_id   │
│ - payment_method│     │ - unit_price    │     │ - unit_id       │
│ - status        │     │ - total_price   │     │ - barcode       │
│ - created_at    │     │ - created_at    │     │ - is_active     │
│ - updated_at    │     │ - updated_at    │     │ - created_at    │
└─────────────────┘     └─────────────────┘     │ - updated_at    │
                                  │             └─────────────────┘
                                  │                       │
                                  ▼                       │
                        ┌─────────────────┐               │
                        │     batches     │◀──────────────┘
                        │                 │
                        │ - id            │
                        │ - product_id    │
                        │ - batch_number  │
                        │ - quantity      │
                        │ - purchase_price│
                        │ - selling_price │
                        │ - expired_date  │
                        │ - is_active     │
                        │ - created_at    │
                        │ - updated_at    │
                        └─────────────────┘

┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   categories    │◀────│    products     │────▶│   suppliers     │
│                 │     │                 │     │                 │
│ - id            │     │ (see above)     │     │ - id            │
│ - name          │     │                 │     │ - name          │
│ - description   │     │                 │     │ - contact_person│
│ - is_active     │     │                 │     │ - phone         │
│ - created_at    │     │                 │     │ - email         │
│ - updated_at    │     │                 │     │ - address       │
└─────────────────┘     │                 │     │ - is_active     │
                        │                 │     │ - created_at    │
                        │                 │     │ - updated_at    │
                        │                 │     └─────────────────┘
                        │                 │
                        │                 ▼
                        │       ┌─────────────────┐
                        │       │      units      │
                        │       │                 │
                        │       │ - id            │
                        │       │ - name          │
                        │       │ - symbol        │
                        │       │ - is_active     │
                        │       │ - created_at    │
                        │       │ - updated_at    │
                        │       └─────────────────┘
                        │
                        ▼
              ┌─────────────────┐
              │ stock_movements │
              │                 │
              │ - id            │
              │ - product_id    │
              │ - batch_id      │
              │ - type          │
              │ - quantity      │
              │ - reference_id  │
              │ - reference_type│
              │ - notes         │
              │ - created_at    │
              │ - updated_at    │
              └─────────────────┘
```

## Tabel Database

### 1. Tabel Default Laravel

#### users
Tabel untuk menyimpan data pengguna sistem.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID unik pengguna |
| name | varchar(255) | NOT NULL | Nama lengkap pengguna |
| email | varchar(255) | NOT NULL, UNIQUE | Email pengguna |
| email_verified_at | timestamp | NULL | Waktu verifikasi email |
| password | varchar(255) | NOT NULL | Password terenkripsi |
| remember_token | varchar(100) | NULL | Token remember me |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update terakhir |

#### password_reset_tokens
Tabel untuk menyimpan token reset password.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| email | varchar(255) | PRIMARY KEY | Email pengguna |
| token | varchar(255) | NOT NULL | Token reset password |
| created_at | timestamp | NULL | Waktu pembuatan token |

#### sessions
Tabel untuk menyimpan session pengguna.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | varchar(255) | PRIMARY KEY | ID session |
| user_id | bigint unsigned | NULL, INDEX | ID pengguna |
| ip_address | varchar(45) | NULL | IP address |
| user_agent | text | NULL | User agent |
| payload | longtext | NOT NULL | Data session |
| last_activity | int | NOT NULL, INDEX | Aktivitas terakhir |

### 2. Tabel Spatie Permission

#### permissions
Tabel untuk menyimpan daftar permission.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID permission |
| name | varchar(255) | NOT NULL | Nama permission |
| guard_name | varchar(255) | NOT NULL | Guard name |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Unique Index**: (name, guard_name)

#### roles
Tabel untuk menyimpan daftar role.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID role |
| name | varchar(255) | NOT NULL | Nama role |
| guard_name | varchar(255) | NOT NULL | Guard name |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Unique Index**: (name, guard_name)

#### model_has_permissions
Tabel pivot untuk relasi model dengan permission.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| permission_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID permission |
| model_type | varchar(255) | NOT NULL | Tipe model |
| model_id | bigint unsigned | NOT NULL | ID model |

**Primary Key**: (permission_id, model_id, model_type)

#### model_has_roles
Tabel pivot untuk relasi model dengan role.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| role_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID role |
| model_type | varchar(255) | NOT NULL | Tipe model |
| model_id | bigint unsigned | NOT NULL | ID model |

**Primary Key**: (role_id, model_id, model_type)

#### role_has_permissions
Tabel pivot untuk relasi role dengan permission.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| permission_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID permission |
| role_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID role |

**Primary Key**: (permission_id, role_id)

### 3. Tabel Master Data

#### categories
Tabel untuk menyimpan kategori produk.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID kategori |
| name | varchar(255) | NOT NULL | Nama kategori |
| description | text | NULL | Deskripsi kategori |
| is_active | boolean | DEFAULT true | Status aktif |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

#### suppliers
Tabel untuk menyimpan data supplier.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID supplier |
| name | varchar(255) | NOT NULL | Nama supplier |
| contact_person | varchar(255) | NULL | Nama kontak person |
| phone | varchar(20) | NULL | Nomor telepon |
| email | varchar(255) | NULL | Email supplier |
| address | text | NULL | Alamat supplier |
| is_active | boolean | DEFAULT true | Status aktif |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

#### units
Tabel untuk menyimpan satuan produk.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID satuan |
| name | varchar(255) | NOT NULL | Nama satuan |
| symbol | varchar(10) | NOT NULL | Simbol satuan |
| is_active | boolean | DEFAULT true | Status aktif |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

### 4. Tabel Stok & Batch

#### products
Tabel untuk menyimpan data produk.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID produk |
| name | varchar(255) | NOT NULL | Nama produk |
| description | text | NULL | Deskripsi produk |
| category_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID kategori |
| supplier_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID supplier |
| unit_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID satuan |
| barcode | varchar(255) | NULL, UNIQUE | Barcode produk |
| is_active | boolean | DEFAULT true | Status aktif |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Foreign Keys**:
- category_id → categories(id)
- supplier_id → suppliers(id)
- unit_id → units(id)

#### batches
Tabel untuk menyimpan data batch produk.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID batch |
| product_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID produk |
| batch_number | varchar(255) | NOT NULL | Nomor batch |
| quantity | int | NOT NULL, DEFAULT 0 | Jumlah stok |
| purchase_price | decimal(10,2) | NOT NULL | Harga beli |
| selling_price | decimal(10,2) | NOT NULL | Harga jual |
| expired_date | date | NULL | Tanggal kadaluarsa |
| is_active | boolean | DEFAULT true | Status aktif |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Foreign Keys**:
- product_id → products(id)

**Unique Index**: (product_id, batch_number)

#### stock_movements
Tabel untuk menyimpan pergerakan stok.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID movement |
| product_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID produk |
| batch_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID batch |
| type | enum('in','out') | NOT NULL | Tipe movement |
| quantity | int | NOT NULL | Jumlah |
| reference_id | bigint unsigned | NULL | ID referensi |
| reference_type | varchar(255) | NULL | Tipe referensi |
| notes | text | NULL | Catatan |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Foreign Keys**:
- product_id → products(id)
- batch_id → batches(id)

### 5. Tabel Penjualan

#### sales
Tabel untuk menyimpan data penjualan.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID penjualan |
| user_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID kasir |
| total_amount | decimal(12,2) | NOT NULL | Total amount |
| tax_amount | decimal(10,2) | DEFAULT 0 | Jumlah pajak |
| discount_amount | decimal(10,2) | DEFAULT 0 | Jumlah diskon |
| payment_method | enum('cash','card','transfer') | NOT NULL | Metode pembayaran |
| status | enum('pending','completed','cancelled') | DEFAULT 'pending' | Status penjualan |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Foreign Keys**:
- user_id → users(id)

#### sale_items
Tabel untuk menyimpan item penjualan.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID item |
| sale_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID penjualan |
| product_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID produk |
| batch_id | bigint unsigned | NOT NULL, FOREIGN KEY | ID batch |
| quantity | int | NOT NULL | Jumlah |
| unit_price | decimal(10,2) | NOT NULL | Harga satuan |
| total_price | decimal(12,2) | NOT NULL | Total harga |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update |

**Foreign Keys**:
- sale_id → sales(id)
- product_id → products(id)
- batch_id → batches(id)

## Indexes

### Performance Indexes

```sql
-- Products table
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_supplier ON products(supplier_id);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_barcode ON products(barcode);

-- Batches table
CREATE INDEX idx_batches_product ON batches(product_id);
CREATE INDEX idx_batches_expired ON batches(expired_date);
CREATE INDEX idx_batches_active ON batches(is_active);

-- Stock movements table
CREATE INDEX idx_stock_movements_product ON stock_movements(product_id);
CREATE INDEX idx_stock_movements_batch ON stock_movements(batch_id);
CREATE INDEX idx_stock_movements_type ON stock_movements(type);
CREATE INDEX idx_stock_movements_reference ON stock_movements(reference_id, reference_type);

-- Sales table
CREATE INDEX idx_sales_user ON sales(user_id);
CREATE INDEX idx_sales_status ON sales(status);
CREATE INDEX idx_sales_created ON sales(created_at);

-- Sale items table
CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);
CREATE INDEX idx_sale_items_product ON sale_items(product_id);
CREATE INDEX idx_sale_items_batch ON sale_items(batch_id);
```

## Constraints & Validations

### Foreign Key Constraints

```sql
-- Products constraints
ALTER TABLE products ADD CONSTRAINT fk_products_category 
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT;
ALTER TABLE products ADD CONSTRAINT fk_products_supplier 
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT;
ALTER TABLE products ADD CONSTRAINT fk_products_unit 
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT;

-- Batches constraints
ALTER TABLE batches ADD CONSTRAINT fk_batches_product 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;

-- Stock movements constraints
ALTER TABLE stock_movements ADD CONSTRAINT fk_stock_movements_product 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;
ALTER TABLE stock_movements ADD CONSTRAINT fk_stock_movements_batch 
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE;

-- Sales constraints
ALTER TABLE sales ADD CONSTRAINT fk_sales_user 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT;

-- Sale items constraints
ALTER TABLE sale_items ADD CONSTRAINT fk_sale_items_sale 
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE;
ALTER TABLE sale_items ADD CONSTRAINT fk_sale_items_product 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT;
ALTER TABLE sale_items ADD CONSTRAINT fk_sale_items_batch 
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE RESTRICT;
```

### Check Constraints

```sql
-- Batches constraints
ALTER TABLE batches ADD CONSTRAINT chk_batches_quantity 
    CHECK (quantity >= 0);
ALTER TABLE batches ADD CONSTRAINT chk_batches_purchase_price 
    CHECK (purchase_price >= 0);
ALTER TABLE batches ADD CONSTRAINT chk_batches_selling_price 
    CHECK (selling_price >= 0);

-- Stock movements constraints
ALTER TABLE stock_movements ADD CONSTRAINT chk_stock_movements_quantity 
    CHECK (quantity > 0);

-- Sales constraints
ALTER TABLE sales ADD CONSTRAINT chk_sales_total_amount 
    CHECK (total_amount >= 0);
ALTER TABLE sales ADD CONSTRAINT chk_sales_tax_amount 
    CHECK (tax_amount >= 0);
ALTER TABLE sales ADD CONSTRAINT chk_sales_discount_amount 
    CHECK (discount_amount >= 0);

-- Sale items constraints
ALTER TABLE sale_items ADD CONSTRAINT chk_sale_items_quantity 
    CHECK (quantity > 0);
ALTER TABLE sale_items ADD CONSTRAINT chk_sale_items_unit_price 
    CHECK (unit_price >= 0);
ALTER TABLE sale_items ADD CONSTRAINT chk_sale_items_total_price 
    CHECK (total_price >= 0);
```

## Sample Data

### Categories
```sql
INSERT INTO categories (name, description, is_active) VALUES
('Obat Bebas', 'Obat yang dapat dibeli tanpa resep dokter', true),
('Obat Bebas Terbatas', 'Obat yang dapat dibeli dengan batasan tertentu', true),
('Obat Keras', 'Obat yang hanya dapat dibeli dengan resep dokter', true),
('Suplemen', 'Suplemen kesehatan dan vitamin', true),
('Alat Kesehatan', 'Peralatan medis dan kesehatan', true);
```

### Units
```sql
INSERT INTO units (name, symbol, is_active) VALUES
('Tablet', 'tab', true),
('Kapsul', 'kaps', true),
('Botol', 'btl', true),
('Tube', 'tube', true),
('Strip', 'strip', true),
('Box', 'box', true),
('Pieces', 'pcs', true);
```

### Suppliers
```sql
INSERT INTO suppliers (name, contact_person, phone, email, address, is_active) VALUES
('PT Kimia Farma', 'John Doe', '021-1234567', 'contact@kimiafarma.co.id', 'Jakarta Pusat', true),
('PT Kalbe Farma', 'Jane Smith', '021-2345678', 'info@kalbe.co.id', 'Jakarta Timur', true),
('PT Dexa Medica', 'Bob Johnson', '021-3456789', 'sales@dexa-medica.com', 'Tangerang', true);
```

### Roles & Permissions
```sql
-- Roles
INSERT INTO roles (name, guard_name) VALUES
('super-admin', 'web'),
('admin', 'web'),
('kasir', 'web'),
('staff', 'web');

-- Permissions
INSERT INTO permissions (name, guard_name) VALUES
('view-dashboard', 'web'),
('manage-users', 'web'),
('manage-products', 'web'),
('manage-categories', 'web'),
('manage-suppliers', 'web'),
('manage-sales', 'web'),
('view-reports', 'web'),
('manage-settings', 'web');
```

## Database Maintenance

### Backup Strategy
- **Daily**: Automated full backup
- **Hourly**: Transaction log backup
- **Weekly**: Full backup with verification
- **Monthly**: Archive old backups

### Optimization
- **Index Maintenance**: Weekly index rebuild
- **Statistics Update**: Daily statistics update
- **Query Optimization**: Monthly query performance review
- **Storage Cleanup**: Monthly cleanup of old data

### Monitoring
- **Performance Metrics**: Query execution time, index usage
- **Storage Metrics**: Database size, table growth
- **Connection Metrics**: Active connections, deadlocks
- **Error Monitoring**: Failed queries, constraint violations
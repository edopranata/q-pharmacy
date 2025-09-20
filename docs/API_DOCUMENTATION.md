# API Documentation Q-Pharmacy

## Overview

API Q-Pharmacy adalah RESTful API yang dibangun dengan Laravel 12 dan menggunakan Laravel Sanctum untuk autentikasi. API ini menyediakan endpoint untuk manajemen apotek lengkap termasuk produk, stok, penjualan, dan sistem role-based access control dengan dukungan penuh untuk pagination, search, filter, dan sorting.

**Current Status**: User Management Phase Complete  
**Last Updated**: 15 Januari 2025  
**Version**: 0.5.0

### 🎯 Implementation Status

#### ✅ Implemented APIs
- **Authentication**: Login, Register, Logout, Password Reset
- **Master Data**: Categories, Suppliers, Units (full CRUD dengan pagination, search, filter, sort)
- **User Management**: Complete CRUD dengan advanced features, profile management, avatar upload/delete
- **Role Management**: Role assignment dan permission management
- **Audit System**: Activity logging dan audit trails

#### 🔄 In Development
- **Product Management**: Product CRUD dengan barcode support
- **Inventory Management**: Basic stock tracking

#### ⏳ Planned APIs
- **Inventory Management**: Advanced stock tracking dan batch management
- **Point of Sale**: Transaction processing APIs
- **Reporting**: Analytics dan reporting endpoints

## Base URL

```
Development: http://localhost:8000/api/v1
Production: https://api.q-pharmacy.com/api/v1
```

## Query Parameters untuk Data Tables

Semua endpoint yang mengembalikan list data mendukung parameter berikut untuk pagination, search, filter, dan sorting sesuai dengan Quasar Table best practices:

### Pagination
- `page` (integer): Nomor halaman (default: 1)
- `per_page` (integer): Jumlah item per halaman (default: 15, max: 100)

### Search
- `search` (string): Pencarian global di semua field yang dapat dicari
- `search_fields` (array): Field spesifik untuk pencarian (opsional)

### Sorting
- `sort_by` (string): Field untuk sorting
- `sort_order` (string): Arah sorting (`asc` atau `desc`, default: `asc`)

### Filtering
- `filters` (object): Filter spesifik per field
- `date_from` (date): Filter tanggal mulai (format: Y-m-d)
- `date_to` (date): Filter tanggal akhir (format: Y-m-d)

### Contoh Request dengan Query Parameters
```http
GET /products?page=1&per_page=15&search=paracetamol&sort_by=name&sort_order=asc&filters[category_id]=1&filters[status]=active
```

### Standard Response Format untuk Data Tables
```json
{
  "status": "success",
  "data": {
    "items": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 150,
      "last_page": 10,
      "from": 1,
      "to": 15,
      "has_more_pages": true
    },
    "sorting": {
      "sort_by": "name",
      "sort_order": "asc"
    },
    "filters_applied": {
      "search": "paracetamol",
      "category_id": 1,
      "status": "active"
    }
  }
}
```

## Authentication

### Laravel Sanctum

API menggunakan Laravel Sanctum untuk autentikasi berbasis token.

#### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "roles": ["admin"],
      "permissions": ["manage-users", "manage-products"]
    },
    "token": "1|abc123def456ghi789",
    "expires_at": "2025-09-16T10:30:00.000000Z"
  }
}
```

#### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Logout successful"
}
```

#### Refresh Token
```http
POST /auth/refresh
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "token": "2|new123token456here",
    "expires_at": "2025-09-16T11:30:00.000000Z"
  }
}
```

### Authorization Header

Semua endpoint yang memerlukan autentikasi harus menyertakan header:

```http
Authorization: Bearer {your-token-here}
```

### Super-Admin Role

Sistem mengimplementasikan Super-Admin role mengikuti best practice Spatie Laravel Permission:

- **Super-Admin** memiliki akses penuh ke semua permission tanpa perlu assignment eksplisit
- Implementasi menggunakan `Gate::before()` untuk memberikan akses otomatis
- Super-Admin dapat melakukan semua operasi CRUD pada semua resource
- Untuk keamanan, beberapa operasi sensitif tetap memerlukan konfirmasi tambahan

**Contoh Response untuk Super-Admin:**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Administrator",
      "email": "superadmin@q-pharmacy.com",
      "roles": ["Super-Admin"],
      "permissions": ["*"],
      "is_super_admin": true
    },
    "token": "1|abc123def456ghi789",
    "expires_at": "2025-09-16T10:30:00.000000Z"
  }
}
```

**Catatan Implementasi:**
- Super-Admin role dicheck menggunakan `Gate::before()` di `AppServiceProvider`
- Semua permission check akan return `true` untuk user dengan role "Super-Admin"
- Gunakan `$user->can()` method untuk permission checking (bukan `hasPermissionTo()`)
- Super-Admin tetap dapat di-assign permission spesifik jika diperlukan

## Response Format

### Success Response
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  },
  "meta": {
    // Pagination or additional metadata
  }
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "code": "ERROR_CODE"
}
```

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 204 | No Content - Request successful, no content returned |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation errors |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

## Rate Limiting

- **Authenticated users**: 60 requests per minute
- **Guest users**: 30 requests per minute
- **Login endpoint**: 5 attempts per minute

## Endpoints

### Authentication

#### POST /auth/login
User login

**Parameters:**
- `email` (string, required): User email
- `password` (string, required): User password

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "roles": ["admin"],
      "permissions": ["manage-users", "manage-products"]
    },
    "token": "1|abc123def456ghi789",
    "expires_at": "2025-09-16T10:30:00.000000Z"
  }
}
```

#### POST /auth/register
Register new user (requires `manage-users` permission)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "roles": ["kasir"]
}
```

#### POST /auth/logout
User logout (requires authentication)

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Logout successful"
}
```

#### POST /auth/refresh
Refresh authentication token (requires authentication)

**Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "token": "2|new123token456here",
    "expires_at": "2025-09-16T11:30:00.000000Z"
  }
}
```

#### GET /auth/me
Get current user information (requires authentication)

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@example.com",
    "roles": ["admin"],
    "permissions": ["manage-users", "manage-products"]
  }
}
```

#### POST /auth/forgot-password
Request password reset

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

#### POST /auth/reset-password
Reset password with token

**Request Body:**
```json
{
  "token": "reset_token_here",
  "email": "user@example.com",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

### Users Management

#### GET /users
Get list of users (requires `manage-users` permission)

**Query Parameters:**
- `page` (integer): Nomor halaman (default: 1)
- `per_page` (integer): Items per halaman (default: 15, max: 100)
- `search` (string): Pencarian global di name dan email
- `sort_by` (string): Field untuk sorting (name, email, created_at)
- `sort_order` (string): Arah sorting (asc/desc)
- `filters[role]` (string): Filter berdasarkan role
- `filters[status]` (string): Filter berdasarkan status (active/inactive)
- `date_from` (date): Filter tanggal mulai
- `date_to` (date): Filter tanggal akhir

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Administrator",
      "email": "admin@example.com",
      "roles": ["admin"],
      "is_active": true,
      "created_at": "2025-09-15T10:00:00.000000Z",
      "updated_at": "2025-09-15T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 25,
    "last_page": 2
  }
}
```

#### POST /users
Create new user (requires `manage-users` permission)

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "roles": ["kasir"]
}
```

#### GET /users/{id}
Get user details (requires `manage-users` permission)

#### PUT /users/{id}
Update user (requires `manage-users` permission)

#### DELETE /users/{id}
Delete user (requires `manage-users` permission)

### Products Management

#### GET /products
Get list of products

**Query Parameters:**
- `page` (integer): Nomor halaman (default: 1)
- `per_page` (integer): Items per halaman (default: 15, max: 100)
- `search` (string): Pencarian global di name, description, dan barcode
- `sort_by` (string): Field untuk sorting (name, created_at, current_stock)
- `sort_order` (string): Arah sorting (asc/desc)
- `filters[category_id]` (integer): Filter berdasarkan kategori
- `filters[supplier_id]` (integer): Filter berdasarkan supplier
- `filters[is_active]` (boolean): Filter berdasarkan status
- `filters[low_stock]` (boolean): Filter produk dengan stok rendah
- `date_from` (date): Filter tanggal mulai
- `date_to` (date): Filter tanggal akhir

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
    {
      "id": 1,
      "name": "Paracetamol 500mg",
      "description": "Obat penurun panas dan pereda nyeri",
      "barcode": "1234567890123",
      "category": {
        "id": 1,
        "name": "Obat Bebas"
      },
      "supplier": {
        "id": 1,
        "name": "PT Kimia Farma"
      },
      "unit": {
        "id": 1,
        "name": "Tablet",
        "symbol": "tab"
      },
      "current_stock": 150,
      "is_active": true,
      "created_at": "2025-09-15T10:00:00.000000Z",
      "updated_at": "2025-09-15T10:00:00.000000Z"
    }],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 150,
      "last_page": 10,
      "from": 1,
      "to": 15,
      "has_more_pages": true
    },
    "sorting": {
      "sort_by": "name",
      "sort_order": "asc"
    },
    "filters_applied": {
      "search": "paracetamol",
      "category_id": 1,
      "is_active": true
    }
  }
}
```

#### POST /products
Create new product (requires `manage-products` permission)

**Request Body:**
```json
{
  "name": "Paracetamol 500mg",
  "description": "Obat penurun panas dan pereda nyeri",
  "category_id": 1,
  "supplier_id": 1,
  "unit_id": 1,
  "barcode": "1234567890123"
}
```

#### GET /products/{id}
Get product details

#### PUT /products/{id}
Update product (requires `manage-products` permission)

#### DELETE /products/{id}
Delete product (requires `manage-products` permission)

### Batches Management

#### GET /products/{product_id}/batches
Get product batches

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "batch_number": "BATCH001",
      "quantity": 100,
      "purchase_price": 5000.00,
      "selling_price": 7500.00,
      "expired_date": "2026-12-31",
      "is_active": true,
      "created_at": "2025-09-15T10:00:00.000000Z"
    }
  ]
}
```

#### POST /products/{product_id}/batches
Create new batch (requires `manage-products` permission)

**Request Body:**
```json
{
  "batch_number": "BATCH001",
  "quantity": 100,
  "purchase_price": 5000.00,
  "selling_price": 7500.00,
  "expired_date": "2026-12-31"
}
```

### Sales Management

#### GET /sales
Get list of sales (requires `manage-sales` or `view-reports` permission)

**Query Parameters:**
- `page` (integer, optional): Page number
- `per_page` (integer, optional): Items per page
- `date_from` (date, optional): Filter from date (YYYY-MM-DD)
- `date_to` (date, optional): Filter to date (YYYY-MM-DD)
- `status` (string, optional): Filter by status
- `user_id` (integer, optional): Filter by cashier

#### POST /sales
Create new sale (requires `manage-sales` permission)

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "batch_id": 1,
      "quantity": 2,
      "unit_price": 7500.00
    }
  ],
  "payment_method": "cash",
  "tax_amount": 1500.00,
  "discount_amount": 0.00
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Sale created successfully",
  "data": {
    "id": 1,
    "total_amount": 16500.00,
    "tax_amount": 1500.00,
    "discount_amount": 0.00,
    "payment_method": "cash",
    "status": "completed",
    "items": [
      {
        "product_name": "Paracetamol 500mg",
        "batch_number": "BATCH001",
        "quantity": 2,
        "unit_price": 7500.00,
        "total_price": 15000.00
      }
    ],
    "created_at": "2025-09-15T14:30:00.000000Z"
  }
}
```

#### GET /sales/{id}
Get sale details

#### PUT /sales/{id}/status
Update sale status (requires `manage-sales` permission)

**Request Body:**
```json
{
  "status": "completed"
}
```

### Master Data Management

#### GET /categories
Get list of categories

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search in name and description
- `sort_by` (string): Sort field (name, created_at)
- `sort_order` (string): Sort direction (asc/desc)
- `filters[is_active]` (boolean): Filter by status

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Obat Bebas",
        "description": "Obat yang dapat dibeli tanpa resep dokter",
        "is_active": true,
        "products_count": 25,
        "created_at": "2025-09-15T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 10,
      "last_page": 1
    }
  }
}
```

#### POST /categories
Create new category (requires `manage-categories` permission)

**Request Body:**
```json
{
  "name": "Obat Keras",
  "description": "Obat yang memerlukan resep dokter",
  "is_active": true
}
```

#### GET /categories/{id}
Get category details

#### PUT /categories/{id}
Update category (requires `manage-categories` permission)

#### DELETE /categories/{id}
Delete category (requires `manage-categories` permission)

#### GET /suppliers
Get list of suppliers

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search in name, contact_person, phone
- `sort_by` (string): Sort field (name, created_at)
- `sort_order` (string): Sort direction (asc/desc)
- `filters[is_active]` (boolean): Filter by status

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "PT Kimia Farma",
        "contact_person": "John Doe",
        "phone": "021-1234567",
        "email": "contact@kimiafarma.co.id",
        "address": "Jakarta Pusat",
        "is_active": true,
        "products_count": 150,
        "created_at": "2025-09-15T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 25,
      "last_page": 2
    }
  }
}
```

#### POST /suppliers
Create new supplier (requires `manage-suppliers` permission)

**Request Body:**
```json
{
  "name": "PT Kalbe Farma",
  "contact_person": "Jane Smith",
  "phone": "021-7654321",
  "email": "contact@kalbe.co.id",
  "address": "Jakarta Selatan",
  "is_active": true
}
```

#### GET /suppliers/{id}
Get supplier details

#### PUT /suppliers/{id}
Update supplier (requires `manage-suppliers` permission)

#### DELETE /suppliers/{id}
Delete supplier (requires `manage-suppliers` permission)

#### GET /units
Get list of units

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Tablet",
      "symbol": "tab",
      "is_active": true
    },
    {
      "id": 2,
      "name": "Botol",
      "symbol": "btl",
      "is_active": true
    }
  ]
}
```

#### POST /units
Create new unit (requires `manage-units` permission)

**Request Body:**
```json
{
  "name": "Kapsul",
  "symbol": "kps",
  "is_active": true
}
```

#### PUT /units/{id}
Update unit (requires `manage-units` permission)

#### DELETE /units/{id}
Delete unit (requires `manage-units` permission)

### Statistics & Analytics

#### GET /analytics/dashboard
Get dashboard statistics (requires `view-reports` permission)

**Response:**
```json
{
  "status": "success",
  "data": {
    "today_sales": {
      "total_amount": 2500000.00,
      "transaction_count": 35,
      "average_transaction": 71428.57
    },
    "monthly_sales": {
      "total_amount": 45000000.00,
      "transaction_count": 650,
      "growth_percentage": 15.5
    },
    "stock_alerts": {
      "low_stock_count": 12,
      "expiring_soon_count": 8,
      "expired_count": 3
    },
    "top_products": [
      {
        "product_name": "Paracetamol 500mg",
        "total_sold": 150,
        "revenue": 1125000.00
      }
    ]
  }
}
```

#### GET /analytics/sales-trend
Get sales trend analysis (requires `view-reports` permission)

**Query Parameters:**
- `period` (string): Period type (daily/weekly/monthly)
- `date_from` (date): Start date
- `date_to` (date): End date

**Response:**
```json
{
  "status": "success",
  "data": {
    "period": "daily",
    "trends": [
      {
        "date": "2025-09-15",
        "total_amount": 2500000.00,
        "transaction_count": 35,
        "growth_rate": 12.5
      }
    ],
    "summary": {
      "total_revenue": 45000000.00,
      "average_daily_sales": 1500000.00,
      "peak_day": "2025-09-15",
      "peak_amount": 2500000.00
    }
  }
}
```

#### GET /analytics/product-performance
Get product performance analysis (requires `view-reports` permission)

**Query Parameters:**
- `date_from` (date): Start date
- `date_to` (date): End date
- `category_id` (integer): Filter by category
- `limit` (integer): Limit results (default: 20)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "product": {
        "id": 1,
        "name": "Paracetamol 500mg",
        "category": "Obat Bebas"
      },
      "total_sold": 150,
      "total_revenue": 1125000.00,
      "profit_margin": 33.33,
      "stock_turnover": 2.5
    }
  ]
}
```

### Dropdown Options

#### GET /options/categories
Get categories for dropdown

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "value": 1,
      "label": "Obat Bebas"
    },
    {
      "value": 2,
      "label": "Obat Keras"
    }
  ]
}
```

#### GET /options/suppliers
Get suppliers for dropdown

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "value": 1,
      "label": "PT Kimia Farma"
    },
    {
      "value": 2,
      "label": "PT Kalbe Farma"
    }
  ]
}
```

#### GET /options/units
Get units for dropdown

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "value": 1,
      "label": "Tablet",
      "symbol": "tab"
    },
    {
      "value": 2,
      "label": "Botol",
      "symbol": "btl"
    }
  ]
}
```

#### GET /options/roles
Get roles for dropdown (requires `manage-users` permission)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "value": "admin",
      "label": "Administrator"
    },
    {
      "value": "kasir",
      "label": "Kasir"
    }
  ]
}
```

### Reports

#### GET /reports/sales-summary
Get sales summary report (requires `view-reports` permission)

**Query Parameters:**
- `date_from` (date, required): Start date
- `date_to` (date, required): End date
- `group_by` (string, optional): Group by (day/week/month)

**Response:**
```json
{
  "status": "success",
  "data": {
    "total_sales": 1500000.00,
    "total_transactions": 45,
    "average_transaction": 33333.33,
    "daily_breakdown": [
      {
        "date": "2025-09-15",
        "total_amount": 500000.00,
        "transaction_count": 15
      }
    ]
  }
}
```

#### GET /reports/stock-status
Get stock status report (requires `view-reports` permission)

**Response:**
```json
{
  "status": "success",
  "data": {
    "total_products": 250,
    "low_stock_products": 12,
    "out_of_stock_products": 3,
    "expiring_products": 8,
    "total_stock_value": 125000000.00,
    "categories_breakdown": [
      {
        "category": "Obat Bebas",
        "product_count": 150,
        "stock_value": 75000000.00
      }
    ]
  }
}
```

#### GET /reports/expired-products
Get expired products report (requires `view-reports` permission)

**Query Parameters:**
- `days_ahead` (integer, optional): Days ahead to check (default: 30)

**Response:**
```json
{
  "status": "success",
  "data": {
    "expired_products": [
      {
        "product": {
          "id": 1,
          "name": "Paracetamol 500mg"
        },
        "batch": {
          "id": 1,
          "batch_number": "BATCH001",
          "expired_date": "2025-08-15",
          "quantity": 25
        },
        "days_expired": 31,
        "estimated_loss": 187500.00
      }
    ],
    "expiring_soon": [
      {
        "product": {
          "id": 2,
          "name": "Amoxicillin 500mg"
        },
        "batch": {
          "id": 2,
          "batch_number": "BATCH002",
          "expired_date": "2025-10-15",
          "quantity": 50
        },
        "days_to_expire": 30,
        "estimated_value": 375000.00
      }
    ],
    "summary": {
      "total_expired_value": 562500.00,
      "total_expiring_value": 1125000.00,
      "action_required_count": 15
    }
  }
}
```

#### GET /reports/financial
Get financial report (requires `view-reports` permission)

**Query Parameters:**
- `date_from` (date, required): Start date
- `date_to` (date, required): End date
- `format` (string): Export format (json/pdf/excel)

**Response:**
```json
{
  "status": "success",
  "data": {
    "revenue": {
      "gross_sales": 45000000.00,
      "discounts": 500000.00,
      "net_sales": 44500000.00
    },
    "costs": {
      "cost_of_goods_sold": 30000000.00,
      "operating_expenses": 5000000.00,
      "total_costs": 35000000.00
    },
    "profit": {
      "gross_profit": 14500000.00,
      "net_profit": 9500000.00,
      "profit_margin": 21.35
    },
    "payment_methods": [
      {
        "method": "cash",
        "amount": 25000000.00,
        "percentage": 56.18
      },
      {
        "method": "card",
        "amount": 19500000.00,
        "percentage": 43.82
      }
    ]
  }
}
```

### Inventory Management

#### GET /inventory/stock-movements
Get stock movements history

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search in product name, batch number
- `sort_by` (string): Sort field (created_at, quantity)
- `sort_order` (string): Sort direction (asc/desc)
- `filters[product_id]` (integer): Filter by product
- `filters[type]` (string): Filter by movement type (in/out/adjustment)
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date

**Response:**
```json
{
  "status": "success",
  "data": {
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Paracetamol 500mg",
          "barcode": "1234567890123"
        },
        "batch": {
          "id": 1,
          "batch_number": "BATCH001",
          "expired_date": "2026-12-31"
        },
        "type": "in",
        "quantity": 50,
        "notes": "Stock replenishment",
        "user": {
          "id": 1,
          "name": "Administrator"
        },
        "created_at": "2025-09-15T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7
    }
  }
}
```

#### POST /inventory/stock-movements
Create stock movement (requires `manage-products` permission)

**Request Body:**
```json
{
  "product_id": 1,
  "batch_id": 1,
  "type": "in",
  "quantity": 50,
  "notes": "Stock replenishment"
}
```

#### POST /inventory/adjustment
Stock adjustment (requires `manage-products` permission)

**Request Body:**
```json
{
  "adjustments": [
    {
      "product_id": 1,
      "batch_id": 1,
      "actual_quantity": 95,
      "system_quantity": 100,
      "notes": "Physical count adjustment"
    }
  ],
  "adjustment_date": "2025-09-15",
  "notes": "Monthly stock opname"
}
```

#### POST /inventory/opname
Stock opname (requires `manage-products` permission)

**Request Body:**
```json
{
  "opname_date": "2025-09-15",
  "items": [
    {
      "product_id": 1,
      "batch_id": 1,
      "physical_count": 95,
      "notes": "Counted by staff"
    }
  ],
  "notes": "Monthly stock opname"
}
```

#### GET /inventory/low-stock
Get low stock products

**Query Parameters:**
- `threshold` (integer): Stock threshold (default: 10)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "product": {
        "id": 1,
        "name": "Paracetamol 500mg",
        "barcode": "1234567890123"
      },
      "current_stock": 5,
      "minimum_stock": 10,
      "status": "critical"
    }
  ]
}
```

#### GET /inventory/expiring
Get expiring products

**Query Parameters:**
- `days_ahead` (integer): Days ahead to check (default: 30)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "product": {
        "id": 1,
        "name": "Paracetamol 500mg"
      },
      "batch": {
        "id": 1,
        "batch_number": "BATCH001",
        "expired_date": "2025-10-15",
        "quantity": 25
      },
      "days_to_expire": 30,
      "status": "warning"
    }
  ]
}
```

### POS (Point of Sale)

#### GET /pos/products/search
Search products for POS (requires `manage-sales` permission)

**Query Parameters:**
- `q` (string): Search query (name, barcode)
- `limit` (integer): Limit results (default: 10, max: 50)

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Paracetamol 500mg",
      "barcode": "1234567890123",
      "current_stock": 150,
      "selling_price": 7500.00,
      "available_batches": [
        {
          "id": 1,
          "batch_number": "BATCH001",
          "quantity": 100,
          "expired_date": "2026-12-31",
          "selling_price": 7500.00
        }
      ]
    }
  ]
}
```

#### GET /pos/products/{barcode}
Get product by barcode for POS

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Paracetamol 500mg",
    "barcode": "1234567890123",
    "current_stock": 150,
    "selling_price": 7500.00,
    "category": {
      "id": 1,
      "name": "Obat Bebas"
    },
    "available_batches": [
      {
        "id": 1,
        "batch_number": "BATCH001",
        "quantity": 100,
        "expired_date": "2026-12-31",
        "selling_price": 7500.00
      }
    ]
  }
}
```

## Error Codes

| Code | Description |
|------|-------------|
| AUTH_001 | Invalid credentials |
| AUTH_002 | Token expired |
| AUTH_003 | Token invalid |
| AUTH_004 | Insufficient permissions |
| VALIDATION_001 | Required field missing |
| VALIDATION_002 | Invalid field format |
| VALIDATION_003 | Field value out of range |
| RESOURCE_001 | Resource not found |
| RESOURCE_002 | Resource already exists |
| BUSINESS_001 | Insufficient stock |
| BUSINESS_002 | Product expired |
| BUSINESS_003 | Invalid operation |
| SYSTEM_001 | Database error |
| SYSTEM_002 | External service error |

## Validation Rules

### User Creation
- `name`: required, string, max:255
- `email`: required, email, unique:users
- `password`: required, string, min:8, confirmed
- `roles`: required, array, exists:roles,name

### Product Creation
- `name`: required, string, max:255
- `description`: nullable, string
- `category_id`: required, exists:categories,id
- `supplier_id`: required, exists:suppliers,id
- `unit_id`: required, exists:units,id
- `barcode`: nullable, string, unique:products

### Batch Creation
- `batch_number`: required, string, max:255
- `quantity`: required, integer, min:0
- `purchase_price`: required, numeric, min:0
- `selling_price`: required, numeric, min:0
- `expired_date`: nullable, date, after:today

### Sale Creation
- `items`: required, array, min:1
- `items.*.product_id`: required, exists:products,id
- `items.*.batch_id`: required, exists:batches,id
- `items.*.quantity`: required, integer, min:1
- `items.*.unit_price`: required, numeric, min:0
- `payment_method`: required, in:cash,card,transfer
- `tax_amount`: nullable, numeric, min:0
- `discount_amount`: nullable, numeric, min:0

## Testing

### Postman Collection

Import Postman collection untuk testing API:

```json
{
  "info": {
    "name": "Q-Pharmacy API",
    "description": "API collection for Q-Pharmacy pharmacy management system"
  },
  "auth": {
    "type": "bearer",
    "bearer": [
      {
        "key": "token",
        "value": "{{auth_token}}",
        "type": "string"
      }
    ]
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8000/api/v1"
    },
    {
      "key": "auth_token",
      "value": ""
    }
  ]
}
```

### Sample cURL Commands

#### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

#### Get Products
```bash
curl -X GET http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

#### Create Sale
```bash
curl -X POST http://localhost:8000/api/v1/sales \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "batch_id": 1,
        "quantity": 2,
        "unit_price": 7500.00
      }
    ],
    "payment_method": "cash"
  }'
```

## Changelog

### v1.0.0 (2025-09-15)
- Initial API release
- Authentication endpoints
- User management
- Product management
- Sales management
- Basic reporting

### Planned Features
- Real-time notifications
- Bulk operations
- Advanced reporting
- API versioning
- GraphQL support
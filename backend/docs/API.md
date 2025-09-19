# API Documentation

## Overview

Dokumentasi lengkap untuk API endpoints Q-Pharmacy backend. API menggunakan RESTful conventions dengan JSON responses dan Laravel Sanctum untuk authentication.

## Base Information

- **Base URL**: `http://localhost:8000/api` (development)
- **Content-Type**: `application/json`
- **Authentication**: Bearer Token (Laravel Sanctum)
- **Rate Limiting**: 60 requests per minute per user
- **Permission System**: Permission-based access control

## API Structure

API endpoints diorganisir berdasarkan modul fungsional:

- `/auth/*` - Authentication endpoints
- `/app/master/*` - Master data management
- `/app/products/*` - Product management
- `/app/inventories/*` - Inventory management
- `/app/sells/*` - Sales transactions
- `/app/management/*` - User, Role & Permission management
- `/app/stats/*` - Statistics and analytics
- `/options/*` - Dropdown options with server-side filtering

## Route Naming Convention

Route names mengikuti pola 4 segment dengan format:
- **Pattern**: `{module}.{submodule}.{resource}.{action}`
- **Example**: `app.master.categories.index`, `app.products.items.store`
- Route names juga berfungsi sebagai permission names

## Authentication

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "roles": ["admin"]
    },
    "token": "1|abc123def456..."
  }
}
```

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Get User Profile
```http
GET /api/auth/user
Authorization: Bearer {token}
```

### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

## Master Data Management

### Categories

#### List Categories
```http
GET /api/app/master/categories?page=1&per_page=15&search=obat
Authorization: Bearer {token}
Permission: app.master.categories.index
```

#### Create Category
```http
POST /api/app/master/categories
Authorization: Bearer {token}
Permission: app.master.categories.store
Content-Type: application/json

{
  "name": "Obat Keras",
  "description": "Kategori obat yang memerlukan resep dokter"
}
```

#### Update Category
```http
PUT /api/app/master/categories/{id}
Authorization: Bearer {token}
Permission: app.master.categories.update
```

#### Delete Category
```http
DELETE /api/app/master/categories/{id}
Authorization: Bearer {token}
Permission: app.master.categories.destroy
```

### Suppliers

#### List Suppliers
```http
GET /api/app/master/suppliers
Authorization: Bearer {token}
Permission: app.master.suppliers.index
```

### Units

#### List Units
```http
GET /api/app/master/units
Authorization: Bearer {token}
Permission: app.master.units.index
```

## User Management

### List Users
```http
GET /api/users?page=1&per_page=15&search=john
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "roles": ["kasir"],
      "created_at": "2025-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "per_page": 15,
      "total": 75
    }
  }
}
```

### Create User
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "roles": ["kasir"]
}
```

### Update User
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith Updated",
  "email": "jane.updated@example.com",
  "roles": ["admin"]
}
```

### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

## Product Management

### List Products
```http
GET /api/app/products/items?page=1&per_page=15&search=paracetamol&category_id=1&sort=name&order=asc
Authorization: Bearer {token}
Permission: app.products.items.index
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)
- `search`: Search by name or barcode
- `category_id`: Filter by category
- `sort`: Sort field (name, price, stock, created_at)
- `order`: Sort order (asc, desc)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Paracetamol 500mg",
      "barcode": "1234567890123",
      "price": 5000,
      "stock": 100,
      "category": {
        "id": 1,
        "name": "Obat Bebas"
      },
      "unit": {
        "id": 1,
        "name": "Strip"
      },
      "image_url": "https://example.com/images/paracetamol.jpg",
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}
```

### Create Product
```http
POST /api/app/products/items
Authorization: Bearer {token}
Permission: app.products.items.store
Content-Type: multipart/form-data

name=Paracetamol 500mg
barcode=1234567890123
price=5000
stock=100
category_id=1
unit_id=1
description=Obat pereda nyeri dan demam
image=@/path/to/image.jpg
```

### Update Product
```http
PUT /api/app/products/items/{id}
Authorization: Bearer {token}
Permission: app.products.items.update
Content-Type: application/json

{
  "name": "Paracetamol 500mg Updated",
  "price": 5500,
  "stock": 150
}
```

### Delete Product
```http
DELETE /api/app/products/items/{id}
Authorization: Bearer {token}
Permission: app.products.items.destroy
```

### Get Product by Barcode
```http
GET /api/app/products/items/barcode/{barcode}
Authorization: Bearer {token}
Permission: app.products.items.show
```

### Product Pricing

#### List Product Prices
```http
GET /api/app/products/pricing
Authorization: Bearer {token}
Permission: app.products.pricing.index
```

#### Update Product Price
```http
PUT /api/app/products/pricing/{id}
Authorization: Bearer {token}
Permission: app.products.pricing.update
```

## Inventory Management

### Stock Movements
```http
GET /api/app/inventories/movements?product_id=1&type=in&date_from=2025-01-01&date_to=2025-01-31
Authorization: Bearer {token}
Permission: app.inventories.movements.index
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "type": "in",
      "quantity": 50,
      "reference": "PO-001",
      "notes": "Purchase order",
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}
```

### Stock Adjustment
```http
POST /api/app/inventories/adjustments
Authorization: Bearer {token}
Permission: app.inventories.adjustments.store
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 10,
  "type": "in",
  "reason": "Stock correction",
  "notes": "Manual adjustment"
}
```

### Stock Opname
```http
POST /api/app/inventories/opname
Authorization: Bearer {token}
Permission: app.inventories.opname.store
Content-Type: application/json

{
  "items": [
    {
      "product_id": 1,
      "physical_stock": 95,
      "system_stock": 100,
      "notes": "Physical count difference"
    }
  ]
}
```

### Low Stock Alert
```http
GET /api/app/inventories/alerts/low-stock?threshold=10
Authorization: Bearer {token}
Permission: app.inventories.alerts.index
```

### Expiring Products
```http
GET /api/app/inventories/alerts/expiring?days=30
Authorization: Bearer {token}
Permission: app.inventories.alerts.index
```

## Sales Management

### Transactions

#### List Transactions
```http
GET /api/app/sells/transactions?page=1&per_page=15&date_from=2025-01-01&date_to=2025-01-31
Authorization: Bearer {token}
Permission: app.sells.transactions.index
```

#### Create Transaction
```http
POST /api/app/sells/transactions
Authorization: Bearer {token}
Permission: app.sells.transactions.store
Content-Type: application/json

{
  "customer_name": "John Doe",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 5000
    }
  ],
  "payment_method": "cash",
  "paid_amount": 10000
}
```

#### Get Transaction Detail
```http
GET /api/app/sells/transactions/{id}
Authorization: Bearer {token}
Permission: app.sells.transactions.show
```

### Point of Sale (POS)

#### Create POS Transaction
```http
POST /api/app/sells/pos
Authorization: Bearer {token}
Permission: app.sells.pos.store
```

#### Print Receipt
```http
GET /api/app/sells/pos/{id}/receipt
Authorization: Bearer {token}
Permission: app.sells.pos.show
```

## Statistics & Analytics

### Dashboard Stats
```http
GET /api/app/stats/dashboard?period=today
Authorization: Bearer {token}
Permission: app.stats.dashboard.index
```

### Sales Reports
```http
GET /api/app/stats/sales?date_from=2025-01-01&date_to=2025-01-31&group_by=daily
Authorization: Bearer {token}
Permission: app.stats.sales.index
```

### Inventory Reports
```http
GET /api/app/stats/inventory?type=stock_levels
Authorization: Bearer {token}
Permission: app.stats.inventory.index
```

## Options Endpoints (Dropdown Data)

### Roles Options
```http
GET /api/options/roles?search=admin&limit=10
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Roles options retrieved successfully",
  "data": [
    {
      "value": 1,
      "label": "Admin",
      "name": "admin"
    },
    {
      "value": 2,
      "label": "Manager", 
      "name": "manager"
    }
  ]
}
```

### Categories Options
```http
GET /api/options/categories?search=obat&limit=10
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "value": 1,
      "label": "Obat Bebas",
      "description": "Kategori obat yang dapat dibeli tanpa resep"
    }
  ]
}
```

### Suppliers Options
```http
GET /api/options/suppliers?search=kimia&limit=10
Authorization: Bearer {token}
```

### Units Options
```http
GET /api/options/units?search=strip&limit=10
Authorization: Bearer {token}
```

### Products Options
```http
GET /api/options/products?search=paracetamol&limit=10&category_id=1
Authorization: Bearer {token}
```

## Batch Management

### List Batches
```http
GET /api/batches?product_id=1&expiry_date_from=2025-01-01&expiry_date_to=2025-12-31
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "batch_number": "BATCH001",
      "quantity": 100,
      "expiry_date": "2025-12-31",
      "supplier_id": 1,
      "purchase_price": 4000,
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}
```

### Create Batch
```http
POST /api/batches
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "batch_number": "BATCH002",
  "quantity": 50,
  "expiry_date": "2025-12-31",
  "supplier_id": 1,
  "purchase_price": 4000
}
```

### Expiring Batches
```http
GET /api/batches/expiring?days=30
Authorization: Bearer {token}
```

## Point of Sale (POS)

### Create Transaction
```http
POST /api/transactions
Authorization: Bearer {token}
Content-Type: application/json

{
  "customer_name": "John Doe",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 5000
    },
    {
      "product_id": 2,
      "quantity": 1,
      "price": 10000
    }
  ],
  "payment_method": "cash",
  "payment_amount": 25000,
  "discount_amount": 0,
  "tax_amount": 2000
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transaction_number": "TRX-20250101-001",
    "customer_name": "John Doe",
    "subtotal": 20000,
    "discount_amount": 0,
    "tax_amount": 2000,
    "total_amount": 22000,
    "payment_amount": 25000,
    "change_amount": 3000,
    "items": [
      {
        "product_id": 1,
        "product_name": "Paracetamol 500mg",
        "quantity": 2,
        "price": 5000,
        "total": 10000
      }
    ],
    "created_at": "2025-01-01T00:00:00Z"
  }
}
```

### Get Transaction
```http
GET /api/transactions/{id}
Authorization: Bearer {token}
```

### List Transactions
```http
GET /api/transactions?page=1&date_from=2025-01-01&date_to=2025-01-31&customer_name=john
Authorization: Bearer {token}
```

## Reports

### Sales Report
```http
GET /api/reports/sales?date_from=2025-01-01&date_to=2025-01-31&group_by=day
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_sales": 1000000,
      "total_transactions": 50,
      "average_transaction": 20000
    },
    "details": [
      {
        "date": "2025-01-01",
        "sales": 50000,
        "transactions": 5
      }
    ]
  }
}
```

### Inventory Report
```http
GET /api/reports/inventory?category_id=1&low_stock=true
Authorization: Bearer {token}
```

### Financial Report
```http
GET /api/reports/financial?date_from=2025-01-01&date_to=2025-01-31
Authorization: Bearer {token}
```

## Error Handling

### Error Response Format
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### HTTP Status Codes
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `429`: Too Many Requests
- `500`: Internal Server Error

## Rate Limiting

- **Default**: 60 requests per minute per user
- **Authentication**: 5 requests per minute per IP
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

## Pagination

Semua list endpoints mendukung pagination:

```json
{
  "meta": {
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "per_page": 15,
      "total": 150,
      "from": 1,
      "to": 15
    }
  }
}
```

## Testing

### Postman Collection
Import collection dari: `/backend/docs/examples/postman/Q-Pharmacy-API.postman_collection.json`

### cURL Examples
Lihat file: `/backend/docs/examples/api-requests/`

## Changelog

### v1.0.0 (2025-01-01)
- Initial API release
- Authentication endpoints
- User management
- Product management
- Basic POS functionality

### v1.1.0 (2025-02-01)
- Batch management
- Advanced reporting
- Performance improvements
# Arsitektur Sistem Q-Pharmacy

## Overview

Q-Pharmacy adalah sistem manajemen apotek modern yang dibangun dengan arsitektur client-server menggunakan Laravel 12 sebagai backend API dan Quasar Framework (Vue.js 3) sebagai frontend. Sistem ini dirancang dengan prinsip separation of concerns, scalability, dan maintainability.

## Arsitektur Tingkat Tinggi

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │   Mobile    │    │   Tablet    │    │   Desktop   │         │
│  │   Browser   │    │   Browser   │    │   Browser   │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │   Quasar    │    │   Vue.js 3  │    │    Pinia    │         │
│  │ Components  │    │ Components  │    │   Stores    │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      API GATEWAY                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    CORS     │    │    Auth     │    │ Rate Limit  │         │
│  │ Middleware  │    │ Middleware  │    │ Middleware  │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │ Controllers │    │  Services   │    │ Repositories│         │
│  │   (API)     │    │   Layer     │    │   Layer     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     DATA ACCESS LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │  Eloquent   │    │ Migrations  │    │   Seeders   │         │
│  │    ORM      │    │             │    │             │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    MySQL    │    │   SQLite    │    │    Redis    │         │
│  │ (Production)│    │(Development)│    │   (Cache)   │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

## Frontend Architecture (Quasar + Vue.js 3)

### Struktur Komponen

```
frontend/web/src/
├── boot/                    # Quasar boot files
│   ├── auth.js              # Auth initialization
│   ├── axios.js             # HTTP client setup
│   └── i18n.js              # Internationalization
├── components/              # Reusable components
│   ├── common/              # Common UI components
│   ├── forms/               # Form components
│   └── charts/              # Chart components
├── layouts/                 # Layout templates
│   ├── AuthLayout.vue       # Authentication layout
│   ├── MainLayout.vue       # Main app layout
│   └── PublicLayout.vue     # Public pages layout
├── pages/                   # Page components
│   ├── auth/                # Authentication pages
│   ├── dashboard/           # Dashboard pages
│   ├── products/            # Product management
│   ├── reports/             # Reports & analytics
│   └── settings/            # System settings
├── router/                  # Vue Router
│   ├── guard.js             # Route guards
│   ├── index.js             # Router configuration
│   └── routes.js            # Route definitions
├── services/                # API services
│   ├── auth.js              # Authentication API
│   ├── products.js          # Products API
│   └── index.js             # Service aggregator
└── stores/                  # Pinia stores
    ├── auth.js              # Auth state management
    ├── products.js          # Products state
    └── themes.js            # Theme management
```

### Data Flow Frontend

```
User Interaction
       │
       ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Vue       │───▶│   Pinia     │───▶│   Service   │
│ Component   │    │   Store     │    │   Layer     │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Props &   │    │   State     │    │ HTTP Client │
│   Events    │    │ Management  │    │   (Axios)   │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Component   │    │ Reactive    │    │   Backend   │
│ Re-render   │◀───│ Updates     │◀───│   (Laravel) │
└─────────────┘    └─────────────┘    └─────────────┘
```

## Backend Architecture (Laravel 12)

### Diagram Sistem Backend

```
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │   Quasar    │    │   Vue.js 3  │    │    Pinia    │         │
│  │ Components  │    │ Components  │    │   Stores    │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      API GATEWAY                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    CORS     │    │    Auth     │    │ Rate Limit  │         │
│  │ Middleware  │    │ Middleware  │    │ Middleware  │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                  LARAVEL APPLICATION                            │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │ Controllers │    │  Services   │    │ Repositories│         │
│  │   Layer     │    │   Layer     │    │   Layer     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     MODELS LAYER                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │  Eloquent   │    │   Events    │    │    Jobs     │         │
│  │   Models    │    │             │    │   Queue     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATA LAYER                                 │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    MySQL    │    │    Redis    │    │   Storage   │         │
│  │  Database   │    │   Cache     │    │    Files    │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

### Pola Arsitektur

#### 1. Repository Pattern
Memisahkan logika akses data dari business logic:

```php
// Interface Repository
interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findByCategory(int $categoryId): Collection;
    public function create(array $data): Product;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

// Implementasi Repository
class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }
    
    public function findByCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)->get();
    }
}
```

#### 2. Service Layer Pattern
Mengelola business logic dan orchestration:

```php
class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private InventoryService $inventoryService
    ) {}
    
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        
        try {
            $product = $this->productRepository->create($data);
            
            // Update inventory
            $this->inventoryService->initializeStock($product->id, $data['initial_stock']);
            
            // Trigger events
            event(new ProductCreated($product));
            
            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
```

#### 3. Event-Driven Architecture
Menggunakan events untuk loose coupling:

```php
// Event
class ProductCreated
{
    public function __construct(public Product $product) {}
}

// Listener
class UpdateInventoryListener
{
    public function handle(ProductCreated $event): void
    {
        // Update inventory logic
        InventoryService::updateStock($event->product);
    }
}
```

### Struktur Backend

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/    # API Controllers
│   │   ├── Middleware/            # Custom middleware
│   │   ├── Requests/              # Form requests
│   │   └── Resources/             # API resources
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic
│   ├── Repositories/             # Data access layer
│   ├── Events/                   # Event classes
│   ├── Jobs/                     # Queue jobs
│   └── Policies/                 # Authorization policies
├── database/
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories
├── routes/
│   ├── api.php                   # API routes
│   └── web.php                   # Web routes
└── config/                       # Configuration files
```

### Struktur Modul

#### Authentication Module
```
app/
├── Http/Controllers/Api/V1/Auth/
│   ├── LoginController.php
│   ├── RegisterController.php
│   └── LogoutController.php
├── Services/Auth/
│   ├── AuthService.php
│   └── TokenService.php
├── Repositories/Auth/
│   └── UserRepository.php
└── Models/
    ├── User.php
    └── PersonalAccessToken.php
```

#### Product Module
```
app/
├── Http/Controllers/Api/V1/Products/
│   ├── ProductController.php
│   └── CategoryController.php
├── Services/Products/
│   ├── ProductService.php
│   └── CategoryService.php
├── Repositories/Products/
│   ├── ProductRepository.php
│   └── CategoryRepository.php
└── Models/
    ├── Product.php
    └── Category.php
```

#### Inventory Module
```
app/
├── Http/Controllers/Api/V1/Inventory/
│   ├── StockController.php
│   └── MovementController.php
├── Services/Inventory/
│   ├── InventoryService.php
│   └── StockMovementService.php
├── Repositories/Inventory/
│   ├── InventoryRepository.php
│   └── StockMovementRepository.php
└── Models/
    ├── Inventory.php
    └── StockMovement.php
```

### Data Flow Backend

```
HTTP Request
     │
     ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Middleware  │───▶│ Controller  │───▶│  Request    │
│ (Auth, etc) │    │             │    │ Validation  │
└─────────────┘    └─────────────┘    └─────────────┘
                           │                   │
                           ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │   Service   │───▶│ Repository  │
                   │   Layer     │    │   Layer     │
                   └─────────────┘    └─────────────┘
                           │                   │
                           ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │   Events    │    │   Models    │
                   │   & Jobs    │    │ (Eloquent)  │
                   └─────────────┘    └─────────────┘
                           │                   │
                           ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │ Background  │    │  Database   │
                   │ Processing  │    │   (MySQL)   │
                   └─────────────┘    └─────────────┘
                           │                   │
                           ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │  Response   │◀───│   Result    │
                   │ (JSON API)  │    │   Data      │
                   └─────────────┘    └─────────────┘
```

## Teknologi Stack

### Frontend
- **Framework**: Quasar Framework 2.16.0
- **JavaScript Framework**: Vue.js 3.5.20
- **State Management**: Pinia
- **HTTP Client**: Axios
- **Build Tool**: Vite
- **CSS Framework**: Quasar CSS
- **Icons**: Material Design Icons
- **Internationalization**: Vue I18n

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Database ORM**: Eloquent
- **Queue**: Laravel Queue
- **Cache**: Redis
- **Testing**: PHPUnit

### Database
- **Production**: MySQL 8.0+
- **Development**: SQLite
- **Cache**: Redis
- **Search**: Laravel Scout (optional)

## Prinsip Desain

### 1. Separation of Concerns
- Frontend dan backend terpisah sepenuhnya
- Business logic di service layer
- Data access di repository layer
- Presentation logic di component layer

### 2. Scalability
- Horizontal scaling dengan load balancer
- Database sharding untuk data besar
- Caching strategy dengan Redis
- CDN untuk static assets

### 3. Security
- API authentication dengan Sanctum
- Role-based access control
- Input validation dan sanitization
- CORS configuration
- Rate limiting

### 4. Performance
- Lazy loading components
- Database query optimization
- Caching strategy
- Image optimization
- Code splitting

### 5. Maintainability
- Clean code principles
- SOLID principles
- Design patterns implementation
- Comprehensive testing
- Documentation

## Deployment Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         PRODUCTION                              │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │     CDN     │    │ Load        │    │   Nginx     │         │
│  │  (Static)   │    │ Balancer    │    │ (Reverse    │         │
│  │             │    │             │    │  Proxy)     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
│                              │                   │             │
│                              ▼                   ▼             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │   Frontend  │    │   Backend   │    │   Backend   │         │
│  │  (Quasar)   │    │ (Laravel)   │    │ (Laravel)   │         │
│  │   Server    │    │  Instance 1 │    │  Instance 2 │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
│                              │                   │             │
│                              ▼                   ▼             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    Redis    │    │    MySQL    │    │   Queue     │         │
│  │   (Cache)   │    │ (Database)  │    │  Worker     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

## Desain Database

### Diagram Entitas Hubungan

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    Users    │    │    Roles    │    │ Permissions │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ id (PK)     │    │ id (PK)     │    │ id (PK)     │
│ name        │    │ name        │    │ name        │
│ email       │    │ guard_name  │    │ guard_name  │
│ password    │    │ created_at  │    │ created_at  │
│ created_at  │    │ updated_at  │    │ updated_at  │
│ updated_at  │    └─────────────┘    └─────────────┘
└─────────────┘           │                   │
       │                  │                   │
       │            ┌─────────────┐    ┌─────────────┐
       │            │ User_Roles  │    │Role_Perms   │
       │            ├─────────────┤    ├─────────────┤
       └────────────│ user_id(FK) │    │ role_id(FK) │
                    │ role_id(FK) │    │ perm_id(FK) │
                    └─────────────┘    └─────────────┘

┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Products   │    │ Categories  │    │ Inventory   │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ id (PK)     │    │ id (PK)     │    │ id (PK)     │
│ name        │────│ name        │    │ product_id  │
│ category_id │    │ description │    │ quantity    │
│ price       │    │ created_at  │    │ min_stock   │
│ description │    │ updated_at  │    │ created_at  │
│ created_at  │    └─────────────┘    │ updated_at  │
│ updated_at  │                       └─────────────┘
└─────────────┘
       │
       │
┌─────────────┐    ┌─────────────┐
│    Sales    │    │ Sale_Items  │
├─────────────┤    ├─────────────┤
│ id (PK)     │    │ id (PK)     │
│ user_id(FK) │────│ sale_id(FK) │
│ total       │    │ product_id  │
│ status      │    │ quantity    │
│ created_at  │    │ price       │
│ updated_at  │    │ subtotal    │
└─────────────┘    └─────────────┘
```

## Arsitektur Keamanan

### Flow Autentikasi

```
Client Request
     │
     ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Login     │───▶│  Validate   │───▶│   Generate  │
│ Credentials │    │ Credentials │    │    Token    │
└─────────────┘    └─────────────┘    └─────────────┘
                           │                   │
                           ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │   Failed    │    │   Success   │
                   │  Response   │    │  Response   │
                   └─────────────┘    └─────────────┘
```

### Lapisan Otorisasi

```php
// Middleware untuk API Routes
class ApiAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $next($request);
    }
}

// Policy untuk Resource Authorization
class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('view products');
    }
    
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create products');
    }
    
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('update products');
    }
}
```

## Optimasi Performa

### Strategi Caching

```php
// Repository dengan Cache
class ProductRepository
{
    public function findById(int $id): ?Product
    {
        return Cache::remember(
            "product.{$id}",
            3600, // 1 hour
            fn() => Product::find($id)
        );
    }
    
    public function findByCategory(int $categoryId): Collection
    {
        return Cache::remember(
            "products.category.{$categoryId}",
            1800, // 30 minutes
            fn() => Product::where('category_id', $categoryId)->get()
        );
    }
}
```

### Optimasi Database

```php
// Eager Loading untuk menghindari N+1 Problem
class ProductService
{
    public function getProductsWithCategory(): Collection
    {
        return Product::with(['category', 'inventory'])
            ->orderBy('name')
            ->get();
    }
    
    // Query Optimization dengan Index
    public function searchProducts(string $term): Collection
    {
        return Product::where('name', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%")
            ->with('category')
            ->limit(50)
            ->get();
    }
}
```

## Monitoring & Logging

### Metrik Aplikasi
- Response time per endpoint
- Error rate dan status codes
- Database query performance
- Memory usage dan CPU utilization

### Metrik Sistem
- Server uptime dan availability
- Network latency
- Disk usage dan I/O performance
- Load balancer metrics

### Metrik Bisnis
- User registration rate
- Transaction volume
- Revenue metrics
- Feature usage analytics

### Tools Monitoring
- **Application Monitoring**: Laravel Telescope
- **Error Tracking**: Sentry
- **Performance Monitoring**: New Relic
- **Log Management**: ELK Stack
- **Uptime Monitoring**: Pingdom
- **Database Monitoring**: MySQL Performance Schema

## Arsitektur Deployment

### Lingkungan Produksi

```
┌─────────────────────────────────────────────────────────────────┐
│                      PRODUCTION ENVIRONMENT                     │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │     CDN     │    │ Load        │    │   Nginx     │         │
│  │ (Cloudflare)│    │ Balancer    │    │ (Reverse    │         │
│  │             │    │ (HAProxy)   │    │  Proxy)     │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
│                              │                   │             │
│                              ▼                   ▼             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │   Frontend  │    │   Backend   │    │   Backend   │         │
│  │  (Quasar)   │    │ (Laravel)   │    │ (Laravel)   │         │
│  │   Server    │    │  App Server │    │  App Server │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
│                              │                   │             │
│                              ▼                   ▼             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐         │
│  │    Redis    │    │    MySQL    │    │   Queue     │         │
│  │   Cluster   │    │   Cluster   │    │  Workers    │         │
│  │  (Cache)    │    │ (Master/    │    │ (Supervisor)│         │
│  │             │    │  Slave)     │    │             │         │
│  └─────────────┘    └─────────────┘    └─────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

## Strategi Versi API

### URL Versioning
```
GET /api/v1/products
GET /api/v2/products
```

### Header Versioning
```
GET /api/products
Headers: Accept: application/vnd.api+json;version=1
```

### Backward Compatibility
- Maintain support untuk 2 versi sebelumnya
- Deprecation warnings untuk versi lama
- Migration guides untuk breaking changes
- Semantic versioning untuk API releases

## Backup & Recovery

- **Database Backup**: Daily automated backups
- **File Backup**: S3 compatible storage
- **Code Backup**: Git repository
- **Recovery Testing**: Monthly recovery drills
- **Disaster Recovery**: Multi-region deployment

## Kesimpulan

Arsitektur backend Q-Pharmacy dirancang untuk:
- **Scalability**: Mendukung pertumbuhan pengguna dan data
- **Maintainability**: Code yang mudah dipelihara dan dikembangkan
- **Security**: Perlindungan data dan sistem yang komprehensif
- **Performance**: Response time yang optimal untuk user experience
- **Reliability**: High availability dan disaster recovery
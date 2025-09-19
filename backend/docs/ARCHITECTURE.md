# Backend Architecture Documentation

## Overview

Dokumentasi arsitektur sistem backend Q-Pharmacy yang dibangun menggunakan Laravel 12 dengan pendekatan modular dan scalable architecture.

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Frontend Layer                          │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Web Client    │  │  Mobile Client  │  │  Admin Panel    │ │
│  │   (Vue.js)      │  │   (Flutter)     │  │   (Vue.js)      │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                         API Gateway                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │  Rate Limiting  │  │  Authentication │  │   CORS Policy   │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Laravel Application                       │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                    Controllers                          │   │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────────┐   │   │
│  │  │    Auth     │ │   Product   │ │   Transaction   │   │   │
│  │  │ Controller  │ │ Controller  │ │   Controller    │   │   │
│  │  └─────────────┘ └─────────────┘ └─────────────────┘   │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                │                                │
│                                ▼                                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                   Services Layer                       │   │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────────┐   │   │
│  │  │    Auth     │ │  Inventory  │ │   Transaction   │   │   │
│  │  │   Service   │ │   Service   │ │    Service      │   │   │
│  │  └─────────────┘ └─────────────┘ └─────────────────┘   │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                │                                │
│                                ▼                                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                 Repository Layer                       │   │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────────┐   │   │
│  │  │    User     │ │   Product   │ │   Transaction   │   │   │
│  │  │ Repository  │ │ Repository  │ │   Repository    │   │   │
│  │  └─────────────┘ └─────────────┘ └─────────────────┘   │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                │                                │
│                                ▼                                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                   Models Layer                         │   │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────────┐   │   │
│  │  │    User     │ │   Product   │ │   Transaction   │   │   │
│  │  │    Model    │ │    Model    │ │     Model       │   │   │
│  │  └─────────────┘ └─────────────┘ └─────────────────┘   │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Data Layer                               │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │     MySQL       │  │      Redis      │  │   File Storage  │ │
│  │   (Primary DB)  │  │    (Cache)      │  │    (Images)     │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## Architectural Patterns

### 1. Repository Pattern

Menggunakan Repository Pattern untuk abstraksi data access layer:

```php
// Interface
interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findByBarcode(string $barcode): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
}

// Implementation
class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $model) {}
    
    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }
    
    // ... other methods
}
```

### 2. Service Layer Pattern

Business logic dipisahkan ke dalam service classes:

```php
class InventoryService
{
    public function __construct(
        private ProductRepository $productRepo,
        private BatchRepository $batchRepo,
        private StockMovementRepository $stockMovementRepo
    ) {}
    
    public function adjustStock(int $productId, int $quantity, string $reason): void
    {
        $product = $this->productRepo->findById($productId);
        
        if (!$product) {
            throw new ProductNotFoundException();
        }
        
        // Business logic for stock adjustment
        $this->updateProductStock($product, $quantity);
        $this->recordStockMovement($product, $quantity, $reason);
        $this->checkLowStockAlert($product);
    }
}
```

### 3. Event-Driven Architecture

Menggunakan Laravel Events untuk loose coupling:

```php
// Event
class ProductStockUpdated
{
    public function __construct(
        public Product $product,
        public int $oldStock,
        public int $newStock
    ) {}
}

// Listener
class CheckLowStockAlert
{
    public function handle(ProductStockUpdated $event): void
    {
        if ($event->newStock <= $event->product->minimum_stock) {
            // Send low stock alert
        }
    }
}
```

## Module Structure

### Authentication Module
```
app/Modules/Auth/
├── Controllers/
│   ├── AuthController.php
│   └── UserController.php
├── Services/
│   ├── AuthService.php
│   └── UserService.php
├── Repositories/
│   └── UserRepository.php
├── Models/
│   └── User.php
├── Requests/
│   ├── LoginRequest.php
│   └── RegisterRequest.php
├── Resources/
│   └── UserResource.php
└── Events/
    ├── UserRegistered.php
    └── UserLoggedIn.php
```

### Product Module
```
app/Modules/Product/
├── Controllers/
│   ├── ProductController.php
│   ├── CategoryController.php
│   └── UnitController.php
├── Services/
│   ├── ProductService.php
│   └── CategoryService.php
├── Repositories/
│   ├── ProductRepository.php
│   └── CategoryRepository.php
├── Models/
│   ├── Product.php
│   ├── Category.php
│   └── Unit.php
└── Observers/
    └── ProductObserver.php
```

### Inventory Module
```
app/Modules/Inventory/
├── Controllers/
│   ├── InventoryController.php
│   └── BatchController.php
├── Services/
│   ├── InventoryService.php
│   └── BatchService.php
├── Repositories/
│   ├── StockMovementRepository.php
│   └── BatchRepository.php
├── Models/
│   ├── StockMovement.php
│   └── Batch.php
└── Jobs/
    ├── ProcessStockMovement.php
    └── CheckExpiringBatches.php
```

## Database Design

### Entity Relationship Diagram

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│      Users      │     │    Products     │     │   Categories    │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id (PK)         │     │ id (PK)         │     │ id (PK)         │
│ name            │     │ name            │     │ name            │
│ email           │     │ barcode         │     │ description     │
│ password        │     │ price           │     │ created_at      │
│ created_at      │     │ stock           │     │ updated_at      │
│ updated_at      │     │ category_id(FK) │     └─────────────────┘
└─────────────────┘     │ unit_id (FK)    │              │
         │               │ image_path      │              │
         │               │ created_at      │              │
         │               │ updated_at      │              │
         │               └─────────────────┘              │
         │                        │                       │
         │                        └───────────────────────┘
         │
         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Transactions   │     │ Transaction     │     │     Batches     │
├─────────────────┤     │     Items       │     ├─────────────────┤
│ id (PK)         │────▶├─────────────────┤     │ id (PK)         │
│ user_id (FK)    │     │ id (PK)         │     │ product_id (FK) │
│ customer_name   │     │ transaction_id  │     │ batch_number    │
│ total_amount    │     │ product_id (FK) │     │ quantity        │
│ payment_method  │     │ quantity        │     │ expiry_date     │
│ created_at      │     │ price           │     │ supplier_id     │
│ updated_at      │     │ total           │     │ purchase_price  │
└─────────────────┘     └─────────────────┘     │ created_at      │
                                 │               │ updated_at      │
                                 │               └─────────────────┘
                                 │                        │
                                 └────────────────────────┘
```

## Security Architecture

### Authentication Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Client    │    │     API     │    │   Laravel   │    │  Database   │
│             │    │   Gateway   │    │ Application │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
       │ POST /auth/login  │                   │                   │
       ├──────────────────▶│                   │                   │
       │                   │ Validate Request  │                   │
       │                   ├──────────────────▶│                   │
       │                   │                   │ Check Credentials │
       │                   │                   ├──────────────────▶│
       │                   │                   │ User Data         │
       │                   │                   │◀──────────────────┤
       │                   │ Generate Token    │                   │
       │                   │◀──────────────────┤                   │
       │ Token Response    │                   │                   │
       │◀──────────────────┤                   │                   │
       │                   │                   │                   │
       │ API Request       │                   │                   │
       │ + Bearer Token    │                   │                   │
       ├──────────────────▶│                   │                   │
       │                   │ Verify Token      │                   │
       │                   ├──────────────────▶│                   │
       │                   │ Process Request   │                   │
       │                   │◀──────────────────┤                   │
       │ API Response      │                   │                   │
       │◀──────────────────┤                   │                   │
```

### Authorization Layers

1. **Route Middleware**: Basic authentication check
2. **Permission Middleware**: Role-based access control
3. **Policy Classes**: Resource-specific authorization
4. **Gate Definitions**: Custom authorization logic

```php
// Route definition
Route::middleware(['auth:sanctum', 'permission:manage-products'])
    ->group(function () {
        Route::apiResource('products', ProductController::class);
    });

// Policy example
class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('edit-products') || 
               $user->id === $product->created_by;
    }
}
```

## Performance Optimization

### Caching Strategy

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Application   │    │      Redis      │    │     MySQL       │
│     Cache       │    │     Cache       │    │    Database     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │ 1. Check App Cache    │                       │
         ├──────────────────────▶│                       │
         │ 2. Cache Miss         │                       │
         │◀──────────────────────┤                       │
         │ 3. Check Redis        │                       │
         ├──────────────────────▶│                       │
         │ 4. Cache Miss         │                       │
         │◀──────────────────────┤                       │
         │ 5. Query Database     │                       │
         ├───────────────────────┼──────────────────────▶│
         │ 6. Data Response      │                       │
         │◀──────────────────────┼───────────────────────┤
         │ 7. Store in Redis     │                       │
         ├──────────────────────▶│                       │
         │ 8. Store in App Cache │                       │
         ├──────────────────────▶│                       │
```

### Database Optimization

1. **Indexing Strategy**:
   - Primary keys (auto-indexed)
   - Foreign keys
   - Frequently queried columns
   - Composite indexes for complex queries

2. **Query Optimization**:
   - Eager loading relationships
   - Query scopes for reusable logic
   - Database query monitoring
   - N+1 query prevention

3. **Connection Pooling**:
   - Read/write splitting
   - Connection reuse
   - Timeout configuration

## Monitoring & Logging

### Application Monitoring

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Application   │    │    Monitoring   │    │   Alert System │
│     Metrics     │    │     Service     │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │ Performance Metrics   │                       │
         ├──────────────────────▶│                       │
         │ Error Logs            │                       │
         ├──────────────────────▶│                       │
         │ Business Metrics      │                       │
         ├──────────────────────▶│                       │
         │                       │ Threshold Exceeded    │
         │                       ├──────────────────────▶│
         │                       │                       │
         │                       │ Send Notification     │
         │                       │◀──────────────────────┤
```

### Key Metrics

1. **Performance Metrics**:
   - Response time
   - Throughput (requests/second)
   - Error rate
   - Database query time

2. **Business Metrics**:
   - Transaction volume
   - User activity
   - Revenue tracking
   - Inventory turnover

3. **System Metrics**:
   - CPU usage
   - Memory consumption
   - Disk I/O
   - Network latency

## Deployment Architecture

### Production Environment

```
┌─────────────────────────────────────────────────────────────────┐
│                        Load Balancer                           │
│                         (Nginx)                                │
└─────────────────────────────────────────────────────────────────┘
                                │
                ┌───────────────┼───────────────┐
                ▼               ▼               ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│   App Server 1  │ │   App Server 2  │ │   App Server 3  │
│   (Laravel)     │ │   (Laravel)     │ │   (Laravel)     │
└─────────────────┘ └─────────────────┘ └─────────────────┘
         │                   │                   │
         └───────────────────┼───────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Database Cluster                          │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Master DB     │  │   Slave DB 1    │  │   Slave DB 2    │ │
│  │   (Write)       │  │   (Read)        │  │   (Read)        │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Redis Cluster                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Redis Node 1  │  │   Redis Node 2  │  │   Redis Node 3  │ │
│  │   (Master)      │  │   (Slave)       │  │   (Slave)       │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## API Versioning Strategy

### URL Versioning
```
/api/v1/products
/api/v2/products
```

### Header Versioning
```
Accept: application/vnd.qpharmacy.v1+json
Accept: application/vnd.qpharmacy.v2+json
```

### Backward Compatibility
- Maintain previous API versions
- Gradual deprecation process
- Clear migration documentation
- Version sunset timeline

## Conclusion

Arsitektur backend Q-Pharmacy dirancang untuk:
- **Scalability**: Dapat menangani pertumbuhan traffic dan data
- **Maintainability**: Kode yang mudah dipelihara dan dikembangkan
- **Security**: Implementasi keamanan berlapis
- **Performance**: Optimasi untuk response time yang cepat
- **Reliability**: High availability dan fault tolerance

Arsitektur ini akan terus berkembang seiring dengan kebutuhan bisnis dan teknologi terbaru.
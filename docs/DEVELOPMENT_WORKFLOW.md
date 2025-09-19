# Development Workflow & Guidelines

## Overview

Dokumen ini menjelaskan workflow pengembangan, standar coding, dan guidelines untuk proyek Q-Pharmacy. Panduan ini memastikan konsistensi, kualitas, dan kolaborasi yang efektif antar tim developer.

## Table of Contents

- [Git Workflow](#git-workflow)
- [Branch Strategy](#branch-strategy)
- [Code Standards](#code-standards)
- [Development Environment](#development-environment)
- [Code Review Process](#code-review-process)
- [Testing Guidelines](#testing-guidelines)
- [Deployment Process](#deployment-process)
- [Documentation Standards](#documentation-standards)
- [Performance Guidelines](#performance-guidelines)
- [Security Guidelines](#security-guidelines)
- [Troubleshooting](#troubleshooting)

## Git Workflow

### Branch Strategy

Kami menggunakan **Git Flow** dengan modifikasi untuk mendukung continuous integration:

```
main (production)
├── develop (integration)
│   ├── feature/user-authentication
│   ├── feature/product-management
│   ├── feature/inventory-tracking
│   └── feature/pos-system
├── release/v1.0.0
├── hotfix/critical-bug-fix
└── bugfix/minor-issue-fix
```

### Branch Types

#### Main Branches
- **`main`**: Production-ready code
- **`develop`**: Integration branch untuk development

#### Supporting Branches
- **`feature/*`**: New features
- **`bugfix/*`**: Bug fixes
- **`hotfix/*`**: Critical production fixes
- **`release/*`**: Release preparation
- **`chore/*`**: Maintenance tasks

### Branch Naming Convention

```bash
# Features
feature/user-authentication
feature/product-catalog
feature/inventory-management

# Bug fixes
bugfix/login-validation-error
bugfix/product-search-issue

# Hotfixes
hotfix/security-vulnerability
hotfix/payment-gateway-error

# Releases
release/v1.0.0
release/v1.1.0

# Chores
chore/update-dependencies
chore/refactor-api-structure
```

### Commit Message Convention

Menggunakan **Conventional Commits** format:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

#### Types
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting, etc.)
- **refactor**: Code refactoring
- **test**: Adding or updating tests
- **chore**: Maintenance tasks
- **perf**: Performance improvements
- **ci**: CI/CD changes

#### Examples

```bash
# Feature
feat(auth): add user login functionality

# Bug fix
fix(products): resolve search filter issue

# Documentation
docs(api): update authentication endpoints

# Refactoring
refactor(components): extract reusable button component

# Breaking change
feat(api)!: change user authentication flow

BREAKING CHANGE: authentication now requires email verification
```

### Git Commands Workflow

#### Starting New Feature

```bash
# Update develop branch
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/user-authentication

# Work on feature...
git add .
git commit -m "feat(auth): implement user login form"

# Push feature branch
git push -u origin feature/user-authentication
```

#### Finishing Feature

```bash
# Update feature branch with latest develop
git checkout develop
git pull origin develop
git checkout feature/user-authentication
git rebase develop

# Push updated feature
git push --force-with-lease origin feature/user-authentication

# Create Pull Request via GitHub/GitLab
```

#### Hotfix Process

```bash
# Create hotfix from main
git checkout main
git pull origin main
git checkout -b hotfix/security-vulnerability

# Fix issue
git add .
git commit -m "fix(security): patch authentication vulnerability"

# Push hotfix
git push -u origin hotfix/security-vulnerability

# Create PR to main AND develop
```

## Code Standards

### General Principles

1. **SOLID Principles**
2. **DRY (Don't Repeat Yourself)**
3. **KISS (Keep It Simple, Stupid)**
4. **YAGNI (You Aren't Gonna Need It)**
5. **Clean Code Principles**

### Backend Standards (Laravel/PHP)

#### PSR Standards
- Follow PSR-12 coding standard
- Use PSR-4 autoloading
- Implement PSR-7 HTTP message interfaces

#### Laravel Conventions

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getPaginatedProducts(
            $request->get('search'),
            $request->get('category'),
            $request->get('per_page', 15)
        );

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->validated()
        );

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product created successfully'
        ], 201);
    }
}
```

#### Naming Conventions

```php
// Models (singular, PascalCase)
class Product extends Model {}
class UserProfile extends Model {}

// Controllers (PascalCase + Controller suffix)
class ProductController extends Controller {}
class UserProfileController extends Controller {}

// Services (PascalCase + Service suffix)
class ProductService {}
class NotificationService {}

// Requests (PascalCase + Request suffix)
class StoreProductRequest extends FormRequest {}
class UpdateUserRequest extends FormRequest {}

// Resources (PascalCase + Resource suffix)
class ProductResource extends JsonResource {}
class UserResource extends JsonResource {}

// Migrations (snake_case with descriptive action)
2024_01_01_000000_create_products_table.php
2024_01_02_000000_add_category_to_products_table.php

// Database tables (snake_case, plural)
products
user_profiles
product_categories

// Variables and methods (camelCase)
$productName = 'Aspirin';
$userProfile = $user->profile;
public function getActiveProducts() {}

// Constants (SCREAMING_SNAKE_CASE)
const MAX_UPLOAD_SIZE = 1024;
const DEFAULT_PAGINATION_LIMIT = 15;
```

### Frontend Standards (Vue.js)

#### Vue.js Conventions

```vue
<template>
  <div class="product-card">
    <div class="product-card__header">
      <h3 class="product-card__title">{{ product.name }}</h3>
      <span class="product-card__price">${{ formatPrice(product.price) }}</span>
    </div>
    
    <div class="product-card__content">
      <p class="product-card__description">{{ product.description }}</p>
    </div>
    
    <div class="product-card__actions">
      <BaseButton 
        variant="primary" 
        @click="handleAddToCart"
        :loading="isLoading"
      >
        Add to Cart
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import BaseButton from '@/components/base/BaseButton.vue'
import { formatCurrency } from '@/utils/formatters'

// Props
const props = defineProps({
  product: {
    type: Object,
    required: true,
    validator: (product) => {
      return product && typeof product.id === 'number' && product.name
    }
  }
})

// Emits
const emit = defineEmits(['add-to-cart', 'view-details'])

// Composables
const cartStore = useCartStore()

// State
const isLoading = ref(false)

// Computed
const formatPrice = computed(() => {
  return (price) => formatCurrency(price, 'USD')
})

// Methods
const handleAddToCart = async () => {
  try {
    isLoading.value = true
    await cartStore.addItem(props.product)
    emit('add-to-cart', props.product)
  } catch (error) {
    console.error('Failed to add product to cart:', error)
  } finally {
    isLoading.value = false
  }
}
</script>

<style lang="scss" scoped>
.product-card {
  @apply bg-white rounded-lg shadow-md p-4 transition-shadow hover:shadow-lg;
  
  &__header {
    @apply flex justify-between items-start mb-3;
  }
  
  &__title {
    @apply text-lg font-semibold text-gray-900 mb-1;
  }
  
  &__price {
    @apply text-xl font-bold text-primary-600;
  }
  
  &__content {
    @apply mb-4;
  }
  
  &__description {
    @apply text-gray-600 text-sm line-clamp-3;
  }
  
  &__actions {
    @apply flex justify-end;
  }
}
</style>
```

#### Naming Conventions

```javascript
// Components (PascalCase)
ProductCard.vue
UserProfile.vue
BaseButton.vue

// Composables (camelCase with 'use' prefix)
useAuth.js
useProducts.js
useApi.js

// Stores (camelCase with 'Store' suffix)
authStore.js
productStore.js
cartStore.js

// Utils (camelCase)
formatters.js
validators.js
helpers.js

// Constants (SCREAMING_SNAKE_CASE)
const API_BASE_URL = 'https://api.example.com'
const MAX_FILE_SIZE = 1024 * 1024

// Variables and functions (camelCase)
const userName = 'john_doe'
const isAuthenticated = true
function getUserProfile() {}

// CSS classes (kebab-case with BEM)
.product-card {}
.product-card__title {}
.product-card__title--highlighted {}
```

### Database Standards

#### Table Design

```sql
-- Table naming: snake_case, plural
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2),
    category_id BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_products_sku (sku),
    INDEX idx_products_category (category_id),
    INDEX idx_products_active (is_active),
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Junction tables: alphabetical order
CREATE TABLE product_suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    supplier_sku VARCHAR(100),
    cost_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_product_supplier (product_id, supplier_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);
```

#### Column Naming

```sql
-- Use snake_case
user_name
first_name
last_name
email_address
phone_number

-- Boolean columns: is_, has_, can_, should_
is_active
has_permission
can_edit
should_notify

-- Foreign keys: table_name_id
user_id
product_id
category_id

-- Timestamps
created_at
updated_at
deleted_at
published_at
```

## Development Environment

### Required Tools

#### Backend Development
- **PHP 8.2+**
- **Composer 2.x**
- **Laravel 10.x**
- **MySQL 8.0+** or **PostgreSQL 14+**
- **Redis 6.x+**
- **Docker & Docker Compose**

#### Frontend Development
- **Node.js 18.x+**
- **npm 9.x+** or **yarn 1.22+**
- **Vue.js 3.x**
- **Quasar Framework 2.x**
- **Vite 4.x+**

#### Development Tools
- **Git 2.x+**
- **VS Code** with extensions:
  - PHP Intelephense
  - Laravel Extension Pack
  - Vetur or Volar (Vue)
  - ESLint
  - Prettier
  - GitLens

### Environment Setup

#### Backend Setup

```bash
# Clone repository
git clone https://github.com/your-org/q-pharmacy.git
cd q-pharmacy

# Backend setup
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

#### Frontend Setup

```bash
# Frontend setup
cd frontend/web
npm install
cp .env.example .env.local

# Start development server
npm run dev
```

#### Docker Setup

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec backend php artisan migrate

# Install frontend dependencies
docker-compose exec frontend npm install
```

### IDE Configuration

#### VS Code Settings

```json
{
  "editor.formatOnSave": true,
  "editor.codeActionsOnSave": {
    "source.fixAll.eslint": true,
    "source.organizeImports": true
  },
  "php.suggest.basic": false,
  "php.validate.enable": false,
  "intelephense.files.maxSize": 3000000,
  "vue.codeActions.enabled": true,
  "vue.complete.casing.tags": "kebab",
  "vue.complete.casing.props": "camel",
  "eslint.validate": [
    "javascript",
    "javascriptreact",
    "vue"
  ],
  "prettier.requireConfig": true,
  "emmet.includeLanguages": {
    "vue-html": "html"
  }
}
```

## Code Review Process

### Pull Request Guidelines

#### PR Template

```markdown
## Description
Brief description of changes made.

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Testing
- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] E2E tests pass
- [ ] Manual testing completed

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Code is commented where necessary
- [ ] Documentation updated
- [ ] No console.log or dd() statements
- [ ] Database migrations are reversible

## Screenshots (if applicable)
[Add screenshots here]

## Related Issues
Closes #123
Related to #456
```

#### Review Checklist

**Code Quality**
- [ ] Code follows established conventions
- [ ] No code duplication
- [ ] Functions are small and focused
- [ ] Variable names are descriptive
- [ ] Comments explain "why", not "what"

**Functionality**
- [ ] Feature works as expected
- [ ] Edge cases are handled
- [ ] Error handling is appropriate
- [ ] Performance considerations addressed

**Testing**
- [ ] Adequate test coverage
- [ ] Tests are meaningful
- [ ] Tests pass consistently
- [ ] Manual testing performed

**Security**
- [ ] Input validation implemented
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] Authentication/authorization checks

**Documentation**
- [ ] API documentation updated
- [ ] README updated if needed
- [ ] Code comments added where necessary

### Review Process

1. **Self Review**: Author reviews their own code
2. **Automated Checks**: CI/CD pipeline runs tests
3. **Peer Review**: At least 2 team members review
4. **Address Feedback**: Author addresses review comments
5. **Final Approval**: Lead developer approves
6. **Merge**: Code is merged to target branch

## Testing Guidelines

### Testing Pyramid

```
        E2E Tests (Few)
      ┌─────────────────┐
     │                 │
    │   Integration     │
   │      Tests         │
  │     (Some)          │
 ┌─────────────────────┐
│                     │
│    Unit Tests       │
│      (Many)         │
└─────────────────────┘
```

### Test Coverage Requirements

- **Unit Tests**: 80% minimum coverage
- **Integration Tests**: Critical paths covered
- **E2E Tests**: Main user journeys covered

### Testing Commands

```bash
# Backend testing
cd backend
php artisan test
php artisan test --coverage
php artisan test --filter ProductTest

# Frontend testing
cd frontend/web
npm run test
npm run test:coverage
npm run test:e2e
```

## Deployment Process

### Environments

1. **Development**: Local development
2. **Staging**: Pre-production testing
3. **Production**: Live application

### Deployment Pipeline

```yaml
# .github/workflows/deploy.yml
name: Deploy Application

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Run Tests
        run: |
          # Backend tests
          cd backend && composer install && php artisan test
          # Frontend tests
          cd frontend/web && npm ci && npm run test
  
  deploy-staging:
    needs: test
    if: github.ref == 'refs/heads/develop'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Staging
        run: |
          # Deployment script
  
  deploy-production:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        run: |
          # Production deployment script
```

### Deployment Checklist

**Pre-deployment**
- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] Backup created

**Deployment**
- [ ] Application deployed
- [ ] Database migrations run
- [ ] Cache cleared
- [ ] Services restarted

**Post-deployment**
- [ ] Health checks pass
- [ ] Monitoring alerts configured
- [ ] Performance metrics normal
- [ ] User acceptance testing

## Documentation Standards

### Code Documentation

#### PHP DocBlocks

```php
/**
 * Create a new product in the system.
 *
 * @param array $data The product data
 * @param User $user The user creating the product
 * @return Product The created product
 * @throws ValidationException When data is invalid
 * @throws AuthorizationException When user lacks permission
 */
public function createProduct(array $data, User $user): Product
{
    // Implementation
}
```

#### JavaScript JSDoc

```javascript
/**
 * Formats a price value with currency symbol
 * @param {number} price - The price to format
 * @param {string} currency - The currency code (e.g., 'USD')
 * @param {string} locale - The locale for formatting
 * @returns {string} The formatted price string
 * @example
 * formatPrice(99.99, 'USD', 'en-US') // '$99.99'
 */
function formatPrice(price, currency = 'USD', locale = 'en-US') {
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currency
  }).format(price)
}
```

### API Documentation

Use OpenAPI/Swagger for API documentation:

```php
/**
 * @OA\Post(
 *     path="/api/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "sku", "price"},
 *             @OA\Property(property="name", type="string", example="Aspirin"),
 *             @OA\Property(property="sku", type="string", example="ASP-001"),
 *             @OA\Property(property="price", type="number", format="float", example=9.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     )
 * )
 */
public function store(StoreProductRequest $request): JsonResponse
{
    // Implementation
}
```

## Performance Guidelines

### Backend Performance

#### Database Optimization

```php
// Use eager loading to prevent N+1 queries
$products = Product::with(['category', 'supplier'])->get();

// Use database indexes
Schema::table('products', function (Blueprint $table) {
    $table->index(['category_id', 'is_active']);
    $table->index('sku');
});

// Use query optimization
$products = Product::select(['id', 'name', 'price'])
    ->where('is_active', true)
    ->orderBy('name')
    ->paginate(15);
```

#### Caching Strategy

```php
// Cache expensive queries
$categories = Cache::remember('product_categories', 3600, function () {
    return Category::with('products')->get();
});

// Use Redis for session storage
'SESSION_DRIVER' => 'redis'

// Cache API responses
Route::middleware('cache.headers:public;max_age=3600')->group(function () {
    Route::get('/api/products', [ProductController::class, 'index']);
});
```

### Frontend Performance

#### Vue.js Optimization

```vue
<script setup>
// Use computed for expensive calculations
const expensiveValue = computed(() => {
  return heavyCalculation(props.data)
})

// Use v-memo for expensive lists
<template>
  <div v-for="item in list" :key="item.id" v-memo="[item.id, item.name]">
    {{ item.name }}
  </div>
</template>

// Lazy load components
const LazyComponent = defineAsyncComponent(() => 
  import('./components/HeavyComponent.vue')
)
</script>
```

#### Bundle Optimization

```javascript
// vite.config.js
export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          ui: ['quasar']
        }
      }
    }
  }
})
```

## Security Guidelines

### Backend Security

#### Input Validation

```php
// Use Form Requests for validation
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ];
    }
}

// Sanitize input
$name = strip_tags($request->input('name'));
$description = htmlspecialchars($request->input('description'));
```

#### Authentication & Authorization

```php
// Use Laravel Sanctum for API authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});

// Implement role-based access control
class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);
        // Implementation
    }
}
```

### Frontend Security

#### XSS Prevention

```vue
<template>
  <!-- Safe: Vue automatically escapes -->  
  <p>{{ userInput }}</p>
  
  <!-- Dangerous: Only use v-html with trusted content -->
  <div v-html="trustedHtml"></div>
  
  <!-- Safe: Use text content -->
  <p v-text="userInput"></p>
</template>
```

#### CSRF Protection

```javascript
// Include CSRF token in API requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = 
  document.querySelector('meta[name="csrf-token"]').getAttribute('content')
```

## Troubleshooting

### Common Issues

#### Backend Issues

**Database Connection Error**
```bash
# Check database configuration
php artisan config:cache
php artisan config:clear

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

**Cache Issues**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Permission Issues**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

#### Frontend Issues

**Build Errors**
```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
```

**Development Server Issues**
```bash
# Check port availability
lsof -ti:3000

# Kill process on port
kill -9 $(lsof -ti:3000)
```

### Debug Tools

#### Backend Debugging

```php
// Use Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev

// Use Telescope for monitoring
composer require laravel/telescope --dev
php artisan telescope:install

// Log debugging
Log::debug('User data', ['user' => $user]);
Log::info('Product created', ['product_id' => $product->id]);
```

#### Frontend Debugging

```javascript
// Vue DevTools
// Install browser extension

// Console debugging
console.log('Component data:', this.$data)
console.table(products)

// Performance monitoring
console.time('expensive-operation')
// ... operation
console.timeEnd('expensive-operation')
```

### Performance Monitoring

#### Backend Monitoring

```php
// Use Laravel Horizon for queue monitoring
composer require laravel/horizon
php artisan horizon:install

// Monitor database queries
DB::listen(function ($query) {
    Log::info('Query executed', [
        'sql' => $query->sql,
        'time' => $query->time
    ]);
});
```

#### Frontend Monitoring

```javascript
// Performance API
const observer = new PerformanceObserver((list) => {
  for (const entry of list.getEntries()) {
    console.log('Performance entry:', entry)
  }
})
observer.observe({ entryTypes: ['navigation', 'resource'] })

// Bundle analyzer
npm run build -- --analyze
```

## Conclusion

Mengikuti workflow dan guidelines ini akan memastikan:

1. **Konsistensi** dalam pengembangan
2. **Kualitas** kode yang tinggi
3. **Kolaborasi** tim yang efektif
4. **Maintainability** jangka panjang
5. **Performance** yang optimal
6. **Security** yang terjaga

Panduan ini adalah dokumen hidup yang akan terus diperbarui seiring dengan evolusi proyek dan teknologi yang digunakan.

---

**Catatan**: Pastikan semua anggota tim memahami dan mengikuti guidelines ini. Lakukan review berkala untuk memastikan guidelines tetap relevan dan efektif.
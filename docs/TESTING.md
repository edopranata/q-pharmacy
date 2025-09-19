# Testing Strategy Q-Pharmacy

## Overview

Dokumen ini menjelaskan strategi pengujian menyeluruh untuk sistem Q-Pharmacy, mencakup unit testing, integration testing, end-to-end testing, dan performance testing dengan target minimum code coverage 80%.

## Testing Philosophy

### Prinsip Testing
1. **Test-Driven Development (TDD)**: Menulis test sebelum implementasi
2. **Pyramid Testing**: Lebih banyak unit test, sedikit integration test, minimal E2E test
3. **Fast Feedback**: Test harus cepat dan memberikan feedback yang jelas
4. **Reliable**: Test harus konsisten dan tidak flaky
5. **Maintainable**: Test code sama pentingnya dengan production code

### Testing Pyramid

```
        ┌─────────────────┐
        │   E2E Tests     │  ← Sedikit, lambat, mahal
        │   (Cypress)     │
        └─────────────────┘
      ┌───────────────────────┐
      │ Integration Tests     │  ← Sedang, medium speed
      │ (PHPUnit + Pest)      │
      └───────────────────────┘
    ┌─────────────────────────────┐
    │      Unit Tests             │  ← Banyak, cepat, murah
    │   (PHPUnit + Pest + Jest)   │
    └─────────────────────────────┘
```

## Backend Testing (Laravel)

### Testing Stack
- **Pest PHP**: Modern testing framework untuk PHP
- **PHPUnit**: Foundation testing framework
- **Laravel Sanctum**: API authentication testing
- **Faker**: Generate test data
- **RefreshDatabase**: Database state management

### Setup Testing Environment

#### PHPUnit Configuration

**phpunit.xml**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
        <exclude>
            <directory>app/Console</directory>
            <file>app/Http/Kernel.php</file>
        </exclude>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

#### Pest Configuration

**tests/Pest.php**
```php
<?php

uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit');

// Global functions
function actingAsAdmin()
{
    $user = User::factory()->create();
    $user->assignRole('admin');
    return test()->actingAs($user);
}

function actingAsKasir()
{
    $user = User::factory()->create();
    $user->assignRole('kasir');
    return test()->actingAs($user);
}

function actingAsPharmacist()
{
    $user = User::factory()->create();
    $user->assignRole('pharmacist');
    return test()->actingAs($user);
}
```

### Test Directory Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── LogoutTest.php
│   │   └── RegistrationTest.php
│   ├── Products/
│   │   ├── ProductCrudTest.php
│   │   ├── ProductSearchTest.php
│   │   └── ProductValidationTest.php
│   ├── Sales/
│   │   ├── SaleProcessTest.php
│   │   ├── SaleValidationTest.php
│   │   └── SaleReportTest.php
│   ├── Inventory/
│   │   ├── StockMovementTest.php
│   │   ├── StockAdjustmentTest.php
│   │   └── LowStockAlertTest.php
│   └── Reports/
│       ├── SalesReportTest.php
│       ├── InventoryReportTest.php
│       └── FinancialReportTest.php
├── Unit/
│   ├── Models/
│   │   ├── ProductTest.php
│   │   ├── SaleTest.php
│   │   ├── BatchTest.php
│   │   └── UserTest.php
│   ├── Services/
│   │   ├── ProductServiceTest.php
│   │   ├── SaleServiceTest.php
│   │   ├── InventoryServiceTest.php
│   │   └── ReportServiceTest.php
│   └── Helpers/
│       ├── PriceCalculatorTest.php
│       ├── StockCalculatorTest.php
│       └── DateHelperTest.php
├── TestCase.php
├── ApiTestCase.php
└── Pest.php
```

### Unit Testing

#### Model Testing

**tests/Unit/Models/ProductTest.php**
```php
<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Batch;

it('can create a product', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $unit = Unit::factory()->create();
    
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
    ]);
    
    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->category)->toBeInstanceOf(Category::class)
        ->and($product->supplier)->toBeInstanceOf(Supplier::class)
        ->and($product->unit)->toBeInstanceOf(Unit::class);
});

it('has relationship with category', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    
    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->id)->toBe($category->id);
});

it('has relationship with supplier', function () {
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['supplier_id' => $supplier->id]);
    
    expect($product->supplier)->toBeInstanceOf(Supplier::class)
        ->and($product->supplier->id)->toBe($supplier->id);
});

it('calculates current stock correctly', function () {
    $product = Product::factory()->create();
    
    // Create batches with different quantities
    $product->batches()->create([
        'batch_number' => 'BATCH001',
        'quantity' => 100,
        'purchase_price' => 5000,
        'selling_price' => 7500,
    ]);
    
    $product->batches()->create([
        'batch_number' => 'BATCH002',
        'quantity' => 50,
        'purchase_price' => 5000,
        'selling_price' => 7500,
    ]);
    
    expect($product->current_stock)->toBe(150);
});

it('calculates discounted price correctly', function () {
    $product = Product::factory()->create();
    $batch = $product->batches()->create([
        'batch_number' => 'BATCH001',
        'quantity' => 100,
        'purchase_price' => 5000,
        'selling_price' => 10000,
        'discount_percentage' => 10,
    ]);
    
    $discountedPrice = $batch->selling_price * (1 - $batch->discount_percentage / 100);
    expect($batch->discounted_price)->toBe($discountedPrice);
});

it('validates unique SKU', function () {
    Product::factory()->create(['sku' => 'PROD001']);
    
    expect(fn() => Product::factory()->create(['sku' => 'PROD001']))
        ->toThrow(Exception::class);
});

it('validates required fields', function () {
    expect(fn() => Product::create([]))
        ->toThrow(Exception::class);
});
```

#### Service Testing

**tests/Unit/Services/ProductServiceTest.php**
```php
<?php

use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;

beforeEach(function () {
    $this->productService = new ProductService();
});

it('can create product with valid data', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $unit = Unit::factory()->create();
    
    $data = [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
        'barcode' => '1234567890123',
    ];
    
    $product = $this->productService->create($data);
    
    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Test Product')
        ->and($product->barcode)->toBe('1234567890123');
});

it('can update product', function () {
    $product = Product::factory()->create();
    
    $updateData = [
        'name' => 'Updated Product Name',
        'description' => 'Updated Description',
    ];
    
    $updatedProduct = $this->productService->update($product->id, $updateData);
    
    expect($updatedProduct->name)->toBe('Updated Product Name')
        ->and($updatedProduct->description)->toBe('Updated Description');
});

it('can search products by name', function () {
    Product::factory()->create(['name' => 'Paracetamol 500mg']);
    Product::factory()->create(['name' => 'Amoxicillin 250mg']);
    Product::factory()->create(['name' => 'Vitamin C']);
    
    $results = $this->productService->search('Paracetamol');
    
    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toContain('Paracetamol');
});

it('throws exception for duplicate barcode', function () {
    Product::factory()->create(['barcode' => '1234567890123']);
    
    $data = [
        'name' => 'Another Product',
        'barcode' => '1234567890123',
        'category_id' => Category::factory()->create()->id,
        'supplier_id' => Supplier::factory()->create()->id,
        'unit_id' => Unit::factory()->create()->id,
    ];
    
    expect(fn() => $this->productService->create($data))
        ->toThrow(Exception::class);
});

it('throws exception when product not found', function () {
    expect(fn() => $this->productService->update(999, ['name' => 'Test']))
        ->toThrow(Exception::class);
});
```

### Feature Testing

#### API Testing

**tests/Feature/Products/ProductCrudTest.php**
```php
<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    Sanctum::actingAs($this->user);
});

it('can get products list', function () {
    Product::factory()->count(5)->create();
    
    $response = $this->getJson('/api/v1/products');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'category',
                    'supplier',
                    'unit',
                    'current_stock',
                    'is_active',
                ]
            ],
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ]
        ]);
});

it('can create product', function () {
    $category = Category::factory()->create();
    $supplier = Supplier::factory()->create();
    $unit = Unit::factory()->create();
    
    $data = [
        'name' => 'New Product',
        'description' => 'Product Description',
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'unit_id' => $unit->id,
        'barcode' => '1234567890123',
    ];
    
    $response = $this->postJson('/api/v1/products', $data);
    
    $response->assertStatus(201)
        ->assertJsonFragment([
            'name' => 'New Product',
            'barcode' => '1234567890123',
        ]);
    
    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'barcode' => '1234567890123',
    ]);
});

it('can update product', function () {
    $product = Product::factory()->create();
    
    $updateData = [
        'name' => 'Updated Product',
        'description' => 'Updated Description',
    ];
    
    $response = $this->putJson("/api/v1/products/{$product->id}", $updateData);
    
    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'Updated Product',
            'description' => 'Updated Description',
        ]);
});

it('can delete product', function () {
    $product = Product::factory()->create();
    
    $response = $this->deleteJson("/api/v1/products/{$product->id}");
    
    $response->assertStatus(200);
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

it('validates required fields when creating product', function () {
    $response = $this->postJson('/api/v1/products', []);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'category_id',
            'supplier_id',
            'unit_id',
        ]);
});

it('requires authentication', function () {
    Sanctum::actingAs(null);
    
    $response = $this->getJson('/api/v1/products');
    
    $response->assertStatus(401);
});
```

#### Authentication Testing

**tests/Feature/Auth/LoginTest.php**
```php
<?php

use App\Models\User;

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'user',
                'token',
            ]
        ]);
});

it('cannot login with invalid credentials', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrongpassword',
    ]);
    
    $response->assertStatus(401);
});

it('validates required fields', function () {
    $response = $this->postJson('/api/v1/auth/login', []);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});
```

**tests/Feature/Auth/LogoutTest.php**
```php
<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can logout successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    $response = $this->postJson('/api/v1/auth/logout');
    
    $response->assertStatus(200);
});

it('requires authentication for logout', function () {
    $response = $this->postJson('/api/v1/auth/logout');
    
    $response->assertStatus(401);
```

### Integration Testing

#### Database Testing

**tests/Feature/Database/MigrationTest.php**
```php
<?php

use Illuminate\Support\Facades\Schema;

it('runs migrations successfully', function () {
    // Test that all required tables exist
    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasTable('products'))->toBeTrue()
        ->and(Schema::hasTable('categories'))->toBeTrue()
        ->and(Schema::hasTable('suppliers'))->toBeTrue()
        ->and(Schema::hasTable('units'))->toBeTrue()
        ->and(Schema::hasTable('batches'))->toBeTrue()
        ->and(Schema::hasTable('sales'))->toBeTrue()
        ->and(Schema::hasTable('sale_items'))->toBeTrue();
});

it('has correct table structure for users', function () {
    expect(Schema::hasColumns('users', [
        'id', 'name', 'email', 'password', 'role', 'is_active'
    ]))->toBeTrue();
});

it('has correct table structure for products', function () {
    expect(Schema::hasColumns('products', [
        'id', 'name', 'description', 'category_id', 'supplier_id', 
        'unit_id', 'barcode', 'sku', 'is_active'
    ]))->toBeTrue();
});
```

**tests/Feature/Database/ProductDatabaseTest.php**
```php
<?php

use App\Models\Product;
use App\Models\Batch;
use App\Models\Sale;
use App\Models\SaleItem;

it('maintains referential integrity', function () {
    $product = Product::factory()->create();
    $batch = $product->batches()->create([
        'batch_number' => 'BATCH001',
        'quantity' => 100,
        'purchase_price' => 5000,
        'selling_price' => 7500,
    ]);
    
    // Create sale with this batch
    $sale = Sale::factory()->create();
    $saleItem = $sale->items()->create([
        'product_id' => $product->id,
        'batch_id' => $batch->id,
        'quantity' => 5,
        'unit_price' => 7500,
        'total_price' => 37500,
    ]);
    
    // Should not be able to delete product with existing sales
    expect(fn() => $product->delete())
        ->toThrow(Exception::class);
});

it('updates stock correctly on sale', function () {
    $product = Product::factory()->create();
    $batch = $product->batches()->create([
        'batch_number' => 'BATCH001',
        'quantity' => 100,
        'purchase_price' => 5000,
        'selling_price' => 7500,
    ]);
    
    $initialStock = $batch->quantity;
    
    // Create sale
    $sale = Sale::factory()->create();
    $sale->items()->create([
        'product_id' => $product->id,
        'batch_id' => $batch->id,
        'quantity' => 10,
        'unit_price' => 7500,
        'total_price' => 75000,
    ]);
    
    $batch->refresh();
    expect($batch->quantity)->toBe($initialStock - 10);
});
```

#### Payment Gateway Integration

**tests/Feature/Integration/PaymentGatewayTest.php**
```php
<?php

use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->paymentService = new PaymentGatewayService();
});

it('processes payment successfully', function () {
    Http::fake([
        'payment-gateway.com/*' => Http::response([
            'status' => 'success',
            'transaction_id' => 'TXN123456',
            'amount' => 100000,
        ], 200)
    ]);
    
    $result = $this->paymentService->processPayment([
        'amount' => 100000,
        'payment_method' => 'credit_card',
        'card_number' => '4111111111111111',
    ]);
    
    expect($result['status'])->toBe('success')
        ->and($result['transaction_id'])->toBe('TXN123456');
});

it('handles payment gateway errors', function () {
    Http::fake([
        'payment-gateway.com/*' => Http::response([
            'status' => 'error',
            'message' => 'Insufficient funds',
        ], 400)
    ]);
    
    expect(fn() => $this->paymentService->processPayment([
        'amount' => 100000,
        'payment_method' => 'credit_card',
        'card_number' => '4111111111111111',
    ]))->toThrow(Exception::class);
});
```

### Performance Testing

**tests/Feature/Performance/ApiPerformanceTest.php**
```php
<?php

use App\Models\Product;
use Laravel\Sanctum\Sanctum;

it('products api responds within acceptable time', function () {
    actingAsAdmin();
    Product::factory()->count(1000)->create();
    
    $start = microtime(true);
    $response = $this->getJson('/api/v1/products?per_page=50');
    $duration = microtime(true) - $start;
    
    $response->assertStatus(200);
    expect($duration)->toBeLessThan(1.0); // Should respond within 1 second
});

it('product search performs well', function () {
    actingAsAdmin();
    Product::factory()->count(5000)->create();
    
    $start = microtime(true);
    $response = $this->getJson('/api/v1/products?search=paracetamol');
    $duration = microtime(true) - $start;
    
    $response->assertStatus(200);
    expect($duration)->toBeLessThan(0.5); // Search should be fast
});

it('handles concurrent requests', function () {
    actingAsAdmin();
    Product::factory()->count(100)->create();
    
    $promises = [];
    for ($i = 0; $i < 10; $i++) {
        $promises[] = $this->getJson('/api/v1/products');
    }
    
    foreach ($promises as $response) {
        $response->assertStatus(200);
    }
});
```

#### Database Performance Testing

**tests/Feature/Performance/DatabasePerformanceTest.php**
```php
<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

it('product query with relationships performs well', function () {
    Product::factory()->count(1000)->create();
    
    $start = microtime(true);
    $products = Product::with(['category', 'supplier', 'unit', 'batches'])
        ->paginate(50);
    $duration = microtime(true) - $start;
    
    expect($duration)->toBeLessThan(0.3)
        ->and($products->count())->toBe(50);
});

it('inventory calculation performs well', function () {
    $products = Product::factory()->count(100)->create();
    
    // Create batches for each product
    $products->each(function ($product) {
        $product->batches()->createMany([
            ['batch_number' => 'B001', 'quantity' => 100, 'purchase_price' => 5000, 'selling_price' => 7500],
            ['batch_number' => 'B002', 'quantity' => 50, 'purchase_price' => 5000, 'selling_price' => 7500],
        ]);
    });
    
    $start = microtime(true);
    $totalStock = DB::table('batches')
        ->join('products', 'batches.product_id', '=', 'products.id')
        ->where('products.is_active', true)
        ->sum('batches.quantity');
    $duration = microtime(true) - $start;
    
    expect($duration)->toBeLessThan(0.1)
        ->and($totalStock)->toBeGreaterThan(0);
});
```

## Frontend Testing (Quasar + Vue.js)

### Setup Testing Environment

#### Jest Configuration

**jest.config.js**
```javascript
module.exports = {
  testEnvironment: 'jsdom',
  setupFilesAfterEnv: ['<rootDir>/test/jest/jest.setup.js'],
  moduleFileExtensions: ['vue', 'js', 'json'],
  transform: {
    '^.+\.vue$': '@vue/vue3-jest',
    '^.+\.js$': 'babel-jest'
  },
  collectCoverageFrom: [
    'src/**/*.{js,vue}',
    '!src/boot/*.js',
    '!**/node_modules/**'
  ],
  coverageThreshold: {
    global: {
      branches: 80,
      functions: 80,
      lines: 80,
      statements: 80
    }
  }
}
```

#### Test Setup

**test/jest/jest.setup.js**
```javascript
import { config } from '@vue/test-utils'
import { Quasar } from 'quasar'
import { createTestingPinia } from '@pinia/testing'

// Mock Quasar
config.global.plugins = [Quasar]
config.global.plugins.push([createTestingPinia(), { stubActions: false }])

// Mock API
global.fetch = jest.fn()

// Setup DOM
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: jest.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: jest.fn(),
    removeListener: jest.fn(),
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    dispatchEvent: jest.fn(),
  })),
})
```

### Component Testing

**test/jest/__tests__/components/ProductCard.spec.js**
```javascript
import { mount } from '@vue/test-utils'
import ProductCard from 'src/components/ProductCard.vue'
import { Quasar } from 'quasar'

describe('ProductCard Component', () => {
  const mockProduct = {
    id: 1,
    name: 'Paracetamol 500mg',
    description: 'Pain reliever',
    current_stock: 150,
    category: { name: 'Medicine' },
    supplier: { name: 'PT Kimia Farma' }
  }

  it('renders product information correctly', () => {
    const wrapper = mount(ProductCard, {
      props: { product: mockProduct },
      global: {
        plugins: [Quasar]
      }
    })

    expect(wrapper.text()).toContain('Paracetamol 500mg')
    expect(wrapper.text()).toContain('Pain reliever')
    expect(wrapper.text()).toContain('150')
    expect(wrapper.text()).toContain('Medicine')
  })

  it('emits edit event when edit button clicked', async () => {
    const wrapper = mount(ProductCard, {
      props: { product: mockProduct },
      global: {
        plugins: [Quasar]
      }
    })

    await wrapper.find('[data-test="edit-button"]').trigger('click')
    
    expect(wrapper.emitted('edit')).toBeTruthy()
    expect(wrapper.emitted('edit')[0]).toEqual([mockProduct])
  })

  it('shows low stock warning when stock is low', () => {
    const lowStockProduct = { ...mockProduct, current_stock: 5 }
    
    const wrapper = mount(ProductCard, {
      props: { product: lowStockProduct },
      global: {
        plugins: [Quasar]
      }
    })

    expect(wrapper.find('[data-test="low-stock-warning"]').exists()).toBe(true)
  })
})
```

### Store Testing

**test/jest/__tests__/stores/products.spec.js**
```javascript
import { setActivePinia, createPinia } from 'pinia'
import { useProductStore } from 'src/stores/products'
import { api } from 'src/services'

// Mock API
jest.mock('src/services', () => ({
  api: {
    get: jest.fn(),
    post: jest.fn(),
    put: jest.fn(),
    delete: jest.fn()
  }
}))

describe('Product Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    jest.clearAllMocks()
  })

  it('fetches products successfully', async () => {
    const mockProducts = [
      { id: 1, name: 'Product 1' },
      { id: 2, name: 'Product 2' }
    ]

    api.get.mockResolvedValue({
      data: { data: mockProducts, meta: { total: 2 } }
    })

    const store = useProductStore()
    await store.fetchProducts()

    expect(store.products).toEqual(mockProducts)
    expect(store.total).toBe(2)
    expect(store.loading).toBe(false)
  })

  it('handles fetch products error', async () => {
    api.get.mockRejectedValue(new Error('Network error'))

    const store = useProductStore()
    await store.fetchProducts()

    expect(store.products).toEqual([])
    expect(store.error).toBe('Failed to fetch products')
    expect(store.loading).toBe(false)
  })

  it('creates product successfully', async () => {
    const newProduct = { name: 'New Product', category_id: 1 }
    const createdProduct = { id: 3, ...newProduct }

    api.post.mockResolvedValue({
      data: { data: createdProduct }
    })

    const store = useProductStore()
    const result = await store.createProduct(newProduct)

    expect(result).toEqual(createdProduct)
    expect(api.post).toHaveBeenCalledWith('/products', newProduct)
  })
})
```

### E2E Testing (Cypress)

#### Cypress Configuration

**cypress.config.js**
```javascript
const { defineConfig } = require('cypress')

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost:9000',
    supportFile: 'test/cypress/support/e2e.js',
    specPattern: 'test/cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    videosFolder: 'test/cypress/videos',
    screenshotsFolder: 'test/cypress/screenshots',
    fixturesFolder: 'test/cypress/fixtures'
  },
  component: {
    devServer: {
      framework: 'vue',
      bundler: 'vite'
    },
    supportFile: 'test/cypress/support/component.js',
    specPattern: 'src/**/*.cy.{js,jsx,ts,tsx}'
  }
})
```

#### E2E Test Examples

**test/cypress/e2e/product-management.cy.js**
```javascript
describe('Product Management', () => {
  beforeEach(() => {
    // Login as admin
    cy.login('admin@example.com', 'password')
    cy.visit('/products')
  })

  it('should display products list', () => {
    cy.get('[data-test="products-table"]').should('be.visible')
    cy.get('[data-test="product-row"]').should('have.length.greaterThan', 0)
  })

  it('should create new product', () => {
    cy.get('[data-test="add-product-btn"]').click()
    
    // Fill form
    cy.get('[data-test="product-name"]').type('Test Product')
    cy.get('[data-test="product-description"]').type('Test Description')
    cy.get('[data-test="category-select"]').click()
    cy.get('[data-test="category-option-1"]').click()
    cy.get('[data-test="supplier-select"]').click()
    cy.get('[data-test="supplier-option-1"]').click()
    cy.get('[data-test="unit-select"]').click()
    cy.get('[data-test="unit-option-1"]').click()
    
    // Submit form
    cy.get('[data-test="save-product-btn"]').click()
    
    // Verify success
    cy.get('[data-test="success-notification"]').should('be.visible')
    cy.get('[data-test="products-table"]').should('contain', 'Test Product')
  })

  it('should edit existing product', () => {
    cy.get('[data-test="product-row"]').first().within(() => {
      cy.get('[data-test="edit-btn"]').click()
    })
    
    cy.get('[data-test="product-name"]').clear().type('Updated Product')
    cy.get('[data-test="save-product-btn"]').click()
    
    cy.get('[data-test="success-notification"]').should('be.visible')
    cy.get('[data-test="products-table"]').should('contain', 'Updated Product')
  })

  it('should delete product', () => {
    cy.get('[data-test="product-row"]').first().within(() => {
      cy.get('[data-test="delete-btn"]').click()
    })
    
    cy.get('[data-test="confirm-delete-btn"]').click()
    
    cy.get('[data-test="success-notification"]').should('be.visible')
  })
})
```

## Test Execution

### Running Tests

#### Backend Tests
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/Api/ProductApiTest.php

# Run with parallel execution
php artisan test --parallel

# Run specific test method
php artisan test --filter=test_can_create_product

# Run tests with specific configuration
php artisan test --env=testing

# Run tests with verbose output
php artisan test --verbose
```

#### Pest Commands

```bash
# Run all Pest tests
./vendor/bin/pest

# Run with coverage
./vendor/bin/pest --coverage

# Run specific test
./vendor/bin/pest tests/Unit/ProductTest.php

# Run with parallel processing
./vendor/bin/pest --parallel

# Run tests with specific group
./vendor/bin/pest --group=unit

# Run tests excluding specific group
./vendor/bin/pest --exclude-group=integration

# Run tests with minimum coverage threshold
./vendor/bin/pest --coverage --min=80
```

#### Frontend Tests
```bash
# Run unit tests
npm run test:unit

# Run with coverage
npm run test:unit:coverage

# Run in watch mode
npm run test:unit:watch

# Run E2E tests
npm run test:e2e

# Run E2E tests in headless mode
npm run test:e2e:headless
```

### CI/CD Integration

#### GitHub Actions

**.github/workflows/tests.yml**
```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, dom, fileinfo, mysql
        coverage: xdebug
    
    - name: Install dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader
      working-directory: ./backend
    
    - name: Copy environment file
      run: cp .env.testing .env
      working-directory: ./backend
    
    - name: Generate application key
      run: php artisan key:generate
      working-directory: ./backend
    
    - name: Run tests
      run: php artisan test --coverage --min=80
      working-directory: ./backend

  frontend-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18'
        cache: 'npm'
        cache-dependency-path: frontend/web/package-lock.json
    
    - name: Install dependencies
      run: npm ci
      working-directory: ./frontend/web
    
    - name: Run unit tests
      run: npm run test:unit:coverage
      working-directory: ./frontend/web
    
    - name: Run E2E tests
      run: npm run test:e2e:headless
      working-directory: ./frontend/web
```

## Code Coverage

### Target Coverage
- **Minimum**: 80% overall coverage
- **Unit Tests**: 90% coverage
- **Integration Tests**: 70% coverage
- **Critical Paths**: 95% coverage

### Coverage Reports

#### Backend Coverage
```bash
# Generate HTML coverage report
php artisan test --coverage-html=coverage

# Generate Clover XML for CI
php artisan test --coverage-clover=coverage.xml
```

#### Frontend Coverage
```bash
# Generate coverage report
npm run test:unit:coverage

# View coverage report
open coverage/lcov-report/index.html
```

### Coverage Exclusions

#### Backend
- Configuration files
- Migration files
- Seeder files
- Blade templates
- Third-party packages

#### Frontend
- Boot files
- Configuration files
- Third-party libraries
- Mock files

## Performance Testing

### Load Testing

#### Artillery Configuration

**artillery.yml**
```yaml
config:
  target: 'http://localhost:8000'
  phases:
    - duration: 60
      arrivalRate: 10
    - duration: 120
      arrivalRate: 50
    - duration: 60
      arrivalRate: 100
  defaults:
    headers:
      Authorization: 'Bearer {{ $processEnvironment.API_TOKEN }}'

scenarios:
  - name: 'Product API Load Test'
    flow:
      - get:
          url: '/api/v1/products'
      - think: 1
      - get:
          url: '/api/v1/products/{{ $randomInt(1, 100) }}'
      - think: 2
      - post:
          url: '/api/v1/sales'
          json:
            items:
              - product_id: '{{ $randomInt(1, 50) }}'
                batch_id: '{{ $randomInt(1, 100) }}'
                quantity: '{{ $randomInt(1, 5) }}'
                unit_price: 7500
            payment_method: 'cash'
```

### Database Performance

#### Query Performance Tests
```php
// Test slow queries
it('products query performs well with large dataset', function () {
    Product::factory()->count(10000)->create();
    
    $start = microtime(true);
    $products = Product::with(['category', 'supplier', 'unit'])
        ->paginate(50);
    $duration = microtime(true) - $start;
    
    expect($duration)->toBeLessThan(0.5); // Should complete within 500ms
    expect($products->count())->toBe(50);
});
```

## Test Data Management

### Factories

**database/factories/ProductFactory.php**
```php
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'unit_id' => Unit::factory(),
            'barcode' => $this->faker->unique()->ean13(),
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'is_active' => true,
        ];
    }
    
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
    
    public function medicine(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement([
                'Paracetamol 500mg',
                'Amoxicillin 250mg',
                'Ibuprofen 400mg',
                'Vitamin C 1000mg',
                'Omeprazole 20mg'
            ]),
            'category_id' => Category::factory()->state(['name' => 'Medicine']),
        ]);
    }
}
```

### Seeders for Testing

**database/seeders/TestDataSeeder.php**
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Batch;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'kasir']);
        Role::create(['name' => 'pharmacist']);
        
        // Create test users
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        $admin->assignRole('admin');
        
        $kasir = User::factory()->create([
            'email' => 'kasir@test.com',
            'password' => bcrypt('password')
        ]);
        $kasir->assignRole('kasir');
        
        $pharmacist = User::factory()->create([
            'email' => 'pharmacist@test.com',
            'password' => bcrypt('password')
        ]);
        $pharmacist->assignRole('pharmacist');
        
        // Create master data
        $categories = Category::factory()->count(5)->create();
        $suppliers = Supplier::factory()->count(3)->create();
        $units = Unit::factory()->count(4)->create();
        
        // Create products with batches
        $products = Product::factory()->count(50)->create();
        $products->each(function ($product) {
            Batch::factory()->count(rand(1, 3))->create([
                'product_id' => $product->id,
            ]);
        });
    }
}
```

### Helper Test Classes

**tests/TestCase.php**
```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable exception handling for better error visibility
        $this->withoutExceptionHandling();
    }

    protected function actingAsAdmin(): self
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        
        return $this;
    }

    protected function actingAsPharmacist(): self
    {
        $pharmacist = User::factory()->create(['role' => 'pharmacist']);
        Sanctum::actingAs($pharmacist);
        
        return $this;
    }

    protected function actingAsUser(): self
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);
        
        return $this;
    }

    protected function assertApiResponse(array $expectedStructure, $response): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure($expectedStructure);
    }

    protected function assertValidationErrors(array $fields, $response): void
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);
    }
}
```

### API Test Case

**tests/ApiTestCase.php**
```php
<?php
// tests/ApiTestCase.php

namespace Tests;

use Laravel\Sanctum\Sanctum;
use App\Models\User;

abstract class ApiTestCase extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    protected function getJsonWithAuth(string $uri, array $headers = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson($uri, array_merge([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
        ], $headers));
    }

    protected function postJsonWithAuth(string $uri, array $data = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($uri, $data, array_merge([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
        ], $headers));
    }
}
```

## Debugging Tests

### Using Xdebug

```bash
# Enable Xdebug for testing
export XDEBUG_MODE=debug
php artisan test --filter=test_specific_method
```

### Using Ray for Debugging

```php
// In your test
use function Spatie\Ray\ray;

test('can debug with ray', function () {
    $product = Product::factory()->create();
    
    ray($product); // Debug output
    
    expect($product->name)->not->toBeEmpty();
});
```

### Using Laravel Telescope

```php
// Enable Telescope in testing
use Laravel\Telescope\Telescope;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        if (app()->environment('testing')) {
            Telescope::tag(function () {
                return ['test:' . $this->getName()];
            });
        }
    }
}
```

## Best Practices

### Test Organization
- Group related tests in the same file
- Use descriptive test names that explain the behavior
- Follow AAA pattern (Arrange, Act, Assert)
- Keep tests independent and isolated
- Use consistent naming conventions

### Test Structure Guidelines

```php
// Good: Descriptive test name
test('should calculate discount price correctly when product has valid discount percentage', function () {
    // Arrange
    $product = Product::factory()->create([
        'price' => 100,
        'discount_percentage' => 10
    ]);
    
    // Act
    $discountPrice = $product->getDiscountPrice();
    
    // Assert
    expect($discountPrice)->toBe(90.0);
});

// Bad: Unclear test name
test('discount test', function () {
    $product = Product::factory()->create(['price' => 100, 'discount_percentage' => 10]);
    expect($product->getDiscountPrice())->toBe(90.0);
});
```

### Test Maintenance
- Regularly update test data and scenarios
- Remove obsolete tests when features are removed
- Refactor tests when code structure changes
- Document complex test scenarios and edge cases
- Keep test dependencies minimal

### Performance Optimization
- Use database transactions for faster tests
- Mock external services and APIs
- Avoid unnecessary database queries in tests
- Use factories instead of manual data creation
- Leverage parallel test execution for large test suites
- Use in-memory databases for unit tests when possible

### Code Coverage Guidelines
- Aim for 80%+ code coverage
- Focus on critical business logic
- Don't chase 100% coverage at the expense of test quality
- Exclude generated files and vendor code
- Review coverage reports regularly

### Testing Anti-patterns to Avoid
- Testing implementation details instead of behavior
- Writing tests that are too tightly coupled to code structure
- Creating tests that depend on external services without mocking
- Writing overly complex tests that are hard to understand
- Ignoring test failures or marking them as skipped without fixing

## Monitoring & Reporting

### Monitoring & Reporting

### Test Metrics
- **Test Coverage**: Persentase code yang di-test
- **Test Execution Time**: Waktu eksekusi test
- **Test Success Rate**: Persentase test yang berhasil
- **Flaky Test Detection**: Identifikasi test yang tidak stabil

### Reporting Tools
- **PHPUnit HTML Report**: Coverage report untuk backend
- **Jest Coverage Report**: Coverage report untuk frontend
- **Cypress Dashboard**: E2E test reporting
- **SonarQube**: Code quality dan coverage analysis

### Continuous Monitoring
- **Daily Test Runs**: Jalankan test setiap hari
- **Coverage Tracking**: Monitor perubahan coverage
- **Performance Regression**: Monitor performa test
- **Alert System**: Alert jika test gagal atau coverage turun

## Conclusion

Testing yang komprehensif adalah fondasi dari aplikasi Q-Pharmacy yang berkualitas tinggi. Dengan mengimplementasikan strategi testing yang mencakup unit tests, integration tests, feature tests, dan performance tests, kita dapat memastikan:

### Key Benefits
1. **Reliability**: Aplikasi berjalan sesuai ekspektasi
2. **Maintainability**: Code mudah diubah dan dipelihara
3. **Confidence**: Developer yakin saat melakukan perubahan
4. **Quality Assurance**: Bug terdeteksi lebih awal
5. **Documentation**: Test berfungsi sebagai dokumentasi hidup

### Testing Philosophy
- **Test-Driven Development (TDD)**: Tulis test sebelum implementasi
- **Behavior-Driven Development (BDD)**: Focus pada behavior, bukan implementasi
- **Continuous Testing**: Integrate testing dalam development workflow
- **Quality over Quantity**: Test yang berkualitas lebih penting dari coverage 100%

### Next Steps
1. Implement automated testing dalam CI/CD pipeline
2. Setup code coverage monitoring
3. Establish testing standards dan guidelines
4. Train team dalam best practices testing
5. Regular review dan improvement testing strategy

Dengan mengikuti panduan ini, Q-Pharmacy akan memiliki foundation testing yang solid untuk mendukung pengembangan aplikasi yang reliable dan maintainable.
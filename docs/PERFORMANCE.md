# Performance Optimization Guide Q-Pharmacy

## Overview

Dokumen ini menyediakan panduan komprehensif untuk optimasi performa sistem Q-Pharmacy, mencakup benchmark, monitoring, teknik caching, dan strategi optimasi untuk memastikan sistem dapat menangani beban kerja apotek dengan efisien dan responsif.

**Performance Goals:**
- Response time < 200ms untuk 95% requests
- Throughput > 1000 requests/second
- Database query time < 50ms
- Page load time < 2 seconds
- 99.9% uptime availability

**Last Updated**: 20 September 2025

## Performance Architecture

### System Performance Stack

```
┌─────────────────────────────────────────────────────────────┐
│                    CDN & Edge Caching                       │
│  • CloudFlare • Static Assets • Geographic Distribution     │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                   Load Balancer                             │
│  • Nginx • SSL Termination • Request Distribution          │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                 Application Servers                         │
│  • PHP-FPM • OPcache • Application Caching                 │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                   Caching Layer                             │
│  • Redis • Memcached • Session Storage                     │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                   Database Layer                            │
│  • MySQL • Read Replicas • Query Optimization              │
└─────────────────────────────────────────────────────────────┘
```

## Backend Performance Optimization

### Laravel Performance Configuration

#### 1. OPcache Configuration

```ini
; php.ini OPcache settings
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.max_wasted_percentage=5
opcache.use_cwd=1
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

#### 2. Laravel Optimization Commands

```bash
#!/bin/bash
# Production optimization script

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache application
php artisan optimize

# Queue optimization
php artisan queue:restart
```

#### 3. Database Query Optimization

```php
// Optimized Product Repository
class ProductRepository
{
    public function getProductsWithRelations($perPage = 15)
    {
        return Product::with([
            'category:id,name',
            'supplier:id,name',
            'unit:id,name',
            'batches' => function ($query) {
                $query->where('expiry_date', '>', now())
                      ->where('quantity', '>', 0)
                      ->orderBy('expiry_date');
            }
        ])
        ->select(['id', 'name', 'barcode', 'category_id', 'supplier_id', 'unit_id', 'is_active'])
        ->where('is_active', true)
        ->paginate($perPage);
    }
    
    public function searchProducts($query, $limit = 10)
    {
        return Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('barcode', 'LIKE', "%{$query}%");
            })
            ->with('category:id,name', 'unit:id,name')
            ->limit($limit)
            ->get(['id', 'name', 'barcode', 'category_id', 'unit_id']);
    }
    
    public function getProductsByCategory($categoryId)
    {
        return Cache::tags(['products', 'category_' . $categoryId])
            ->remember("products_category_{$categoryId}", 3600, function () use ($categoryId) {
                return Product::where('category_id', $categoryId)
                             ->where('is_active', true)
                             ->with('unit:id,name')
                             ->get(['id', 'name', 'barcode', 'unit_id']);
            });
    }
}
```

#### 4. Eloquent Performance Best Practices

```php
// Avoid N+1 Query Problem
class SaleController extends Controller
{
    public function index()
    {
        // BAD: N+1 queries
        // $sales = Sale::all();
        // foreach ($sales as $sale) {
        //     echo $sale->user->name;
        //     echo $sale->items->count();
        // }
        
        // GOOD: Eager loading
        $sales = Sale::with([
            'user:id,name',
            'items.product:id,name',
            'items.batch:id,batch_number'
        ])->paginate(20);
        
        return response()->json($sales);
    }
    
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $sale = Sale::create($request->validated());
            
            // Bulk insert for better performance
            $items = collect($request->items)->map(function ($item) use ($sale) {
                return [
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            });
            
            SaleItem::insert($items->toArray());
            
            // Update stock in batch
            $this->updateStockBatch($request->items);
        });
    }
    
    private function updateStockBatch($items)
    {
        foreach ($items as $item) {
            Batch::where('id', $item['batch_id'])
                 ->decrement('quantity', $item['quantity']);
        }
    }
}
```

### Caching Strategies

#### 1. Redis Configuration

```php
// config/cache.php
return [
    'default' => env('CACHE_DRIVER', 'redis'),
    
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],
    ],
    
    'prefix' => env('CACHE_PREFIX', 'q_potek_cache'),
];

// config/database.php - Redis connections
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
];
```

#### 2. Application-Level Caching

```php
class CacheService
{
    const CACHE_TTL = [
        'products' => 3600,        // 1 hour
        'categories' => 7200,      // 2 hours
        'suppliers' => 7200,       // 2 hours
        'users' => 1800,           // 30 minutes
        'reports' => 900,          // 15 minutes
        'dashboard' => 300,        // 5 minutes
    ];
    
    public function getProducts($page = 1, $perPage = 15)
    {
        $cacheKey = "products_page_{$page}_per_{$perPage}";
        
        return Cache::tags(['products'])
            ->remember($cacheKey, self::CACHE_TTL['products'], function () use ($page, $perPage) {
                return Product::with(['category:id,name', 'unit:id,name'])
                             ->where('is_active', true)
                             ->paginate($perPage, ['*'], 'page', $page);
            });
    }
    
    public function getDashboardStats()
    {
        return Cache::remember('dashboard_stats', self::CACHE_TTL['dashboard'], function () {
            return [
                'total_products' => Product::where('is_active', true)->count(),
                'low_stock_products' => $this->getLowStockCount(),
                'expired_products' => $this->getExpiredCount(),
                'today_sales' => Sale::whereDate('created_at', today())->sum('total_amount'),
                'monthly_sales' => Sale::whereMonth('created_at', now()->month)->sum('total_amount'),
            ];
        });
    }
    
    public function invalidateProductCache($productId = null)
    {
        if ($productId) {
            Cache::tags(['products', "product_{$productId}"])->flush();
        } else {
            Cache::tags(['products'])->flush();
        }
    }
    
    public function warmupCache()
    {
        // Preload frequently accessed data
        $this->getProducts();
        $this->getDashboardStats();
        
        // Cache categories and suppliers
        Cache::remember('categories_all', self::CACHE_TTL['categories'], function () {
            return Category::where('is_active', true)->get(['id', 'name']);
        });
        
        Cache::remember('suppliers_all', self::CACHE_TTL['suppliers'], function () {
            return Supplier::where('is_active', true)->get(['id', 'name']);
        });
    }
}
```

#### 3. Database Query Caching

```php
class QueryCacheService
{
    public function getCachedQuery($key, $query, $ttl = 3600)
    {
        return Cache::remember($key, $ttl, function () use ($query) {
            return $query->get();
        });
    }
    
    public function getTopSellingProducts($limit = 10)
    {
        return Cache::remember('top_selling_products', 3600, function () use ($limit) {
            return DB::table('sale_items')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->select('products.id', 'products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
                ->where('sale_items.created_at', '>=', now()->subDays(30))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get();
        });
    }
    
    public function getSalesReport($startDate, $endDate)
    {
        $cacheKey = "sales_report_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return Sale::whereBetween('created_at', [$startDate, $endDate])
                      ->with(['items.product:id,name', 'user:id,name'])
                      ->get();
        });
    }
}
```

### Queue Optimization

#### 1. Queue Configuration

```php
// config/queue.php
return [
    'default' => env('QUEUE_CONNECTION', 'redis'),
    
    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
        ],
        
        'high' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'high',
            'retry_after' => 90,
        ],
        
        'low' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'low',
            'retry_after' => 300,
        ],
    ],
];
```

#### 2. Background Job Processing

```php
class ProcessSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $timeout = 60;
    public $tries = 3;
    
    protected $saleData;
    
    public function __construct(array $saleData)
    {
        $this->saleData = $saleData;
        $this->onQueue('high'); // High priority queue
    }
    
    public function handle()
    {
        DB::transaction(function () {
            $sale = Sale::create($this->saleData);
            
            // Process sale items
            foreach ($this->saleData['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
                
                // Update stock
                Batch::where('id', $item['batch_id'])
                     ->decrement('quantity', $item['quantity']);
            }
            
            // Clear related caches
            Cache::tags(['products', 'dashboard'])->flush();
            
            // Send receipt (low priority)
            SendReceiptJob::dispatch($sale)->onQueue('low');
        });
    }
    
    public function failed(Exception $exception)
    {
        Log::error('Sale processing failed', [
            'sale_data' => $this->saleData,
            'error' => $exception->getMessage()
        ]);
    }
}
```

## Frontend Performance Optimization

### Vue.js & Quasar Optimization

#### 1. Component Lazy Loading

```javascript
// router/routes.js
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('pages/Dashboard.vue')
      },
      {
        path: 'products',
        component: () => import('pages/Products.vue')
      },
      {
        path: 'sales',
        component: () => import('pages/Sales.vue')
      }
    ]
  }
]

export default routes
```

#### 2. Virtual Scrolling for Large Lists

```vue
<template>
  <q-page class="q-pa-md">
    <q-virtual-scroll
      :items="products"
      :item-size="60"
      v-slot="{ item, index }"
      style="max-height: 400px;"
    >
      <q-item :key="index" clickable>
        <q-item-section avatar>
          <q-avatar>
            <img :src="item.image || '/default-product.png'" />
          </q-avatar>
        </q-item-section>
        
        <q-item-section>
          <q-item-label>{{ item.name }}</q-item-label>
          <q-item-label caption>{{ item.barcode }}</q-item-label>
        </q-item-section>
        
        <q-item-section side>
          <q-item-label>{{ formatCurrency(item.price) }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-virtual-scroll>
  </q-page>
</template>

<script>
export default {
  name: 'ProductList',
  
  data() {
    return {
      products: [],
      loading: false
    }
  },
  
  async mounted() {
    await this.loadProducts()
  },
  
  methods: {
    async loadProducts() {
      this.loading = true
      try {
        const response = await this.$api.get('/products', {
          params: { per_page: 1000 } // Load more items for virtual scrolling
        })
        this.products = response.data.data
      } catch (error) {
        this.$q.notify({
          type: 'negative',
          message: 'Failed to load products'
        })
      } finally {
        this.loading = false
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount)
    }
  }
}
</script>
```

#### 3. State Management Optimization

```javascript
// stores/products.js
import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useProductStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    loading: false,
    cache: new Map(),
    lastFetch: null
  }),
  
  getters: {
    activeProducts: (state) => state.products.filter(p => p.is_active),
    
    productsByCategory: (state) => (categoryId) => {
      return state.products.filter(p => p.category_id === categoryId)
    },
    
    searchProducts: (state) => (query) => {
      const lowercaseQuery = query.toLowerCase()
      return state.products.filter(p => 
        p.name.toLowerCase().includes(lowercaseQuery) ||
        p.barcode.includes(query)
      )
    }
  },
  
  actions: {
    async fetchProducts(force = false) {
      // Cache for 5 minutes
      const cacheKey = 'products_all'
      const cacheExpiry = 5 * 60 * 1000 // 5 minutes
      
      if (!force && this.cache.has(cacheKey)) {
        const cached = this.cache.get(cacheKey)
        if (Date.now() - cached.timestamp < cacheExpiry) {
          this.products = cached.data
          return
        }
      }
      
      this.loading = true
      try {
        const response = await api.get('/products')
        this.products = response.data.data
        
        // Cache the result
        this.cache.set(cacheKey, {
          data: this.products,
          timestamp: Date.now()
        })
        
        this.lastFetch = Date.now()
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },
    
    async searchProductsAPI(query) {
      if (query.length < 2) return []
      
      const cacheKey = `search_${query}`
      if (this.cache.has(cacheKey)) {
        return this.cache.get(cacheKey).data
      }
      
      try {
        const response = await api.get('/products/search', {
          params: { q: query, limit: 20 }
        })
        
        const results = response.data.data
        this.cache.set(cacheKey, {
          data: results,
          timestamp: Date.now()
        })
        
        return results
      } catch (error) {
        console.error('Search failed:', error)
        return []
      }
    },
    
    clearCache() {
      this.cache.clear()
    }
  }
})
```

#### 4. Image Optimization

```vue
<template>
  <q-img
    :src="optimizedImageUrl"
    :placeholder-src="placeholderUrl"
    loading="lazy"
    :ratio="1"
    class="product-image"
    @error="handleImageError"
  >
    <template v-slot:loading>
      <q-skeleton type="rect" />
    </template>
  </q-img>
</template>

<script>
export default {
  name: 'OptimizedImage',
  
  props: {
    src: String,
    width: {
      type: Number,
      default: 200
    },
    height: {
      type: Number,
      default: 200
    }
  },
  
  computed: {
    optimizedImageUrl() {
      if (!this.src) return this.placeholderUrl
      
      // Use image optimization service
      return `${this.src}?w=${this.width}&h=${this.height}&q=80&f=webp`
    },
    
    placeholderUrl() {
      return `/images/placeholder-${this.width}x${this.height}.webp`
    }
  },
  
  methods: {
    handleImageError() {
      this.$emit('error')
    }
  }
}
</script>
```

### Build Optimization

#### 1. Webpack Configuration

```javascript
// quasar.config.js
module.exports = configure(function (ctx) {
  return {
    build: {
      vueRouterMode: 'history',
      
      // Optimize chunks
      chainWebpack(chain) {
        chain.optimization.splitChunks({
          chunks: 'all',
          cacheGroups: {
            vendor: {
              name: 'vendor',
              test: /[\\/]node_modules[\\/]/,
              chunks: 'all',
              priority: 10
            },
            quasar: {
              name: 'quasar',
              test: /[\\/]node_modules[\\/]quasar[\\/]/,
              chunks: 'all',
              priority: 20
            }
          }
        })
        
        // Preload important chunks
        chain.plugin('preload').tap(options => {
          options[0] = {
            rel: 'preload',
            include: 'initial',
            fileBlacklist: [/\.map$/, /hot-update\.js$/]
          }
          return options
        })
      },
      
      // Enable gzip compression
      gzip: true,
      
      // Analyze bundle size
      analyze: ctx.mode.spa,
      
      // Source maps for production debugging
      sourcemap: ctx.dev,
      
      // Minification
      minify: true,
      
      // Tree shaking
      extractCSS: true
    }
  }
})
```

## Database Performance

### MySQL Optimization

#### 1. Database Configuration

```ini
# my.cnf MySQL configuration
[mysqld]
# Memory settings
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
innodb_log_buffer_size = 64M
innodb_flush_log_at_trx_commit = 2

# Connection settings
max_connections = 200
max_connect_errors = 1000000

# Query cache
query_cache_type = 1
query_cache_size = 256M
query_cache_limit = 2M

# Temporary tables
tmp_table_size = 256M
max_heap_table_size = 256M

# MyISAM settings
key_buffer_size = 128M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 1
log_queries_not_using_indexes = 1
```

#### 2. Index Optimization

```sql
-- Essential indexes for Q-Pharmacy

-- Products table
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_category ON products(category_id, is_active);
CREATE INDEX idx_products_supplier ON products(supplier_id, is_active);
CREATE INDEX idx_products_barcode ON products(barcode);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_search ON products(name, barcode, is_active);

-- Sales table
CREATE INDEX idx_sales_date ON sales(created_at);
CREATE INDEX idx_sales_user ON sales(user_id, created_at);
CREATE INDEX idx_sales_total ON sales(total_amount, created_at);

-- Sale items table
CREATE INDEX idx_sale_items_product ON sale_items(product_id, created_at);
CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);
CREATE INDEX idx_sale_items_batch ON sale_items(batch_id);

-- Batches table
CREATE INDEX idx_batches_product ON batches(product_id, expiry_date);
CREATE INDEX idx_batches_expiry ON batches(expiry_date, quantity);
CREATE INDEX idx_batches_quantity ON batches(quantity);

-- Audit logs table
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id, created_at);
CREATE INDEX idx_audit_logs_model ON audit_logs(model_type, model_id);
CREATE INDEX idx_audit_logs_date ON audit_logs(created_at);

-- Composite indexes for common queries
CREATE INDEX idx_products_category_active ON products(category_id, is_active, name);
CREATE INDEX idx_batches_product_expiry ON batches(product_id, expiry_date, quantity);
CREATE INDEX idx_sales_user_date ON sales(user_id, created_at, total_amount);
```

#### 3. Query Optimization Examples

```php
class OptimizedQueries
{
    // Optimized product search with full-text search
    public function searchProductsFullText($query)
    {
        return DB::select("
            SELECT p.id, p.name, p.barcode, c.name as category_name, u.name as unit_name,
                   MATCH(p.name, p.description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN units u ON p.unit_id = u.id
            WHERE p.is_active = 1
            AND (MATCH(p.name, p.description) AGAINST(? IN NATURAL LANGUAGE MODE)
                 OR p.barcode LIKE ?)
            ORDER BY relevance DESC, p.name ASC
            LIMIT 20
        ", [$query, $query, "%{$query}%"]);
    }
    
    // Optimized sales report with aggregation
    public function getSalesReport($startDate, $endDate)
    {
        return DB::select("
            SELECT 
                DATE(s.created_at) as sale_date,
                COUNT(s.id) as total_transactions,
                SUM(s.total_amount) as total_sales,
                AVG(s.total_amount) as avg_transaction,
                COUNT(DISTINCT s.user_id) as unique_cashiers
            FROM sales s
            WHERE s.created_at BETWEEN ? AND ?
            GROUP BY DATE(s.created_at)
            ORDER BY sale_date DESC
        ", [$startDate, $endDate]);
    }
    
    // Optimized low stock report
    public function getLowStockProducts($threshold = 10)
    {
        return DB::select("
            SELECT 
                p.id,
                p.name,
                p.barcode,
                c.name as category_name,
                COALESCE(SUM(b.quantity), 0) as total_stock,
                COUNT(b.id) as batch_count,
                MIN(b.expiry_date) as nearest_expiry
            FROM products p
            LEFT JOIN batches b ON p.id = b.product_id AND b.quantity > 0
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            GROUP BY p.id, p.name, p.barcode, c.name
            HAVING total_stock <= ?
            ORDER BY total_stock ASC, nearest_expiry ASC
        ", [$threshold]);
    }
    
    // Optimized top selling products
    public function getTopSellingProducts($days = 30, $limit = 10)
    {
        return DB::select("
            SELECT 
                p.id,
                p.name,
                p.barcode,
                SUM(si.quantity) as total_sold,
                SUM(si.total_price) as total_revenue,
                COUNT(DISTINCT si.sale_id) as transaction_count,
                AVG(si.unit_price) as avg_price
            FROM products p
            JOIN sale_items si ON p.id = si.product_id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND p.is_active = 1
            GROUP BY p.id, p.name, p.barcode
            ORDER BY total_sold DESC
            LIMIT ?
        ", [$days, $limit]);
    }
}
```

### Database Connection Optimization

```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ]) : [],
],

// Read replica configuration
'mysql_read' => [
    'driver' => 'mysql',
    'host' => env('DB_READ_HOST', env('DB_HOST', '127.0.0.1')),
    'port' => env('DB_READ_PORT', env('DB_PORT', '3306')),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_READ_USERNAME', env('DB_USERNAME', 'forge')),
    'password' => env('DB_READ_PASSWORD', env('DB_PASSWORD', '')),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

## Performance Monitoring

### Application Performance Monitoring

#### 1. Laravel Telescope Configuration

```php
// config/telescope.php
return [
    'enabled' => env('TELESCOPE_ENABLED', true),
    
    'watchers' => [
        Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
        Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
        Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
        Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
        Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
        Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
        Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
        Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_MODEL_WATCHER', true),
            'hydrations' => true,
        ],
        Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
            'slow' => 100, // Log queries slower than 100ms
        ],
        Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
        Watchers\RequestWatcher::class => [
            'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
            'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
        ],
        Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
        Watchers\ViewWatcher::class => env('TELESCOPE_VIEW_WATCHER', true),
    ],
];
```

#### 2. Custom Performance Metrics

```php
class PerformanceMonitor
{
    public function trackApiResponse($request, $response, $startTime)
    {
        $duration = microtime(true) - $startTime;
        $memoryUsage = memory_get_peak_usage(true);
        
        Log::info('API Performance', [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'duration_ms' => round($duration * 1000, 2),
            'memory_mb' => round($memoryUsage / 1024 / 1024, 2),
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);
        
        // Alert if response is too slow
        if ($duration > 1.0) { // 1 second
            $this->alertSlowResponse($request, $duration);
        }
    }
    
    public function trackDatabaseQuery($query, $bindings, $time)
    {
        if ($time > 100) { // 100ms threshold
            Log::warning('Slow Database Query', [
                'query' => $query,
                'bindings' => $bindings,
                'time_ms' => $time,
                'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);
        }
    }
    
    public function getPerformanceMetrics()
    {
        return [
            'avg_response_time' => $this->getAverageResponseTime(),
            'slow_queries_count' => $this->getSlowQueriesCount(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'memory_usage' => $this->getMemoryUsage(),
            'active_connections' => $this->getActiveConnections()
        ];
    }
    
    private function getAverageResponseTime()
    {
        return Cache::remember('avg_response_time', 300, function () {
            $logs = DB::table('telescope_entries')
                     ->where('type', 'request')
                     ->where('created_at', '>=', now()->subHour())
                     ->get();
            
            if ($logs->isEmpty()) return 0;
            
            $totalTime = $logs->sum(function ($log) {
                $content = json_decode($log->content, true);
                return $content['duration'] ?? 0;
            });
            
            return round($totalTime / $logs->count(), 2);
        });
    }
}
```

#### 3. Real-time Performance Dashboard

```vue
<template>
  <q-page class="q-pa-md">
    <div class="row q-gutter-md">
      <!-- Response Time Chart -->
      <div class="col-md-6 col-12">
        <q-card>
          <q-card-section>
            <div class="text-h6">Average Response Time</div>
            <canvas ref="responseTimeChart"></canvas>
          </q-card-section>
        </q-card>
      </div>
      
      <!-- Performance Metrics -->
      <div class="col-md-6 col-12">
        <q-card>
          <q-card-section>
            <div class="text-h6">Performance Metrics</div>
            <q-list>
              <q-item>
                <q-item-section>
                  <q-item-label>Average Response Time</q-item-label>
                  <q-item-label caption>{{ metrics.avg_response_time }}ms</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-chip :color="getResponseTimeColor(metrics.avg_response_time)" text-color="white">
                    {{ getResponseTimeStatus(metrics.avg_response_time) }}
                  </q-chip>
                </q-item-section>
              </q-item>
              
              <q-item>
                <q-item-section>
                  <q-item-label>Cache Hit Rate</q-item-label>
                  <q-item-label caption>{{ metrics.cache_hit_rate }}%</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-circular-progress
                    :value="metrics.cache_hit_rate"
                    size="50px"
                    :thickness="0.2"
                    color="primary"
                    track-color="grey-3"
                    show-value
                  />
                </q-item-section>
              </q-item>
              
              <q-item>
                <q-item-section>
                  <q-item-label>Memory Usage</q-item-label>
                  <q-item-label caption>{{ metrics.memory_usage }}MB</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-linear-progress
                    :value="metrics.memory_usage / 1024"
                    size="20px"
                    :color="getMemoryColor(metrics.memory_usage)"
                  />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'PerformanceDashboard',
  
  data() {
    return {
      metrics: {
        avg_response_time: 0,
        cache_hit_rate: 0,
        memory_usage: 0,
        slow_queries_count: 0
      },
      chart: null
    }
  },
  
  async mounted() {
    await this.loadMetrics()
    this.initChart()
    this.startRealTimeUpdates()
  },
  
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
  
  methods: {
    async loadMetrics() {
      try {
        const response = await this.$api.get('/admin/performance/metrics')
        this.metrics = response.data
      } catch (error) {
        this.$q.notify({
          type: 'negative',
          message: 'Failed to load performance metrics'
        })
      }
    },
    
    initChart() {
      const ctx = this.$refs.responseTimeChart.getContext('2d')
      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [{
            label: 'Response Time (ms)',
            data: [],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      })
    },
    
    startRealTimeUpdates() {
      setInterval(async () => {
        await this.loadMetrics()
        this.updateChart()
      }, 30000) // Update every 30 seconds
    },
    
    updateChart() {
      const now = new Date().toLocaleTimeString()
      this.chart.data.labels.push(now)
      this.chart.data.datasets[0].data.push(this.metrics.avg_response_time)
      
      // Keep only last 20 data points
      if (this.chart.data.labels.length > 20) {
        this.chart.data.labels.shift()
        this.chart.data.datasets[0].data.shift()
      }
      
      this.chart.update()
    },
    
    getResponseTimeColor(time) {
      if (time < 100) return 'green'
      if (time < 300) return 'orange'
      return 'red'
    },
    
    getResponseTimeStatus(time) {
      if (time < 100) return 'Excellent'
      if (time < 300) return 'Good'
      return 'Slow'
    },
    
    getMemoryColor(usage) {
      if (usage < 512) return 'green'
      if (usage < 768) return 'orange'
      return 'red'
    }
  }
}
</script>
```

## Load Testing & Benchmarks

### Load Testing with Artillery

```yaml
# artillery-config.yml
config:
  target: 'http://localhost:8000'
  phases:
    - duration: 60
      arrivalRate: 10
      name: "Warm up"
    - duration: 120
      arrivalRate: 50
      name: "Ramp up load"
    - duration: 300
      arrivalRate: 100
      name: "Sustained load"
  payload:
    path: "test-data.csv"
    fields:
      - "email"
      - "password"

scenarios:
  - name: "Authentication Flow"
    weight: 30
    flow:
      - post:
          url: "/api/login"
          json:
            email: "{{ email }}"
            password: "{{ password }}"
          capture:
            - json: "$.token"
              as: "authToken"
      - get:
          url: "/api/user"
          headers:
            Authorization: "Bearer {{ authToken }}"
  
  - name: "Product Search"
    weight: 40
    flow:
      - get:
          url: "/api/products/search"
          qs:
            q: "paracetamol"
            limit: 20
  
  - name: "Sales Transaction"
    weight: 30
    flow:
      - post:
          url: "/api/login"
          json:
            email: "cashier@q-pharmacy.com"
            password: "password"
          capture:
            - json: "$.token"
              as: "authToken"
      - post:
          url: "/api/sales"
          headers:
            Authorization: "Bearer {{ authToken }}"
          json:
            customer_name: "Test Customer"
            items:
              - product_id: 1
                batch_id: 1
                quantity: 2
                unit_price: 5000
```

### Performance Benchmarks

```bash
#!/bin/bash
# performance-benchmark.sh

echo "Starting Q-Pharmacy Performance Benchmark"
echo "======================================"

# API Endpoint Tests
echo "Testing API Endpoints..."

# Login endpoint
echo "Login Endpoint:"
ab -n 1000 -c 10 -p login.json -T application/json http://localhost:8000/api/login

# Product search
echo "Product Search:"
ab -n 1000 -c 10 "http://localhost:8000/api/products/search?q=paracetamol"

# Dashboard data
echo "Dashboard Data:"
ab -n 500 -c 5 -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/dashboard

# Database Performance
echo "Database Performance Tests..."
mysql -u root -p$DB_PASSWORD -e "
  SELECT 'Product Search Performance' as test_name;
  SELECT BENCHMARK(10000, (SELECT COUNT(*) FROM products WHERE name LIKE '%para%'));
  
  SELECT 'Sales Report Performance' as test_name;
  SELECT BENCHMARK(1000, (
    SELECT COUNT(*), SUM(total_amount) 
    FROM sales 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
  ));
" q_potek

echo "Benchmark completed!"
```

### Performance Targets

| Metric | Target | Measurement |
|--------|--------|-------------|
| API Response Time | < 200ms | 95th percentile |
| Database Query Time | < 50ms | Average |
| Page Load Time | < 2s | First Contentful Paint |
| Throughput | > 1000 req/s | Sustained load |
| Memory Usage | < 512MB | Peak usage |
| CPU Usage | < 70% | Average under load |
| Cache Hit Rate | > 90% | Application cache |
| Database Connections | < 50 | Concurrent |

## Optimization Checklist

### Backend Optimization

- [ ] Enable OPcache in production
- [ ] Configure Laravel caching (config, routes, views)
- [ ] Implement Redis for session and cache storage
- [ ] Optimize database queries and add proper indexes
- [ ] Use eager loading to prevent N+1 queries
- [ ] Implement queue system for heavy operations
- [ ] Enable gzip compression
- [ ] Configure proper error handling and logging
- [ ] Use database read replicas for read-heavy operations
- [ ] Implement API rate limiting

### Frontend Optimization

- [ ] Enable lazy loading for routes and components
- [ ] Implement virtual scrolling for large lists
- [ ] Optimize images (WebP format, lazy loading)
- [ ] Use service workers for caching
- [ ] Minimize bundle size with tree shaking
- [ ] Implement code splitting
- [ ] Use CDN for static assets
- [ ] Enable browser caching headers
- [ ] Optimize CSS and JavaScript delivery
- [ ] Implement progressive web app features

### Database Optimization

- [ ] Create proper indexes for all queries
- [ ] Optimize MySQL configuration
- [ ] Implement query caching
- [ ] Use connection pooling
- [ ] Monitor slow query log
- [ ] Implement database partitioning if needed
- [ ] Regular database maintenance (OPTIMIZE TABLE)
- [ ] Use appropriate data types
- [ ] Implement proper foreign key constraints
- [ ] Regular backup and recovery testing

### Infrastructure Optimization

- [ ] Use SSD storage for database
- [ ] Implement load balancing
- [ ] Configure proper server resources
- [ ] Use CDN for global content delivery
- [ ] Implement monitoring and alerting
- [ ] Configure auto-scaling if using cloud
- [ ] Use HTTP/2 for improved performance
- [ ] Implement proper security headers
- [ ] Configure log rotation and management
- [ ] Regular security updates and patches

---

**Document Control:**
- **Version**: 1.0
- **Classification**: Internal
- **Owner**: Development Team
- **Approved By**: CTO
- **Next Review**: 15 December 2025
- **Distribution**: Development Team, DevOps Team
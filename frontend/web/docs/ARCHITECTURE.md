# Frontend Architecture Documentation

## Overview

Dokumentasi ini menjelaskan arsitektur frontend Q-Pharmacy yang dibangun menggunakan Vue.js 3 dan Quasar Framework. Aplikasi ini dirancang dengan pola arsitektur yang modular, scalable, dan maintainable.

## Technology Stack

### Core Technologies
- **Vue.js 3**: Progressive JavaScript framework dengan Composition API
- **Quasar Framework**: Vue.js based framework untuk cross-platform development
- **Pinia**: State management library untuk Vue.js
- **Vue Router**: Official router untuk Vue.js
- **Axios**: HTTP client untuk API communication
- **TypeScript**: Type-safe JavaScript development

### Development Tools
- **Vite**: Build tool dan development server
- **ESLint**: Code linting dan quality assurance
- **Prettier**: Code formatting
- **Vitest**: Unit testing framework

## Architecture Patterns

### Component-Based Architecture

```
src/
├── components/          # Reusable UI components
│   ├── base/           # Base components (buttons, inputs, etc.)
│   ├── layout/         # Layout components
│   ├── forms/          # Form-specific components
│   └── business/       # Business logic components
├── pages/              # Page components (routes)
├── layouts/            # Layout templates
├── stores/             # Pinia stores (state management)
├── composables/        # Vue composables (reusable logic)
├── services/           # API services
├── utils/              # Utility functions
└── types/              # TypeScript type definitions
```

### State Management Pattern

```typescript
// Pinia Store Structure
export const useUserStore = defineStore('user', () => {
  // State
  const user = ref<User | null>(null)
  const isAuthenticated = computed(() => !!user.value)
  
  // Actions
  const login = async (credentials: LoginCredentials) => {
    // Login logic
  }
  
  const logout = () => {
    // Logout logic
  }
  
  return {
    user,
    isAuthenticated,
    login,
    logout
  }
})
```

### Service Layer Pattern

```typescript
// API Service Structure
class ApiService {
  private api: AxiosInstance
  
  constructor() {
    this.api = axios.create({
      baseURL: process.env.VUE_APP_API_URL,
      timeout: 10000
    })
    
    this.setupInterceptors()
  }
  
  private setupInterceptors() {
    // Request interceptor
    this.api.interceptors.request.use(
      (config) => {
        // Add auth token
        return config
      }
    )
    
    // Response interceptor
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        // Handle errors
        return Promise.reject(error)
      }
    )
  }
}
```

## Module Structure

### Authentication Module

```
auth/
├── components/
│   ├── LoginForm.vue
│   ├── RegisterForm.vue
│   └── PasswordReset.vue
├── stores/
│   └── authStore.ts
├── services/
│   └── authService.ts
├── types/
│   └── auth.types.ts
└── composables/
    └── useAuth.ts
```

### Product Management Module

```
products/
├── components/
│   ├── ProductCard.vue
│   ├── ProductForm.vue
│   ├── ProductList.vue
│   └── ProductSearch.vue
├── stores/
│   └── productStore.ts
├── services/
│   └── productService.ts
├── types/
│   └── product.types.ts
└── composables/
    └── useProducts.ts
```

### Inventory Module

```
inventory/
├── components/
│   ├── StockMovement.vue
│   ├── StockOpname.vue
│   ├── LowStockAlert.vue
│   └── ExpiringProducts.vue
├── stores/
│   └── inventoryStore.ts
├── services/
│   └── inventoryService.ts
├── types/
│   └── inventory.types.ts
└── composables/
    └── useInventory.ts
```

## Routing Structure

### Frontend Routes

Aplikasi menggunakan struktur routing yang terorganisir berdasarkan modul fungsional:

```typescript
// Route Structure
const routes = [
  // Landing Page
  {
    path: '/',
    name: 'landing',
    component: () => import('pages/LandingPage.vue'),
    meta: {
      title: 'Q-Pharmacy - Sistem Manajemen Apotek',
      requiresAuth: false
    }
  },

  // Authentication Routes
  {
    path: '/auth',
    children: [
      {
        path: 'login',
        name: 'auth.login',
        component: () => import('pages/auth/LoginPage.vue'),
        meta: {
          title: 'Login',
          requiresAuth: false,
          requiresGuest: true
        }
      }
    ]
  },

  // Application Routes
  {
    path: '/app',
    children: [
      // Dashboard
      {
        path: 'dashboard',
        name: 'app.dashboard',
        component: () => import('pages/core/DashboardPage.vue'),
        meta: {
          title: 'Dashboard',
          requiresAuth: true,
          permission: 'app.dashboard.index'
        }
      },

      // Master Data
      {
        path: 'master',
        children: [
          {
            path: 'categories',
            name: 'app.master.categories',
            component: () => import('pages/master/CategoriesPage.vue'),
            meta: {
              title: 'Kategori Produk',
              breadcrumb: ['Dashboard', 'Master Data', 'Kategori'],
              permission: 'app.master.categories.index'
            }
          }
        ]
      },

      // Product Management
      {
        path: 'products',
        children: [
          {
            path: 'items',
            name: 'app.products.items',
            component: () => import('pages/products/ItemsPage.vue'),
            meta: {
              title: 'Manajemen Produk',
              breadcrumb: ['Dashboard', 'Produk', 'Daftar Produk'],
              permission: 'app.products.items.index'
            }
          },
          {
            path: 'pricing',
            name: 'app.products.pricing',
            component: () => import('pages/products/PricingPage.vue'),
            meta: {
              title: 'Harga Produk',
              breadcrumb: ['Dashboard', 'Produk', 'Harga'],
              permission: 'app.products.pricing.index'
            }
          }
        ]
      },

      // Inventory Management
      {
        path: 'inventories',
        children: [
          {
            path: 'movements',
            name: 'app.inventories.movements',
            component: () => import('pages/inventories/MovementsPage.vue'),
            meta: {
              title: 'Pergerakan Stok',
              breadcrumb: ['Dashboard', 'Inventori', 'Pergerakan Stok'],
              permission: 'app.inventories.movements.index'
            }
          },
          {
            path: 'opname',
            name: 'app.inventories.opname',
            component: () => import('pages/inventories/OpnamePage.vue'),
            meta: {
              title: 'Stock Opname',
              breadcrumb: ['Dashboard', 'Inventori', 'Stock Opname'],
              permission: 'app.inventories.opname.index'
            }
          }
        ]
      },

      // Sales Management
      {
        path: 'sells',
        children: [
          {
            path: 'pos',
            name: 'app.sells.pos',
            component: () => import('pages/sells/POSPage.vue'),
            meta: {
              title: 'Point of Sale',
              breadcrumb: ['Dashboard', 'Penjualan', 'POS'],
              permission: 'app.sells.pos.index'
            }
          },
          {
            path: 'transactions',
            name: 'app.sells.transactions',
            component: () => import('pages/sells/TransactionsPage.vue'),
            meta: {
              title: 'Transaksi Penjualan',
              breadcrumb: ['Dashboard', 'Penjualan', 'Transaksi'],
              permission: 'app.sells.transactions.index'
            }
          }
        ]
      }
    ]
  }
]
```

### Route Guards

Sistem menggunakan permission-based access control:

```typescript
// Route Guard Implementation
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Check authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'auth.login' })
  }
  
  // Check guest access
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return next({ name: 'app.dashboard' })
  }
  
  // Check permissions
  if (to.meta.permission && !authStore.hasPermission(to.meta.permission)) {
    return next({ name: 'error.forbidden' })
  }
  
  next()
})

// Update page title
router.afterEach((to) => {
  document.title = to.meta.title ? `${to.meta.title} - Q-Pharmacy` : 'Q-Pharmacy'
})
```

### Meta Data Structure

Setiap route memiliki meta data yang konsisten:

```typescript
interface RouteMeta {
  title: string                    // Page title
  requiresAuth?: boolean           // Requires authentication
  requiresGuest?: boolean          // Guest only access
  permission?: string              // Required permission
  breadcrumb?: string[]            // Breadcrumb navigation
  layout?: string                  // Layout component
}
```
│   ├── InventoryTable.vue
│   ├── StockAlert.vue
│   ├── BatchTracker.vue
│   └── ExpiryMonitor.vue
├── stores/
│   └── inventoryStore.ts
├── services/
│   └── inventoryService.ts
├── types/
│   └── inventory.types.ts
└── composables/
    └── useInventory.ts
```

## Component Architecture

### Base Components

Komponen dasar yang dapat digunakan kembali di seluruh aplikasi:

```vue
<!-- BaseButton.vue -->
<template>
  <q-btn
    :class="buttonClasses"
    :loading="loading"
    :disable="disabled"
    v-bind="$attrs"
    @click="handleClick"
  >
    <slot />
  </q-btn>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'danger'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const buttonClasses = computed(() => ({
  [`btn-${props.variant}`]: true,
  [`btn-${props.size}`]: true
}))

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>
```

### Composables Pattern

```typescript
// useApi.ts
export function useApi<T>() {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const data = ref<T | null>(null)
  
  const execute = async (apiCall: () => Promise<T>) => {
    try {
      loading.value = true
      error.value = null
      data.value = await apiCall()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Unknown error'
    } finally {
      loading.value = false
    }
  }
  
  return {
    loading: readonly(loading),
    error: readonly(error),
    data: readonly(data),
    execute
  }
}
```

## Routing Architecture

### Route Structure

```typescript
// router/index.ts - Router configuration with guards
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('pages/Dashboard.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/products',
        component: () => import('pages/Products/ProductList.vue'),
        meta: { requiresAuth: true, permission: 'products.view' }
      },
      {
        path: '/inventory',
        component: () => import('pages/Inventory/InventoryDashboard.vue'),
        meta: { requiresAuth: true, permission: 'inventory.view' }
      }
    ]
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        component: () => import('pages/Auth/Login.vue')
      }
    ]
  }
]
```

### Route Guards

```typescript
// Route guard implementation
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/auth/login')
    return
  }
  
  if (to.meta.permission && !authStore.hasPermission(to.meta.permission)) {
    next('/unauthorized')
    return
  }
  
  next()
})
```

## Performance Optimization

### Code Splitting

```typescript
// Lazy loading components
const ProductList = defineAsyncComponent(() => 
  import('../components/ProductList.vue')
)

// Route-based code splitting
const routes = [
  {
    path: '/products',
    component: () => import('../pages/Products.vue')
  }
]
```

### Virtual Scrolling

```vue
<template>
  <q-virtual-scroll
    :items="items"
    :item-size="60"
    v-slot="{ item, index }"
  >
    <ProductCard :product="item" :key="index" />
  </q-virtual-scroll>
</template>
```

### Memoization

```typescript
// Computed memoization
const expensiveComputation = computed(() => {
  return heavyCalculation(props.data)
})

// Component memoization
const MemoizedComponent = defineComponent({
  // Component definition
})
```

## Error Handling

### Global Error Handler

```typescript
// main.ts
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err)
  // Send to error reporting service
}

// Error boundary component
const ErrorBoundary = defineComponent({
  setup(_, { slots }) {
    const error = ref<Error | null>(null)
    
    onErrorCaptured((err) => {
      error.value = err
      return false
    })
    
    return () => {
      if (error.value) {
        return h('div', { class: 'error-boundary' }, [
          h('h2', 'Something went wrong'),
          h('p', error.value.message)
        ])
      }
      
      return slots.default?.()
    }
  }
})
```

## Testing Architecture

### Unit Testing

```typescript
// Component testing
import { mount } from '@vue/test-utils'
import ProductCard from '../ProductCard.vue'

describe('ProductCard', () => {
  it('renders product information correctly', () => {
    const product = {
      id: 1,
      name: 'Test Product',
      price: 100
    }
    
    const wrapper = mount(ProductCard, {
      props: { product }
    })
    
    expect(wrapper.text()).toContain('Test Product')
    expect(wrapper.text()).toContain('100')
  })
})
```

### Integration Testing

```typescript
// Store testing
import { setActivePinia, createPinia } from 'pinia'
import { useProductStore } from '../stores/productStore'

describe('Product Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })
  
  it('fetches products correctly', async () => {
    const store = useProductStore()
    await store.fetchProducts()
    
    expect(store.products).toHaveLength(10)
  })
})
```

## Security Considerations

### XSS Prevention

```vue
<!-- Safe HTML rendering -->
<template>
  <div v-html="sanitizedHtml"></div>
</template>

<script setup>
import DOMPurify from 'dompurify'

const props = defineProps<{
  htmlContent: string
}>()

const sanitizedHtml = computed(() => 
  DOMPurify.sanitize(props.htmlContent)
)
</script>
```

### CSRF Protection

```typescript
// API service with CSRF token
class ApiService {
  constructor() {
    this.api.defaults.headers.common['X-CSRF-TOKEN'] = 
      document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  }
}
```

## Build and Deployment

### Build Configuration

```javascript
// quasar.config.js
module.exports = {
  build: {
    vueRouterMode: 'history',
    env: {
      API_URL: process.env.API_URL || 'http://localhost:8000/api'
    },
    extendWebpack(cfg) {
      cfg.resolve.alias = {
        ...cfg.resolve.alias,
        '@': path.resolve(__dirname, 'src')
      }
    }
  },
  
  devServer: {
    port: 3000,
    open: true
  }
}
```

### Environment Configuration

```bash
# .env.development
VUE_APP_API_URL=http://localhost:8000/api
VUE_APP_ENV=development

# .env.production
VUE_APP_API_URL=https://api.q-pharmacy.com
VUE_APP_ENV=production
```

## Monitoring and Analytics

### Performance Monitoring

```typescript
// Performance tracking
const trackPageLoad = () => {
  const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming
  const loadTime = navigation.loadEventEnd - navigation.loadEventStart
  
  // Send to analytics
  analytics.track('page_load_time', {
    duration: loadTime,
    page: router.currentRoute.value.path
  })
}
```

### Error Tracking

```typescript
// Error reporting
const reportError = (error: Error, context?: any) => {
  errorReporting.captureException(error, {
    tags: {
      component: context?.component,
      route: router.currentRoute.value.path
    },
    extra: context
  })
}
```

## Best Practices

### Component Design
1. **Single Responsibility**: Setiap komponen harus memiliki satu tanggung jawab
2. **Props Validation**: Selalu validasi props dengan TypeScript
3. **Event Naming**: Gunakan kebab-case untuk nama event
4. **Slot Usage**: Gunakan slot untuk konten yang fleksibel

### State Management
1. **Store Separation**: Pisahkan store berdasarkan domain
2. **Computed Properties**: Gunakan computed untuk derived state
3. **Action Naming**: Gunakan verb untuk nama action
4. **Mutation Tracking**: Track semua state changes

### Performance
1. **Lazy Loading**: Load komponen secara lazy
2. **Virtual Scrolling**: Untuk list yang panjang
3. **Memoization**: Cache hasil komputasi yang mahal
4. **Bundle Splitting**: Split bundle berdasarkan route

## Conclusion

Arsitektur frontend Q-Pharmacy dirancang untuk mendukung pengembangan yang scalable dan maintainable. Dengan mengikuti pola arsitektur yang telah ditetapkan, tim dapat mengembangkan fitur baru dengan konsisten dan efisien.

Untuk informasi lebih lanjut, lihat dokumentasi komponen dan panduan pengembangan lainnya.
# Frontend Documentation - Q-Pharmacy Web Application

## Overview

Dokumentasi lengkap untuk aplikasi web frontend Q-Pharmacy yang dibangun menggunakan Vue.js 3 dengan Composition API, TypeScript, dan Vite.

## Table of Contents

- [Quick Start](#quick-start)
- [Project Structure](#project-structure)
- [Core Features](#core-features)
- [Development Guidelines](#development-guidelines)
- [Component Library](#component-library)
- [State Management](#state-management)
- [Routing](#routing)
- [API Integration](#api-integration)
- [Testing](#testing)
- [Build & Deployment](#build--deployment)
- [Troubleshooting](#troubleshooting)

## Quick Start

### Prerequisites

- **Node.js**: >= 18.0
- **npm**: >= 8.0 atau **yarn**: >= 1.22
- **Git**: Latest version

### Installation

```bash
# Clone repository
git clone https://github.com/your-org/q-pharmacy-frontend.git
cd q-pharmacy-frontend

# Install dependencies
npm install
# atau
yarn install

# Copy environment file
cp .env.example .env.local

# Start development server
npm run dev
# atau
yarn dev
```

### Environment Configuration

```env
# .env.local
VITE_APP_TITLE="Q-Pharmacy"
VITE_API_BASE_URL="http://localhost:8000/api"
VITE_APP_VERSION="1.0.0"
VITE_ENABLE_MOCK="false"
VITE_ENABLE_PWA="true"
```

## Project Structure

```
frontend/web/
├── public/                 # Static assets
│   ├── favicon.ico
│   ├── manifest.json
│   └── icons/
├── src/
│   ├── assets/            # Images, fonts, styles
│   │   ├── images/
│   │   ├── fonts/
│   │   └── styles/
│   ├── components/        # Reusable components
│   │   ├── common/        # Common UI components
│   │   ├── forms/         # Form components
│   │   ├── layout/        # Layout components
│   │   └── ui/            # Base UI components
│   ├── composables/       # Vue composables
│   │   ├── useApi.ts
│   │   ├── useAuth.ts
│   │   └── useNotification.ts
│   ├── layouts/           # Page layouts
│   │   ├── DefaultLayout.vue
│   │   ├── AuthLayout.vue
│   │   └── AdminLayout.vue
│   ├── pages/             # Page components
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── products/
│   │   ├── inventory/
│   │   ├── pos/
│   │   └── reports/
│   ├── plugins/           # Vue plugins
│   │   ├── router.ts
│   │   ├── pinia.ts
│   │   └── i18n.ts
│   ├── services/          # API services
│   │   ├── api.ts
│   │   ├── auth.service.ts
│   │   ├── product.service.ts
│   │   └── inventory.service.ts
│   ├── stores/            # Pinia stores
│   │   ├── auth.store.ts
│   │   ├── product.store.ts
│   │   └── ui.store.ts
│   ├── types/             # TypeScript types
│   │   ├── api.types.ts
│   │   ├── auth.types.ts
│   │   └── product.types.ts
│   ├── utils/             # Utility functions
│   │   ├── helpers.ts
│   │   ├── validators.ts
│   │   └── constants.ts
│   ├── App.vue            # Root component
│   └── main.ts            # Application entry point
├── tests/                 # Test files
│   ├── unit/
│   ├── integration/
│   └── e2e/
├── docs/                  # Documentation
│   ├── README.md
│   ├── COMPONENTS.md
│   ├── ARCHITECTURE.md
│   ├── DEPLOYMENT.md
│   └── TESTING.md
├── .env.example           # Environment template
├── .gitignore
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
└── vitest.config.ts
```

## Core Features

### 1. Authentication & Authorization

- **Login/Logout**: JWT-based authentication
- **Role-based Access**: Admin, Cashier, Manager roles
- **Route Guards**: Protected routes based on permissions
- **Session Management**: Auto-refresh tokens

### 2. Product Management

- **Product CRUD**: Create, read, update, delete products
- **Category Management**: Organize products by categories
- **Barcode Scanner**: Integrated barcode scanning
- **Image Upload**: Product image management
- **Bulk Operations**: Import/export products

### 3. Inventory Management

- **Stock Tracking**: Real-time stock levels
- **Batch Management**: Track product batches and expiry
- **Stock Movements**: Record all stock changes
- **Low Stock Alerts**: Automated notifications
- **Stock Adjustments**: Manual stock corrections

### 4. Point of Sale (POS)

- **Transaction Processing**: Fast checkout process
- **Payment Methods**: Cash, card, digital payments
- **Receipt Generation**: Print and digital receipts
- **Customer Management**: Customer information tracking
- **Discount Management**: Apply various discount types

### 5. Reporting & Analytics

- **Sales Reports**: Daily, weekly, monthly sales
- **Inventory Reports**: Stock levels and movements
- **Financial Reports**: Revenue and profit analysis
- **Export Options**: PDF, Excel, CSV formats
- **Dashboard Analytics**: Visual charts and metrics

## Development Guidelines

### Code Style

```typescript
// Use Composition API with <script setup>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Product } from '@/types/product.types'

// Props with TypeScript
interface Props {
  productId: number
  readonly?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  readonly: false
})

// Reactive data
const product = ref<Product | null>(null)
const loading = ref(false)

// Computed properties
const isAvailable = computed(() => {
  return product.value && product.value.stock > 0
})

// Methods
const fetchProduct = async () => {
  loading.value = true
  try {
    // API call
  } catch (error) {
    console.error('Failed to fetch product:', error)
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchProduct()
})
</script>
```

### Component Naming

- **PascalCase**: Component files and names
- **kebab-case**: HTML elements and props
- **camelCase**: JavaScript variables and functions

```vue
<!-- Good -->
<ProductCard :product-data="product" @update-stock="handleStockUpdate" />

<!-- Bad -->
<product-card :productData="product" @updateStock="handleStockUpdate" />
```

### File Organization

```typescript
// Component structure
<template>
  <!-- Template content -->
</template>

<script setup lang="ts">
// 1. Imports
// 2. Props/Emits definitions
// 3. Reactive data
// 4. Computed properties
// 5. Methods
// 6. Lifecycle hooks
// 7. Watchers
</script>

<style scoped>
/* Component-specific styles */
</style>
```

## Component Library

Aplikasi menggunakan komponen UI yang konsisten dan dapat digunakan kembali:

### Base Components

- **BaseButton**: Tombol dengan berbagai varian
- **BaseInput**: Input field dengan validasi
- **BaseModal**: Modal dialog
- **BaseTable**: Tabel data dengan sorting dan pagination
- **BaseCard**: Card container

### Form Components

- **FormField**: Wrapper untuk form input
- **FormSelect**: Dropdown selection
- **FormCheckbox**: Checkbox input
- **FormRadio**: Radio button input
- **FormDatePicker**: Date selection

### Layout Components

- **AppHeader**: Application header
- **AppSidebar**: Navigation sidebar
- **AppFooter**: Application footer
- **PageContainer**: Page wrapper
- **ContentSection**: Content section wrapper

## State Management

Menggunakan Pinia untuk state management:

```typescript
// stores/auth.store.ts
import { defineStore } from 'pinia'
import type { User, LoginCredentials } from '@/types/auth.types'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const isAuthenticated = ref(false)

  // Getters
  const userRole = computed(() => user.value?.role)
  const hasPermission = computed(() => (permission: string) => {
    return user.value?.permissions?.includes(permission) ?? false
  })

  // Actions
  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await authService.login(credentials)
      user.value = response.user
      token.value = response.token
      isAuthenticated.value = true
      
      // Store in localStorage
      localStorage.setItem('auth_token', response.token)
    } catch (error) {
      throw error
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    isAuthenticated.value = false
    localStorage.removeItem('auth_token')
  }

  return {
    // State
    user,
    token,
    isAuthenticated,
    // Getters
    userRole,
    hasPermission,
    // Actions
    login,
    logout
  }
})
```

## Routing

Menggunakan Vue Router dengan route guards:

```typescript
// plugins/router.ts
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/pages/dashboard/DashboardPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('@/pages/products/ProductsPage.vue'),
    meta: { 
      requiresAuth: true,
      permissions: ['view-products']
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Route guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/dashboard')
  } else if (to.meta.permissions) {
    const hasPermission = to.meta.permissions.every(permission => 
      authStore.hasPermission(permission)
    )
    
    if (!hasPermission) {
      next('/unauthorized')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
```

## API Integration

Menggunakan Axios untuk HTTP requests:

```typescript
// services/api.ts
import axios from 'axios'
import { useAuthStore } from '@/stores/auth.store'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: 10000
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.logout()
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
```

## Testing

### Unit Testing dengan Vitest

```typescript
// tests/unit/components/ProductCard.test.ts
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ProductCard from '@/components/products/ProductCard.vue'

describe('ProductCard', () => {
  const mockProduct = {
    id: 1,
    name: 'Test Product',
    price: 10000,
    stock: 50
  }

  it('renders product information correctly', () => {
    const wrapper = mount(ProductCard, {
      props: { product: mockProduct }
    })

    expect(wrapper.text()).toContain('Test Product')
    expect(wrapper.text()).toContain('Rp 10,000')
    expect(wrapper.text()).toContain('50')
  })

  it('emits update-stock event when stock is updated', async () => {
    const wrapper = mount(ProductCard, {
      props: { product: mockProduct }
    })

    await wrapper.find('[data-testid="update-stock-btn"]').trigger('click')
    
    expect(wrapper.emitted('update-stock')).toBeTruthy()
  })
})
```

### E2E Testing dengan Playwright

```typescript
// tests/e2e/auth.spec.ts
import { test, expect } from '@playwright/test'

test.describe('Authentication', () => {
  test('should login successfully with valid credentials', async ({ page }) => {
    await page.goto('/login')
    
    await page.fill('[data-testid="email-input"]', 'admin@example.com')
    await page.fill('[data-testid="password-input"]', 'password')
    await page.click('[data-testid="login-button"]')
    
    await expect(page).toHaveURL('/dashboard')
    await expect(page.locator('[data-testid="user-menu"]')).toBeVisible()
  })

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/login')
    
    await page.fill('[data-testid="email-input"]', 'invalid@example.com')
    await page.fill('[data-testid="password-input"]', 'wrongpassword')
    await page.click('[data-testid="login-button"]')
    
    await expect(page.locator('[data-testid="error-message"]')).toBeVisible()
  })
})
```

## Build & Deployment

### Development Build

```bash
# Start development server
npm run dev

# Build for development
npm run build:dev
```

### Production Build

```bash
# Build for production
npm run build

# Preview production build
npm run preview

# Analyze bundle size
npm run analyze
```

### Docker Deployment

```dockerfile
# Dockerfile
FROM node:18-alpine as build-stage

WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production

COPY . .
RUN npm run build

FROM nginx:alpine as production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

## Troubleshooting

### Common Issues

1. **Build Errors**
   ```bash
   # Clear node_modules and reinstall
   rm -rf node_modules package-lock.json
   npm install
   ```

2. **TypeScript Errors**
   ```bash
   # Check TypeScript configuration
   npx tsc --noEmit
   ```

3. **Hot Reload Not Working**
   ```bash
   # Check Vite configuration
   # Ensure file watching is enabled
   ```

4. **API Connection Issues**
   ```typescript
   // Check environment variables
   console.log('API Base URL:', import.meta.env.VITE_API_BASE_URL)
   ```

### Performance Optimization

1. **Code Splitting**
   ```typescript
   // Use dynamic imports for routes
   const ProductsPage = () => import('@/pages/products/ProductsPage.vue')
   ```

2. **Image Optimization**
   ```vue
   <template>
     <img 
       :src="optimizedImageUrl" 
       loading="lazy" 
       alt="Product image"
     />
   </template>
   ```

3. **Bundle Analysis**
   ```bash
   npm run analyze
   ```

## Contributing

Untuk berkontribusi pada project ini:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## Support

Untuk bantuan dan pertanyaan:

- **Documentation**: Lihat file dokumentasi di folder `/docs`
- **Issues**: Buat issue di GitHub repository
- **Team Contact**: Hubungi tim development

---

**Q-Pharmacy Frontend Team**  
Version 1.0.0 | Last Updated: December 2024
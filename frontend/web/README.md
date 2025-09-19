# Q-Pharmacy - Frontend Web Application

## Daftar Isi
- [Tentang Proyek](#tentang-proyek)
- [Teknologi Stack](#teknologi-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Development](#development)
- [Build & Deployment](#build--deployment)
- [Struktur Proyek](#struktur-proyek)
- [Komponen Utama](#komponen-utama)
- [State Management](#state-management)
- [Routing](#routing)
- [API Integration](#api-integration)
- [Testing](#testing)
- [Performance](#performance)
- [Troubleshooting](#troubleshooting)
- [Resources](#resources)

## Tentang Proyek

Frontend web application untuk Q-Pharmacy, sistem manajemen apotek modern yang dibangun menggunakan Vue.js 3 dan Quasar Framework. Aplikasi ini menyediakan interface yang responsif dan user-friendly untuk mengelola operasional apotek.

### Fitur Utama
- **Dashboard Analytics** - Visualisasi data penjualan dan inventory
- **Manajemen Produk** - CRUD produk dengan kategori dan supplier
- **Point of Sale (POS)** - Sistem kasir dengan barcode scanner
- **Inventory Management** - Tracking stok dan notifikasi low stock
- **User Management** - Role-based access control
- **Laporan** - Generate laporan penjualan dan inventory
- **Responsive Design** - Optimized untuk desktop dan mobile

## Teknologi Stack

### Core Framework
- **Vue.js 3** - Progressive JavaScript framework
- **Quasar Framework 2** - Vue.js based framework
- **Composition API** - Modern Vue.js development approach

### State Management
- **Pinia** - Vue.js state management
- **Vuex** - Legacy state management (migration to Pinia)

### UI/UX
- **Quasar Components** - Material Design components
- **CSS Grid & Flexbox** - Modern layout systems
- **SCSS** - Enhanced CSS with variables and mixins

### Development Tools
- **Vite** - Fast build tool and dev server
- **ESLint** - Code linting and formatting
- **Prettier** - Code formatting
- **TypeScript** - Type safety (optional)

### Testing
- **Vitest** - Unit testing framework
- **Cypress** - E2E testing
- **Vue Test Utils** - Vue component testing

## Persyaratan Sistem

### Minimum Requirements
- **Node.js** >= 16.x
- **npm** >= 8.x atau **yarn** >= 1.22.x
- **RAM** >= 4GB
- **Storage** >= 2GB free space

### Recommended
- **Node.js** >= 18.x
- **yarn** >= 3.x
- **RAM** >= 8GB
- **SSD Storage**

### Browser Support
- Chrome >= 90
- Firefox >= 88
- Safari >= 14
- Edge >= 90

## Instalasi

### 1. Clone Repository
```bash
cd frontend/web
```

### 2. Install Dependencies
```bash
# Menggunakan yarn (recommended)
yarn install

# Atau menggunakan npm
npm install
```

### 3. Install Quasar CLI
```bash
# Global installation
npm install -g @quasar/cli
# atau
yarn global add @quasar/cli
```

## Konfigurasi

### Environment Variables
Buat file `.env` berdasarkan `.env.example`:

```bash
# API Configuration
VUE_APP_API_BASE_URL=http://localhost:8000/api
VUE_APP_API_TIMEOUT=30000

# Application Configuration
VUE_APP_NAME=Q-Pharmacy
VUE_APP_VERSION=1.0.0
VUE_APP_ENVIRONMENT=development

# Authentication
VUE_APP_TOKEN_STORAGE_KEY=q_pharmacy_token
VUE_APP_SESSION_TIMEOUT=3600000

# Features
VUE_APP_ENABLE_PWA=true
VUE_APP_ENABLE_ANALYTICS=false
```

### Quasar Configuration
Konfigurasi utama di `quasar.config.js`:

```javascript
module.exports = {
  // Build configuration
  build: {
    vueRouterMode: 'history',
    env: {
      API_BASE_URL: process.env.VUE_APP_API_BASE_URL
    }
  },
  
  // Development server
  devServer: {
    port: 3000,
    open: true
  },
  
  // Framework configuration
  framework: {
    config: {},
    plugins: ['Notify', 'Dialog', 'Loading']
  }
}
```

## Development

### Start Development Server
```bash
# Menggunakan Quasar CLI
quasar dev

# Atau menggunakan yarn/npm
yarn dev
npm run dev
```

Aplikasi akan berjalan di `http://localhost:3000`

### Development Commands
```bash
# Linting
yarn lint
npm run lint

# Format code
yarn format
npm run format

# Type checking (jika menggunakan TypeScript)
yarn type-check
npm run type-check
```

## Build & Deployment

### Production Build
```bash
# Build untuk production
quasar build

# Atau
yarn build
npm run build
```

### Build Options
```bash
# Build dengan PWA
quasar build -m pwa

# Build untuk Electron
quasar build -m electron

# Build untuk Capacitor (mobile)
quasar build -m capacitor -T android
```

### Deployment

#### Static Hosting (Netlify, Vercel)
```bash
# Build files akan ada di dist/spa/
quasar build
```

#### Docker Deployment
```dockerfile
FROM node:18-alpine as build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist/spa /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

## Struktur Proyek

```
src/
├── components/          # Reusable components
│   ├── common/         # Common UI components
│   ├── forms/          # Form components
│   └── charts/         # Chart components
├── layouts/            # Layout components
│   ├── MainLayout.vue  # Main application layout
│   └── AuthLayout.vue  # Authentication layout
├── pages/              # Page components
│   ├── auth/          # Authentication pages
│   ├── dashboard/     # Dashboard pages
│   ├── products/      # Product management
│   ├── pos/           # Point of Sale
│   └── reports/       # Reports pages
├── router/            # Vue Router configuration
│   ├── index.js       # Main router file
│   └── routes.js      # Route definitions
├── stores/            # Pinia stores
│   ├── auth.js        # Authentication store
│   ├── products.js    # Products store
│   └── pos.js         # POS store
├── services/          # API services
│   ├── api.js         # Axios configuration
│   ├── auth.js        # Auth API calls
│   └── products.js    # Products API calls
├── utils/             # Utility functions
│   ├── helpers.js     # General helpers
│   ├── validators.js  # Form validators
│   └── constants.js   # App constants
├── composables/       # Vue 3 composables
│   ├── useAuth.js     # Authentication composable
│   └── useApi.js      # API composable
└── assets/            # Static assets
    ├── images/        # Images
    └── styles/        # SCSS files
```

## Komponen Utama

### Authentication
- **LoginPage** - User login interface
- **AuthGuard** - Route protection
- **TokenManager** - JWT token handling

### Dashboard
- **DashboardOverview** - Main dashboard with KPIs
- **SalesChart** - Sales analytics visualization
- **InventoryWidget** - Quick inventory overview

### Product Management
- **ProductList** - Product listing with search/filter
- **ProductForm** - Add/edit product form
- **CategoryManager** - Product category management

### Point of Sale
- **POSInterface** - Main cashier interface
- **BarcodeScanner** - Barcode scanning component
- **PaymentProcessor** - Payment handling

## State Management

### Pinia Stores

#### Auth Store
```javascript
// stores/auth.js
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),
  
  actions: {
    async login(credentials) {
      // Login logic
    },
    
    async logout() {
      // Logout logic
    }
  }
})
```

#### Products Store
```javascript
// stores/products.js
export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    loading: false
  }),
  
  actions: {
    async fetchProducts() {
      // Fetch products logic
    }
  }
})
```

## Routing

### Route Configuration
```javascript
// router/routes.js
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        component: () => import('pages/Dashboard.vue')
      },
      {
        path: '/products',
        component: () => import('pages/products/ProductList.vue')
      }
    ]
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        component: () => import('pages/auth/Login.vue')
      }
    ]
  }
]
```

### Route Guards
```javascript
// router/index.js
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/auth/login')
  } else {
    next()
  }
})
```

## API Integration

### Axios Configuration
```javascript
// services/api.js
import axios from 'axios'

const api = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL,
  timeout: 30000
})

// Request interceptor
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Handle unauthorized
    }
    return Promise.reject(error)
  }
)
```

### API Services
```javascript
// services/products.js
export const productService = {
  async getProducts(params) {
    const response = await api.get('/products', { params })
    return response.data
  },
  
  async createProduct(data) {
    const response = await api.post('/products', data)
    return response.data
  }
}
```

## Testing

### Unit Testing
```bash
# Run unit tests
yarn test:unit
npm run test:unit

# Run with coverage
yarn test:unit --coverage
```

### E2E Testing
```bash
# Run E2E tests
yarn test:e2e
npm run test:e2e

# Run in headless mode
yarn test:e2e:headless
```

### Test Structure
```
tests/
├── unit/              # Unit tests
│   ├── components/    # Component tests
│   └── stores/        # Store tests
└── e2e/               # E2E tests
    ├── specs/         # Test specifications
    └── support/       # Test utilities
```

## Performance

### Optimization Techniques
- **Code Splitting** - Route-based code splitting
- **Lazy Loading** - Component lazy loading
- **Image Optimization** - WebP format and lazy loading
- **Bundle Analysis** - Webpack bundle analyzer

### Performance Monitoring
```javascript
// Performance metrics
const observer = new PerformanceObserver((list) => {
  for (const entry of list.getEntries()) {
    console.log(entry.name, entry.duration)
  }
})

observer.observe({ entryTypes: ['measure'] })
```

### Build Optimization
```javascript
// quasar.config.js
build: {
  analyze: true,
  minify: true,
  gzip: true,
  extractCSS: true
}
```

## Troubleshooting

### Common Issues

#### 1. Build Errors
```bash
# Clear cache
rm -rf node_modules/.cache
rm -rf .quasar

# Reinstall dependencies
rm -rf node_modules
yarn install
```

#### 2. Development Server Issues
```bash
# Check port availability
lsof -ti:3000

# Kill process if needed
kill -9 $(lsof -ti:3000)
```

#### 3. API Connection Issues
- Verify API base URL in environment variables
- Check CORS configuration on backend
- Verify network connectivity

### Debug Mode
```javascript
// Enable Vue devtools
Vue.config.devtools = true

// Enable debug logging
if (process.env.NODE_ENV === 'development') {
  console.log('Debug mode enabled')
}
```

## Resources

### Documentation
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Quasar Framework](https://quasar.dev/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vue Router](https://router.vuejs.org/)

### Tools
- [Vue DevTools](https://devtools.vuejs.org/)
- [Quasar DevTools](https://github.com/quasarframework/quasar-devtools)
- [Vite DevTools](https://github.com/webfansplz/vite-plugin-vue-devtools)

### Community
- [Vue.js Community](https://vuejs.org/community/)
- [Quasar Discord](https://discord.gg/5TDhbDg)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/vue.js)

---

**Q-Pharmacy Frontend Team**  
Version: 0.0.1  
Last Updated: September 2025
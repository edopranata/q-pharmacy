# Frontend Testing Documentation

## Overview

Dokumentasi ini menjelaskan strategi testing, tools, dan best practices untuk frontend Q-Pharmacy yang dibangun dengan Vue.js 3 dan Quasar Framework. Testing mencakup unit testing, component testing, integration testing, dan end-to-end testing.

## Testing Stack

### Core Testing Tools
- **Vitest**: Modern testing framework untuk Vite projects
- **Vue Test Utils**: Official testing utilities untuk Vue.js
- **@testing-library/vue**: Simple and complete testing utilities
- **Cypress**: End-to-end testing framework
- **MSW (Mock Service Worker)**: API mocking untuk testing
- **@quasar/testing**: Quasar-specific testing utilities

### Additional Tools
- **ESLint**: Code linting dan quality assurance
- **Prettier**: Code formatting
- **Istanbul**: Code coverage reporting
- **Storybook**: Component development dan testing
- **Playwright**: Alternative E2E testing (optional)

## Testing Structure

```
src/
├── components/
│   ├── __tests__/           # Component tests
│   │   ├── BaseButton.test.js
│   │   ├── ProductCard.test.js
│   │   └── InventoryTable.test.js
│   └── ...
├── pages/
│   ├── __tests__/           # Page tests
│   │   ├── Dashboard.test.js
│   │   ├── ProductList.test.js
│   │   └── Login.test.js
│   └── ...
├── stores/
│   ├── __tests__/           # Store tests
│   │   ├── authStore.test.js
│   │   ├── productStore.test.js
│   │   └── inventoryStore.test.js
│   └── ...
├── composables/
│   ├── __tests__/           # Composable tests
│   │   ├── useApi.test.js
│   │   ├── useAuth.test.js
│   │   └── useProducts.test.js
│   └── ...
├── utils/
│   ├── __tests__/           # Utility tests
│   │   ├── formatters.test.js
│   │   ├── validators.test.js
│   │   └── helpers.test.js
│   └── ...
tests/
├── e2e/                     # End-to-end tests
│   ├── cypress/
│   │   ├── fixtures/
│   │   ├── integration/
│   │   ├── plugins/
│   │   └── support/
│   └── specs/
│       ├── auth.cy.js
│       ├── products.cy.js
│       ├── inventory.cy.js
│       └── pos.cy.js
├── integration/             # Integration tests
│   ├── api-integration.test.js
│   ├── router-integration.test.js
│   └── store-integration.test.js
├── performance/             # Performance tests
│   ├── component-performance.test.js
│   └── page-load.test.js
├── accessibility/            # Accessibility tests
│   ├── a11y.test.js
│   └── keyboard-navigation.test.js
├── fixtures/                # Test data
│   ├── products.json
│   ├── users.json
│   └── inventory.json
├── mocks/                   # Mock implementations
│   ├── api.js
│   ├── localStorage.js
│   └── router.js
└── setup/                   # Test setup files
    ├── vitest.config.js
    ├── test-utils.js
    └── global-setup.js
```

## Test Configuration

### Vitest Configuration

```javascript
// vitest.config.js
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
import path from 'path'

export default defineConfig({
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: 'src/quasar-variables.sass'
    })
  ],
  test: {
    globals: true,
    environment: 'happy-dom',
    setupFiles: ['tests/setup/global-setup.js'],
    coverage: {
      provider: 'istanbul',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.d.ts',
        'src/boot/',
        'src/router/routes.js'
      ],
      thresholds: {
        global: {
          branches: 80,
          functions: 80,
          lines: 80,
          statements: 80
        }
      }
    },
    deps: {
      inline: ['@quasar/extras']
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      'src': path.resolve(__dirname, './src')
    }
  }
})
```

### Global Test Setup

```javascript
// tests/setup/global-setup.js
import { config } from '@vue/test-utils'
import { Quasar, Notify } from 'quasar'
import { createTestingPinia } from '@pinia/testing'
import { vi } from 'vitest'

// Global Quasar configuration
config.global.plugins = [
  [Quasar, {
    plugins: [Notify]
  }]
]

// Global Pinia configuration
config.global.plugins.push([createTestingPinia({
  createSpy: vi.fn
})])

// Mock global objects
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
})

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn(),
}
vi.stubGlobal('localStorage', localStorageMock)

// Mock sessionStorage
const sessionStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn(),
}
vi.stubGlobal('sessionStorage', sessionStorageMock)
```

### Test Utils

```javascript
// tests/setup/test-utils.js
import { mount } from '@vue/test-utils'
import { Quasar } from 'quasar'
import { createTestingPinia } from '@pinia/testing'
import { createRouter, createWebHistory } from 'vue-router'
import { vi } from 'vitest'

// Create test router
export const createTestRouter = (routes = []) => {
  return createRouter({
    history: createWebHistory(),
    routes: [
      { path: '/', component: { template: '<div>Home</div>' } },
      ...routes
    ]
  })
}

// Enhanced mount function
export const mountComponent = (component, options = {}) => {
  const defaultOptions = {
    global: {
      plugins: [
        [Quasar, {}],
        createTestingPinia({
          createSpy: vi.fn,
          stubActions: false
        })
      ],
      stubs: {
        'router-link': true,
        'router-view': true
      }
    }
  }

  return mount(component, {
    ...defaultOptions,
    ...options,
    global: {
      ...defaultOptions.global,
      ...options.global
    }
  })
}

// Mock API responses
export const mockApiResponse = (data, status = 200) => {
  return Promise.resolve({
    data,
    status,
    statusText: 'OK',
    headers: {},
    config: {}
  })
}

// Mock API error
export const mockApiError = (message = 'API Error', status = 500) => {
  const error = new Error(message)
  error.response = {
    data: { message },
    status,
    statusText: 'Internal Server Error'
  }
  return Promise.reject(error)
}

// Wait for next tick and DOM updates
export const waitForUpdate = async () => {
  await new Promise(resolve => setTimeout(resolve, 0))
}

// Create mock user
export const createMockUser = (overrides = {}) => {
  return {
    id: 1,
    name: 'Test User',
    email: 'test@example.com',
    role: 'pharmacist',
    ...overrides
  }
}

// Create mock product
export const createMockProduct = (overrides = {}) => {
  return {
    id: 1,
    name: 'Test Product',
    sku: 'TEST-001',
    price: 100.00,
    category: 'Medicine',
    stock: 50,
    ...overrides
  }
}
```

## Unit Testing

### Component Testing

```javascript
// src/components/__tests__/BaseButton.test.js
import { describe, it, expect, vi } from 'vitest'
import { mountComponent } from '../../../tests/setup/test-utils'
import BaseButton from '../BaseButton.vue'

describe('BaseButton', () => {
  it('renders button with correct text', () => {
    const wrapper = mountComponent(BaseButton, {
      slots: {
        default: 'Click me'
      }
    })

    expect(wrapper.text()).toBe('Click me')
    expect(wrapper.find('button').exists()).toBe(true)
  })

  it('emits click event when clicked', async () => {
    const wrapper = mountComponent(BaseButton)
    
    await wrapper.find('button').trigger('click')
    
    expect(wrapper.emitted('click')).toHaveLength(1)
  })

  it('applies correct variant class', () => {
    const wrapper = mountComponent(BaseButton, {
      props: {
        variant: 'primary'
      }
    })

    expect(wrapper.classes()).toContain('btn-primary')
  })

  it('disables button when loading', () => {
    const wrapper = mountComponent(BaseButton, {
      props: {
        loading: true
      }
    })

    expect(wrapper.find('button').attributes('disabled')).toBeDefined()
  })

  it('shows loading spinner when loading', () => {
    const wrapper = mountComponent(BaseButton, {
      props: {
        loading: true
      }
    })

    expect(wrapper.find('.q-spinner').exists()).toBe(true)
  })
})
```

### Store Testing

```javascript
// src/stores/__tests__/authStore.test.js
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '../authStore'
import { mockApiResponse, mockApiError } from '../../../tests/setup/test-utils'

// Mock API service
vi.mock('../../services/authService', () => ({
  login: vi.fn(),
  logout: vi.fn(),
  getCurrentUser: vi.fn()
}))

import authService from '../../services/authService'

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('initializes with correct default state', () => {
    const store = useAuthStore()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.loading).toBe(false)
  })

  it('logs in user successfully', async () => {
    const store = useAuthStore()
    const mockUser = { id: 1, name: 'Test User', email: 'test@example.com' }
    const mockToken = 'mock-token'

    authService.login.mockResolvedValue({
      data: {
        user: mockUser,
        token: mockToken
      }
    })

    await store.login({
      email: 'test@example.com',
      password: 'password'
    })

    expect(store.user).toEqual(mockUser)
    expect(store.token).toBe(mockToken)
    expect(store.isAuthenticated).toBe(true)
    expect(store.loading).toBe(false)
  })

  it('handles login error', async () => {
    const store = useAuthStore()

    authService.login.mockRejectedValue(new Error('Invalid credentials'))

    await expect(store.login({
      email: 'test@example.com',
      password: 'wrong-password'
    })).rejects.toThrow('Invalid credentials')

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('logs out user', async () => {
    const store = useAuthStore()
    
    // Set initial authenticated state
    store.user = { id: 1, name: 'Test User' }
    store.token = 'mock-token'

    authService.logout.mockResolvedValue({})

    await store.logout()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })
})
```

### Composable Testing

```javascript
// src/composables/__tests__/useApi.test.js
import { describe, it, expect, vi } from 'vitest'
import { ref } from 'vue'
import { useApi } from '../useApi'
import { mockApiResponse, mockApiError } from '../../../tests/setup/test-utils'

// Mock axios
vi.mock('axios', () => ({
  default: {
    create: vi.fn(() => ({
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn()
    }))
  }
}))

describe('useApi', () => {
  it('initializes with correct default state', () => {
    const { data, loading, error } = useApi()

    expect(data.value).toBeNull()
    expect(loading.value).toBe(false)
    expect(error.value).toBeNull()
  })

  it('handles successful API call', async () => {
    const { data, loading, error, execute } = useApi()
    const mockData = { id: 1, name: 'Test' }

    const apiCall = vi.fn().mockResolvedValue(mockData)

    await execute(apiCall)

    expect(data.value).toEqual(mockData)
    expect(loading.value).toBe(false)
    expect(error.value).toBeNull()
    expect(apiCall).toHaveBeenCalledOnce()
  })

  it('handles API error', async () => {
    const { data, loading, error, execute } = useApi()
    const errorMessage = 'API Error'

    const apiCall = vi.fn().mockRejectedValue(new Error(errorMessage))

    await execute(apiCall)

    expect(data.value).toBeNull()
    expect(loading.value).toBe(false)
    expect(error.value).toBe(errorMessage)
  })

  it('sets loading state during API call', async () => {
    const { loading, execute } = useApi()
    let loadingDuringCall = false

    const apiCall = vi.fn().mockImplementation(() => {
      loadingDuringCall = loading.value
      return Promise.resolve({})
    })

    await execute(apiCall)

    expect(loadingDuringCall).toBe(true)
    expect(loading.value).toBe(false)
  })
})
```

## Integration Testing

### Page Component Testing

```javascript
// src/pages/__tests__/ProductList.test.js
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mountComponent, createTestRouter, createMockProduct } from '../../../tests/setup/test-utils'
import ProductList from '../ProductList.vue'
import { useProductStore } from '../../stores/productStore'

vi.mock('../../stores/productStore')

describe('ProductList Page', () => {
  let mockProductStore
  let router

  beforeEach(() => {
    mockProductStore = {
      products: [],
      loading: false,
      fetchProducts: vi.fn(),
      searchProducts: vi.fn()
    }
    
    useProductStore.mockReturnValue(mockProductStore)
    
    router = createTestRouter([
      { path: '/products', component: ProductList }
    ])
  })

  it('renders product list correctly', async () => {
    const mockProducts = [
      createMockProduct({ id: 1, name: 'Product 1' }),
      createMockProduct({ id: 2, name: 'Product 2' })
    ]
    
    mockProductStore.products = mockProducts

    const wrapper = mountComponent(ProductList, {
      global: {
        plugins: [router]
      }
    })

    expect(wrapper.findAll('[data-testid="product-card"]')).toHaveLength(2)
    expect(wrapper.text()).toContain('Product 1')
    expect(wrapper.text()).toContain('Product 2')
  })

  it('shows loading state', () => {
    mockProductStore.loading = true

    const wrapper = mountComponent(ProductList, {
      global: {
        plugins: [router]
      }
    })

    expect(wrapper.find('[data-testid="loading-spinner"]').exists()).toBe(true)
  })

  it('fetches products on mount', () => {
    mountComponent(ProductList, {
      global: {
        plugins: [router]
      }
    })

    expect(mockProductStore.fetchProducts).toHaveBeenCalledOnce()
  })

  it('searches products when search input changes', async () => {
    const wrapper = mountComponent(ProductList, {
      global: {
        plugins: [router]
      }
    })

    const searchInput = wrapper.find('[data-testid="search-input"]')
    await searchInput.setValue('aspirin')
    await searchInput.trigger('input')

    expect(mockProductStore.searchProducts).toHaveBeenCalledWith('aspirin')
  })

  it('navigates to product detail when product is clicked', async () => {
    const mockProducts = [createMockProduct({ id: 1 })]
    mockProductStore.products = mockProducts

    const wrapper = mountComponent(ProductList, {
      global: {
        plugins: [router]
      }
    })

    await router.push('/products')
    await wrapper.find('[data-testid="product-card"]').trigger('click')

    expect(router.currentRoute.value.path).toBe('/products/1')
  })
})
```

### Router Integration Testing

```javascript
// tests/integration/router-integration.test.js
import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import App from '../../src/App.vue'
import routes from '../../src/router/routes'

describe('Router Integration', () => {
  let router

  beforeEach(() => {
    router = createRouter({
      history: createWebHistory(),
      routes
    })
  })

  it('navigates to dashboard by default', async () => {
    router.push('/')
    await router.isReady()

    const wrapper = mount(App, {
      global: {
        plugins: [router]
      }
    })

    expect(router.currentRoute.value.path).toBe('/')
  })

  it('navigates to products page', async () => {
    router.push('/products')
    await router.isReady()

    expect(router.currentRoute.value.path).toBe('/products')
    expect(router.currentRoute.value.name).toBe('products')
  })

  it('redirects to login when not authenticated', async () => {
    // Mock unauthenticated state
    router.push('/dashboard')
    await router.isReady()

    // Should redirect to login
    expect(router.currentRoute.value.path).toBe('/auth/login')
  })

  it('handles 404 routes', async () => {
    router.push('/non-existent-route')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('404')
  })
})
```

## End-to-End Testing

### Cypress Configuration

```javascript
// cypress.config.js
import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost:3000',
    supportFile: 'tests/e2e/cypress/support/e2e.js',
    specPattern: 'tests/e2e/specs/**/*.cy.{js,jsx,ts,tsx}',
    fixturesFolder: 'tests/e2e/cypress/fixtures',
    screenshotsFolder: 'tests/e2e/cypress/screenshots',
    videosFolder: 'tests/e2e/cypress/videos',
    viewportWidth: 1280,
    viewportHeight: 720,
    video: true,
    screenshot: true,
    chromeWebSecurity: false,
    env: {
      apiUrl: 'http://localhost:8000/api'
    }
  },
  component: {
    devServer: {
      framework: 'vue',
      bundler: 'vite'
    },
    supportFile: 'tests/e2e/cypress/support/component.js',
    specPattern: 'src/**/*.cy.{js,jsx,ts,tsx}'
  }
})
```

### E2E Test Examples

```javascript
// tests/e2e/specs/auth.cy.js
describe('Authentication Flow', () => {
  beforeEach(() => {
    cy.visit('/auth/login')
  })

  it('logs in successfully with valid credentials', () => {
    cy.get('[data-testid="email-input"]').type('admin@example.com')
    cy.get('[data-testid="password-input"]').type('password123')
    cy.get('[data-testid="login-button"]').click()

    cy.url().should('include', '/dashboard')
    cy.get('[data-testid="user-menu"]').should('be.visible')
    cy.get('[data-testid="welcome-message"]').should('contain', 'Welcome')
  })

  it('shows error with invalid credentials', () => {
    cy.get('[data-testid="email-input"]').type('admin@example.com')
    cy.get('[data-testid="password-input"]').type('wrongpassword')
    cy.get('[data-testid="login-button"]').click()

    cy.get('[data-testid="error-message"]')
      .should('be.visible')
      .and('contain', 'Invalid credentials')
  })

  it('validates required fields', () => {
    cy.get('[data-testid="login-button"]').click()

    cy.get('[data-testid="email-error"]')
      .should('be.visible')
      .and('contain', 'Email is required')
    
    cy.get('[data-testid="password-error"]')
      .should('be.visible')
      .and('contain', 'Password is required')
  })

  it('logs out successfully', () => {
    // Login first
    cy.login('admin@example.com', 'password123')
    
    cy.get('[data-testid="user-menu"]').click()
    cy.get('[data-testid="logout-button"]').click()

    cy.url().should('include', '/auth/login')
    cy.get('[data-testid="login-form"]').should('be.visible')
  })
})
```

```javascript
// tests/e2e/specs/products.cy.js
describe('Product Management', () => {
  beforeEach(() => {
    cy.login('admin@example.com', 'password123')
    cy.visit('/products')
  })

  it('displays product list', () => {
    cy.get('[data-testid="product-list"]').should('be.visible')
    cy.get('[data-testid="product-card"]').should('have.length.at.least', 1)
  })

  it('searches products', () => {
    cy.get('[data-testid="search-input"]').type('aspirin')
    cy.get('[data-testid="search-button"]').click()

    cy.get('[data-testid="product-card"]')
      .should('contain.text', 'aspirin')
  })

  it('creates new product', () => {
    cy.get('[data-testid="add-product-button"]').click()
    
    cy.get('[data-testid="product-name-input"]').type('New Product')
    cy.get('[data-testid="product-sku-input"]').type('NEW-001')
    cy.get('[data-testid="product-price-input"]').type('100.00')
    cy.get('[data-testid="product-category-select"]').select('Medicine')
    
    cy.get('[data-testid="save-product-button"]').click()

    cy.get('[data-testid="success-message"]')
      .should('be.visible')
      .and('contain', 'Product created successfully')
    
    cy.get('[data-testid="product-list"]')
      .should('contain', 'New Product')
  })

  it('edits existing product', () => {
    cy.get('[data-testid="product-card"]').first().click()
    cy.get('[data-testid="edit-product-button"]').click()
    
    cy.get('[data-testid="product-name-input"]')
      .clear()
      .type('Updated Product Name')
    
    cy.get('[data-testid="save-product-button"]').click()

    cy.get('[data-testid="success-message"]')
      .should('contain', 'Product updated successfully')
  })

  it('deletes product', () => {
    cy.get('[data-testid="product-card"]').first().click()
    cy.get('[data-testid="delete-product-button"]').click()
    
    cy.get('[data-testid="confirm-delete-button"]').click()

    cy.get('[data-testid="success-message"]')
      .should('contain', 'Product deleted successfully')
  })
})
```

### Custom Cypress Commands

```javascript
// tests/e2e/cypress/support/commands.js
Cypress.Commands.add('login', (email, password) => {
  cy.session([email, password], () => {
    cy.visit('/auth/login')
    cy.get('[data-testid="email-input"]').type(email)
    cy.get('[data-testid="password-input"]').type(password)
    cy.get('[data-testid="login-button"]').click()
    cy.url().should('include', '/dashboard')
  })
})

Cypress.Commands.add('createProduct', (productData) => {
  cy.request({
    method: 'POST',
    url: `${Cypress.env('apiUrl')}/products`,
    body: productData,
    headers: {
      'Authorization': `Bearer ${window.localStorage.getItem('auth_token')}`
    }
  })
})

Cypress.Commands.add('seedDatabase', () => {
  cy.request({
    method: 'POST',
    url: `${Cypress.env('apiUrl')}/test/seed`,
    headers: {
      'Authorization': `Bearer ${window.localStorage.getItem('auth_token')}`
    }
  })
})

Cypress.Commands.add('clearDatabase', () => {
  cy.request({
    method: 'DELETE',
    url: `${Cypress.env('apiUrl')}/test/clear`,
    headers: {
      'Authorization': `Bearer ${window.localStorage.getItem('auth_token')}`
    }
  })
})
```

## Performance Testing

### Component Performance

```javascript
// tests/performance/component-performance.test.js
import { describe, it, expect } from 'vitest'
import { mountComponent, createMockProduct } from '../setup/test-utils'
import ProductList from '../../src/components/ProductList.vue'

describe('Component Performance', () => {
  it('renders large product list efficiently', async () => {
    const products = Array.from({ length: 1000 }, (_, i) => 
      createMockProduct({ id: i + 1, name: `Product ${i + 1}` })
    )

    const startTime = performance.now()
    
    const wrapper = mountComponent(ProductList, {
      props: { products }
    })

    const endTime = performance.now()
    const renderTime = endTime - startTime

    expect(renderTime).toBeLessThan(100) // Should render within 100ms
    expect(wrapper.findAll('[data-testid="product-item"]')).toHaveLength(1000)
  })

  it('handles frequent prop updates efficiently', async () => {
    const wrapper = mountComponent(ProductList, {
      props: { products: [] }
    })

    const startTime = performance.now()

    // Simulate frequent updates
    for (let i = 0; i < 100; i++) {
      await wrapper.setProps({
        products: [createMockProduct({ id: i })]
      })
    }

    const endTime = performance.now()
    const updateTime = endTime - startTime

    expect(updateTime).toBeLessThan(500) // Should handle updates within 500ms
  })
})
```

## Accessibility Testing

### A11y Testing

```javascript
// tests/accessibility/a11y.test.js
import { describe, it, expect } from 'vitest'
import { mountComponent } from '../setup/test-utils'
import { axe, toHaveNoViolations } from 'jest-axe'
import ProductCard from '../../src/components/ProductCard.vue'

expect.extend(toHaveNoViolations)

describe('Accessibility', () => {
  it('ProductCard has no accessibility violations', async () => {
    const wrapper = mountComponent(ProductCard, {
      props: {
        product: {
          id: 1,
          name: 'Test Product',
          price: 100,
          description: 'Test description'
        }
      }
    })

    const results = await axe(wrapper.element)
    expect(results).toHaveNoViolations()
  })

  it('supports keyboard navigation', async () => {
    const wrapper = mountComponent(ProductCard, {
      props: {
        product: { id: 1, name: 'Test Product', price: 100 }
      }
    })

    const card = wrapper.find('[data-testid="product-card"]')
    
    // Should be focusable
    expect(card.attributes('tabindex')).toBe('0')
    
    // Should handle Enter key
    await card.trigger('keydown.enter')
    expect(wrapper.emitted('click')).toHaveLength(1)
    
    // Should handle Space key
    await card.trigger('keydown.space')
    expect(wrapper.emitted('click')).toHaveLength(2)
  })

  it('has proper ARIA labels', () => {
    const wrapper = mountComponent(ProductCard, {
      props: {
        product: { id: 1, name: 'Test Product', price: 100 }
      }
    })

    expect(wrapper.find('[data-testid="product-card"]').attributes('aria-label'))
      .toBe('Product: Test Product, Price: $100')
  })
})
```

## Mock Service Worker (MSW)

### API Mocking Setup

```javascript
// tests/mocks/handlers.js
import { rest } from 'msw'

export const handlers = [
  // Auth endpoints
  rest.post('/api/auth/login', (req, res, ctx) => {
    const { email, password } = req.body
    
    if (email === 'admin@example.com' && password === 'password123') {
      return res(
        ctx.status(200),
        ctx.json({
          user: {
            id: 1,
            name: 'Admin User',
            email: 'admin@example.com',
            role: 'admin'
          },
          token: 'mock-jwt-token'
        })
      )
    }
    
    return res(
      ctx.status(401),
      ctx.json({ message: 'Invalid credentials' })
    )
  }),

  // Products endpoints
  rest.get('/api/products', (req, res, ctx) => {
    const page = req.url.searchParams.get('page') || 1
    const perPage = req.url.searchParams.get('per_page') || 10
    
    const products = Array.from({ length: perPage }, (_, i) => ({
      id: i + 1,
      name: `Product ${i + 1}`,
      sku: `PROD-${String(i + 1).padStart(3, '0')}`,
      price: (i + 1) * 10,
      category: 'Medicine'
    }))
    
    return res(
      ctx.status(200),
      ctx.json({
        data: products,
        meta: {
          current_page: page,
          total: 100,
          per_page: perPage
        }
      })
    )
  }),

  rest.post('/api/products', (req, res, ctx) => {
    const product = req.body
    
    return res(
      ctx.status(201),
      ctx.json({
        id: Date.now(),
        ...product,
        created_at: new Date().toISOString()
      })
    )
  }),

  // Error simulation
  rest.get('/api/products/error', (req, res, ctx) => {
    return res(
      ctx.status(500),
      ctx.json({ message: 'Internal server error' })
    )
  })
]
```

```javascript
// tests/mocks/server.js
import { setupServer } from 'msw/node'
import { handlers } from './handlers'

export const server = setupServer(...handlers)
```

## Running Tests

### Package.json Scripts

```json
{
  "scripts": {
    "test": "vitest",
    "test:ui": "vitest --ui",
    "test:run": "vitest run",
    "test:coverage": "vitest run --coverage",
    "test:watch": "vitest --watch",
    "test:e2e": "cypress run",
    "test:e2e:open": "cypress open",
    "test:component": "cypress run --component",
    "test:all": "npm run test:run && npm run test:e2e"
  }
}
```

### CI/CD Integration

```yaml
# .github/workflows/frontend-tests.yml
name: Frontend Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  unit-tests:
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
      working-directory: frontend/web
      run: npm ci
    
    - name: Run unit tests
      working-directory: frontend/web
      run: npm run test:coverage
    
    - name: Upload coverage
      uses: codecov/codecov-action@v3
      with:
        file: ./frontend/web/coverage/lcov.info
        flags: frontend
  
  e2e-tests:
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
      working-directory: frontend/web
      run: npm ci
    
    - name: Build application
      working-directory: frontend/web
      run: npm run build
    
    - name: Start application
      working-directory: frontend/web
      run: npm run preview &
    
    - name: Wait for application
      run: npx wait-on http://localhost:3000
    
    - name: Run E2E tests
      working-directory: frontend/web
      run: npm run test:e2e
    
    - name: Upload E2E artifacts
      uses: actions/upload-artifact@v3
      if: failure()
      with:
        name: cypress-screenshots
        path: frontend/web/tests/e2e/cypress/screenshots
```

## Best Practices

### Testing Guidelines

1. **Test Behavior, Not Implementation**
   - Focus on what the component does, not how it does it
   - Test user interactions and expected outcomes

2. **Use Data Test IDs**
   - Add `data-testid` attributes for reliable element selection
   - Avoid using CSS classes or IDs for testing

3. **Mock External Dependencies**
   - Mock API calls, external services, and complex dependencies
   - Use MSW for realistic API mocking

4. **Test Accessibility**
   - Include accessibility tests in your test suite
   - Test keyboard navigation and screen reader compatibility

5. **Keep Tests Independent**
   - Each test should be able to run in isolation
   - Clean up after each test

### Performance Considerations

1. **Parallel Testing**
   - Run tests in parallel when possible
   - Use appropriate test isolation

2. **Selective Testing**
   - Use test filters for faster feedback
   - Run relevant tests based on changed files

3. **Efficient Mocking**
   - Mock heavy dependencies
   - Use lightweight test doubles

## Conclusion

Testing yang komprehensif adalah fondasi untuk aplikasi frontend yang reliable dan maintainable. Dengan mengikuti panduan ini, tim dapat membangun confidence dalam kualitas kode dan mengurangi bug di production.

Untuk informasi lebih lanjut, lihat dokumentasi Vitest, Vue Test Utils, dan Cypress.
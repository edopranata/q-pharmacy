const routes = [
  // Landing page with PublicLayout
  {
    path: '/',
    component: () => import('layouts/PublicLayout.vue'),
    children: [
      {
        path: '',
        name: 'landing',
        component: () => import('pages/IndexPage.vue'),
        meta: {
          title: 'Q-Pharmacy - Sistem Manajemen Apotek',
          requiresAuth: false
        }
      }
    ]
  },

  // Authentication routes
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
          path: 'login',
          name: 'auth.login',
          component: () => import('pages/auth/LoginPage.vue'),
          meta: {
            requiresGuest: true,
            title: 'Login - Q-Pharmacy',
            breadcrumb: [{ label: 'Login' }]
          }
        },
        {
          path: 'register',
          name: 'auth.register',
          component: () => import('pages/auth/RegisterPage.vue'),
          meta: {
            requiresGuest: true,
            title: 'Register - Q-Pharmacy',
            breadcrumb: [{ label: 'Register' }]
          }
        },
      {
          path: 'forgot-password',
          name: 'auth.forgot-password',
          component: () => import('pages/auth/ForgotPasswordPage.vue'),
          meta: {
            requiresGuest: true,
            title: 'Lupa Password - Q-Pharmacy',
            breadcrumb: [{ label: 'Lupa Password' }]
          }
        }
    ]
  },

  // Application routes
  {
    path: '/app',
    component: () => import('layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/app/dashboard'
      },
      {
        path: 'dashboard',
        name: 'app.dashboard',
        component: () => import('pages/core/DashboardPage.vue'),
        meta: {
          requiresAuth: true,
          title: 'Dashboard - Q-Pharmacy',
          breadcrumb: [{ label: 'Dashboard' }]
        }
      },
      {
        path: 'profile',
        name: 'app.profile',
        component: () => import('pages/core/ProfilePage.vue'),
        meta: {
          requiresAuth: true,
          title: 'Profile - Q-Pharmacy',
          breadcrumb: [{ label: 'Profile' }]
        }
      },

      // Master Data Management
      {
        path: 'master',
        children: [
          {
            path: 'categories',
            name: 'app.master.categories.index',
            component: () => import('pages/master/CategoriesPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.master.categories.index',
              title: 'Kategori - Q-Pharmacy',
              breadcrumb: [
                { label: 'Master Data' },
                { label: 'Kategori' }
              ]
            }
          },
          {
            path: 'suppliers',
            name: 'app.master.suppliers.index',
            component: () => import('pages/master/SuppliersPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.master.suppliers.index',
              title: 'Supplier - Q-Pharmacy',
              breadcrumb: [
                { label: 'Master Data' },
                { label: 'Supplier' }
              ]
            }
          },
          {
            path: 'units',
            name: 'app.master.units.index',
            component: () => import('pages/master/UnitsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.master.units.index',
              title: 'Satuan - Q-Pharmacy',
              breadcrumb: [
                { label: 'Master Data' },
                { label: 'Satuan' }
              ]
            }
          },
          {
            path: 'units/:id/edit',
            name: 'app.master.units.edit',
            component: () => import('pages/master/UnitsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.master.units.edit',
              title: 'Edit Satuan - Q-Pharmacy',
              breadcrumb: [
                { label: 'Master Data' },
                { label: 'Satuan', to: { name: 'app.master.units.index' } },
                { label: 'Edit' }
              ]
            }
          }
        ]
      },

      // Product Management
      {
        path: 'products',
        children: [
          {
            path: '',
            name: 'app.products.index',
            component: () => import('pages/inventory/ProductsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.products.index',
              title: 'Produk - Q-Pharmacy',
              breadcrumb: [{ label: 'Produk' }]
            }
          },
          {
            path: 'create',
            name: 'app.products.create',
            component: () => import('pages/inventory/ProductsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.products.create',
              title: 'Tambah Produk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Produk', to: { name: 'app.products.index' } },
                { label: 'Tambah' }
              ]
            }
          },
          {
            path: ':id/edit',
            name: 'app.products.edit',
            component: () => import('pages/inventory/ProductsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.products.edit',
              title: 'Edit Produk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Produk', to: { name: 'app.products.index' } },
                { label: 'Edit' }
              ]
            }
          },
          {
            path: ':id',
            name: 'app.products.show',
            component: () => import('pages/inventory/ProductsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.products.show',
              title: 'Detail Produk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Produk', to: { name: 'app.products.index' } },
                { label: 'Detail' }
              ]
            }
          },
          {
            path: 'pricing',
            name: 'app.products.pricing.index',
            component: () => import('pages/inventory/ProductsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.products.pricing.index',
              title: 'Harga Produk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Produk' },
                { label: 'Harga Produk' }
              ]
            }
          }
        ]
      },

      // Inventory Management
      {
        path: 'inventories',
        children: [
          {
            path: '',
            name: 'app.inventories.index',
            component: () => import('pages/inventory/InventoryPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.inventories.index',
              title: 'Inventory - Q-Pharmacy',
              breadcrumb: [{ label: 'Inventory' }]
            }
          },
          {
            path: 'stock-in',
            name: 'app.inventories.stock-in.index',
            component: () => import('pages/inventory/StockInPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.inventories.stock-in.index',
              title: 'Stock Masuk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Inventory' },
                { label: 'Stock Masuk' }
              ]
            }
          },
          {
            path: 'stock-in/create',
            name: 'app.inventories.stock-in.create',
            component: () => import('pages/inventory/StockInPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.inventories.stock-in.create',
              title: 'Tambah Stock Masuk - Q-Pharmacy',
              breadcrumb: [
                { label: 'Inventory' },
                { label: 'Stock Masuk', to: { name: 'app.inventories.stock-in.index' } },
                { label: 'Tambah' }
              ]
            }
          },
          {
            path: 'stock-out',
            name: 'app.inventories.stock-out.index',
            component: () => import('pages/inventory/StockOutPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.inventories.stock-out.index',
              title: 'Stock Keluar - Q-Pharmacy',
              breadcrumb: [
                { label: 'Inventory' },
                { label: 'Stock Keluar' }
              ]
            }
          },
          {
            path: 'adjustments',
            name: 'app.inventories.adjustments.index',
            component: () => import('pages/inventory/AdjustmentsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.inventories.adjustments.index',
              title: 'Penyesuaian Stock - Q-Pharmacy',
              breadcrumb: [
                { label: 'Inventory' },
                { label: 'Penyesuaian Stock' }
              ]
            }
          }
        ]
      },

      // Sales Management
      {
        path: 'sells',
        children: [
          {
            path: '',
            name: 'app.sells.index',
            component: () => import('pages/management/SalesPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.sells.index',
              title: 'Penjualan - Q-Pharmacy',
              breadcrumb: [{ label: 'Penjualan' }]
            }
          },
          {
            path: 'pos',
            name: 'app.sells.pos',
            component: () => import('pages/pos/POSPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.sells.pos',
              title: 'Point of Sale - Q-Pharmacy',
              breadcrumb: [
                { label: 'Penjualan' },
                { label: 'POS' }
              ]
            }
          },
          {
            path: 'transactions',
            name: 'app.sells.transactions.index',
            component: () => import('pages/pos/TransactionsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.sells.transactions.index',
              title: 'Transaksi - Q-Pharmacy',
              breadcrumb: [
                { label: 'Penjualan' },
                { label: 'Transaksi' }
              ]
            }
          },
          {
            path: 'transactions/:id',
            name: 'app.sells.transactions.show',
            component: () => import('pages/pos/TransactionsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.sells.transactions.show',
              title: 'Detail Transaksi - Q-Pharmacy',
              breadcrumb: [
                { label: 'Penjualan' },
                { label: 'Transaksi', to: { name: 'app.sells.transactions.index' } },
                { label: 'Detail' }
              ]
            }
          }
        ]
      },

      // Reports & Analytics
      {
        path: 'reports',
        children: [
          {
            path: '',
            name: 'app.reports.index',
            component: () => import('pages/core/DashboardPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.reports.index',
              title: 'Laporan - Q-Pharmacy',
              breadcrumb: [{ label: 'Laporan' }]
            }
          },
          {
            path: 'sales',
            name: 'app.reports.sales',
            component: () => import('pages/reports/ReportsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.reports.sales',
              title: 'Laporan Penjualan - Q-Pharmacy',
              breadcrumb: [
                { label: 'Laporan' },
                { label: 'Penjualan' }
              ]
            }
          },
          {
            path: 'inventory',
            name: 'app.reports.inventory',
            component: () => import('pages/reports/ReportsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.reports.inventory',
              title: 'Laporan Inventory - Q-Pharmacy',
              breadcrumb: [
                { label: 'Laporan' },
                { label: 'Inventory' }
              ]
            }
          },
          {
            path: 'financial',
            name: 'app.reports.financial',
            component: () => import('pages/reports/ReportsPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.reports.financial',
              title: 'Laporan Keuangan - Q-Pharmacy',
              breadcrumb: [
                { label: 'Laporan' },
                { label: 'Keuangan' }
              ]
            }
          }
        ]
      },

      // User Management
      {
        path: 'management',
        children: [
          {
            path: 'users',
            name: 'app.management.users.index',
            component: () => import('pages/management/UserManagementPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.management.users.index',
              title: 'Manajemen User - Q-Pharmacy',
              breadcrumb: [{ label: 'Manajemen User' }]
            }
          },
          {
            path: 'roles',
            name: 'app.management.roles.index',
            component: () => import('pages/management/RoleManagementPage.vue'),
            meta: {
              requiresAuth: true,
              permission: 'app.management.roles.index',
              title: 'Role & Permission - Q-Pharmacy',
              breadcrumb: [
                { label: 'Manajemen User' },
                { label: 'Role & Permission' }
              ]
            }
          }
        ]
      }
    ]
  },

  // Legacy redirects
  {
    path: '/login',
    redirect: '/auth/login'
  },
  {
    path: '/register',
    redirect: '/auth/register'
  },
  {
    path: '/dashboard',
    redirect: '/app/dashboard'
  },

  // Error pages
  {
    path: '/error',
    component: () => import('layouts/AppLayout.vue'),
    children: [
      {
        path: 'unauthorized',
        name: 'unauthorized',
        component: () => import('pages/ErrorNotFound.vue'),
        meta: {
          title: 'Unauthorized - Q-Pharmacy',
          breadcrumb: [{ label: 'Unauthorized' }]
        }
      },
      {
        path: 'forbidden',
        name: 'forbidden',
        component: () => import('pages/ErrorNotFound.vue'),
        meta: {
          title: 'Forbidden - Q-Pharmacy',
          breadcrumb: [{ label: 'Forbidden' }]
        }
      }
    ]
  },
  {
    path: '/unauthorized',
    redirect: '/error/unauthorized'
  },
  {
    path: '/forbidden',
    redirect: '/error/forbidden'
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('layouts/AppLayout.vue'),
    children: [
      {
        path: '',
        name: 'not-found',
        component: () => import('pages/ErrorNotFound.vue'),
        meta: {
          title: 'Page Not Found - Q-Pharmacy',
          breadcrumb: [{ label: '404 - Not Found' }]
        }
      }
    ]
  },
]

export default routes

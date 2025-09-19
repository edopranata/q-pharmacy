/**
 * API Response Types
 */

// Base API Response Structure
export const ApiResponse = {
  success: Boolean,
  message: String,
  data: Object,
  errors: Object,
  meta: Object
}

// Pagination Meta
export const PaginationMeta = {
  current_page: Number,
  from: Number,
  last_page: Number,
  per_page: Number,
  to: Number,
  total: Number
}

// User Types
export const User = {
  id: Number,
  name: String,
  email: String,
  email_verified_at: String, // nullable timestamp
  last_login: String, // nullable timestamp
  avatar: String, // nullable
  last_activity: String, // nullable timestamp
  phone: String, // nullable
  bio: String, // nullable text
  location: String, // nullable
  roles: Array, // relationship data
  permissions: Array, // relationship data
  is_active: Boolean, // computed or added by backend
  created_at: String,
  updated_at: String
}

export const UserRequest = {
  name: String,
  email: String,
  password: String,
  password_confirmation: String,
  roles: Array,
  phone: String, // nullable
  bio: String, // nullable
  location: String, // nullable
  is_active: Boolean // if supported by backend
}

export const UserUpdateRequest = {
  name: String,
  email: String,
  roles: Array,
  phone: String, // nullable
  bio: String, // nullable
  location: String, // nullable
  is_active: Boolean // if supported by backend
}

export const UserStats = {
  total_users: Number,
  active_users: Number,
  inactive_users: Number,
  verified_users: Number,
  unverified_users: Number
}

export const Role = {
  id: Number,
  name: String,
  display_name: String,
  description: String,
  permissions: Array,
  created_at: String,
  updated_at: String
}

export const Permission = {
  id: Number,
  name: String,
  display_name: String,
  description: String,
  created_at: String,
  updated_at: String
}

// Auth Types
export const LoginRequest = {
  email: String,
  password: String,
  remember: Boolean
}

export const LoginResponse = {
  user: User,
  token: String
}

export const RegisterRequest = {
  name: String,
  email: String,
  password: String,
  password_confirmation: String
}

// Category Types
export const Category = {
  id: Number,
  name: String,
  description: String,
  is_active: Boolean,
  created_at: String,
  updated_at: String
}

export const CategoryRequest = {
  name: String,
  description: String,
  is_active: Boolean
}

// Product Types
export const Product = {
  id: Number,
  name: String,
  description: String,
  sku: String,
  barcode: String,
  category_id: Number,
  category: Category,
  unit_id: Number,
  unit: Object,
  purchase_price: Number,
  selling_price: Number,
  stock: Number,
  min_stock: Number,
  is_active: Boolean,
  created_at: String,
  updated_at: String
}

export const ProductRequest = {
  name: String,
  description: String,
  sku: String,
  barcode: String,
  category_id: Number,
  unit_id: Number,
  purchase_price: Number,
  selling_price: Number,
  stock: Number,
  min_stock: Number,
  is_active: Boolean
}

// Supplier Types
export const Supplier = {
  id: Number,
  name: String,
  contact_person: String,
  phone: String,
  email: String,
  address: String,
  is_active: Boolean,
  created_at: String,
  updated_at: String
}

export const SupplierRequest = {
  name: String,
  contact_person: String,
  phone: String,
  email: String,
  address: String,
  is_active: Boolean
}

// Unit Types
export const Unit = {
  id: Number,
  name: String,
  symbol: String,
  is_active: Boolean,
  created_at: String,
  updated_at: String
}

export const UnitRequest = {
  name: String,
  symbol: String,
  is_active: Boolean
}

// Transaction Types
export const Transaction = {
  id: Number,
  transaction_number: String,
  customer_name: String,
  total_amount: Number,
  paid_amount: Number,
  change_amount: Number,
  payment_method: String,
  status: String,
  items: Array,
  created_at: String,
  updated_at: String
}

export const TransactionItem = {
  id: Number,
  product_id: Number,
  product: Product,
  quantity: Number,
  price: Number,
  subtotal: Number
}

export const TransactionRequest = {
  customer_name: String,
  payment_method: String,
  paid_amount: Number,
  items: Array // Array of { product_id, quantity, price }
}

// Error Types
export const ApiError = {
  message: String,
  errors: Object,
  status: Number
}

// HTTP Methods
export const HttpMethods = {
  GET: 'GET',
  POST: 'POST',
  PUT: 'PUT',
  PATCH: 'PATCH',
  DELETE: 'DELETE'
}

// API Endpoints
export const ApiEndpoints = {
  // Auth endpoints
  LOGIN: '/auth/login',
  REGISTER: '/auth/register',
  LOGOUT: '/app/auth/logout',
  PROFILE: '/app/auth/profile',
  USER: '/app/auth/user',
  CHANGE_PASSWORD: '/app/auth/password',
  
  // User management endpoints
  USERS: '/app/management/users',
  USER_STATS: '/app/management/users/stats',
  USER_ASSIGN_ROLES: '/app/management/users/{id}/roles',
  USER_PERMISSIONS: '/app/management/users/{id}/permissions',
  
  // Role management endpoints
  ROLES: '/app/management/roles',
  ROLE_OPTIONS: '/options/roles',
  PERMISSIONS: '/app/management/permissions',
  PERMISSION_OPTIONS: '/options/permissions',
  
  // Master data endpoints
  CATEGORIES: '/app/master/categories',
  CATEGORY: (id) => `/app/master/categories/${id}`,
  CATEGORY_OPTIONS: '/app/options/categories',
  
  PRODUCTS: '/app/products',
  PRODUCT: (id) => `/app/products/${id}`,
  PRODUCT_OPTIONS: '/app/options/products',
  
  SUPPLIERS: '/app/master/suppliers',
  SUPPLIER: (id) => `/app/master/suppliers/${id}`,
  SUPPLIER_OPTIONS: '/app/options/suppliers',
  
  UNITS: '/app/master/units',
  UNIT: (id) => `/app/master/units/${id}`,
  UNIT_OPTIONS: '/app/options/units',
  
  // Transaction endpoints
  TRANSACTIONS: '/app/sells',
  TRANSACTION: (id) => `/app/sells/${id}`,
  
  // Dashboard endpoints
  DASHBOARD_STATS: '/app/stats/dashboard'
}
/**
 * Services Index
 * Central export point for all API services
 */

// Import all services
import apiService from './api'
import authService from './authService'
import categoryService from './categoryService'
import productService from './productService'
import supplierService from './supplierService'
import unitService from './unitService'
import transactionService from './transactionService'
import dashboardService from './dashboardService'
import roleService from './roleService'
import userService from './userService'

// Export individual services
export {
  apiService,
  authService,
  categoryService,
  productService,
  supplierService,
  unitService,
  transactionService,
  dashboardService,
  roleService,
  userService
}

// Export services as a grouped object
export const services = {
  api: apiService,
  auth: authService,
  category: categoryService,
  product: productService,
  supplier: supplierService,
  unit: unitService,
  transaction: transactionService,
  dashboard: dashboardService,
  role: roleService,
  user: userService
}

// Default export for convenience
export default services

/**
 * Service Usage Examples:
 * 
 * // Import individual services
 * import { authService, productService } from 'src/services'
 * 
 * // Import all services as object
 * import services from 'src/services'
 * const user = await services.auth.login(credentials)
 * 
 * // Import specific service
 * import authService from 'src/services/authService'
 * const user = await authService.login(credentials)
 */
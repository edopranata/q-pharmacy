import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Product Service
 * Handles all product-related API calls
 */
class ProductService {
  /**
   * Get all products with optional pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Products list response
   */
  async getProducts(params = {}) {
    return await apiService.get(ApiEndpoints.PRODUCTS, params)
  }

  /**
   * Get product by ID
   * @param {number} id - Product ID
   * @returns {Promise<Object>} Product data
   */
  async getProduct(id) {
    return await apiService.get(ApiEndpoints.PRODUCT(id))
  }

  /**
   * Create new product
   * @param {Object} productData - Product data
   * @returns {Promise<Object>} Created product response
   */
  async createProduct(productData) {
    return await apiService.post(ApiEndpoints.PRODUCTS, productData)
  }

  /**
   * Update existing product
   * @param {number} id - Product ID
   * @param {Object} productData - Updated product data
   * @returns {Promise<Object>} Updated product response
   */
  async updateProduct(id, productData) {
    return await apiService.put(ApiEndpoints.PRODUCT(id), productData)
  }

  /**
   * Delete product
   * @param {number} id - Product ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteProduct(id) {
    return await apiService.delete(ApiEndpoints.PRODUCT(id))
  }

  /**
   * Toggle product active status
   * @param {number} id - Product ID
   * @returns {Promise<Object>} Toggle response
   */
  async toggleProductStatus(id) {
    return await apiService.patch(`${ApiEndpoints.PRODUCT(id)}/toggle-status`)
  }

  /**
   * Get products by category
   * @param {number} categoryId - Category ID
   * @returns {Promise<Object>} Products response
   */
  async getProductsByCategory(categoryId) {
    return await apiService.get(ApiEndpoints.PRODUCTS, { category_id: categoryId })
  }

  /**
   * Search products by barcode
   * @param {string} barcode - Product barcode
   * @returns {Promise<Object>} Product response
   */
  async searchByBarcode(barcode) {
    return await apiService.get(`${ApiEndpoints.PRODUCTS}/search-barcode`, { barcode })
  }

  /**
   * Update product stock
   * @param {number} id - Product ID
   * @param {Object} stockData - Stock update data
   * @returns {Promise<Object>} Stock update response
   */
  async updateStock(id, stockData) {
    return await apiService.patch(`${ApiEndpoints.PRODUCT(id)}/stock`, stockData)
  }

  /**
   * Get low stock products
   * @returns {Promise<Object>} Low stock products response
   */
  async getLowStockProducts() {
    return await apiService.get(`${ApiEndpoints.PRODUCTS}/low-stock`)
  }

  /**
   * Bulk delete products
   * @param {Array<number>} ids - Array of product IDs
   * @returns {Promise<Object>} Bulk delete response
   */
  async bulkDeleteProducts(ids) {
    return await apiService.post(`${ApiEndpoints.PRODUCTS}/bulk-delete`, { ids })
  }

  /**
   * Export products
   * @param {string} format - Export format
   * @param {Object} filters - Export filters
   * @returns {Promise<Blob>} Export file
   */
  async exportProducts(format = 'excel', filters = {}) {
    return await apiService.download(`${ApiEndpoints.PRODUCTS}/export`, `products.${format}`, {
      params: { format, ...filters }
    })
  }

  /**
   * Import products from file
   * @param {File} file - Import file
   * @param {Function} onProgress - Upload progress callback
   * @returns {Promise<Object>} Import response
   */
  async importProducts(file, onProgress = null) {
    const formData = new FormData()
    formData.append('file', file)
    
    return await apiService.upload(`${ApiEndpoints.PRODUCTS}/import`, formData, onProgress)
  }
}

// Create and export singleton instance
const productService = new ProductService()
export default productService

// Export class for testing purposes
export { ProductService }
import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Category Service
 * Handles all category-related API calls
 */
class CategoryService {
  /**
   * Get all categories with optional pagination and filters
   * @param {Object} params - Query parameters
   * @param {number} params.page - Page number
   * @param {number} params.per_page - Items per page
   * @param {string} params.search - Search term
   * @param {string} params.sort_by - Sort field
   * @param {string} params.sort_order - Sort order (asc/desc)
   * @param {boolean} params.is_active - Filter by active status
   * @returns {Promise<Object>} Categories list response
   */
  async getCategories(params = {}) {
    return await apiService.get(ApiEndpoints.CATEGORIES, params)
  }

  /**
   * Get category by ID
   * @param {number} id - Category ID
   * @returns {Promise<Object>} Category data
   */
  async getCategory(id) {
    return await apiService.get(ApiEndpoints.CATEGORY(id))
  }

  /**
   * Create new category
   * @param {Object} categoryData - Category data
   * @param {string} categoryData.name - Category name
   * @param {string} categoryData.description - Category description
   * @param {boolean} categoryData.is_active - Category active status
   * @returns {Promise<Object>} Created category response
   */
  async createCategory(categoryData) {
    return await apiService.post(ApiEndpoints.CATEGORIES, categoryData)
  }

  /**
   * Update existing category
   * @param {number} id - Category ID
   * @param {Object} categoryData - Updated category data
   * @returns {Promise<Object>} Updated category response
   */
  async updateCategory(id, categoryData) {
    return await apiService.put(ApiEndpoints.CATEGORY(id), categoryData)
  }

  /**
   * Delete category
   * @param {number} id - Category ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteCategory(id) {
    return await apiService.delete(ApiEndpoints.CATEGORY(id))
  }

  /**
   * Toggle category active status
   * @param {number} id - Category ID
   * @returns {Promise<Object>} Toggle response
   */
  async toggleCategoryStatus(id) {
    return await apiService.patch(`${ApiEndpoints.CATEGORY(id)}/toggle-status`)
  }

  /**
   * Get active categories only (for dropdowns)
   * @returns {Promise<Object>} Active categories response
   */
  async getActiveCategories() {
    return await apiService.get(ApiEndpoints.CATEGORIES, { is_active: true })
  }

  /**
   * Bulk delete categories
   * @param {Array<number>} ids - Array of category IDs
   * @returns {Promise<Object>} Bulk delete response
   */
  async bulkDeleteCategories(ids) {
    return await apiService.post(`${ApiEndpoints.CATEGORIES}/bulk-delete`, { ids })
  }

  /**
   * Export categories to Excel/CSV
   * @param {string} format - Export format (excel/csv)
   * @param {Object} filters - Export filters
   * @returns {Promise<Blob>} Export file
   */
  async exportCategories(format = 'excel', filters = {}) {
    return await apiService.download(`${ApiEndpoints.CATEGORIES}/export`, `categories.${format}`, {
      params: { format, ...filters }
    })
  }

  /**
   * Import categories from file
   * @param {File} file - Import file
   * @param {Function} onProgress - Upload progress callback
   * @returns {Promise<Object>} Import response
   */
  async importCategories(file, onProgress = null) {
    const formData = new FormData()
    formData.append('file', file)
    
    return await apiService.upload(`${ApiEndpoints.CATEGORIES}/import`, formData, onProgress)
  }
}

// Create and export singleton instance
const categoryService = new CategoryService()
export default categoryService

// Export class for testing purposes
export { CategoryService }
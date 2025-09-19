import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Supplier Service
 * Handles all supplier-related API calls
 */
class SupplierService {
  /**
   * Get all suppliers with optional pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Suppliers list response
   */
  async getSuppliers(params = {}) {
    return await apiService.get(ApiEndpoints.SUPPLIERS, params)
  }

  /**
   * Get supplier by ID
   * @param {number} id - Supplier ID
   * @returns {Promise<Object>} Supplier data
   */
  async getSupplier(id) {
    return await apiService.get(ApiEndpoints.SUPPLIER(id))
  }

  /**
   * Create new supplier
   * @param {Object} supplierData - Supplier data
   * @returns {Promise<Object>} Created supplier response
   */
  async createSupplier(supplierData) {
    return await apiService.post(ApiEndpoints.SUPPLIERS, supplierData)
  }

  /**
   * Update existing supplier
   * @param {number} id - Supplier ID
   * @param {Object} supplierData - Updated supplier data
   * @returns {Promise<Object>} Updated supplier response
   */
  async updateSupplier(id, supplierData) {
    return await apiService.put(ApiEndpoints.SUPPLIER(id), supplierData)
  }

  /**
   * Delete supplier
   * @param {number} id - Supplier ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteSupplier(id) {
    return await apiService.delete(ApiEndpoints.SUPPLIER(id))
  }

  /**
   * Toggle supplier active status
   * @param {number} id - Supplier ID
   * @returns {Promise<Object>} Toggle response
   */
  async toggleSupplierStatus(id) {
    return await apiService.patch(`${ApiEndpoints.SUPPLIER(id)}/toggle-status`)
  }

  /**
   * Get active suppliers only (for dropdowns)
   * @returns {Promise<Object>} Active suppliers response
   */
  async getActiveSuppliers() {
    return await apiService.get(ApiEndpoints.SUPPLIERS, { is_active: true })
  }

  /**
   * Bulk delete suppliers
   * @param {Array<number>} ids - Array of supplier IDs
   * @returns {Promise<Object>} Bulk delete response
   */
  async bulkDeleteSuppliers(ids) {
    return await apiService.post(`${ApiEndpoints.SUPPLIERS}/bulk-delete`, { ids })
  }

  /**
   * Export suppliers
   * @param {string} format - Export format
   * @param {Object} filters - Export filters
   * @returns {Promise<Blob>} Export file
   */
  async exportSuppliers(format = 'excel', filters = {}) {
    return await apiService.download(`${ApiEndpoints.SUPPLIERS}/export`, `suppliers.${format}`, {
      params: { format, ...filters }
    })
  }

  /**
   * Import suppliers from file
   * @param {File} file - Import file
   * @param {Function} onProgress - Upload progress callback
   * @returns {Promise<Object>} Import response
   */
  async importSuppliers(file, onProgress = null) {
    const formData = new FormData()
    formData.append('file', file)
    
    return await apiService.upload(`${ApiEndpoints.SUPPLIERS}/import`, formData, onProgress)
  }
}

// Create and export singleton instance
const supplierService = new SupplierService()
export default supplierService

// Export class for testing purposes
export { SupplierService }
import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Unit Service
 * Handles all unit-related API calls
 */
class UnitService {
  /**
   * Get all units with optional pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Units list response
   */
  async getUnits(params = {}) {
    return await apiService.get(ApiEndpoints.UNITS, params)
  }

  /**
   * Get unit by ID
   * @param {number} id - Unit ID
   * @returns {Promise<Object>} Unit data
   */
  async getUnit(id) {
    return await apiService.get(ApiEndpoints.UNIT(id))
  }

  /**
   * Create new unit
   * @param {Object} unitData - Unit data
   * @returns {Promise<Object>} Created unit response
   */
  async createUnit(unitData) {
    return await apiService.post(ApiEndpoints.UNITS, unitData)
  }

  /**
   * Update existing unit
   * @param {number} id - Unit ID
   * @param {Object} unitData - Updated unit data
   * @returns {Promise<Object>} Updated unit response
   */
  async updateUnit(id, unitData) {
    return await apiService.put(ApiEndpoints.UNIT(id), unitData)
  }

  /**
   * Delete unit
   * @param {number} id - Unit ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteUnit(id) {
    return await apiService.delete(ApiEndpoints.UNIT(id))
  }

  /**
   * Toggle unit active status
   * @param {number} id - Unit ID
   * @returns {Promise<Object>} Toggle response
   */
  async toggleUnitStatus(id) {
    return await apiService.patch(`${ApiEndpoints.UNIT(id)}/toggle-status`)
  }

  /**
   * Get active units only (for dropdowns)
   * @returns {Promise<Object>} Active units response
   */
  async getActiveUnits() {
    return await apiService.get(ApiEndpoints.UNITS, { is_active: true })
  }

  /**
   * Bulk delete units
   * @param {Array<number>} ids - Array of unit IDs
   * @returns {Promise<Object>} Bulk delete response
   */
  async bulkDeleteUnits(ids) {
    return await apiService.post(`${ApiEndpoints.UNITS}/bulk-delete`, { ids })
  }

  /**
   * Export units
   * @param {string} format - Export format
   * @param {Object} filters - Export filters
   * @returns {Promise<Blob>} Export file
   */
  async exportUnits(format = 'excel', filters = {}) {
    return await apiService.download(`${ApiEndpoints.UNITS}/export`, `units.${format}`, {
      params: { format, ...filters }
    })
  }

  /**
   * Import units from file
   * @param {File} file - Import file
   * @param {Function} onProgress - Upload progress callback
   * @returns {Promise<Object>} Import response
   */
  async importUnits(file, onProgress = null) {
    const formData = new FormData()
    formData.append('file', file)
    
    return await apiService.upload(`${ApiEndpoints.UNITS}/import`, formData, onProgress)
  }
}

// Create and export singleton instance
const unitService = new UnitService()
export default unitService

// Export class for testing purposes
export { UnitService }
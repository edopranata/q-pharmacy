import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * User Service
 * Handles all user-related API operations
 */
class UserService {
  /**
   * Get all users with optional pagination and filters
   * @param {Object} params - Query parameters
   * @param {number} params.page - Page number
   * @param {number} params.per_page - Items per page
   * @param {string} params.search - Search term
   * @param {string} params.sort_by - Sort field
   * @param {string} params.sort_order - Sort order (asc/desc)
   * @param {string} params.role - Filter by role
   * @param {string} params.status - Filter by status
   * @returns {Promise<Object>} Users list response
   */
  async getUsers(params = {}) {
    return await apiService.get(ApiEndpoints.USERS, params)
  }

  /**
   * Get user by ID
   * @param {number} id - User ID
   * @returns {Promise<Object>} User details response
   */
  async getUser(id) {
    return await apiService.get(`${ApiEndpoints.USERS}/${id}`)
  }

  /**
   * Create new user
   * @param {Object} userData - User data
   * @param {string} userData.name - User full name
   * @param {string} userData.email - User email
   * @param {string} userData.phone - User phone number
   * @param {string} userData.password - User password
   * @param {string} userData.password_confirmation - Password confirmation
   * @param {Array<string>} userData.roles - Array of role names
   * @returns {Promise<Object>} Created user response
   */
  async createUser(userData) {
    return await apiService.post(ApiEndpoints.USERS, userData)
  }

  /**
   * Update existing user
   * @param {number} id - User ID
   * @param {Object} userData - Updated user data
   * @param {string} userData.name - User full name
   * @param {string} userData.email - User email
   * @param {string} userData.phone - User phone number
   * @param {Array<string>} userData.roles - Array of role names
   * @returns {Promise<Object>} Updated user response
   */
  async updateUser(id, userData) {
    return await apiService.put(`${ApiEndpoints.USERS}/${id}`, userData)
  }

  /**
   * Delete user
   * @param {number} id - User ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteUser(id) {
    return await apiService.delete(`${ApiEndpoints.USERS}/${id}`)
  }

  /**
   * Restore deleted user
   * @param {number} id - User ID
   * @returns {Promise<Object>} Restore response
   */
  async restoreUser(id) {
    return await apiService.post(`${ApiEndpoints.USERS}/${id}/restore`)
  }

  /**
   * Assign roles to user
   * @param {number} userId - User ID
   * @param {Array} roles - Array of role IDs
   * @returns {Promise} API response
   */
  async assignRoles(userId, roles) {
    const endpoint = ApiEndpoints.USER_ASSIGN_ROLES.replace('{id}', userId)
    return await apiService.post(endpoint, { roles })
  }

  /**
   * Remove roles from user
   * @param {number} userId - User ID
   * @param {Array} roles - Array of role IDs
   * @returns {Promise} API response
   */
  async removeRoles(userId, roles) {
    const endpoint = ApiEndpoints.USER_ASSIGN_ROLES.replace('{id}', userId)
    return await apiService.delete(endpoint, { data: { roles } })
  }

  /**
   * Get user roles
   * @param {number} userId - User ID
   * @returns {Promise} API response
   */
  async getUserRoles(userId) {
    const endpoint = ApiEndpoints.USER_ASSIGN_ROLES.replace('{id}', userId)
    return await apiService.get(endpoint)
  }

  /**
   * Get user permissions
   * @param {number} userId - User ID
   * @returns {Promise} API response
   */
  async getUserPermissions(userId) {
    const endpoint = ApiEndpoints.USER_PERMISSIONS.replace('{id}', userId)
    return await apiService.get(endpoint)
  }

  /**
   * Reset user password
   * @param {number} userId - User ID
   * @returns {Promise} API response
   */
  async resetPassword(userId) {
    return await apiService.post(`${ApiEndpoints.USERS}/${userId}/reset-password`)
  }

  /**
   * Toggle user status
   * @param {number} userId - User ID
   * @returns {Promise} API response
   */
  async toggleStatus(userId) {
    return await apiService.patch(`${ApiEndpoints.USERS}/${userId}/status`)
  }

  /**
   * Bulk delete users
   * @param {Array} ids - Array of user IDs
   * @returns {Promise} API response
   */
  async bulkDeleteUsers(ids) {
    return await apiService.delete(`${ApiEndpoints.USERS}/bulk`, { data: { ids } })
  }

  /**
   * Get user statistics
   * @returns {Promise} API response
   */
  async getUserStats() {
    return await apiService.get(ApiEndpoints.USER_STATS)
  }

  /**
   * Export users data
   * @param {string} format - Export format (excel, csv, pdf)
   * @param {Object} filters - Export filters
   * @returns {Promise} API response
   */
  async exportUsers(format = 'excel', filters = {}) {
    return await apiService.get(`${ApiEndpoints.USERS}/export`, {
      params: { format, ...filters },
      responseType: 'blob'
    })
  }

  /**
   * Import users from file
   * @param {File} file - File to import
   * @returns {Promise} API response
   */
  async importUsers(file) {
    const formData = new FormData()
    formData.append('file', file)
    
    return await apiService.post(`${ApiEndpoints.USERS}/import`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }

  /**
   * Get available roles for assignment
   * @param {Object} params - Query parameters
   * @param {string} params.search - Search term
   * @param {number} params.limit - Limit number of results
   * @returns {Promise} API response
   */
  async getAvailableRoles(params = {}) {
    return await apiService.get(ApiEndpoints.ROLE_OPTIONS, params)
  }

  /**
   * Get available permissions
   * @returns {Promise} API response
   */
  async getAvailablePermissions() {
    return await apiService.get(ApiEndpoints.PERMISSION_OPTIONS)
  }
}

// Create and export singleton instance
const userService = new UserService()
export default userService

// Export class for testing purposes
export { UserService }
import apiService from './api'

/**
 * Role Service
 * Handles all role and permission-related API calls
 */
class RoleService {
  /**
   * Get all roles with optional pagination and filters
   * @param {Object} params - Query parameters
   * @param {number} params.page - Page number
   * @param {number} params.per_page - Items per page
   * @param {string} params.search - Search term
   * @param {string} params.sort_by - Sort field
   * @param {string} params.sort_order - Sort order (asc/desc)
   * @returns {Promise<Object>} Roles list response
   */
  async getRoles(params = {}) {
    return await apiService.get('/app/management/roles', params)
  }

  /**
   * Get role by ID
   * @param {number} id - Role ID
   * @returns {Promise<Object>} Role details response
   */
  async getRole(id) {
    return await apiService.get(`/app/management/roles/${id}`)
  }

  /**
   * Create new role
   * @param {Object} roleData - Role data
   * @param {string} roleData.name - Role name
   * @param {string} roleData.description - Role description
   * @param {Array<string>} roleData.permissions - Array of permission names
   * @returns {Promise<Object>} Created role response
   */
  async createRole(roleData) {
    return await apiService.post('/app/management/roles', roleData)
  }

  /**
   * Update existing role
   * @param {number} id - Role ID
   * @param {Object} roleData - Updated role data
   * @param {string} roleData.name - Role name
   * @param {string} roleData.description - Role description
   * @param {Array<string>} roleData.permissions - Array of permission names
   * @returns {Promise<Object>} Updated role response
   */
  async updateRole(id, roleData) {
    return await apiService.put(`/app/management/roles/${id}`, roleData)
  }

  /**
   * Delete role
   * @param {number} id - Role ID
   * @returns {Promise<Object>} Delete response
   */
  async deleteRole(id) {
    return await apiService.delete(`/app/management/roles/${id}`)
  }

  /**
   * Get all permissions
   * @returns {Promise<Object>} Permissions list response
   */
  async getPermissions() {
    return await apiService.get('/app/management/roles/available/permissions')
  }

  /**
   * Get permissions by role ID
   * @param {number} roleId - Role ID
   * @returns {Promise<Object>} Role permissions response
   */
  async getRolePermissions(roleId) {
    return await apiService.get(`/app/management/roles/${roleId}/permissions`)
  }

  /**
   * Assign permissions to role
   * @param {number} roleId - Role ID
   * @param {Array<string>} permissions - Array of permission names
   * @returns {Promise<Object>} Assignment response
   */
  async assignPermissions(roleId, permissions) {
    return await apiService.post(`/app/management/roles/${roleId}/permissions`, {
      permissions: permissions
    })
  }

  /**
   * Get users assigned to a role
   * @param {number} roleId - Role ID
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Role users response
   */
  async getRoleUsers(roleId, params = {}) {
    return await apiService.get(`/app/management/roles/${roleId}/users`, params)
  }

  /**
   * Get role statistics
   * @returns {Promise<Object>} Role statistics response
   */
  async getRoleStats() {
    return await apiService.get('/app/management/roles/stats')
  }

  /**
   * Sync role permissions (replace all permissions)
   * @param {number} roleId - Role ID
   * @param {Array<number>} permissionIds - Array of permission IDs
   * @returns {Promise<Object>} Sync response
   */
  async syncRolePermissions(roleId, permissionIds) {
    return await apiService.put(`/roles/${roleId}/permissions/sync`, {
      permissions: permissionIds
    })
  }

  /**
   * Check if role has specific permission
   * @param {number} roleId - Role ID
   * @param {string} permission - Permission name
   * @returns {Promise<Object>} Permission check response
   */
  async hasPermission(roleId, permission) {
    return await apiService.get(`/roles/${roleId}/has-permission/${permission}`)
  }

  /**
   * Get available permissions for assignment
   * @param {number} roleId - Role ID (optional, to exclude already assigned)
   * @returns {Promise<Object>} Available permissions response
   */
  async getAvailablePermissions(roleId = null) {
    const params = roleId ? { exclude_role: roleId } : {}
    return await apiService.get('/permissions/available', params)
  }
}

// Create and export singleton instance
const roleService = new RoleService()
export default roleService

// Export class for testing purposes
export { RoleService }
import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Authentication Service
 * Handles all authentication-related API calls
 */
class AuthService {
  /**
   * Login user with credentials
   * @param {Object} credentials - Login credentials
   * @param {string} credentials.email - User email
   * @param {string} credentials.password - User password
   * @param {boolean} credentials.remember - Remember user
   * @returns {Promise<Object>} Login response with user and token
   */
  async login(credentials) {
    return await apiService.post(ApiEndpoints.LOGIN, credentials)
  }

  /**
   * Register new user
   * @param {Object} userData - Registration data
   * @param {string} userData.name - User name
   * @param {string} userData.email - User email
   * @param {string} userData.password - User password
   * @param {string} userData.password_confirmation - Password confirmation
   * @returns {Promise<Object>} Registration response
   */
  async register(userData) {
    return await apiService.post(ApiEndpoints.REGISTER, userData)
  }

  /**
   * Logout current user
   * @returns {Promise<Object>} Logout response
   */
  async logout() {
    return await apiService.post(ApiEndpoints.LOGOUT)
  }

  /**
   * Get user profile
   * @returns {Promise<Object>} User profile response
   */
  async getProfile() {
    return await apiService.get(ApiEndpoints.USER)
  }

  /**
   * Update user profile
   * @param {Object} profileData - Profile update data
   * @param {string} profileData.name - User name
   * @param {string} profileData.email - User email
   * @returns {Promise<Object>} Updated profile response
   */
  async updateProfile(profileData) {
    return await apiService.put(ApiEndpoints.PROFILE, profileData)
  }

  /**
   * Change user password
   * @param {Object} passwordData - Password change data
   * @param {string} passwordData.current_password - Current password
   * @param {string} passwordData.password - New password
   * @param {string} passwordData.password_confirmation - Password confirmation
   * @returns {Promise<Object>} Password change response
   */
  async changePassword(passwordData) {
    return await apiService.put(ApiEndpoints.CHANGE_PASSWORD, passwordData)
  }

  /**
   * Refresh authentication token
   * @returns {Promise<Object>} Refresh token response
   */
  async refreshToken() {
    return await apiService.post('/refresh-token')
  }

  /**
   * Verify email address
   * @param {string} token - Email verification token
   * @returns {Promise<Object>} Email verification response
   */
  async verifyEmail(token) {
    return await apiService.post(`/email/verify/${token}`)
  }

  /**
   * Request password reset
   * @param {string} email - User email
   * @returns {Promise<Object>} Password reset request response
   */
  async requestPasswordReset(email) {
    return await apiService.post('/password/reset-request', { email })
  }

  /**
   * Reset password with token
   * @param {Object} resetData - Password reset data
   * @param {string} resetData.token - Reset token
   * @param {string} resetData.email - User email
   * @param {string} resetData.password - New password
   * @param {string} resetData.password_confirmation - Password confirmation
   * @returns {Promise<Object>} Password reset response
   */
  async resetPassword(resetData) {
    return await apiService.post('/password/reset', resetData)
  }

  /**
   * Get user permissions
   * @returns {Promise<Object>} User permissions response
   */
  async getUserPermissions() {
    return await apiService.get('/user/permissions')
  }

  /**
   * Get user roles
   * @returns {Promise<Object>} User roles response
   */
  async getUserRoles() {
    return await apiService.get('/user/roles')
  }

  /**
   * Upload user avatar
   * @param {File} file - Avatar image file
   * @returns {Promise<Object>} Avatar upload response
   */
  async uploadAvatar(file) {
    const formData = new FormData()
    formData.append('avatar', file)
    
    return await apiService.post('/app/auth/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }

  /**
   * Delete user avatar
   * @returns {Promise<Object>} Avatar delete response
   */
  async deleteAvatar() {
    return await apiService.delete('/app/auth/avatar')
  }
}

// Create and export singleton instance
const authService = new AuthService()
export default authService

// Export class for testing purposes
export { AuthService }
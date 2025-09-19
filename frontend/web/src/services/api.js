import axios from 'axios'
import { Notify, LocalStorage } from 'quasar'

/**
 * Base API Service Class
 * Handles all HTTP requests with consistent error handling and configuration
 */
class ApiService {
  constructor() {
    this.router = null
    this.client = axios.create({
      baseURL: process.env.VITE_API_URL || process.env.VUE_APP_API_URL || 'http://localhost:8000/api',
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      timeout: parseInt(process.env.VITE_API_TIMEOUT) || 30000 // 30 seconds timeout
    })

    this.setupInterceptors()
  }

  /**
   * Setup request and response interceptors
   */
  setupInterceptors() {
    // Request interceptor - Add auth token
    this.client.interceptors.request.use(
      (config) => {
        const token = this.getAuthToken()
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        
        // Log request in development
        if (process.env.NODE_ENV === 'development') {
          console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`, {
            data: config.data,
            params: config.params
          })
        }
        
        return config
      },
      (error) => {
        console.error('❌ Request Error:', error)
        return Promise.reject(this.handleError(error))
      }
    )

    // Response interceptor - Handle responses and errors
    this.client.interceptors.response.use(
      (response) => {
        // Log response in development
        if (process.env.NODE_ENV === 'development') {
          console.log(`✅ API Response: ${response.config.method?.toUpperCase()} ${response.config.url}`, response.data)
        }
        
        return response
      },
      (error) => {
        // Only log non-authentication errors to avoid console spam
        if (error.response?.status !== 401) {
          console.error('❌ Response Error:', error)
        }
        
        // Handle 401 Unauthorized - Auto logout
        if (error.response?.status === 401) {
          this.handleUnauthorized()
        }
        
        // Handle 403 Forbidden
        if (error.response?.status === 403) {
          this.handleForbidden()
        }
        
        // Handle 500 Server Error
        if (error.response?.status >= 500) {
          this.handleServerError()
        }
        
        return Promise.reject(this.handleError(error))
      }
    )
  }

  /**
   * Get authentication token from localStorage
   */
  getAuthToken() {
    return LocalStorage.getItem('auth_token')
  }

  /**
   * Set authentication token
   */
  setAuthToken(token) {
    if (token) {
      LocalStorage.set('auth_token', token)
    } else {
      LocalStorage.remove('auth_token')
    }
  }

  /**
   * Set router instance for navigation
   */
  setRouter(router) {
    this.router = router
  }

  /**
   * Handle unauthorized access (401)
   */
  handleUnauthorized() {
    LocalStorage.remove('auth_token')
    LocalStorage.remove('user')
    
    // Only show notification and redirect if user is on an authenticated page
    const currentPath = this.router ? this.router.currentRoute.value.path : window.location.pathname
    const isAuthPage = currentPath.startsWith('/auth/')
    
    if (!isAuthPage) {
      Notify.create({
        type: 'negative',
        message: 'Sesi Anda telah berakhir. Silakan login kembali.',
        position: 'top'
      })
      
      // Redirect to login page using router if available, fallback to window.location
      if (currentPath !== '/auth/login') {
        if (this.router) {
          this.router.replace('/auth/login')
        } else {
          window.location.href = '/auth/login'
        }
      }
    }
  }

  /**
   * Handle forbidden access (403)
   */
  handleForbidden() {
    Notify.create({
      type: 'negative',
      message: 'Anda tidak memiliki akses untuk melakukan tindakan ini.',
      position: 'top'
    })
  }

  /**
   * Handle server errors (5xx)
   */
  handleServerError() {
    Notify.create({
      type: 'negative',
      message: 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
      position: 'top'
    })
  }

  /**
   * Standardize error handling
   */
  handleError(error) {
    const apiError = {
      message: 'Terjadi kesalahan yang tidak diketahui',
      errors: {},
      status: 0
    }

    if (error.response) {
      // Server responded with error status
      apiError.status = error.response.status
      apiError.message = error.response.data?.message || `HTTP Error ${error.response.status}`
      apiError.errors = error.response.data?.errors || {}
    } else if (error.request) {
      // Request was made but no response received
      apiError.message = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.'
      apiError.status = 0
    } else {
      // Something else happened
      apiError.message = error.message || 'Terjadi kesalahan yang tidak diketahui'
    }

    return apiError
  }

  /**
   * Generic GET request
   */
  async get(url, params = {}, config = {}) {
    const response = await this.client.get(url, { params, ...config })
    return response.data
  }

  /**
   * Generic POST request
   */
  async post(url, data = {}, config = {}) {
    const response = await this.client.post(url, data, config)
    return response.data
  }

  /**
   * Generic PUT request
   */
  async put(url, data = {}, config = {}) {
    const response = await this.client.put(url, data, config)
    return response.data
  }

  /**
   * Generic PATCH request
   */
  async patch(url, data = {}, config = {}) {
    const response = await this.client.patch(url, data, config)
    return response.data
  }

  /**
   * Generic DELETE request
   */
  async delete(url, config = {}) {
    const response = await this.client.delete(url, config)
    return response.data
  }

  /**
   * Upload file with progress tracking
   */
  async upload(url, formData, onUploadProgress = null) {
    const config = {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }
    
    if (onUploadProgress) {
      config.onUploadProgress = onUploadProgress
    }
    
    const response = await this.client.post(url, formData, config)
    return response.data
  }

  /**
   * Download file
   */
  async download(url, filename = null) {
    const response = await this.client.get(url, {
      responseType: 'blob'
    })
    
    // Create download link
    const downloadUrl = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = downloadUrl
    link.download = filename || 'download'
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(downloadUrl)
    
    return response.data
  }
}

// Create and export singleton instance
const apiService = new ApiService()
export default apiService

// Export class for testing purposes
export { ApiService }
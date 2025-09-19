import { defineStore } from 'pinia'
import { authService } from 'src/services'
import { Notify, LocalStorage } from 'quasar'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false
  }),

  getters: {
    userPermissions: (state) => {
      // Check if permissions are directly on user object (from backend response)
      if (state.user?.permissions) {
        return state.user.permissions
      }
      // Fallback to permissions in roles
      if (!state.user?.roles) return []
      return state.user.roles.flatMap(role => role.permissions || [])
    },
    hasPermission: (state) => (permission) => {
      // Check direct permissions first
      if (state.user?.permissions?.includes(permission)) {
        return true
      }
      // Fallback to role-based permissions
      return state.user?.roles?.some(role => 
        role.permissions?.some(perm => perm.name === permission)
      ) || false
    }
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const response = await authService.login(credentials)
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        // Store in LocalStorage
        LocalStorage.set('auth_token', token)
        LocalStorage.set('user', user)
        
        Notify.create({
          type: 'positive',
          message: 'Login berhasil!',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Login gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      try {
        const response = await authService.register(userData)
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        // Store in LocalStorage
        LocalStorage.set('auth_token', token)
        LocalStorage.set('user', user)
        
        Notify.create({
          type: 'positive',
          message: 'Registrasi berhasil!',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Registrasi gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        await authService.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
        this.loading = false
        
        Notify.create({
          type: 'info',
          message: 'Logout berhasil',
          position: 'top'
        })
      }
    },

    async fetchProfile() {
      try {
        const response = await authService.getProfile()
        this.user = response.data
        LocalStorage.set('user', this.user)
      } catch (error) {
        // Only log non-authentication errors
        if (error.status !== 401) {
          console.error('Fetch profile error:', error)
        }
        // Clear auth for any error (including 401)
        this.clearAuth()
      }
    },

    async updateProfile(profileData) {
      this.loading = true
      try {
        const response = await authService.updateProfile(profileData)
        this.user = response.data
        LocalStorage.set('user', this.user)
        
        Notify.create({
          type: 'positive',
          message: 'Profile berhasil diupdate',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.response?.data?.message || 'Update profile gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async changePassword(passwordData) {
      this.loading = true
      try {
        await authService.changePassword(passwordData)
        
        Notify.create({
          type: 'positive',
          message: 'Password berhasil diubah',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Ubah password gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    initializeAuth() {
      const token = LocalStorage.getItem('auth_token')
      const user = LocalStorage.getItem('user')
      
      if (token && user) {
        this.token = token
        this.user = user
        this.isAuthenticated = true
        
        // Fetch fresh profile data
        this.fetchProfile()
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      
      LocalStorage.remove('auth_token')
      LocalStorage.remove('user')
    },

    async uploadAvatar(file) {
      this.loading = true
      try {
        const response = await authService.uploadAvatar(file)
        this.user = response.data.user
        LocalStorage.set('user', this.user)
        
        Notify.create({
          type: 'positive',
          message: 'Avatar berhasil diupload',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Upload avatar gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    async deleteAvatar() {
      this.loading = true
      try {
        const response = await authService.deleteAvatar()
        this.user = response.data.user
        LocalStorage.set('user', this.user)
        
        Notify.create({
          type: 'positive',
          message: 'Avatar berhasil dihapus',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || 'Hapus avatar gagal'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    }
  }
})
import { defineStore } from 'pinia'
import { userService } from 'src/services'
import { Notify } from 'quasar'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    user: null,
    userStats: {
      total_users: 0,
      active_users: 0,
      admin_users: 0,
      cashier_users: 0,
      total_user: 0,
      active_user: 0,
      today_login: 0,
      roles_count: 0
    },
    availableRoles: [],
    availablePermissions: [],
    loading: false,
    pagination: {
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      sortBy: 'created_at',
      descending: false
    },
    filters: {
      search: '',
      role: null,
      status: null,
      verified: null
    }
  }),

  getters: {
    activeUsers: (state) => state.users.filter(user => user.is_active),
    usersForSelect: (state) => state.users.map(user => ({
      label: `${user.name} (${user.email})`,
      value: user.id
    })),
    verifiedUsers: (state) => state.users.filter(user => user.email_verified_at),
    unverifiedUsers: (state) => state.users.filter(user => !user.email_verified_at)
  },

  actions: {
    async fetchUsers(params = {}) {
      this.loading = true
      try {
        const queryParams = {
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          sort_by: this.pagination.sortBy,
          sort_order: this.pagination.descending ? 'desc' : 'asc',
          ...this.filters,
          ...params
        }

        const response = await userService.getUsers(queryParams)
        this.users = (response.data || []).map(user => ({
          ...user,
          status: user.email_verified_at ? 'Active' : 'Inactive'
        }))
        
        if (response.meta) {
          this.pagination = {
            ...this.pagination,
            page: response.meta.current_page,
            rowsNumber: response.meta.total,
            rowsPerPage: response.meta.per_page
          }
        }

        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengambil data pengguna'
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

    async fetchUser(id) {
      this.loading = true
      try {
        const response = await userService.getUser(id)
        this.user = {
          ...response.data.data,
          status: response.data.data.email_verified_at ? 'Active' : 'Inactive'
        }
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengambil data pengguna'
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

    async createUser(userData) {
      this.loading = true
      try {
        const response = await userService.createUser(userData)
        
        // Validate response structure - apiService returns response.data directly
        // So response = { success, message, data }
        if (response && response.success === true && response.data) {
          const newUser = {
            ...response.data,
            status: response.data.email_verified_at ? 'Active' : 'Inactive'
          }
          
          // Add to local state - ensure this.users is an array
          if (Array.isArray(this.users)) {
            this.users.unshift(newUser)
          } else {
            console.warn('this.users is not an array, initializing as empty array')
            this.users = [newUser]
          }
        } else {
          console.error('Invalid response structure:', response)
          throw new Error('Invalid response structure from server')
        }
        
        Notify.create({
          type: 'positive',
          message: 'Pengguna berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat pengguna'
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

    async updateUser(id, userData) {
      this.loading = true
      try {
        const response = await userService.updateUser(id, userData)
        
        // Update local state
        const updatedUser = {
          ...response.data,
          status: response.data.email_verified_at ? 'Active' : 'Inactive'
        }
        
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index] = updatedUser
        }
        
        if (this.user && this.user.id === id) {
          this.user = updatedUser
        }
        
        Notify.create({
          type: 'positive',
          message: 'Pengguna berhasil diperbarui',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal memperbarui pengguna'
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

    async deleteUser(id) {
      this.loading = true
      try {
        await userService.deleteUser(id)
        
        // Remove from local state
        this.users = this.users.filter(user => user.id !== id)
        
        if (this.user && this.user.id === id) {
          this.user = null
        }
        
        Notify.create({
          type: 'positive',
          message: 'Pengguna berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus pengguna'
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

    async toggleUserStatus(id) {
      try {
        const response = await userService.toggleStatus(id)
        
        // Update local state based on response
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index].status = response.data.status
          this.users[index].email_verified_at = response.data.user.email_verified_at
        }
        
        if (this.user && this.user.id === id) {
          this.user.status = response.data.status
          this.user.email_verified_at = response.data.user.email_verified_at
        }
        
        Notify.create({
          type: 'positive',
          message: `User status updated to ${response.data.status}`,
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengubah status pengguna'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async assignRoles(userId, roles) {
      try {
        await userService.assignRoles(userId, roles)
        
        // Update local state
        const index = this.users.findIndex(user => user.id === userId)
        if (index !== -1) {
          // Refresh user data to get updated roles
          await this.fetchUser(userId)
        }
        
        Notify.create({
          type: 'positive',
          message: 'Role berhasil ditetapkan',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menetapkan role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchUserStats() {
      try {
        const response = await userService.getUserStats()
        this.userStats = response.data || {}
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengambil statistik pengguna'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchAvailableRoles(params = {}) {
      try {
        const response = await userService.getAvailableRoles(params)

        const roles = response.data || []
        
        // Format roles for dropdown usage
        const formattedRoles = roles.map(role => ({
          value: role.value,
          label: role.label,
          name: role.name
        }))
        
        // If no search params, update the store state
        if (!params.search) {
          this.availableRoles = formattedRoles
        }
        
        return { success: true, data: formattedRoles }
      } catch (error) {
        const message = error.message || 'Gagal mengambil daftar role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchAvailablePermissions() {
      try {
        const response = await userService.getAvailablePermissions()
        this.availablePermissions = response.data.data || []
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengambil daftar permission'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async resetPassword(userId) {
      try {
        const response = await userService.resetPassword(userId)
        
        Notify.create({
          type: 'positive',
          message: 'Password reset email sent successfully',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.response?.data?.message || error.message || 'Failed to reset password'
        
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        
        throw error
      }
    },

    // Local state management
    setPagination(pagination) {
      this.pagination = { ...this.pagination, ...pagination }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearUser() {
      this.user = null
    },

    clearUsers() {
      this.users = []
    },

    // Update user in local state (for real-time updates)
    updateUserInState(userId, updates) {
      const index = this.users.findIndex(user => user.id === userId)
      if (index !== -1) {
        this.users[index] = { ...this.users[index], ...updates }
      }
    }
  }
})
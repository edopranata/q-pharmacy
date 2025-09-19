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
      inactive_users: 0,
      verified_users: 0,
      unverified_users: 0
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
        this.users = response.data.data || []
        
        if (response.data.meta) {
          this.pagination = {
            ...this.pagination,
            page: response.data.meta.current_page,
            rowsNumber: response.data.meta.total,
            rowsPerPage: response.data.meta.per_page
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
        this.user = response.data.data
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
        
        // Add to local state
        this.users.unshift(response.data.data)
        
        Notify.create({
          type: 'positive',
          message: 'Pengguna berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
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
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index] = response.data.data
        }
        
        if (this.user && this.user.id === id) {
          this.user = response.data.data
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

    async toggleUserStatus(id, status) {
      try {
        await userService.toggleStatus(id, status)
        
        // Update local state
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index].is_active = status
        }
        
        if (this.user && this.user.id === id) {
          this.user.is_active = status
        }
        
        Notify.create({
          type: 'positive',
          message: `Pengguna berhasil ${status ? 'diaktifkan' : 'dinonaktifkan'}`,
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
        this.userStats = response.data.data || {}
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

    async fetchAvailableRoles() {
      try {
        const response = await userService.getAvailableRoles()
        this.availableRoles = response.data.data || []
        return { success: true }
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
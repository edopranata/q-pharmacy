import { defineStore } from 'pinia'
import { roleService } from 'src/services'
import { Notify } from 'quasar'

export const useRoleStore = defineStore('role', {
  state: () => ({
    roles: [],
    role: null,
    stats: {
      total_roles: 0,
      active_roles: 0,
      total_permissions: 0,
      total_assigned_users: 0
    },
    availablePermissions: [],
    loading: false,
    saving: false,
    deleting: false,
    statsLoading: false,
    statsError: null,
    pagination: {
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      sortBy: 'created_at',
      descending: false
    },
    filters: {
      search: '',
      status: null,
      permissions_count: null
    }
  }),

  getters: {
    activeRoles: (state) => state.roles.filter(role => role.status === 'active'),
    rolesForSelect: (state) => state.roles.map(role => ({
      label: role.name,
      value: role.id,
      name: role.name
    })),
    rolesByPermissionCount: (state) => (count) => {
      return state.roles.filter(role => {
        const permCount = role.permissions_count || 0
        if (count === '0') return permCount === 0
        if (count === '1-5') return permCount >= 1 && permCount <= 5
        if (count === '6-10') return permCount >= 6 && permCount <= 10
        if (count === '10+') return permCount > 10
        return true
      })
    }
  },

  actions: {
    async fetchRoles(params = {}) {
      this.loading = true
      try {
        // Clean up empty parameters
        const cleanParams = {}
        Object.keys(params).forEach(key => {
          if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
            cleanParams[key] = params[key]
          }
        })

        const response = await roleService.getRoles(cleanParams)
        
        if (response && response.success === true) {
          this.roles = response.data || []
          
          // Update pagination
          if (response.data?.meta) {
            this.pagination = {
              ...this.pagination,
              page: response.data.meta.current_page || 1,
              rowsPerPage: response.data.meta.per_page || 10,
              rowsNumber: response.data.meta.total || 0
            }
          }
        } else {
          this.roles = []
          this.pagination.rowsNumber = 0
        }
        
        return { success: true, data: response.data }
      } catch (error) {
        console.error('Error fetching roles:', error)
        this.roles = []
        this.pagination.rowsNumber = 0
        
        const message = error.message || 'Gagal mengambil data role'
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

    async fetchRole(id) {
      try {
        const response = await roleService.getRole(id)
        
        if (response && response.success === true && response.data) {
          this.role = response.data
          return { success: true, data: response.data }
        } else {
          throw new Error('Invalid response structure')
        }
      } catch (error) {
        const message = error.message || 'Gagal mengambil detail role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async createRole(roleData) {
      this.saving = true
      try {
        const response = await roleService.createRole(roleData)
        
        if (response && response.success === true && response.data) {
          const newRole = response.data
          
          // Add to local state
          if (Array.isArray(this.roles)) {
            this.roles.unshift(newRole)
          } else {
            this.roles = [newRole]
          }
        } else {
          throw new Error('Invalid response structure from server')
        }
        
        Notify.create({
          type: 'positive',
          message: 'Role berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.saving = false
      }
    },

    async updateRole(id, roleData) {
      this.saving = true
      try {
        const response = await roleService.updateRole(id, roleData)
        
        if (response && response.success === true && response.data) {
          const updatedRole = response.data
          
          // Update local state
          const index = this.roles.findIndex(role => role.id === id)
          if (index !== -1) {
            this.roles[index] = updatedRole
          }
          
          // Update single role if it's the current one
          if (this.role && this.role.id === id) {
            this.role = updatedRole
          }
        } else {
          throw new Error('Invalid response structure from server')
        }
        
        Notify.create({
          type: 'positive',
          message: 'Role berhasil diperbarui',
          position: 'top'
        })
        
        return { success: true, data: response.data }
      } catch (error) {
        const message = error.message || 'Gagal memperbarui role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.saving = false
      }
    },

    async deleteRole(id) {
      this.deleting = true
      try {
        const response = await roleService.deleteRole(id)
        
        if (response && response.success === true) {
          // Remove from local state
          this.roles = this.roles.filter(role => role.id !== id)
          
          // Clear single role if it's the deleted one
          if (this.role && this.role.id === id) {
            this.role = null
          }
        } else {
          throw new Error('Invalid response structure from server')
        }
        
        Notify.create({
          type: 'positive',
          message: 'Role berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus role'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.deleting = false
      }
    },

    async fetchRoleStats() {
      this.statsLoading = true
      this.statsError = null
      try {
        const response = await roleService.getRoleStats()
        
        if (response && response.success === true && response.data) {
          this.stats = {
            total_roles: response.data.total_roles || 0,
            active_roles: response.data.active_roles || 0,
            total_permissions: response.data.total_permissions || 0,
            total_assigned_users: response.data.total_assigned_users || 0
          }
          return { success: true, data: response.data }
        } else {
          throw new Error('Invalid response structure')
        }
      } catch (error) {
        this.statsError = error.message || 'Gagal mengambil statistik role'
        Notify.create({
          type: 'negative',
          message: this.statsError,
          position: 'top'
        })
        return { success: false, message: this.statsError }
      } finally {
        this.statsLoading = false
      }
    },

    async fetchAvailablePermissions() {
      try {
        const response = await roleService.getPermissions()
        
        if (response && response.success === true) {
          // Transform nested structure to flat array
          const permissionData = response.data
          
          if (permissionData && permissionData.permissions && Array.isArray(permissionData.permissions)) {
            // Flatten permissions from categories while preserving category info
            const flatPermissions = []
            
            permissionData.permissions.forEach(category => {
              if (category.permissions && Array.isArray(category.permissions)) {
                // Add category information to each permission
                const permissionsWithCategory = category.permissions.map(permission => ({
                  ...permission,
                  category: category.name || category.category || 'Uncategorized'
                }))
                flatPermissions.push(...permissionsWithCategory)
              }
            })
            
            this.availablePermissions = flatPermissions
            
            console.log('Transformed permissions:', {
              categories: permissionData.permissions.length,
              totalPermissions: flatPermissions.length,
              metadata: permissionData.metadata
            })
            
            return { 
              success: true, 
              data: flatPermissions,
              metadata: permissionData.metadata,
              categories: permissionData.permissions
            }
          } else {
            // Fallback for old structure
            this.availablePermissions = response.data || []
            return { success: true, data: response.data }
          }
        } else {
          throw new Error('Invalid response structure')
        }
      } catch (error) {
        console.error('Error fetching permissions:', error)
        const message = error.message || 'Gagal mengambil daftar permissions'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    // Alias for consistency with RoleManagementPage
    async fetchPermissions() {
      return this.fetchAvailablePermissions()
    },

    // Alias for consistency with RoleManagementPage
    async fetchStats() {
      return this.fetchRoleStats()
    },

    async assignPermissions(roleId, permissions) {
      try {
        const response = await roleService.assignPermissions(roleId, permissions)
        
        if (response && response.success === true) {
          // Update local state
          const index = this.roles.findIndex(role => role.id === roleId)
          if (index !== -1) {
            // Refresh role data to get updated permissions
            await this.fetchRole(roleId)
          }
        } else {
          throw new Error('Invalid response structure from server')
        }
        
        Notify.create({
          type: 'positive',
          message: 'Permissions berhasil ditetapkan',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menetapkan permissions'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async getRoleUsers(roleId, params = {}) {
      try {
        const response = await roleService.getRoleUsers(roleId, params)
        
        if (response && response.success === true) {
          return { success: true, data: response.data }
        } else {
          throw new Error('Invalid response structure')
        }
      } catch (error) {
        const message = error.message || 'Gagal mengambil daftar pengguna role'
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

    setFilter(key, value) {
      this.filters[key] = value
    },

    clearRole() {
      this.role = null
    },

    clearRoles() {
      this.roles = []
    },

    // Update role in local state (for real-time updates)
    updateRoleInState(roleId, updates) {
      const index = this.roles.findIndex(role => role.id === roleId)
      if (index !== -1) {
        this.roles[index] = { ...this.roles[index], ...updates }
      }
    }
  }
})
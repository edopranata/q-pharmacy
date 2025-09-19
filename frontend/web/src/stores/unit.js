import { defineStore } from 'pinia'
import { unitService } from 'src/services'
import { Notify } from 'quasar'

export const useUnitStore = defineStore('unit', {
  state: () => ({
    units: [],
    unit: null,
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
      status: null
    }
  }),

  getters: {
    activeUnits: (state) => state.units.filter(unit => unit.status === 'active'),
    unitsForSelect: (state) => state.units.map(unit => ({
      label: unit.name,
      value: unit.id
    }))
  },

  actions: {
    async fetchUnits(params = {}) {
      this.loading = true
      try {
        const response = await unitService.getUnits({
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          search: this.filters.search,
          status: this.filters.status,
          ...params
        })
        
        // Update untuk struktur response BaseResponseService
        this.units = response.data
        
        // Update pagination dari meta
        if (response.meta) {
          this.pagination.rowsNumber = response.meta.total
          this.pagination.page = response.meta.current_page
          this.pagination.rowsPerPage = response.meta.per_page
        }
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat unit'
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

    async fetchUnitById(id) {
      this.loading = true
      try {
        const response = await unitService.getUnitById(id)
        this.unit = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat unit'
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

    async createUnit(unitData) {
      this.loading = true
      try {
        const response = await unitService.createUnit(unitData)
        
        Notify.create({
          type: 'positive',
          message: 'Unit berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat unit'
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

    async updateUnit(id, unitData) {
      this.loading = true
      try {
        const response = await unitService.updateUnit(id, unitData)
        
        Notify.create({
          type: 'positive',
          message: 'Unit berhasil diupdate',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate unit'
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

    async deleteUnit(id) {
      this.loading = true
      try {
        await unitService.deleteUnit(id)
        
        // Remove from local state
        this.units = this.units.filter(unit => unit.id !== id)
        
        Notify.create({
          type: 'positive',
          message: 'Unit berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus unit'
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

    async toggleUnitStatus(id) {
      try {
        const response = await unitService.toggleUnitStatus(id)
        
        // Update local state
        const index = this.units.findIndex(unit => unit.id === id)
        if (index !== -1) {
          this.units[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Status unit berhasil diubah',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengubah status unit'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchUnitsForSelect() {
      try {
        const response = await unitService.getUnitsForSelect()
        return response.data.data
      } catch (error) {
        console.error('Fetch units for select error:', error)
        return []
      }
    },

    async searchUnits(query) {
      this.loading = true
      try {
        const response = await unitService.searchUnits(query, this.filters)
        this.units = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mencari unit'
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

    // Local state management
    setPagination(pagination) {
      this.pagination = { ...this.pagination, ...pagination }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearUnit() {
      this.unit = null
    },

    clearUnits() {
      this.units = []
    }
  }
})
import { defineStore } from 'pinia'
import { supplierService } from 'src/services'
import { Notify } from 'quasar'

export const useSupplierStore = defineStore('supplier', {
  state: () => ({
    suppliers: [],
    supplier: null,
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
      status: null,
      city: ''
    }
  }),

  getters: {
    activeSuppliers: (state) => state.suppliers.filter(supplier => supplier.status === 'active'),
    suppliersForSelect: (state) => state.suppliers.map(supplier => ({
      label: supplier.name,
      value: supplier.id
    }))
  },

  actions: {
    async fetchSuppliers(params = {}) {
      this.loading = true
      try {
        const response = await supplierService.getSuppliers({
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          search: this.filters.search,
          status: this.filters.status,
          ...params
        })
        
        // Update untuk struktur response BaseResponseService
        this.suppliers = response.data
        
        // Update pagination dari meta
        if (response.meta) {
          this.pagination.rowsNumber = response.meta.total
          this.pagination.page = response.meta.current_page
          this.pagination.rowsPerPage = response.meta.per_page
        }
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat supplier'
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

    async fetchSupplierById(id) {
      this.loading = true
      try {
        const response = await supplierService.getSupplierById(id)
        this.supplier = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat supplier'
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

    async createSupplier(supplierData) {
      this.loading = true
      try {
        const response = await supplierService.createSupplier(supplierData)
        
        Notify.create({
          type: 'positive',
          message: 'Supplier berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat supplier'
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

    async updateSupplier(id, supplierData) {
      this.loading = true
      try {
        const response = await supplierService.updateSupplier(id, supplierData)
        
        Notify.create({
          type: 'positive',
          message: 'Supplier berhasil diupdate',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate supplier'
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

    async deleteSupplier(id) {
      this.loading = true
      try {
        await supplierService.deleteSupplier(id)
        
        // Remove from local state
        this.suppliers = this.suppliers.filter(supplier => supplier.id !== id)
        
        Notify.create({
          type: 'positive',
          message: 'Supplier berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus supplier'
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

    async toggleSupplierStatus(id) {
      try {
        const response = await supplierService.toggleSupplierStatus(id)
        
        // Update local state
        const index = this.suppliers.findIndex(supplier => supplier.id === id)
        if (index !== -1) {
          this.suppliers[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Status supplier berhasil diubah',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengubah status supplier'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchSuppliersForSelect() {
      try {
        const response = await supplierService.getSuppliersForSelect()
        return response.data.data
      } catch (error) {
        console.error('Fetch suppliers for select error:', error)
        return []
      }
    },

    async searchSuppliers(query) {
      this.loading = true
      try {
        const response = await supplierService.searchSuppliers(query, this.filters)
        this.suppliers = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mencari supplier'
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

    clearSupplier() {
      this.supplier = null
    },

    clearSuppliers() {
      this.suppliers = []
    }
  }
})
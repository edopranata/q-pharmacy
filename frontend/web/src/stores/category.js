import { defineStore } from 'pinia'
import { categoryService } from 'src/services'
import { Notify } from 'quasar'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    category: null,
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
    activeCategories: (state) => state.categories.filter(cat => cat.status === 'active'),
    categoriesForSelect: (state) => state.categories.map(cat => ({
      label: cat.name,
      value: cat.id
    }))
  },

  actions: {
    async fetchCategories(params = {}) {
      this.loading = true
      console.log(params)
      console.log(this.filters)
      try {
        const response = await categoryService.getCategories({
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          search: this.filters.search,
          status: this.filters.status,
          ...params
        })
        
        // Update untuk struktur response BaseResponseService
        this.categories = response.data
        
        // Update pagination dari meta
        if (response.meta) {
          this.pagination.rowsNumber = response.meta.total
          this.pagination.page = response.meta.current_page
          this.pagination.rowsPerPage = response.meta.per_page
        }
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat kategori'
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

    async fetchCategoryById(id) {
      this.loading = true
      try {
        const response = await categoryService.getCategoryById(id)
        this.category = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat kategori'
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

    async createCategory(categoryData) {
      this.loading = true
      try {
        const response = await categoryService.createCategory(categoryData)
        
        Notify.create({
          type: 'positive',
          message: 'Kategori berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat kategori'
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

    async updateCategory(id, categoryData) {
      this.loading = true
      try {
        const response = await categoryService.updateCategory(id, categoryData)
        
        Notify.create({
          type: 'positive',
          message: 'Kategori berhasil diupdate',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate kategori'
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

    async deleteCategory(id) {
      this.loading = true
      try {
        await categoryService.deleteCategory(id)
        
        // Remove from local state
        this.categories = this.categories.filter(cat => cat.id !== id)
        
        Notify.create({
          type: 'positive',
          message: 'Kategori berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus kategori'
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

    async toggleCategoryStatus(id) {
      try {
        const response = await categoryService.toggleCategoryStatus(id)
        
        // Update local state
        const index = this.categories.findIndex(cat => cat.id === id)
        if (index !== -1) {
          this.categories[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Status kategori berhasil diubah',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengubah status kategori'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchCategoriesForSelect() {
      try {
        const response = await categoryService.getCategoriesForSelect()
        return response.data.data
      } catch (error) {
        console.error('Fetch categories for select error:', error)
        return []
      }
    },

    async searchCategories(query) {
      this.loading = true
      try {
        const response = await categoryService.searchCategories(query, this.filters)
        this.categories = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mencari kategori'
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

    clearCategory() {
      this.category = null
    },

    clearCategories() {
      this.categories = []
    }
  }
})
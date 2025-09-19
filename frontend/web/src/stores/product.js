import { defineStore } from 'pinia'
import { productService } from 'src/services'
import { Notify } from 'quasar'

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    product: null,
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
      category: null,
      status: null
    }
  }),

  getters: {
    activeProducts: (state) => state.products.filter(product => product.status === 'active'),
    productsForSelect: (state) => state.products.map(product => ({
      label: `${product.name} - ${product.sku}`,
      value: product.id,
      price: product.price,
      stock: product.stock
    })),
    lowStockProducts: (state) => state.products.filter(product => 
      product.stock <= product.min_stock
    )
  },

  actions: {
    async fetchProducts(params = {}) {
      this.loading = true
      try {
        const response = await productService.getProducts({
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          search: this.filters.search,
          category_id: this.filters.category_id,
          supplier_id: this.filters.supplier_id,
          status: this.filters.status,
          min_price: this.filters.min_price,
          max_price: this.filters.max_price,
          ...params
        })
        
        // Update untuk struktur response BaseResponseService
        this.products = response.data
        
        // Update pagination dari meta
        if (response.meta) {
          this.pagination.rowsNumber = response.meta.total
          this.pagination.page = response.meta.current_page
          this.pagination.rowsPerPage = response.meta.per_page
        }
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat produk'
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

    async fetchProductById(id) {
      this.loading = true
      try {
        const response = await productService.getProductById(id)
        this.product = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat produk'
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

    async createProduct(productData) {
      this.loading = true
      try {
        const response = await productService.createProduct(productData)
        
        // Add to local state
        this.products.unshift(response.data.data)
        
        Notify.create({
          type: 'positive',
          message: 'Produk berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat produk'
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

    async updateProduct(id, productData) {
      this.loading = true
      try {
        const response = await productService.updateProduct(id, productData)
        
        // Update local state
        const index = this.products.findIndex(product => product.id === id)
        if (index !== -1) {
          this.products[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Produk berhasil diupdate',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate produk'
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

    async deleteProduct(id) {
      this.loading = true
      try {
        await productService.deleteProduct(id)
        
        // Remove from local state
        this.products = this.products.filter(product => product.id !== id)
        
        Notify.create({
          type: 'positive',
          message: 'Produk berhasil dihapus',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal menghapus produk'
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

    async updateProductStock(id, stockData) {
      try {
        const response = await productService.updateProductStock(id, stockData)
        
        // Update local state
        const index = this.products.findIndex(product => product.id === id)
        if (index !== -1) {
          this.products[index].stock = response.data.data.stock
        }
        
        Notify.create({
          type: 'positive',
          message: 'Stok produk berhasil diupdate',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate stok produk'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async searchProducts(query) {
      this.loading = true
      try {
        const response = await productService.searchProducts(query, this.filters)
        this.products = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mencari produk'
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

    async fetchProductsByBarcode(barcode) {
      try {
        const response = await productService.getProductByBarcode(barcode)
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Produk tidak ditemukan'
        return { success: false, message }
      }
    },

    async fetchLowStockProducts() {
      try {
        const response = await productService.getLowStockProducts()
        return { success: true, data: response.data.data }
      } catch (error) {
        console.error('Fetch low stock products error:', error)
        return { success: false, data: [] }
      }
    },

    // Local state management
    setPagination(pagination) {
      this.pagination = { ...this.pagination, ...pagination }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearProduct() {
      this.product = null
    },

    clearProducts() {
      this.products = []
    },

    // Update product in local state (for real-time updates)
    updateProductInState(productId, updates) {
      const index = this.products.findIndex(product => product.id === productId)
      if (index !== -1) {
        this.products[index] = { ...this.products[index], ...updates }
      }
    }
  }
})
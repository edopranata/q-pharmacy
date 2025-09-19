import { defineStore } from 'pinia'
import { transactionService } from 'src/services'
import { Notify } from 'quasar'

export const useTransactionStore = defineStore('transaction', {
  state: () => ({
    transactions: [],
    transaction: null,
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
      status: 'all',
      payment_method: 'all',
      date_from: null,
      date_to: null,
      cashier_id: null
    },
    cart: {
      items: [],
      customer: null,
      payment_method: 'cash',
      discount: 0,
      tax: 0,
      notes: ''
    }
  }),

  getters: {
    cartTotal: (state) => {
      const subtotal = state.cart.items.reduce((total, item) => {
        return total + (item.price * item.quantity)
      }, 0)
      const discountAmount = (subtotal * state.cart.discount) / 100
      const taxAmount = ((subtotal - discountAmount) * state.cart.tax) / 100
      return subtotal - discountAmount + taxAmount
    },
    
    cartSubtotal: (state) => {
      return state.cart.items.reduce((total, item) => {
        return total + (item.price * item.quantity)
      }, 0)
    },
    
    cartItemsCount: (state) => {
      return state.cart.items.reduce((total, item) => total + item.quantity, 0)
    },
    
    todayTransactions: (state) => {
      const today = new Date().toDateString()
      return state.transactions.filter(transaction => 
        new Date(transaction.created_at).toDateString() === today
      )
    }
  },

  actions: {
    async fetchTransactions(params = {}) {
      this.loading = true
      try {
        const response = await transactionService.getTransactions({
          page: this.pagination.page,
          per_page: this.pagination.rowsPerPage,
          search: this.filters.search,
          status: this.filters.status,
          payment_method: this.filters.payment_method,
          date_from: this.filters.date_from,
          date_to: this.filters.date_to,
          cashier_id: this.filters.cashier_id,
          ...params
        })
        
        this.transactions = response.data.data
        this.pagination.rowsNumber = response.data.total
        this.pagination.page = response.data.current_page
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat transaksi'
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

    async fetchTransactionById(id) {
      this.loading = true
      try {
        const response = await transactionService.getTransactionById(id)
        this.transaction = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat transaksi'
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

    async createTransaction(transactionData) {
      this.loading = true
      try {
        const response = await transactionService.createTransaction(transactionData)
        
        // Add to local state
        this.transactions.unshift(response.data.data)
        
        // Clear cart after successful transaction
        this.clearCart()
        
        Notify.create({
          type: 'positive',
          message: 'Transaksi berhasil dibuat',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal membuat transaksi'
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

    async updateTransaction(id, transactionData) {
      this.loading = true
      try {
        const response = await transactionService.updateTransaction(id, transactionData)
        
        // Update local state
        const index = this.transactions.findIndex(transaction => transaction.id === id)
        if (index !== -1) {
          this.transactions[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Transaksi berhasil diupdate',
          position: 'top'
        })
        
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal mengupdate transaksi'
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

    async cancelTransaction(id, reason = '') {
      this.loading = true
      try {
        const response = await transactionService.cancelTransaction(id, { reason })
        
        // Update local state
        const index = this.transactions.findIndex(transaction => transaction.id === id)
        if (index !== -1) {
          this.transactions[index] = response.data.data
        }
        
        Notify.create({
          type: 'positive',
          message: 'Transaksi berhasil dibatalkan',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal membatalkan transaksi'
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

    async fetchTodayTransactions() {
      try {
        const response = await transactionService.getTodayTransactions()
        return { success: true, data: response.data.data }
      } catch (error) {
        console.error('Fetch today transactions error:', error)
        return { success: false, data: [] }
      }
    },

    async fetchTransactionStats(period = 'today') {
      try {
        const response = await transactionService.getTransactionStats(period)
        return { success: true, data: response.data.data }
      } catch (error) {
        console.error('Fetch transaction stats error:', error)
        return { success: false, data: {} }
      }
    },

    // Cart management
    addToCart(product, quantity = 1) {
      const existingItem = this.cart.items.find(item => item.product_id === product.id)
      
      if (existingItem) {
        existingItem.quantity += quantity
      } else {
        this.cart.items.push({
          product_id: product.id,
          name: product.name,
          price: product.price,
          quantity: quantity,
          sku: product.sku,
          stock: product.stock
        })
      }
    },

    removeFromCart(productId) {
      this.cart.items = this.cart.items.filter(item => item.product_id !== productId)
    },

    updateCartItemQuantity(productId, quantity) {
      const item = this.cart.items.find(item => item.product_id === productId)
      if (item) {
        if (quantity <= 0) {
          this.removeFromCart(productId)
        } else {
          item.quantity = quantity
        }
      }
    },

    setCartCustomer(customer) {
      this.cart.customer = customer
    },

    setCartPaymentMethod(method) {
      this.cart.payment_method = method
    },

    setCartDiscount(discount) {
      this.cart.discount = discount
    },

    setCartTax(tax) {
      this.cart.tax = tax
    },

    setCartNotes(notes) {
      this.cart.notes = notes
    },

    clearCart() {
      this.cart = {
        items: [],
        customer: null,
        payment_method: 'cash',
        discount: 0,
        tax: 0,
        notes: ''
      }
    },

    // Local state management
    setPagination(pagination) {
      this.pagination = { ...this.pagination, ...pagination }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearTransaction() {
      this.transaction = null
    },

    clearTransactions() {
      this.transactions = []
    }
  }
})
import { defineStore } from 'pinia'
import { dashboardService } from 'src/services'
import { Notify } from 'quasar'

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    stats: {
      totalSales: 0,
      totalTransactions: 0,
      totalProducts: 0,
      totalCustomers: 0,
      lowStockProducts: 0,
      todaySales: 0,
      todayTransactions: 0,
      monthlyGrowth: 0
    },
    salesOverview: {
      daily: [],
      weekly: [],
      monthly: [],
      yearly: []
    },
    topProducts: [],
    recentTransactions: [],
    lowStockProducts: [],
    salesByCategory: [],
    loading: {
      stats: false,
      salesOverview: false,
      topProducts: false,
      recentTransactions: false,
      lowStockProducts: false,
      salesByCategory: false
    },
    dateRange: {
      start: null,
      end: null
    }
  }),

  getters: {
    isLoading: (state) => {
      return Object.values(state.loading).some(loading => loading)
    },
    
    totalRevenue: (state) => {
      return state.salesOverview.daily.reduce((total, day) => total + day.revenue, 0)
    },
    
    averageDailySales: (state) => {
      const dailySales = state.salesOverview.daily
      if (dailySales.length === 0) return 0
      return dailySales.reduce((total, day) => total + day.revenue, 0) / dailySales.length
    }
  },

  actions: {
    async fetchDashboardStats(period = 'today') {
      this.loading.stats = true
      try {
        const response = await dashboardService.getDashboardStats(period)
        this.stats = { ...this.stats, ...response.data.data }
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat statistik dashboard'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.stats = false
      }
    },

    async fetchSalesOverview(period = 'daily', dateRange = null) {
      this.loading.salesOverview = true
      try {
        const params = dateRange ? {
          start_date: dateRange.start,
          end_date: dateRange.end
        } : {}
        
        const response = await dashboardService.getSalesOverview(period, params)
        this.salesOverview[period] = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat overview penjualan'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.salesOverview = false
      }
    },

    async fetchTopProducts(limit = 10, period = 'month') {
      this.loading.topProducts = true
      try {
        const response = await dashboardService.getTopProducts({ limit, period })
        this.topProducts = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat produk terlaris'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.topProducts = false
      }
    },

    async fetchRecentTransactions(limit = 10) {
      this.loading.recentTransactions = true
      try {
        const response = await dashboardService.getRecentTransactions({ limit })
        this.recentTransactions = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat transaksi terbaru'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.recentTransactions = false
      }
    },

    async fetchLowStockProducts(limit = 10) {
      this.loading.lowStockProducts = true
      try {
        const response = await dashboardService.getLowStockProducts({ limit })
        this.lowStockProducts = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat produk stok rendah'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.lowStockProducts = false
      }
    },

    async fetchSalesByCategory(period = 'month') {
      this.loading.salesByCategory = true
      try {
        const response = await dashboardService.getSalesByCategory({ period })
        this.salesByCategory = response.data.data
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal memuat penjualan per kategori'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      } finally {
        this.loading.salesByCategory = false
      }
    },

    async fetchSalesReport(params = {}) {
      try {
        const response = await dashboardService.getSalesReport(params)
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal memuat laporan penjualan'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async fetchInventoryReport(params = {}) {
      try {
        const response = await dashboardService.getInventoryReport(params)
        return { success: true, data: response.data.data }
      } catch (error) {
        const message = error.message || 'Gagal memuat laporan inventori'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    async exportSalesReport(params = {}) {
      try {
        const response = await dashboardService.exportSalesReport(params)
        
        // Create download link
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `sales-report-${new Date().toISOString().split('T')[0]}.xlsx`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
        
        Notify.create({
          type: 'positive',
          message: 'Laporan berhasil diunduh',
          position: 'top'
        })
        
        return { success: true }
      } catch (error) {
        const message = error.message || 'Gagal mengunduh laporan'
        Notify.create({
          type: 'negative',
          message,
          position: 'top'
        })
        return { success: false, message }
      }
    },

    // Load all dashboard data
    async loadDashboardData(period = 'today') {
      const promises = [
        this.fetchDashboardStats(period),
        this.fetchSalesOverview('daily'),
        this.fetchTopProducts(5),
        this.fetchRecentTransactions(5),
        this.fetchLowStockProducts(5),
        this.fetchSalesByCategory()
      ]
      
      try {
        await Promise.all(promises)
        return { success: true }
      } catch (error) {
        console.error('Load dashboard data error:', error)
        return { success: false }
      }
    },

    // Refresh specific data
    async refreshStats() {
      return this.fetchDashboardStats()
    },

    async refreshSalesData() {
      const promises = [
        this.fetchSalesOverview('daily'),
        this.fetchTopProducts(5),
        this.fetchSalesByCategory()
      ]
      
      try {
        await Promise.all(promises)
        return { success: true }
      } catch (error) {
        console.error('Refresh sales data error:', error)
        return { success: false }
      }
    },

    // Local state management
    setDateRange(start, end) {
      this.dateRange = { start, end }
    },

    clearDateRange() {
      this.dateRange = { start: null, end: null }
    },

    resetDashboard() {
      this.stats = {
        totalSales: 0,
        totalTransactions: 0,
        totalProducts: 0,
        totalCustomers: 0,
        lowStockProducts: 0,
        todaySales: 0,
        todayTransactions: 0,
        monthlyGrowth: 0
      }
      this.salesOverview = {
        daily: [],
        weekly: [],
        monthly: [],
        yearly: []
      }
      this.topProducts = []
      this.recentTransactions = []
      this.lowStockProducts = []
      this.salesByCategory = []
    }
  }
})
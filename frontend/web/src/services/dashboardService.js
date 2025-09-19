import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Dashboard Service
 * Handles all dashboard-related API calls
 */
class DashboardService {
  /**
   * Get dashboard statistics
   * @param {Object} params - Query parameters
   * @param {string} params.period - Time period (today, week, month, year)
   * @param {string} params.start_date - Start date for custom period
   * @param {string} params.end_date - End date for custom period
   * @returns {Promise<Object>} Dashboard statistics
   */
  async getDashboardStats(params = {}) {
    return await apiService.get(ApiEndpoints.DASHBOARD_STATS, params)
  }

  /**
   * Get sales overview data
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Sales overview
   */
  async getSalesOverview(params = {}) {
    return await apiService.get('/dashboard/sales-overview', params)
  }

  /**
   * Get revenue chart data
   * @param {Object} params - Query parameters
   * @param {string} params.period - Chart period (daily, weekly, monthly)
   * @param {number} params.days - Number of days to include
   * @returns {Promise<Object>} Revenue chart data
   */
  async getRevenueChart(params = {}) {
    return await apiService.get('/dashboard/revenue-chart', params)
  }

  /**
   * Get top selling products
   * @param {Object} params - Query parameters
   * @param {number} params.limit - Number of products to return
   * @param {string} params.period - Time period
   * @returns {Promise<Object>} Top selling products
   */
  async getTopSellingProducts(params = { limit: 10 }) {
    return await apiService.get('/dashboard/top-products', params)
  }

  /**
   * Get recent transactions
   * @param {Object} params - Query parameters
   * @param {number} params.limit - Number of transactions to return
   * @returns {Promise<Object>} Recent transactions
   */
  async getRecentTransactions(params = { limit: 10 }) {
    return await apiService.get('/dashboard/recent-transactions', params)
  }

  /**
   * Get low stock alerts
   * @param {Object} params - Query parameters
   * @param {number} params.limit - Number of alerts to return
   * @returns {Promise<Object>} Low stock alerts
   */
  async getLowStockAlerts(params = { limit: 10 }) {
    return await apiService.get('/dashboard/low-stock-alerts', params)
  }

  /**
   * Get cashier performance data
   * @param {Object} params - Query parameters
   * @param {string} params.period - Time period
   * @returns {Promise<Object>} Cashier performance
   */
  async getCashierPerformance(params = {}) {
    return await apiService.get('/dashboard/cashier-performance', params)
  }

  /**
   * Get category sales distribution
   * @param {Object} params - Query parameters
   * @param {string} params.period - Time period
   * @returns {Promise<Object>} Category sales distribution
   */
  async getCategorySalesDistribution(params = {}) {
    return await apiService.get('/dashboard/category-distribution', params)
  }

  /**
   * Get payment method distribution
   * @param {Object} params - Query parameters
   * @param {string} params.period - Time period
   * @returns {Promise<Object>} Payment method distribution
   */
  async getPaymentMethodDistribution(params = {}) {
    return await apiService.get('/dashboard/payment-distribution', params)
  }

  /**
   * Get inventory summary
   * @returns {Promise<Object>} Inventory summary
   */
  async getInventorySummary() {
    return await apiService.get('/dashboard/inventory-summary')
  }

  /**
   * Get sales comparison data
   * @param {Object} params - Query parameters
   * @param {string} params.current_period - Current period
   * @param {string} params.previous_period - Previous period for comparison
   * @returns {Promise<Object>} Sales comparison
   */
  async getSalesComparison(params = {}) {
    return await apiService.get('/dashboard/sales-comparison', params)
  }

  /**
   * Get profit margin analysis
   * @param {Object} params - Query parameters
   * @param {string} params.period - Time period
   * @returns {Promise<Object>} Profit margin analysis
   */
  async getProfitMarginAnalysis(params = {}) {
    return await apiService.get('/dashboard/profit-analysis', params)
  }

  /**
   * Get customer insights
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Customer insights
   */
  async getCustomerInsights(params = {}) {
    return await apiService.get('/dashboard/customer-insights', params)
  }

  /**
   * Export dashboard report
   * @param {string} format - Export format
   * @param {Object} params - Report parameters
   * @returns {Promise<Blob>} Dashboard report file
   */
  async exportDashboardReport(format = 'pdf', params = {}) {
    return await apiService.download('/dashboard/export', `dashboard-report.${format}`, {
      params: { format, ...params }
    })
  }

  /**
   * Get real-time dashboard updates
   * @returns {Promise<Object>} Real-time updates
   */
  async getRealTimeUpdates() {
    return await apiService.get('/dashboard/real-time')
  }

  /**
   * Get system health status
   * @returns {Promise<Object>} System health data
   */
  async getSystemHealth() {
    return await apiService.get('/dashboard/system-health')
  }
}

// Create and export singleton instance
const dashboardService = new DashboardService()
export default dashboardService

// Export class for testing purposes
export { DashboardService }
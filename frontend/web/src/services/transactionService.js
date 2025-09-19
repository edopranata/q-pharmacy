import apiService from './api'
import { ApiEndpoints } from 'src/types/api'

/**
 * Transaction Service
 * Handles all transaction-related API calls
 */
class TransactionService {
  /**
   * Get all transactions with optional pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Transactions list response
   */
  async getTransactions(params = {}) {
    return await apiService.get(ApiEndpoints.TRANSACTIONS, params)
  }

  /**
   * Get transaction by ID
   * @param {number} id - Transaction ID
   * @returns {Promise<Object>} Transaction data
   */
  async getTransaction(id) {
    return await apiService.get(ApiEndpoints.TRANSACTION(id))
  }

  /**
   * Create new transaction (POS Sale)
   * @param {Object} transactionData - Transaction data
   * @returns {Promise<Object>} Created transaction response
   */
  async createTransaction(transactionData) {
    return await apiService.post(ApiEndpoints.TRANSACTIONS, transactionData)
  }

  /**
   * Update existing transaction
   * @param {number} id - Transaction ID
   * @param {Object} transactionData - Updated transaction data
   * @returns {Promise<Object>} Updated transaction response
   */
  async updateTransaction(id, transactionData) {
    return await apiService.put(ApiEndpoints.TRANSACTION(id), transactionData)
  }

  /**
   * Cancel transaction
   * @param {number} id - Transaction ID
   * @param {string} reason - Cancellation reason
   * @returns {Promise<Object>} Cancel response
   */
  async cancelTransaction(id, reason = '') {
    return await apiService.patch(`${ApiEndpoints.TRANSACTION(id)}/cancel`, { reason })
  }

  /**
   * Get transaction receipt
   * @param {number} id - Transaction ID
   * @returns {Promise<Object>} Receipt data
   */
  async getTransactionReceipt(id) {
    return await apiService.get(`${ApiEndpoints.TRANSACTION(id)}/receipt`)
  }

  /**
   * Print transaction receipt
   * @param {number} id - Transaction ID
   * @returns {Promise<Blob>} Receipt PDF
   */
  async printTransactionReceipt(id) {
    return await apiService.download(`${ApiEndpoints.TRANSACTION(id)}/print`, `receipt-${id}.pdf`)
  }

  /**
   * Get daily sales report
   * @param {string} date - Date in YYYY-MM-DD format
   * @returns {Promise<Object>} Daily sales report
   */
  async getDailySalesReport(date) {
    return await apiService.get(`${ApiEndpoints.TRANSACTIONS}/daily-report`, { date })
  }

  /**
   * Get sales report by date range
   * @param {string} startDate - Start date in YYYY-MM-DD format
   * @param {string} endDate - End date in YYYY-MM-DD format
   * @returns {Promise<Object>} Sales report
   */
  async getSalesReport(startDate, endDate) {
    return await apiService.get(`${ApiEndpoints.TRANSACTIONS}/sales-report`, {
      start_date: startDate,
      end_date: endDate
    })
  }

  /**
   * Get transactions by cashier
   * @param {number} cashierId - Cashier user ID
   * @param {Object} params - Additional query parameters
   * @returns {Promise<Object>} Cashier transactions
   */
  async getTransactionsByCashier(cashierId, params = {}) {
    return await apiService.get(ApiEndpoints.TRANSACTIONS, {
      cashier_id: cashierId,
      ...params
    })
  }

  /**
   * Get transactions by payment method
   * @param {string} paymentMethod - Payment method
   * @param {Object} params - Additional query parameters
   * @returns {Promise<Object>} Payment method transactions
   */
  async getTransactionsByPaymentMethod(paymentMethod, params = {}) {
    return await apiService.get(ApiEndpoints.TRANSACTIONS, {
      payment_method: paymentMethod,
      ...params
    })
  }

  /**
   * Export transactions
   * @param {string} format - Export format
   * @param {Object} filters - Export filters
   * @returns {Promise<Blob>} Export file
   */
  async exportTransactions(format = 'excel', filters = {}) {
    return await apiService.download(`${ApiEndpoints.TRANSACTIONS}/export`, `transactions.${format}`, {
      params: { format, ...filters }
    })
  }

  /**
   * Get transaction statistics
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Transaction statistics
   */
  async getTransactionStats(params = {}) {
    return await apiService.get(`${ApiEndpoints.TRANSACTIONS}/stats`, params)
  }

  /**
   * Refund transaction
   * @param {number} id - Transaction ID
   * @param {Object} refundData - Refund data
   * @returns {Promise<Object>} Refund response
   */
  async refundTransaction(id, refundData) {
    return await apiService.post(`${ApiEndpoints.TRANSACTION(id)}/refund`, refundData)
  }

  /**
   * Get best selling products
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>} Best selling products
   */
  async getBestSellingProducts(params = {}) {
    return await apiService.get(`${ApiEndpoints.TRANSACTIONS}/best-selling`, params)
  }

  /**
   * Get hourly sales data
   * @param {string} date - Date in YYYY-MM-DD format
   * @returns {Promise<Object>} Hourly sales data
   */
  async getHourlySales(date) {
    return await apiService.get(`${ApiEndpoints.TRANSACTIONS}/hourly-sales`, { date })
  }
}

// Create and export singleton instance
const transactionService = new TransactionService()
export default transactionService

// Export class for testing purposes
export { TransactionService }
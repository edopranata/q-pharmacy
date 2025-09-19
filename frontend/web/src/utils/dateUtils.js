import { date } from 'quasar'

/**
 * Date utility functions for formatting and manipulating dates
 */
export class DateUtils {
  /**
   * Format date to readable string
   * @param {string|Date} dateValue - Date to format
   * @param {string} format - Format pattern (default: 'DD/MM/YYYY HH:mm')
   * @returns {string} Formatted date string
   */
  static formatDate(dateValue, format = 'DD/MM/YYYY HH:mm') {
    if (!dateValue) return '-'
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      return date.formatDate(dateObj, format)
    } catch {
      console.warn('Invalid date format:', dateValue)
      return '-'
    }
  }

  /**
   * Format date to short format (DD/MM/YYYY)
   * @param {string|Date} dateValue - Date to format
   * @returns {string} Formatted date string
   */
  static formatDateShort(dateValue) {
    return this.formatDate(dateValue, 'DD/MM/YYYY')
  }

  /**
   * Format date to long format with time (DD MMM YYYY, HH:mm)
   * @param {string|Date} dateValue - Date to format
   * @returns {string} Formatted date string
   */
  static formatDateLong(dateValue) {
    return this.formatDate(dateValue, 'DD MMM YYYY, HH:mm')
  }

  /**
   * Format date to time only (HH:mm)
   * @param {string|Date} dateValue - Date to format
   * @returns {string} Formatted time string
   */
  static formatTime(dateValue) {
    return this.formatDate(dateValue, 'HH:mm')
  }

  /**
   * Get relative time (e.g., '2 hours ago', 'yesterday')
   * @param {string|Date} dateValue - Date to format
   * @returns {string} Relative time string
   */
  static getRelativeTime(dateValue) {
    if (!dateValue) return '-'
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      const now = new Date()
      const diffMs = now - dateObj
      const diffMinutes = Math.floor(diffMs / (1000 * 60))
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
      
      if (diffMinutes < 1) {
        return 'Baru saja'
      } else if (diffMinutes < 60) {
        return `${diffMinutes} menit yang lalu`
      } else if (diffHours < 24) {
        return `${diffHours} jam yang lalu`
      } else if (diffDays === 1) {
        return 'Kemarin'
      } else if (diffDays < 7) {
        return `${diffDays} hari yang lalu`
      } else {
        return this.formatDateShort(dateValue)
      }
    } catch {
      console.warn('Invalid date for relative time:', dateValue)
      return '-'
    }
  }

  /**
   * Check if date is today
   * @param {string|Date} dateValue - Date to check
   * @returns {boolean} True if date is today
   */
  static isToday(dateValue) {
    if (!dateValue) return false
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      const today = new Date()
      return date.isSameDate(dateObj, today, 'day')
    } catch {
      return false
    }
  }

  /**
   * Check if date is yesterday
   * @param {string|Date} dateValue - Date to check
   * @returns {boolean} True if date is yesterday
   */
  static isYesterday(dateValue) {
    if (!dateValue) return false
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      const yesterday = date.subtractFromDate(new Date(), { days: 1 })
      return date.isSameDate(dateObj, yesterday, 'day')
    } catch {
      return false
    }
  }

  /**
   * Get date range for filtering (start and end of day)
   * @param {string|Date} dateValue - Date to get range for
   * @returns {Object} Object with start and end dates
   */
  static getDateRange(dateValue) {
    if (!dateValue) return { start: null, end: null }
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      const start = date.startOfDate(dateObj, 'day')
      const end = date.endOfDate(dateObj, 'day')
      
      return {
        start: date.formatDate(start, 'YYYY-MM-DD HH:mm:ss'),
        end: date.formatDate(end, 'YYYY-MM-DD HH:mm:ss')
      }
    } catch {
      console.warn('Invalid date for range:', dateValue)
      return { start: null, end: null }
    }
  }

  /**
   * Format date for API (ISO string)
   * @param {string|Date} dateValue - Date to format
   * @returns {string} ISO formatted date string
   */
  static formatForAPI(dateValue) {
    if (!dateValue) return null
    
    try {
      const dateObj = typeof dateValue === 'string' ? new Date(dateValue) : dateValue
      return dateObj.toISOString()
    } catch {
      console.warn('Invalid date for API format:', dateValue)
      return null
    }
  }

  /**
   * Parse date from various formats
   * @param {string} dateString - Date string to parse
   * @returns {Date|null} Parsed date object or null if invalid
   */
  static parseDate(dateString) {
    if (!dateString) return null
    
    try {
      // Try parsing as ISO string first
      let dateObj = new Date(dateString)
      
      // If invalid, try parsing DD/MM/YYYY format
      if (isNaN(dateObj.getTime()) && typeof dateString === 'string') {
        const parts = dateString.split('/')
        if (parts.length === 3) {
          // Assume DD/MM/YYYY format
          dateObj = new Date(parts[2], parts[1] - 1, parts[0])
        }
      }
      
      return isNaN(dateObj.getTime()) ? null : dateObj
    } catch {
      console.warn('Failed to parse date:', dateString)
      return null
    }
  }
}

// Export default for easier importing
export default DateUtils
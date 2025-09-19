import { defineStore } from 'pinia'
import { Dark, LocalStorage } from 'quasar'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    theme: 'auto', // 'light', 'dark', 'auto'
    isDark: false,
    systemPrefersDark: false
  }),

  getters: {
    currentTheme: (state) => state.theme,
    isCurrentlyDark: (state) => state.isDark,
    themeIcon: (state) => {
      switch (state.theme) {
        case 'light': return 'light_mode'
        case 'dark': return 'dark_mode'
        case 'auto': return 'brightness_auto'
        default: return 'brightness_auto'
      }
    },
    themeLabel: (state) => {
      switch (state.theme) {
        case 'light': return 'Light Mode'
        case 'dark': return 'Dark Mode'
        case 'auto': return 'Auto Mode'
        default: return 'Auto Mode'
      }
    }
  },

  actions: {
    // Initialize theme system
    initializeTheme() {
      // Get saved theme from LocalStorage
      const savedTheme = LocalStorage.getItem('q_pharmacy_theme')
      if (savedTheme && ['light', 'dark', 'auto'].includes(savedTheme)) {
        this.theme = savedTheme
      }

      // Detect system preference
      this.systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

      // Listen for system theme changes
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        this.systemPrefersDark = e.matches
        this.applyTheme()
      })

      // Apply initial theme
      this.applyTheme()
    },

    // Set theme preference
    setTheme(newTheme) {
      if (!['light', 'dark', 'auto'].includes(newTheme)) {
        console.warn('Invalid theme:', newTheme)
        return
      }

      this.theme = newTheme
      LocalStorage.set('q_pharmacy_theme', newTheme)
      this.applyTheme()
    },

    // Toggle between themes
    toggleTheme() {
      const themes = ['light', 'dark', 'auto']
      const currentIndex = themes.indexOf(this.theme)
      const nextIndex = (currentIndex + 1) % themes.length
      this.setTheme(themes[nextIndex])
    },

    // Apply theme to the application
    applyTheme() {
      let shouldBeDark = false

      switch (this.theme) {
        case 'light':
          shouldBeDark = false
          break
        case 'dark':
          shouldBeDark = true
          break
        case 'auto':
          shouldBeDark = this.systemPrefersDark
          break
      }

      this.isDark = shouldBeDark

      // Apply to Quasar Dark mode
      Dark.set(shouldBeDark)

      // Apply theme class to body
      if (shouldBeDark) {
        document.body.classList.add('theme-dark')
        document.body.classList.remove('theme-light')
      } else {
        document.body.classList.add('theme-light')
        document.body.classList.remove('theme-dark')
      }

      // Update meta theme-color for mobile browsers
      const metaThemeColor = document.querySelector('meta[name="theme-color"]')
      if (metaThemeColor) {
        metaThemeColor.setAttribute('content', shouldBeDark ? '#1a1a1a' : '#ffffff')
      }
    },

    // Get available theme options
    getThemeOptions() {
      return [
        {
          value: 'light',
          label: 'Light Mode',
          icon: 'light_mode',
          description: 'Always use light theme'
        },
        {
          value: 'dark',
          label: 'Dark Mode',
          icon: 'dark_mode',
          description: 'Always use dark theme'
        },
        {
          value: 'auto',
          label: 'Auto Mode',
          icon: 'brightness_auto',
          description: 'Follow system preference'
        }
      ]
    }
  }
})
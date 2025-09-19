import { defineStore } from 'pinia'
import { LocalStorage } from 'quasar'

export const useUIStore = defineStore('ui', {
  state: () => ({
    leftDrawerOpen: false,
    miniState: false,
    expandedMenus: {
      masterData: false,
      products: false,
      inventory: false,
      sales: false,
      reports: false,
      users: false
    }
  }),

  getters: {
    isLeftDrawerOpen: (state) => state.leftDrawerOpen,
    isMiniState: (state) => state.miniState,
    getExpandedMenus: (state) => state.expandedMenus
  },

  actions: {
    /**
     * Initialize UI state from LocalStorage
     */
    initializeUI() {
      // Initialize miniState from LocalStorage
      const savedMiniState = LocalStorage.getItem('drawer-mini-state')
      if (savedMiniState !== null) {
        this.miniState = savedMiniState === 'true'
      } else {
        // Set default value if not in LocalStorage
        this.miniState = false
        LocalStorage.set('drawer-mini-state', 'false')
      }

      // Initialize leftDrawerOpen from LocalStorage
      const savedDrawerState = LocalStorage.getItem('left-drawer-open')
      if (savedDrawerState !== null) {
        this.leftDrawerOpen = savedDrawerState === 'true'
      } else {
        // Set default value if not in LocalStorage
        this.leftDrawerOpen = false
        LocalStorage.set('left-drawer-open', 'false')
      }
    },

    /**
     * Toggle left drawer state
     */
    toggleLeftDrawer() {
      this.leftDrawerOpen = !this.leftDrawerOpen
      LocalStorage.set('left-drawer-open', this.leftDrawerOpen.toString())
    },

    /**
     * Set left drawer state
     */
    setLeftDrawerOpen(value) {
      this.leftDrawerOpen = value
      LocalStorage.set('left-drawer-open', value.toString())
    },

    /**
     * Toggle mini state
     */
    toggleMiniState() {
      this.miniState = !this.miniState
      LocalStorage.set('drawer-mini-state', this.miniState.toString())
    },

    /**
     * Set mini state
     */
    setMiniState(value) {
      this.miniState = value
      LocalStorage.set('drawer-mini-state', value.toString())
    },

    /**
     * Handle drawer click - expand if in mini state and not clicking on menu items
     */
    handleDrawerClick(event) {
      if (this.miniState && event.target.closest('.q-item') === null) {
        this.setMiniState(false)
      }
    },

    /**
     * Initialize expanded menus based on current route
     */
    initializeExpandedMenus(currentRoute) {
      const path = currentRoute.path
      
      // Reset all expanded states
      Object.keys(this.expandedMenus).forEach(key => {
        this.expandedMenus[key] = false
      })
      
      // Check which parent menu should be expanded based on current route
      if (path.includes('/app/master/')) {
        this.expandedMenus.masterData = true
        LocalStorage.set('expanded-menu-masterData', 'true')
      } else if (path.includes('/app/products')) {
        this.expandedMenus.products = true
        LocalStorage.set('expanded-menu-products', 'true')
      } else if (path.includes('/app/inventories')) {
        this.expandedMenus.inventory = true
        LocalStorage.set('expanded-menu-inventory', 'true')
      } else if (path.includes('/app/sells')) {
        this.expandedMenus.sales = true
        LocalStorage.set('expanded-menu-sales', 'true')
      } else if (path.includes('/app/reports')) {
        this.expandedMenus.reports = true
        LocalStorage.set('expanded-menu-reports', 'true')
      } else if (path.includes('/app/users')) {
        this.expandedMenus.users = true
        LocalStorage.set('expanded-menu-users', 'true')
      }
      
      // Save all expanded states to LocalStorage
      LocalStorage.set('expanded-menus', JSON.stringify(this.expandedMenus))
    },

    /**
     * Load expanded menus from LocalStorage
     */
    loadExpandedMenus() {
      const savedExpandedMenus = LocalStorage.getItem('expanded-menus')
      if (savedExpandedMenus) {
        try {
          const parsed = JSON.parse(savedExpandedMenus)
          this.expandedMenus = { ...this.expandedMenus, ...parsed }
        } catch {
          console.warn('Failed to parse expanded menus from LocalStorage')
        }
      }
    },

    /**
     * Toggle expansion state of a menu
     */
    toggleMenuExpansion(menuKey) {
      this.expandedMenus[menuKey] = !this.expandedMenus[menuKey]
      LocalStorage.set(`expanded-menu-${menuKey}`, this.expandedMenus[menuKey].toString())
      LocalStorage.set('expanded-menus', JSON.stringify(this.expandedMenus))
    },

    /**
     * Set expansion state of a menu
     */
    setMenuExpansion(menuKey, value) {
      this.expandedMenus[menuKey] = value
      LocalStorage.set(`expanded-menu-${menuKey}`, value.toString())
      LocalStorage.set('expanded-menus', JSON.stringify(this.expandedMenus))
    }
  }
})
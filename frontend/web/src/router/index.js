import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'
import { useAuthStore } from 'src/stores/auth'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  // Navigation guards
  Router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    
    // Initialize auth state from localStorage
    if (!authStore.isAuthenticated) {
      authStore.initializeAuth()
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      next('/auth/login')
      return
    }

    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
      // Redirect to app dashboard for authenticated users
      next('/app/dashboard')
      return
    }

    // Check permission-based access
    if (to.meta.permission && authStore.isAuthenticated) {
      const requiredPermission = to.meta.permission
      
      // Check if user has the required permission
      if (!authStore.hasPermission(requiredPermission)) {
        next('/forbidden')
        return
      }
    }

    // Set page title from route meta
    if (to.meta.title) {
      document.title = to.meta.title
    }

    next()
  })

  // After each navigation
  Router.afterEach((to) => {
    // Update page title if not set in beforeEach
    if (to.meta.title && document.title !== to.meta.title) {
      document.title = to.meta.title
    }
  })

  return Router
})

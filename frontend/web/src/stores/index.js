import { defineStore } from '#q-app/wrappers'
import { createPinia } from 'pinia'

// Export individual stores
export { useAuthStore } from './auth'
export { useCategoryStore } from './category'
export { useProductStore } from './product'
export { useSupplierStore } from './supplier'
export { useUnitStore } from './unit'
export { useUserStore } from './user'
export { useRoleStore } from './role'
export { useTransactionStore } from './transaction'
export { useDashboardStore } from './dashboard'
export { useThemeStore } from './theme'
export { useUIStore } from './ui'

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default defineStore((/* { ssrContext } */) => {
  const pinia = createPinia()

  // You can add Pinia plugins here
  // pinia.use(SomePiniaPlugin)

  return pinia
})

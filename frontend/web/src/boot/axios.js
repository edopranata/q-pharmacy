import { defineBoot } from '#q-app/wrappers'
import apiService from 'src/services/api'

export default defineBoot(({ router }) => {
  // Set router instance to apiService for navigation
  apiService.setRouter(router)
})
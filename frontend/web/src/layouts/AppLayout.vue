<template>
  <q-layout view="lHh Lpr lFf" class="app-layout">
    <!-- App Header -->
    <AppHeader />

    <!-- App Sidebar -->
    <AppSidebar />

    <q-page-container class="app-page-container">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUIStore } from 'src/stores'
import AppHeader from 'src/components/layout/AppHeader.vue'
import AppSidebar from 'src/components/layout/AppSidebar.vue'

const route = useRoute()
const uiStore = useUIStore()

// Initialize UI state
onMounted(() => {
  uiStore.initializeUI()
})

// Watch route changes to update expanded menus
watch(
  () => route.path,
  (newPath) => {
    uiStore.loadExpandedMenus(newPath)
  },
  { immediate: true }
)
</script>

<style lang="scss" scoped>
.app-layout {
  min-height: 100vh;
}

.app-page-container {
  // Additional page container styles can be added here
}
</style>
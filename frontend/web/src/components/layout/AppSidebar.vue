<template>
  <q-drawer
    v-model="uiStore.leftDrawerOpen"
    show-if-above
    bordered
    :mini="uiStore.miniState"
    @click="handleDrawerClick"
    :width="280"
    :mini-width="60"
  >
    <!-- Sidebar Header -->
    <SidebarHeader />
    
    <q-separator />

    <!-- Navigation Menu -->
    <NavigationMenu />
  </q-drawer>
</template>

<script setup>
import { useUIStore } from 'src/stores'
import SidebarHeader from './SidebarHeader.vue'
import NavigationMenu from './NavigationMenu.vue'

const uiStore = useUIStore()

// Methods
const handleDrawerClick = (event) => {
  uiStore.handleDrawerClick(event)
}
</script>

<style lang="scss" scoped>
// Sidebar specific styles can be added here if needed
</style>
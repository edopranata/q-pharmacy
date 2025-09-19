<template>
  <q-header elevated>
    <q-toolbar>
      <q-btn
        flat
        dense
        round
        icon="menu"
        aria-label="Menu"
        @click="handleToggleDrawer"
      />

      <q-toolbar-title>
        <q-avatar size="32px" class="q-mr-sm">
          <q-icon name="local_pharmacy" size="18px" color="primary" />
        </q-avatar>
        Q-Pharmacy
      </q-toolbar-title>

      <!-- Theme Toggle -->
      <ToggleTheme />
      
      <!-- User Dropdown -->
      <UserDropdown />
    </q-toolbar>
  </q-header>
</template>

<script setup>
import { useUIStore } from 'src/stores'
import ToggleTheme from 'src/components/ToggleTheme.vue'
import UserDropdown from './UserDropdown.vue'

const uiStore = useUIStore()

// Methods
const handleToggleDrawer = () => {
  uiStore.toggleLeftDrawer()
}
</script>

<style lang="scss" scoped>
// Header specific styles can be added here if needed
</style>
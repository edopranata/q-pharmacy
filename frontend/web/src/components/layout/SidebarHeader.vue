<template>
  <q-list>
    <q-item-label header>
      <q-avatar size="32px" class="q-mr-sm">
        <q-icon name="local_pharmacy" size="18px" color="primary" />
      </q-avatar>
      <span class="q-mini-drawer-hide">Q-Pharmacy</span>
      <q-btn
        v-if="!uiStore.miniState"
        flat
        dense
        round
        icon="chevron_left"
        @click="handleToggleMiniState"
        class="float-right"
      />
    </q-item-label>
    
    <!-- Toggle button for mini state -->
    <q-item 
      v-if="uiStore.miniState" 
      clickable 
      @click="handleToggleMiniState"
      class="text-center q-pa-sm"
    >
      <q-item-section>
        <q-btn
          flat
          dense
          round
          icon="chevron_right"
          size="sm"
        />
      </q-item-section>
      <q-tooltip 
        anchor="center right" 
        self="center left" 
        :offset="[10, 0]"
      >
        Expand Menu
      </q-tooltip>
    </q-item>
  </q-list>
</template>

<script setup>
import { useUIStore } from 'src/stores'

const uiStore = useUIStore()

// Methods
const handleToggleMiniState = () => {
  uiStore.toggleMiniState()
}
</script>

<style lang="scss" scoped>
// Sidebar header specific styles can be added here if needed
</style>
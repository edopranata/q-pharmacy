<template>
  <q-btn-dropdown
    :icon="themeStore.themeIcon"
    flat
    :color="themeStore.isCurrentlyDark ? 'white' : ''"
    :tooltip="themeStore.themeLabel"
    class="theme-toggle-btn"
  >
    <q-list>
      <q-item-label header class="text-weight-medium">
        Theme Settings
      </q-item-label>
      
      <q-separator />
      
      <q-item
        v-for="option in themeOptions"
        :key="option.value"
        clickable
        v-close-popup
        :active="themeStore.currentTheme === option.value"
        active-class="bg-primary text-white"
        @click="themeStore.setTheme(option.value)"
        class="theme-option-item"
      >
        <q-item-section avatar>
          <q-icon :name="option.icon" />
        </q-item-section>
        
        <q-item-section>
          <q-item-label>{{ option.label }}</q-item-label>
          <q-item-label caption class="text-caption">
            {{ option.description }}
          </q-item-label>
        </q-item-section>
        
        <q-item-section side v-if="themeStore.currentTheme === option.value">
          <q-icon name="check" color="positive" />
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useThemeStore } from 'src/stores'

// Store
const themeStore = useThemeStore()

// Computed
const themeOptions = computed(() => themeStore.getThemeOptions())

// Lifecycle
onMounted(() => {
  // Initialize theme if not already done
  if (!themeStore.currentTheme) {
    themeStore.initializeTheme()
  }
})
</script>

<style lang="scss" scoped>
.theme-toggle-btn {
  transition: all 0.3s ease;
  
  &:hover {
    transform: scale(1.05);
  }
}

.theme-option-item {
  min-height: 56px;
  
  &:hover {
    background-color: var(--theme-bg-tertiary);
  }
  
  .q-item__section--avatar {
    min-width: 40px;
  }
}

// Dark mode specific styles
:deep(.q-btn-dropdown__arrow) {
  transition: color 0.3s ease;
}

// Responsive adjustments
@media (max-width: 600px) {
  .theme-toggle-btn {
    :deep(.q-btn__content) {
      padding: 0 8px;
    }
  }
}
</style>
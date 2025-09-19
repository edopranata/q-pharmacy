<template>
  <div class="auth-features">
    <div 
      v-for="feature in features" 
      :key="feature.id"
      class="feature-item"
    >
      <q-icon :name="feature.icon" size="24px" color="white" />
      <span class="feature-text">{{ feature.text }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Define features data
const features = ref([
  {
    id: 1,
    icon: 'inventory_2',
    text: 'Manajemen Stok Otomatis'
  },
  {
    id: 2,
    icon: 'receipt_long',
    text: 'Sistem Kasir Terintegrasi'
  },
  {
    id: 3,
    icon: 'analytics',
    text: 'Laporan & Analitik'
  }
])
</script>

<style lang="scss" scoped>
.auth-features {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-lg);
  
  @media (max-width: 768px) {
    flex-direction: row;
    justify-content: center;
    flex-wrap: wrap;
    gap: var(--spacing-md);
  }
  
  @media (max-width: 480px) {
    display: none;
  }
}

.feature-item {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  font-size: var(--font-size-base);
  color: rgba(255, 255, 255, 0.9);
  font-weight: var(--font-weight-medium);
  padding: var(--spacing-md);
  background: rgba(255, 255, 255, 0.1);
  border-radius: var(--border-radius-md);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: var(--transition-normal);
  
  &:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
  }
  
  @media (max-width: 768px) {
    flex-direction: column;
    text-align: center;
    gap: var(--spacing-sm);
    font-size: var(--font-size-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    min-width: 140px;
  }
}

.feature-text {
  line-height: var(--line-height-normal);
  
  @media (max-width: 768px) {
    font-size: var(--font-size-xs);
  }
}
</style>
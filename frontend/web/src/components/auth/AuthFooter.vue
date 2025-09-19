<template>
  <div class="auth-footer">
    <div class="footer-links">
      <a 
        v-for="link in footerLinks" 
        :key="link.id"
        :href="link.href" 
        class="footer-link"
        @click.prevent="handleLinkClick(link)"
      >
        {{ link.text }}
      </a>
    </div>
    <div class="footer-copyright">
      <p>&copy; {{ currentYear }} Q-Pharmacy. All rights reserved.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()

// Footer links data
const footerLinks = ref([
  {
    id: 1,
    text: 'Tentang Kami',
    href: '#about',
    action: 'about'
  },
  {
    id: 2,
    text: 'Bantuan',
    href: '#help',
    action: 'help'
  },
  {
    id: 3,
    text: 'Kebijakan Privasi',
    href: '#privacy',
    action: 'privacy'
  }
])

// Computed properties
const currentYear = computed(() => new Date().getFullYear())

// Methods
const handleLinkClick = (link) => {
  // Handle different link actions
  switch (link.action) {
    case 'about':
      $q.notify({
        message: 'Halaman Tentang Kami akan segera tersedia',
        type: 'info',
        position: 'top'
      })
      break
    case 'help':
      $q.notify({
        message: 'Halaman Bantuan akan segera tersedia',
        type: 'info',
        position: 'top'
      })
      break
    case 'privacy':
      $q.notify({
        message: 'Halaman Kebijakan Privasi akan segera tersedia',
        type: 'info',
        position: 'top'
      })
      break
    default:
      console.log('Link clicked:', link.text)
  }
}
</script>

<style lang="scss" scoped>
.auth-footer {
  padding: var(--spacing-lg) var(--spacing-xl);
  text-align: center;
  z-index: 2;
  background: rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin-top: auto;
  
  @media (max-width: 768px) {
    padding: var(--spacing-md) var(--spacing-lg);
  }
  
  @media (max-width: 480px) {
    padding: var(--spacing-sm) var(--spacing-md);
  }
}

.footer-links {
  display: flex;
  gap: var(--spacing-lg);
  margin-bottom: var(--spacing-md);
  justify-content: center;
  flex-wrap: wrap;
  
  @media (max-width: 768px) {
    gap: var(--spacing-md);
  }
  
  @media (max-width: 480px) {
    flex-direction: column;
    gap: var(--spacing-sm);
  }
}

.footer-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: var(--font-size-sm);
  transition: all var(--transition-normal);
  padding: var(--spacing-xs) var(--spacing-sm);
  border-radius: var(--border-radius-sm);
  font-weight: var(--font-weight-normal);
  cursor: pointer;
  
  &:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
  }
  
  &:active {
    transform: translateY(0);
  }
}

.footer-copyright {
  color: rgba(255, 255, 255, 0.6);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-normal);
  
  p {
    margin: 0;
    line-height: var(--line-height-normal);
  }
}
</style>
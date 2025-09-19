<template>
  <q-btn-dropdown
    flat
    dense
  >
    <template v-slot:label>
      <div class="user-info">
        <q-avatar size="28px">
          <img :src="userAvatarUrl" alt="User" />
        </q-avatar>
        <span class="q-ml-sm">{{ authStore.user?.name }}</span>
      </div>
    </template>
    
    <q-list>
      <q-item clickable v-close-popup :to="'/app/profile'">
        <q-item-section avatar>
          <q-icon name="account_circle" />
        </q-item-section>
        <q-item-section>
          <q-item-label>Profile</q-item-label>
        </q-item-section>
      </q-item>
      
      <q-item clickable v-close-popup>
        <q-item-section avatar>
          <q-icon name="settings" />
        </q-item-section>
        <q-item-section>
          <q-item-label>Pengaturan</q-item-label>
        </q-item-section>
      </q-item>
      
      <q-separator />
      
      <q-item clickable v-close-popup @click="handleLogout">
        <q-item-section avatar>
          <q-icon name="logout" color="negative" />
        </q-item-section>
        <q-item-section>
          <q-item-label class="text-negative">Logout</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores'
import { Notify } from 'quasar'

const router = useRouter()
const authStore = useAuthStore()

// Computed properties
const userAvatarUrl = computed(() => {
  if (authStore.user?.avatar) {
    return authStore.user.avatar
  }
  // Generate avatar based on user name
  const name = authStore.user?.name || 'User'
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1976d2&color=fff&size=128`
})

// Methods
const handleLogout = async () => {
  try {
    await authStore.logout()
    Notify.create({
      type: 'positive',
      message: 'Logged out successfully'
    })
    router.push('/auth/login')
  } catch {
    Notify.create({
      type: 'negative',
      message: 'Logout failed'
    })
  }
}
</script>

<style lang="scss" scoped>
.user-info {
  display: flex;
  align-items: center;
}
</style>
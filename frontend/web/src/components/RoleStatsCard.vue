<template>
  <div class="row q-col-gutter-md">
    <div class="col-12 col-md-3">
      <q-card class="bg-primary text-white">
        <q-card-section>
          <div class="text-h6">Total Roles</div>
          <div class="text-h4">
            <q-skeleton v-if="loading" type="text" width="60px" />
            <span v-else>{{ stats.total_roles || 0 }}</span>
          </div>
          <div class="text-caption">Roles dalam sistem</div>
        </q-card-section>
      </q-card>
    </div>
    
    <div class="col-12 col-md-3">
      <q-card class="bg-secondary text-white">
        <q-card-section>
          <div class="text-h6">Active Roles</div>
          <div class="text-h4">
            <q-skeleton v-if="loading" type="text" width="60px" />
            <span v-else>{{ stats.active_roles || 0 }}</span>
          </div>
          <div class="text-caption">Roles yang digunakan</div>
        </q-card-section>
      </q-card>
    </div>
    
    <div class="col-12 col-md-3">
      <q-card class="bg-positive text-white">
        <q-card-section>
          <div class="text-h6">Total Permissions</div>
          <div class="text-h4">
            <q-skeleton v-if="loading" type="text" width="60px" />
            <span v-else>{{ stats.total_permissions || 0 }}</span>
          </div>
          <div class="text-caption">Permissions tersedia</div>
        </q-card-section>
      </q-card>
    </div>
    
    <div class="col-12 col-md-3">
      <q-card class="bg-info text-white">
        <q-card-section>
          <div class="text-h6">Assigned Users</div>
          <div class="text-h4">
            <q-skeleton v-if="loading" type="text" width="60px" />
            <span v-else>{{ stats.total_assigned_users || 0 }}</span>
          </div>
          <div class="text-caption">Users dengan role</div>
        </q-card-section>
      </q-card>
    </div>
  </div>
  
  <!-- Error State -->
  <div v-if="error" class="q-mt-md">
    <q-banner class="bg-negative text-white" rounded>
      <template v-slot:avatar>
        <q-icon name="error" />
      </template>
      <div>
        <div class="text-subtitle1">Gagal memuat statistik role</div>
        <div class="text-caption">{{ error }}</div>
      </div>
      <template v-slot:action>
        <q-btn 
          flat 
          color="white" 
          label="Coba Lagi" 
          @click="$emit('retry')"
        />
      </template>
    </q-banner>
  </div>
</template>

<script setup>
defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

defineEmits(['retry'])
</script>

<style scoped>
.q-card {
  transition: transform 0.2s ease;
}

.q-card:hover {
  transform: translateY(-2px);
}
</style>
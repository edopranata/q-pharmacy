<template>
  <div v-if="!loading" class="row q-col-gutter-md">
    <!-- Total Users -->
    <div class="col-12 col-md-3">
      <q-card class="bg-primary text-white">
        <q-card-section>
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Total Users</div>
              <div class="text-h4">{{ formatNumber(stats.total_user || stats.total_users || 0) }}</div>
              <div class="text-caption">Registered users</div>
            </div>
            <div class="col-auto">
              <q-icon name="people" size="48px" class="text-white" style="opacity: 0.7" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Active Users -->
    <div class="col-12 col-md-3">
      <q-card class="bg-secondary text-white">
        <q-card-section>
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Active Users</div>
              <div class="text-h4">{{ formatNumber(stats.active_user || stats.active_users || 0) }}</div>
              <div class="text-caption">Currently active</div>
            </div>
            <div class="col-auto">
              <q-icon name="verified_user" size="48px" class="text-white" style="opacity: 0.7" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Today Login -->
    <div class="col-12 col-md-3">
      <q-card class="bg-positive text-white">
        <q-card-section>
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Today Login</div>
              <div class="text-h4">{{ formatNumber(stats.today_login || 0) }}</div>
              <div class="text-caption">Logged in today</div>
            </div>
            <div class="col-auto">
              <q-icon name="login" size="48px" class="text-white" style="opacity: 0.7" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Total Roles -->
    <div class="col-12 col-md-3">
      <q-card class="bg-info text-white">
        <q-card-section>
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Total Roles</div>
              <div class="text-h4">{{ formatNumber(stats.roles_count || 0) }}</div>
              <div class="text-caption">Available roles</div>
            </div>
            <div class="col-auto">
              <q-icon name="admin_panel_settings" size="48px" class="text-white" style="opacity: 0.7" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </div>

  <!-- Loading overlay -->
  <div v-else-if="loading" class="row q-col-gutter-md">
    <div class="col-12 col-md-3" v-for="i in 4" :key="i">
      <q-card>
        <q-card-section>
          <q-skeleton type="text" class="text-h6" />
          <q-skeleton type="text" class="text-h4" />
          <q-skeleton type="text" class="text-caption" />
        </q-card-section>
      </q-card>
    </div>
  </div>

  <!-- Error state -->
  <div v-if="error && !loading" class="row q-col-gutter-md">
    <div class="col-12">
      <q-card class="bg-negative text-white">
        <q-card-section>
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Error Loading Statistics</div>
              <div class="text-body2">{{ error }}</div>
            </div>
            <div class="col-auto">
              <q-btn 
                flat 
                round 
                icon="refresh" 
                @click="$emit('retry')"
                class="text-white"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>
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

// Helper function to format numbers
const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}
</script>

<style scoped>
.q-card {
  min-height: 120px;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.q-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Dark mode support */
.body--dark .q-card {
  border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .q-card {
    min-height: 100px;
  }
  
  .text-h4 {
    font-size: 1.5rem;
  }
  
  .q-icon {
    font-size: 36px !important;
  }
}
</style>
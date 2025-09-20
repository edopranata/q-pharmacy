<template>
  <div class="q-pa-md">
    <div class="text-h5 q-mb-md">Stats Card Examples</div>
    
    <!-- Basic Stats Row -->
    <div class="text-h6 q-mb-sm">Basic Stats</div>
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="1250"
          label="Total Users"
          icon="group"
          variant="primary"
          :loading="loading"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="850"
          label="Active Users"
          icon="verified_user"
          variant="success"
          :loading="loading"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="45"
          label="Today Login"
          icon="login"
          variant="info"
          :loading="loading"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="12"
          label="Total Roles"
          icon="admin_panel_settings"
          variant="warning"
          :loading="loading"
        />
      </div>
    </div>
    
    <!-- Stats with Trends -->
    <div class="text-h6 q-mb-sm">Stats with Trends</div>
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="2500000"
          label="Revenue"
          icon="attach_money"
          variant="success"
          format-type="currency"
          :show-trend="true"
          trend="12.5"
          trend-direction="up"
          subtitle="This month"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="85.7"
          label="Conversion Rate"
          icon="trending_up"
          variant="info"
          format-type="percentage"
          :show-trend="true"
          trend="3.2"
          trend-direction="up"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="156"
          label="Bounce Rate"
          icon="trending_down"
          variant="warning"
          format-type="percentage"
          :show-trend="true"
          trend="5.1"
          trend-direction="down"
        />
      </div>
    </div>
    
    <!-- Custom Formatted Stats -->
    <div class="text-h6 q-mb-sm">Custom Formatting</div>
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          value="99.9"
          label="Uptime"
          icon="cloud_done"
          variant="success"
          :custom-format="(val) => `${val}%`"
          format-type="custom"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="1500000"
          label="Storage Used"
          icon="storage"
          variant="info"
          :custom-format="formatBytes"
          format-type="custom"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          value="24/7"
          label="Support"
          icon="support_agent"
          variant="primary"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="4.8"
          label="Rating"
          icon="star"
          variant="warning"
          :custom-format="(val) => `${val}/5.0`"
          format-type="custom"
        >
          <template #footer>
            <div class="row items-center">
              <q-rating 
                v-model="rating" 
                readonly 
                size="sm" 
                color="orange"
              />
            </div>
          </template>
        </StatsCard>
      </div>
    </div>
    
    <!-- Loading States -->
    <div class="text-h6 q-mb-sm">Loading States</div>
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-3">
        <StatsCard
          :value="0"
          label="Loading Example"
          icon="hourglass_empty"
          variant="primary"
          :loading="true"
        />
      </div>
    </div>
    
    <!-- Controls -->
    <div class="q-mt-lg">
      <q-btn 
        @click="toggleLoading" 
        :label="loading ? 'Stop Loading' : 'Start Loading'"
        color="primary"
        class="q-mr-sm"
      />
      <q-btn 
        @click="refreshData" 
        label="Refresh Data"
        color="secondary"
        outline
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import StatsCard from './StatsCard.vue'

// Reactive data
const loading = ref(false)
const rating = ref(4.8)

// Methods
const toggleLoading = () => {
  loading.value = !loading.value
}

const refreshData = () => {
  loading.value = true
  setTimeout(() => {
    loading.value = false
  }, 2000)
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
/* Additional styling if needed */
</style>
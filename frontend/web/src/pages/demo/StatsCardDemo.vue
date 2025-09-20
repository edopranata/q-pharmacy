<template>
  <q-page class="q-pa-md">
    <div class="row justify-between items-center q-mb-lg">
      <div>
        <div class="text-h4 q-mb-sm">Stats Card Demo</div>
        <div class="text-subtitle1 text-grey-6">
          Demonstrasi komponen StatsCard dengan berbagai konfigurasi dan dukungan tema
        </div>
      </div>
      
      <!-- Theme Toggle -->
      <q-btn
        :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'"
        :label="$q.dark.isActive ? 'Light Mode' : 'Dark Mode'"
        @click="$q.dark.toggle()"
        color="primary"
        outline
      />
    </div>

    <!-- Permission Stats (from PermissionDisplayPage) -->
    <div class="q-mb-xl">
      <div class="text-h5 q-mb-md">Permission Management Stats</div>
      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="mockPermissionData.totalPermissions"
            label="Total Permission"
            icon="security"
            variant="primary"
            :loading="loading"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="mockPermissionData.userPermissions"
            label="Permission Saya"
            icon="verified_user"
            variant="success"
            :loading="loading"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="mockPermissionData.totalCategories"
            label="Kategori"
            icon="category"
            variant="info"
            :loading="loading"
          />
        </div>
      </div>
    </div>

    <!-- Business Stats with Trends -->
    <div class="q-mb-xl">
      <div class="text-h5 q-mb-md">Business Analytics</div>
      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockBusinessData.revenue"
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
        
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockBusinessData.orders"
            label="Total Orders"
            icon="shopping_cart"
            variant="primary"
            :show-trend="true"
            trend="8.3"
            trend-direction="up"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockBusinessData.customers"
            label="Active Customers"
            icon="people"
            variant="info"
            :show-trend="true"
            trend="2.1"
            trend-direction="down"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockBusinessData.conversionRate"
            label="Conversion Rate"
            icon="trending_up"
            variant="warning"
            format-type="percentage"
            :show-trend="true"
            trend="5.7"
            trend-direction="up"
          />
        </div>
      </div>
    </div>

    <!-- System Performance -->
    <div class="q-mb-xl">
      <div class="text-h5 q-mb-md">System Performance</div>
      <div class="row q-col-gutter-md">
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
            :value="mockSystemData.responseTime"
            label="Response Time"
            icon="speed"
            variant="info"
            :custom-format="(val) => `${val}ms`"
            format-type="custom"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockSystemData.storageUsed"
            label="Storage Used"
            icon="storage"
            variant="warning"
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
      </div>
    </div>

    <!-- Custom Formatted Stats -->
    <div class="q-mb-xl">
      <div class="text-h5 q-mb-md">Custom Formatting Examples</div>
      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="4.8"
            label="App Rating"
            icon="star"
            variant="warning"
            :custom-format="(val) => `${val}/5.0`"
            format-type="custom"
          >
            <template #footer>
              <div class="row items-center justify-center">
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
        
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="mockCustomData.temperature"
            label="Server Temperature"
            icon="thermostat"
            variant="info"
            :custom-format="(val) => `${val}°C`"
            format-type="custom"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-4">
          <StatsCard
            :value="mockCustomData.batteryLevel"
            label="Battery Level"
            icon="battery_full"
            variant="success"
            format-type="percentage"
          >
            <template #side>
              <q-circular-progress
                :value="mockCustomData.batteryLevel"
                size="40px"
                :thickness="0.2"
                color="positive"
                track-color="grey-3"
                class="q-ma-md"
              />
            </template>
          </StatsCard>
        </div>
      </div>
    </div>

    <!-- Loading States Demo -->
    <div class="q-mb-xl">
      <div class="text-h5 q-mb-md">Loading States</div>
      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="0"
            label="Loading Example"
            icon="hourglass_empty"
            variant="primary"
            :loading="true"
          />
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
          <StatsCard
            :value="mockBusinessData.revenue"
            label="Simulated Loading"
            icon="sync"
            variant="info"
            format-type="currency"
            :loading="simulatedLoading"
          />
        </div>
      </div>
    </div>

    <!-- Controls -->
    <div class="q-mt-xl">
      <q-card class="q-pa-md">
        <div class="text-h6 q-mb-md">Demo Controls</div>
        <div class="row q-col-gutter-md items-center">
          <div class="col-auto">
            <q-btn 
              @click="toggleLoading" 
              :label="loading ? 'Stop Loading' : 'Start Loading'"
              color="primary"
              :icon="loading ? 'stop' : 'play_arrow'"
            />
          </div>
          
          <div class="col-auto">
            <q-btn 
              @click="refreshData" 
              label="Refresh Data"
              color="secondary"
              outline
              icon="refresh"
            />
          </div>
          
          <div class="col-auto">
            <q-btn 
              @click="toggleSimulatedLoading" 
              :label="simulatedLoading ? 'Stop Simulated Loading' : 'Start Simulated Loading'"
              color="accent"
              outline
              :icon="simulatedLoading ? 'pause' : 'play_circle'"
            />
          </div>
        </div>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, reactive } from 'vue'
import StatsCard from 'src/components/common/StatsCard.vue'

// Reactive data
const loading = ref(false)
const simulatedLoading = ref(false)
const rating = ref(4.8)

// Mock data
const mockPermissionData = reactive({
  totalPermissions: 156,
  userPermissions: 89,
  totalCategories: 12
})

const mockBusinessData = reactive({
  revenue: 2500000,
  orders: 1250,
  customers: 850,
  conversionRate: 85.7
})

const mockSystemData = reactive({
  responseTime: 45,
  storageUsed: 1500000000 // bytes
})

const mockCustomData = reactive({
  temperature: 42,
  batteryLevel: 87
})

// Methods
const toggleLoading = () => {
  loading.value = !loading.value
}

const toggleSimulatedLoading = () => {
  simulatedLoading.value = !simulatedLoading.value
}

const refreshData = () => {
  loading.value = true
  
  // Simulate API call
  setTimeout(() => {
    // Update mock data with random values
    mockBusinessData.revenue = Math.floor(Math.random() * 5000000) + 1000000
    mockBusinessData.orders = Math.floor(Math.random() * 2000) + 500
    mockBusinessData.customers = Math.floor(Math.random() * 1500) + 300
    mockBusinessData.conversionRate = Math.floor(Math.random() * 30) + 70
    
    mockSystemData.responseTime = Math.floor(Math.random() * 100) + 20
    mockSystemData.storageUsed = Math.floor(Math.random() * 2000000000) + 500000000
    
    mockCustomData.temperature = Math.floor(Math.random() * 20) + 35
    mockCustomData.batteryLevel = Math.floor(Math.random() * 40) + 60
    
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
/* Additional demo-specific styling */
.q-page {
  max-width: 1200px;
  margin: 0 auto;
}
</style>
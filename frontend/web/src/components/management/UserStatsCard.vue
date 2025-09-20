<template>
  <div class="row q-gutter-md">
    <!-- Total Users Card -->
    <div class="col-12 col-sm-6 col-md-3">
      <q-card class="card card-stats card-primary hover-lift">
        <q-card-section class="row items-center no-wrap">
          <div class="col">
            <div class="card-icon">
              <q-icon name="group" size="32px" />
            </div>
            <div class="card-content">
              <div class="card-value">{{ formatNumber(stats.total_user) }}</div>
              <div class="card-label">Total Pengguna</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Active Users Card -->
    <div class="col-12 col-sm-6 col-md-3">
      <q-card class="card card-stats card-success hover-lift">
        <q-card-section class="row items-center no-wrap">
          <div class="col">
            <div class="card-icon">
              <q-icon name="verified_user" size="32px" />
            </div>
            <div class="card-content">
              <div class="card-value">{{ formatNumber(stats.active_user) }}</div>
              <div class="card-label">Pengguna Aktif</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Today's Login Card -->
    <div class="col-12 col-sm-6 col-md-3">
      <q-card class="card card-stats card-info hover-lift">
        <q-card-section class="row items-center no-wrap">
          <div class="col">
            <div class="card-icon">
              <q-icon name="login" size="32px" />
            </div>
            <div class="card-content">
              <div class="card-value">{{ formatNumber(stats.today_login) }}</div>
              <div class="card-label">Login Hari Ini</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Total Roles Card -->
    <div class="col-12 col-sm-6 col-md-3">
      <q-card class="card card-stats card-warning hover-lift">
        <q-card-section class="row items-center no-wrap">
          <div class="col">
            <div class="card-icon">
              <q-icon name="admin_panel_settings" size="32px" />
            </div>
            <div class="card-content">
              <div class="card-value">{{ formatNumber(stats.roles_count) }}</div>
              <div class="card-label">Total Peran</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

// Props
defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_user: 0,
      active_user: 0,
      today_login: 0,
      roles_count: 0
    })
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Methods
const formatNumber = (value) => {
  if (value === null || value === undefined) return '0'
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>

<style lang="scss" scoped>
// Component-specific styles only
// Global card styles are now handled by global classes

.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-content {
  text-align: left;
}

.card-value {
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 4px;
}

.card-label {
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

// Responsive adjustments
@media (max-width: 599px) {
  .card-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 12px;
    
    .q-icon {
      font-size: 24px !important;
    }
  }
  
  .card-value {
    font-size: 1.5rem;
  }
  
  .card-label {
    font-size: 0.75rem;
  }
}
</style>
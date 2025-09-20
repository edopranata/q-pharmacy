<template>
  <q-card 
    :class="cardClasses"
    class="card card-stats hover-lift"
  >
    <q-card-section class="row items-center no-wrap">
      <div class="col">
        <!-- Icon Section -->
        <div v-if="icon" class="card-icon">
          <q-icon :name="icon" :size="iconSize" />
        </div>
        
        <!-- Content Section -->
        <div class="card-content">
          <!-- Value with loading state -->
          <div class="card-value">
            <q-skeleton v-if="loading" type="text" width="60px" />
            <span v-else>{{ formattedValue }}</span>
          </div>
          
          <!-- Label -->
          <div class="card-label">{{ label }}</div>
          
          <!-- Optional subtitle -->
          <div v-if="subtitle" class="card-subtitle">{{ subtitle }}</div>
        </div>
      </div>
      
      <!-- Optional side content -->
      <div v-if="$slots.side || showTrend" class="col-auto">
        <slot name="side">
          <!-- Trend indicator -->
          <div v-if="showTrend && trend" class="trend-indicator">
            <q-icon 
              :name="trendIcon" 
              :class="trendClass"
              size="sm"
            />
            <span :class="trendClass" class="text-caption">{{ trend }}%</span>
          </div>
        </slot>
      </div>
    </q-card-section>
    
    <!-- Optional footer slot -->
    <q-card-section v-if="$slots.footer" class="q-pt-none">
      <slot name="footer" />
    </q-card-section>
  </q-card>
</template>

<script setup>
import { computed } from 'vue'

// Props definition
const props = defineProps({
  // Main content
  value: {
    type: [Number, String],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  
  // Visual styling
  icon: {
    type: String,
    default: ''
  },
  iconSize: {
    type: String,
    default: '32px'
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'info', 'secondary'].includes(value)
  },
  
  // States
  loading: {
    type: Boolean,
    default: false
  },
  
  // Trend indicator
  showTrend: {
    type: Boolean,
    default: false
  },
  trend: {
    type: [Number, String],
    default: null
  },
  trendDirection: {
    type: String,
    default: 'up',
    validator: (value) => ['up', 'down', 'neutral'].includes(value)
  },
  
  // Formatting
  formatType: {
    type: String,
    default: 'number',
    validator: (value) => ['number', 'currency', 'percentage', 'custom'].includes(value)
  },
  customFormat: {
    type: Function,
    default: null
  }
})

// Computed properties
const cardClasses = computed(() => {
  return `card-${props.variant}`
})

const formattedValue = computed(() => {
  if (props.loading) return ''
  
  const value = props.value
  
  if (props.customFormat && typeof props.customFormat === 'function') {
    return props.customFormat(value)
  }
  
  switch (props.formatType) {
    case 'currency':
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(value)
    
    case 'percentage':
      return `${value}%`
    
    case 'number':
    default:
      if (typeof value === 'number') {
        // Format large numbers with K, M suffixes
        if (value >= 1000000) {
          return (value / 1000000).toFixed(1) + 'M'
        } else if (value >= 1000) {
          return (value / 1000).toFixed(1) + 'K'
        }
        return new Intl.NumberFormat('id-ID').format(value)
      }
      return value
  }
})

const trendIcon = computed(() => {
  switch (props.trendDirection) {
    case 'up':
      return 'trending_up'
    case 'down':
      return 'trending_down'
    case 'neutral':
    default:
      return 'trending_flat'
  }
})

const trendClass = computed(() => {
  switch (props.trendDirection) {
    case 'up':
      return 'text-positive'
    case 'down':
      return 'text-negative'
    case 'neutral':
    default:
      return 'text-grey-6'
  }
})
</script>

<style lang="scss" scoped>
// Component-specific styles
.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  color: var(--card-stats-icon-text);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  background: var(--card-stats-icon-bg);
}

.card-content {
  text-align: left;
}

.card-value {
  font-size: var(--card-stats-value-size, 2rem);
  font-weight: var(--card-stats-value-weight, 700);
  line-height: 1.2;
  margin-bottom: 4px;
  color: var(--card-stats-value-color);
}

.card-label {
  font-size: var(--card-stats-label-size, 0.875rem);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--card-stats-label-color);
}

.card-subtitle {
  font-size: 0.75rem;
  margin-top: 4px;
  opacity: 0.8;
}

.trend-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

// Hover effects
.hover-lift {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }
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

// Dark mode support
.body--dark {
  .card-icon {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  }
  
  .hover-lift:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
  }
}
</style>
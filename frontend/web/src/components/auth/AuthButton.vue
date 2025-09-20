<template>
  <q-btn
    :type="type"
    :color="computedColor"
    :label="label"
    :size="size"
    :loading="loading"
    :disable="disable"
    :flat="variant === 'flat'"
    :outline="variant === 'outline'"
    :unelevated="variant === 'filled'"
    :no-caps="noCaps"
    :class="buttonClasses"
    @click="handleClick"
  >
    <template v-slot:default>
      <q-icon v-if="prependIcon" :name="prependIcon" class="q-mr-sm" />
      {{ label }}
      <q-icon v-if="appendIcon" :name="appendIcon" class="q-ml-sm" />
    </template>
  </q-btn>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  variant: {
    type: String,
    default: 'filled',
    validator: (value) => ['filled', 'outline', 'flat'].includes(value)
  },
  color: {
    type: String,
    default: 'primary'
  },
  size: {
    type: String,
    default: 'lg',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  },
  disable: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  noCaps: {
    type: Boolean,
    default: true
  },
  prependIcon: {
    type: String,
    default: ''
  },
  appendIcon: {
    type: String,
    default: ''
  },
  rounded: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['click'])

// Computed properties
const computedColor = computed(() => {
  if (props.variant === 'flat') {
    return props.color
  }
  return props.color
})

const buttonClasses = computed(() => {
  const classes = ['btn']
  
  // Add size class
  classes.push(`btn-${props.size}`)
  
  // Add variant class
  if (props.variant === 'outline') {
    classes.push(`btn-outline-${props.color}`)
  } else if (props.variant === 'ghost') {
    classes.push(`btn-ghost-${props.color}`)
  } else {
    classes.push(`btn-${props.color}`)
  }
  
  // Add shape classes
  if (props.rounded) {
    classes.push('btn-pill')
  }
  
  // Add width class
  if (props.fullWidth) {
    classes.push('btn-block')
  }
  
  // Add hover effects
  classes.push('hover-lift')
  
  return classes.join(' ')
})

// Methods
const handleClick = (evt) => {
  if (!props.loading && !props.disable) {
    emit('click', evt)
  }
}
</script>

<style lang="scss" scoped>
// Component-specific styles only
// Global button styles are now handled by global classes
</style>
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
  const classes = ['auth-button']
  
  if (props.fullWidth) {
    classes.push('auth-button--full-width')
  }
  
  if (props.rounded) {
    classes.push('auth-button--rounded')
  }
  
  classes.push(`auth-button--${props.variant}`)
  classes.push(`auth-button--${props.size}`)
  
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
.auth-button {
  font-family: var(--font-family-primary);
  font-weight: var(--font-weight-semibold);
  border-radius: var(--border-radius-md);
  transition: var(--transition-normal);
  position: relative;
  overflow: hidden;
  
  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: var(--transition-normal);
  }
  
  &:hover::before {
    left: 100%;
  }
  
  &:hover:not(.q-btn--disable) {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
  
  &:active:not(.q-btn--disable) {
    transform: translateY(0);
  }
}

// Full width variant
.auth-button--full-width {
  width: 100%;
}

// Rounded variant
.auth-button--rounded {
  border-radius: var(--border-radius-xl);
}

// Size variants
.auth-button--xs {
  height: 32px;
  font-size: var(--font-size-xs);
  padding: var(--spacing-xs) var(--spacing-sm);
}

.auth-button--sm {
  height: 36px;
  font-size: var(--font-size-sm);
  padding: var(--spacing-sm) var(--spacing-md);
}

.auth-button--md {
  height: 40px;
  font-size: var(--font-size-base);
  padding: var(--spacing-sm) var(--spacing-lg);
}

.auth-button--lg {
  height: 48px;
  font-size: var(--font-size-base);
  padding: var(--spacing-md) var(--spacing-xl);
}

.auth-button--xl {
  height: 56px;
  font-size: var(--font-size-lg);
  padding: var(--spacing-lg) var(--spacing-2xl);
}

// Variant styles
.auth-button--filled {
  background: linear-gradient(135deg, var(--auth-primary) 0%, var(--auth-secondary) 100%);
  border: none;
  box-shadow: var(--shadow-md);
  
  &:hover:not(.q-btn--disable) {
    background: linear-gradient(135deg, var(--auth-secondary) 0%, var(--auth-primary) 100%);
  }
}

.auth-button--outline {
  border: 2px solid var(--auth-primary);
  background: transparent;
  color: var(--auth-primary);
  
  &:hover:not(.q-btn--disable) {
    background: var(--auth-primary);
    color: white;
  }
}

.auth-button--flat {
  background: transparent;
  box-shadow: none;
  
  &:hover:not(.q-btn--disable) {
    background: rgba(var(--auth-primary-rgb), 0.1);
    transform: none;
    box-shadow: none;
  }
}

// Loading state
.auth-button.q-btn--loading {
  pointer-events: none;
  
  :deep(.q-btn__content) {
    opacity: 0.6;
  }
}

// Disabled state
.auth-button.q-btn--disable {
  opacity: 0.5;
  cursor: not-allowed;
  
  &:hover {
    transform: none;
    box-shadow: none;
  }
}

// Focus state for accessibility
.auth-button:focus-visible {
  outline: 2px solid var(--auth-accent);
  outline-offset: 2px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .auth-button--xs {
    height: 36px;
    font-size: var(--font-size-sm);
  }
  
  .auth-button--sm {
    height: 40px;
    font-size: var(--font-size-base);
  }
  
  .auth-button--md {
    height: 44px;
    font-size: var(--font-size-base);
  }
  
  .auth-button--lg {
    height: 48px;
    font-size: var(--font-size-base);
    min-height: 44px; /* Touch-friendly minimum */
  }
  
  .auth-button--xl {
    height: 52px;
    font-size: var(--font-size-lg);
  }
}

@media (max-width: 480px) {
  .auth-button {
    min-height: 48px; /* Larger touch targets on mobile */
    
    &--lg {
      height: 52px;
      font-size: var(--font-size-base);
    }
    
    &--xl {
      height: 56px;
      font-size: var(--font-size-base);
    }
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .auth-button {
    &--outline {
      border-width: 3px;
    }
    
    &:focus-visible {
      outline-width: 3px;
    }
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .auth-button {
    transition: none;
    
    &::before {
      transition: none;
    }
    
    &:hover:not(.q-btn--disable) {
      transform: none;
    }
    
    &:active:not(.q-btn--disable) {
      transform: none;
    }
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .auth-button {
    &--flat:hover:not(.q-btn--disable) {
      background: rgba(255, 255, 255, 0.1);
    }
  }
}
</style>
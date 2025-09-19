<template>
  <div class="form-group">
    <q-input
      v-model="inputValue"
      :type="computedType"
      :label="label"
      :placeholder="placeholder"
      :rules="validationRules"
      :disable="disable"
      outlined
      dense
      class="auth-input"
      :error="hasError"
      :error-message="errorMessage"
      @blur="handleBlur"
      @focus="handleFocus"
    >
      <template v-if="prependIcon" v-slot:prepend>
        <q-icon :name="prependIcon" color="grey-6" />
      </template>
      
      <template v-if="isPassword" v-slot:append>
        <q-icon
          :name="showPassword ? 'visibility_off' : 'visibility'"
          class="cursor-pointer"
          color="grey-6"
          @click="togglePassword"
        />
      </template>
      
      <template v-else-if="appendIcon" v-slot:append>
        <q-icon :name="appendIcon" color="grey-6" />
      </template>
    </q-input>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  prependIcon: {
    type: String,
    default: ''
  },
  appendIcon: {
    type: String,
    default: ''
  },
  rules: {
    type: Array,
    default: () => []
  },
  disable: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  minLength: {
    type: Number,
    default: 0
  },
  maxLength: {
    type: Number,
    default: 255
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

// Reactive data
const showPassword = ref(false)
const hasError = ref(false)
const errorMessage = ref('')
const isFocused = ref(false)

// Computed properties
const isPassword = computed(() => props.type === 'password')

const computedType = computed(() => {
  if (isPassword.value) {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})

const inputValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const validationRules = computed(() => {
  const rules = [...props.rules]
  
  // Add required rule if specified
  if (props.required) {
    rules.unshift(val => !!val || `${props.label} wajib diisi`)
  }
  
  // Add min length rule if specified
  if (props.minLength > 0) {
    rules.push(val => !val || val.length >= props.minLength || `${props.label} minimal ${props.minLength} karakter`)
  }
  
  // Add max length rule if specified
  if (props.maxLength > 0) {
    rules.push(val => !val || val.length <= props.maxLength || `${props.label} maksimal ${props.maxLength} karakter`)
  }
  
  // Add email validation for email type
  if (props.type === 'email') {
    rules.push(val => !val || isValidEmail(val) || 'Format email tidak valid')
  }
  
  // Add phone validation for tel type
  if (props.type === 'tel') {
    rules.push(val => !val || isValidPhone(val) || 'Format nomor telepon tidak valid')
  }
  
  return rules
})

// Methods
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleBlur = (evt) => {
  isFocused.value = false
  emit('blur', evt)
}

const handleFocus = (evt) => {
  isFocused.value = true
  emit('focus', evt)
}

const isValidEmail = (email) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailPattern.test(email)
}

const isValidPhone = (phone) => {
  const phonePattern = /^[+]?[0-9]{10,15}$/
  return phonePattern.test(phone.replace(/[\s\-()]/g, ''))
}

// Watch for validation
watch(inputValue, (newValue) => {
  if (newValue && validationRules.value.length > 0) {
    for (const rule of validationRules.value) {
      const result = rule(newValue)
      if (typeof result === 'string') {
        hasError.value = true
        errorMessage.value = result
        return
      }
    }
    hasError.value = false
    errorMessage.value = ''
  }
})
</script>

<style lang="scss" scoped>
.form-group {
  margin-bottom: var(--spacing-md);
}

.auth-input {
  width: 100%;
  transition: var(--transition-normal);
  
  :deep(.q-field__control) {
    border-radius: var(--border-radius-md);
    background: var(--auth-surface);
    backdrop-filter: blur(10px);
    font-family: var(--font-family-primary);
    transition: var(--transition-normal);
  }
  
  :deep(.q-field__outlined .q-field__control:before) {
    border-color: var(--auth-border);
    transition: var(--transition-fast);
  }
  
  :deep(.q-field__outlined .q-field__control:hover:before) {
    border-color: var(--auth-primary);
  }
  
  :deep(.q-field__outlined.q-field--focused .q-field__control:before) {
    border-color: var(--auth-primary);
    border-width: 2px;
  }
  
  :deep(.q-field__outlined.q-field--error .q-field__control:before) {
    border-color: var(--q-negative);
  }
  
  :deep(.q-field__input) {
    font-size: var(--font-size-base);
    line-height: var(--line-height-normal);
  }
  
  :deep(.q-field__label) {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-medium);
    color: var(--auth-text-secondary);
  }
  
  :deep(.q-field__bottom) {
    font-size: var(--font-size-xs);
    padding-top: var(--spacing-xs);
  }
}

// Focus state enhancement
.auth-input:focus-within {
  transform: translateY(-1px);
  
  :deep(.q-field__control) {
    box-shadow: var(--shadow-md);
  }
}

// Error state
.auth-input.q-field--error {
  :deep(.q-field__control) {
    background: rgba(244, 67, 54, 0.05);
  }
}

// Disabled state
.auth-input.q-field--disabled {
  opacity: 0.6;
  
  :deep(.q-field__control) {
    background: rgba(0, 0, 0, 0.05);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .auth-input {
    :deep(.q-field__input) {
      font-size: var(--font-size-sm);
    }
    
    :deep(.q-field__label) {
      font-size: var(--font-size-xs);
    }
    
    :deep(.q-field__control) {
      min-height: 44px; /* Touch-friendly size */
    }
  }
}

@media (max-width: 480px) {
  .form-group {
    margin-bottom: var(--spacing-sm);
  }
  
  .auth-input {
    :deep(.q-field__control) {
      min-height: 48px; /* Larger touch target */
    }
    
    :deep(.q-field__bottom) {
      font-size: var(--font-size-xs);
      padding-top: var(--spacing-xs);
    }
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .auth-input {
    :deep(.q-field__outlined .q-field__control:before) {
      border-width: 2px;
    }
    
    :deep(.q-field__outlined.q-field--focused .q-field__control:before) {
      border-width: 3px;
    }
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .auth-input {
    transition: none;
    
    :deep(.q-field__control) {
      transition: none;
    }
    
    :deep(.q-field__outlined .q-field__control:before) {
      transition: none;
    }
  }
  
  .auth-input:focus-within {
    transform: none;
  }
}
</style>
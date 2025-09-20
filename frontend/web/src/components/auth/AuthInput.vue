<template>
  <div class="input-group">
    <q-input
      v-model="inputValue"
      :type="computedType"
      :label="label"
      :placeholder="placeholder"
      :rules="validationRules"
      :disable="disable"
      outlined
      dense
      :class="inputClasses"
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

const inputClasses = computed(() => {
  const classes = ['input', 'input-md', 'input-outlined']
  
  if (hasError.value) {
    classes.push('input-error')
  }
  
  if (isFocused.value) {
    classes.push('input-focused')
  }
  
  if (props.disable) {
    classes.push('input-disabled')
  }
  
  return classes.join(' ')
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
// Component-specific styles only
// Global input styles are now handled by global classes

.input-group {
  margin-bottom: var(--spacing-md);
}

// Quasar-specific overrides for better integration
:deep(.q-field__control) {
  backdrop-filter: blur(10px);
}
</style>
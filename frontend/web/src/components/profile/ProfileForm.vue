<template>
  <div class="profile-form">
    <div class="text-h6 text-weight-medium q-mb-md">
      <q-icon name="person" class="q-mr-sm" />
      Informasi Profil
    </div>
    <div class="text-body2 text-grey-6 q-mb-lg">
      Perbarui informasi dasar profil Anda
    </div>

    <q-form @submit.prevent="handleSubmit" class="q-gutter-md">
      <!-- Name Field -->
      <q-input
        v-model="localFormData.name"
        label="Nama Lengkap"
        outlined
        :rules="nameRules"
        :disable="loading"
        :error="!!errors.name"
        :error-message="errors.name?.[0]"
        clearable
      >
        <template v-slot:prepend>
          <q-icon name="person" color="primary" />
        </template>
      </q-input>

      <!-- Email Field -->
      <q-input
        v-model="localFormData.email"
        type="email"
        label="Alamat Email"
        outlined
        :rules="emailRules"
        :disable="loading"
        :error="!!errors.email"
        :error-message="errors.email?.[0]"
        clearable
      >
        <template v-slot:prepend>
          <q-icon name="email" color="primary" />
        </template>
      </q-input>

      <!-- Phone Field -->
      <q-input
        v-model="localFormData.phone"
        label="Nomor Telepon"
        outlined
        :rules="phoneRules"
        :disable="loading"
        :error="!!errors.phone"
        :error-message="errors.phone?.[0]"
        clearable
        mask="####-####-####"
        placeholder="0812-3456-7890"
      >
        <template v-slot:prepend>
          <q-icon name="phone" color="primary" />
        </template>
      </q-input>

      <!-- Additional Info Section -->
      <q-separator class="q-my-lg" />
      
      <div class="text-subtitle2 text-weight-medium q-mb-md">
        Informasi Tambahan (Opsional)
      </div>

      <!-- Bio Field -->
      <q-input
        v-model="localFormData.bio"
        label="Bio"
        outlined
        type="textarea"
        rows="3"
        :disable="loading"
        :error="!!errors.bio"
        :error-message="errors.bio?.[0]"
        counter
        maxlength="500"
        placeholder="Ceritakan sedikit tentang diri Anda..."
      >
        <template v-slot:prepend>
          <q-icon name="description" color="primary" />
        </template>
      </q-input>

      <!-- Location Field -->
      <q-input
        v-model="localFormData.location"
        label="Lokasi"
        outlined
        :disable="loading"
        :error="!!errors.location"
        :error-message="errors.location?.[0]"
        clearable
        placeholder="Jakarta, Indonesia"
      >
        <template v-slot:prepend>
          <q-icon name="location_on" color="primary" />
        </template>
      </q-input>

      <!-- Form Actions -->
      <div class="row justify-end q-gutter-sm q-mt-lg">
        <q-btn
          type="button"
          color="grey-7"
          label="Reset"
          outline
          @click="handleReset"
          :disable="loading"
          class="q-px-lg"
        />
        <q-btn
          type="submit"
          color="primary"
          label="Simpan Perubahan"
          :loading="loading"
          :disable="!isFormValid"
          class="q-px-lg"
        >
          <template v-slot:loading>
            <q-spinner-hourglass class="on-left" />
            Menyimpan...
          </template>
        </q-btn>
      </div>
    </q-form>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

/**
 * ProfileForm Component
 * 
 * Handles user profile information updates with validation
 * 
 * @component
 */

// Props
const props = defineProps({
  formData: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      email: '',
      phone: '',
      bio: '',
      location: ''
    })
  },
  loading: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['submit', 'reset'])

// Local form data
const localFormData = ref({ ...props.formData })

// Watch for prop changes
watch(() => props.formData, (newData) => {
  localFormData.value = { ...newData }
}, { deep: true })

// Validation rules
const nameRules = [
  val => !!val || 'Nama lengkap wajib diisi',
  val => val.length >= 2 || 'Nama minimal 2 karakter',
  val => val.length <= 100 || 'Nama maksimal 100 karakter'
]

const emailRules = [
  val => !!val || 'Email wajib diisi',
  val => isValidEmail(val) || 'Format email tidak valid'
]

const phoneRules = [
  val => !!val || 'Nomor telepon wajib diisi',
  val => isValidPhone(val) || 'Format nomor telepon tidak valid'
]

// Validation helpers
const isValidEmail = (email) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailPattern.test(email)
}

const isValidPhone = (phone) => {
  // Remove all non-digit characters
  const cleanPhone = phone.replace(/\D/g, '')
  // Check if it's a valid Indonesian phone number (10-13 digits)
  return cleanPhone.length >= 10 && cleanPhone.length <= 13
}

// Form validation
const isFormValid = computed(() => {
  return localFormData.value.name &&
         localFormData.value.email &&
         localFormData.value.phone &&
         isValidEmail(localFormData.value.email) &&
         isValidPhone(localFormData.value.phone)
})

// Form handlers
const handleSubmit = () => {
  if (isFormValid.value) {
    emit('submit', { ...localFormData.value })
  }
}

const handleReset = () => {
  emit('reset')
}
</script>

<style lang="scss" scoped>
.profile-form {
  .q-field {
    .q-field__control {
      border-radius: 8px;
    }
  }
  
  .q-btn {
    border-radius: 8px;
    font-weight: 500;
  }
}
</style>
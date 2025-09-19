<template>
  <div class="security-form">
    <div class="text-h6 text-weight-medium q-mb-md">
      <q-icon name="security" class="q-mr-sm" />
      Keamanan Akun
    </div>
    <div class="text-body2 text-grey-6 q-mb-lg">
      Ubah password untuk menjaga keamanan akun Anda
    </div>

    <q-form @submit.prevent="handleSubmit" class="q-gutter-md">
      <!-- Current Password -->
      <q-input
        v-model="localFormData.current_password"
        :type="showCurrentPassword ? 'text' : 'password'"
        label="Password Saat Ini"
        outlined
        :rules="currentPasswordRules"
        :disable="loading"
        :error="!!errors.current_password"
        :error-message="errors.current_password?.[0]"
        clearable
      >
        <template v-slot:prepend>
          <q-icon name="lock" color="primary" />
        </template>
        <template v-slot:append>
          <q-icon
            :name="showCurrentPassword ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            color="grey-6"
            @click="showCurrentPassword = !showCurrentPassword"
          />
        </template>
      </q-input>

      <!-- New Password -->
      <q-input
        v-model="localFormData.password"
        :type="showNewPassword ? 'text' : 'password'"
        label="Password Baru"
        outlined
        :rules="newPasswordRules"
        :disable="loading"
        :error="!!errors.password"
        :error-message="errors.password?.[0]"
        @input="handlePasswordInput"
        clearable
      >
        <template v-slot:prepend>
          <q-icon name="lock_reset" color="primary" />
        </template>
        <template v-slot:append>
          <q-icon
            :name="showNewPassword ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            color="grey-6"
            @click="showNewPassword = !showNewPassword"
          />
        </template>
      </q-input>

      <!-- Password Strength Indicator -->
      <div v-if="localFormData.password" class="password-strength q-mb-md">
        <div class="row items-center q-gutter-sm">
          <div class="text-caption text-weight-medium">Kekuatan Password:</div>
          <q-chip
            :color="passwordStrength.color"
            text-color="white"
            size="sm"
            :icon="getStrengthIcon()"
          >
            {{ passwordStrength.text }}
          </q-chip>
        </div>
        
        <!-- Strength Progress Bar -->
        <q-linear-progress
          :value="passwordStrength.score / 5"
          :color="passwordStrength.color"
          size="4px"
          class="q-mt-xs"
        />
        
        <!-- Feedback -->
        <div v-if="passwordStrength.feedback" class="text-caption text-grey-6 q-mt-xs">
          {{ passwordStrength.feedback }}
        </div>
      </div>

      <!-- Confirm Password -->
      <q-input
        v-model="localFormData.password_confirmation"
        :type="showConfirmPassword ? 'text' : 'password'"
        label="Konfirmasi Password Baru"
        outlined
        :rules="confirmPasswordRules"
        :disable="loading"
        :error="!!errors.password_confirmation"
        :error-message="errors.password_confirmation?.[0]"
        clearable
      >
        <template v-slot:prepend>
          <q-icon name="check_circle" color="primary" />
        </template>
        <template v-slot:append>
          <q-icon
            :name="showConfirmPassword ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            color="grey-6"
            @click="showConfirmPassword = !showConfirmPassword"
          />
        </template>
      </q-input>

      <!-- Security Tips -->
      <q-card flat bordered class="q-mt-lg">
        <q-card-section class="q-pa-md">
          <div class="text-subtitle2 text-weight-medium q-mb-sm">
            <q-icon name="tips_and_updates" class="q-mr-sm" color="primary" />
            Tips Keamanan Password
          </div>
          <ul class="text-body2 text-grey-7 q-ma-none q-pl-md">
            <li>Gunakan minimal 8 karakter</li>
            <li>Kombinasikan huruf besar dan kecil</li>
            <li>Sertakan angka dan karakter khusus</li>
            <li>Hindari informasi pribadi yang mudah ditebak</li>
            <li>Gunakan password yang unik untuk setiap akun</li>
          </ul>
        </q-card-section>
      </q-card>

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
          label="Ubah Password"
          :loading="loading"
          :disable="!isFormValid"
          class="q-px-lg"
        >
          <template v-slot:loading>
            <q-spinner-hourglass class="on-left" />
            Mengubah...
          </template>
        </q-btn>
      </div>
    </q-form>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

/**
 * SecurityForm Component
 * 
 * Handles password changes with strength validation and security tips
 * 
 * @component
 */

// Props
const props = defineProps({
  formData: {
    type: Object,
    required: true,
    default: () => ({
      current_password: '',
      password: '',
      password_confirmation: ''
    })
  },
  loading: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  passwordStrength: {
    type: Object,
    default: () => ({
      score: 0,
      feedback: '',
      color: 'grey',
      text: ''
    })
  }
})

// Emits
const emit = defineEmits(['submit', 'reset', 'password-input'])

// Local state
const localFormData = ref({ ...props.formData })
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Watch for prop changes
watch(() => props.formData, (newData) => {
  localFormData.value = { ...newData }
}, { deep: true })

// Validation rules
const currentPasswordRules = [
  val => !!val || 'Password saat ini wajib diisi'
]

const newPasswordRules = [
  val => !!val || 'Password baru wajib diisi',
  val => val.length >= 8 || 'Password minimal 8 karakter',
  val => val !== localFormData.value.current_password || 'Password baru harus berbeda dengan password saat ini'
]

const confirmPasswordRules = [
  val => !!val || 'Konfirmasi password wajib diisi',
  val => val === localFormData.value.password || 'Password tidak sama'
]

// Form validation
const isFormValid = computed(() => {
  return localFormData.value.current_password &&
         localFormData.value.password &&
         localFormData.value.password_confirmation &&
         localFormData.value.password === localFormData.value.password_confirmation &&
         localFormData.value.password.length >= 8
})

// Password strength helpers
const getStrengthIcon = () => {
  const score = props.passwordStrength.score
  if (score >= 4) return 'shield'
  if (score >= 3) return 'security'
  if (score >= 2) return 'warning'
  return 'error'
}

// Form handlers
const handleSubmit = () => {
  if (isFormValid.value) {
    emit('submit', { ...localFormData.value })
  }
}

const handleReset = () => {
  emit('reset')
}

const handlePasswordInput = (value) => {
  emit('password-input', value)
}
</script>

<style lang="scss" scoped>
.security-form {
  .q-field {
    .q-field__control {
      border-radius: 8px;
    }
  }
  
  .q-btn {
    border-radius: 8px;
    font-weight: 500;
  }
  
  .password-strength {
    .q-linear-progress {
      border-radius: 2px;
    }
  }
}
</style>
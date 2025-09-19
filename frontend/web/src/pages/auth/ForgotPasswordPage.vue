<template>
  <div class="forgot-password-container">
    <!-- Header -->
    <div class="text-center q-mb-xl">
      <div class="forgot-password-header fade-in-up">
        <h1 class="text-h4 text-weight-bold theme-text-primary q-mb-sm">
          Lupa Password?
        </h1>
        <p class="text-h6 theme-text-secondary q-mb-none">
          Masukkan email Anda untuk reset password
        </p>
      </div>
    </div>

    <!-- Form -->
    <q-form @submit="handleForgotPassword" class="forgot-password-form fade-in-up">
      <div class="q-gutter-lg">
        <q-input
          v-model="form.email"
          type="email"
          label="Alamat Email"
          outlined
          :disable="loading"
          :rules="emailRules"
          class="forgot-password-input"
        >
          <template v-slot:prepend>
            <q-icon name="email" color="primary" />
          </template>
        </q-input>
      </div>
      <div class="q-mt-lg">
        <q-btn
          type="submit"
          label="Kirim Link Reset Password"
          color="primary"
          unelevated
          :loading="loading"
          :disable="!isFormValid"
          class="full-width q-mt-lg text-weight-bold forgot-password-submit"
        />
      </div>
    </q-form>

    <!-- Success Message -->
    <q-banner
      v-if="showSuccessMessage"
      class="bg-positive text-white q-mt-lg fade-in-up"
      rounded
    >
      <template v-slot:avatar>
        <q-icon name="check_circle" color="white" />
      </template>
      <div class="text-weight-medium">
        Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.
      </div>
    </q-banner>

    <!-- Error Message -->
    <q-banner
      v-if="errorMessage"
      class="bg-negative text-white q-mt-lg fade-in-up"
      rounded
    >
      <template v-slot:avatar>
        <q-icon name="error" color="white" />
      </template>
      <div class="text-weight-medium">
        {{ errorMessage }}
      </div>
    </q-banner>

    <!-- Divider -->
    <div class="row items-center q-my-xl">
      <q-separator class="col" />
      <span class="q-px-md text-grey-6 text-weight-medium">atau</span>
      <q-separator class="col" />
    </div>

    <!-- Footer -->
    <div class="text-center fade-in-up">
      <p class="text-body1 theme-text-secondary q-mb-sm">
        Sudah ingat password anda?
      </p>
      <q-btn
        outline
        no-caps
        color="primary"
        size="lg"
        class="full-width text-weight-bold q-px-xl hover-lift"
        label="Kembali ke login"
        @click="$router.push('/auth/login')"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()

// Reactive data
const loading = ref(false)
const showSuccessMessage = ref(false)
const errorMessage = ref('')
const form = ref({
  email: ''
})

// Validation helpers
const isValidEmail = (email) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailPattern.test(email)
}

// Form validation rules
const emailRules = [
  val => !!val || 'Email wajib diisi',
  val => isValidEmail(val) || 'Format email tidak valid'
]

// Computed properties
const isFormValid = computed(() => {
  return form.value.email && isValidEmail(form.value.email)
})

const handleForgotPassword = async () => {
  loading.value = true
  errorMessage.value = ''
  showSuccessMessage.value = false
  
  try {
    // Simulate API call - replace with actual API endpoint
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // For now, we'll show success message
    // In real implementation, call your forgot password API
    /*
    const response = await api.post('/auth/forgot-password', {
      email: form.value.email
    })
    */
    
    showSuccessMessage.value = true
    
    // Show notification
    $q.notify({
      type: 'positive',
      message: 'Link reset password berhasil dikirim!',
      position: 'top'
    })
    
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
    
    $q.notify({
      type: 'negative',
      message: 'Gagal mengirim link reset password',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss" scoped>
// Forgot password container styling
.forgot-password-container {
  width: 100%;
  max-width: 100%;
}

.forgot-password-header {
  margin-bottom: 1rem;
}

.forgot-password-form {
  margin-bottom: 1rem;
}

.forgot-password-input {
  .q-field__control {
    border-radius: 12px;
    transition: all 0.3s ease;
    
    &:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  }
  
  &.q-field--focused {
    .q-field__control {
      box-shadow: 0 4px 16px rgba(25, 118, 210, 0.2);
    }
  }
}

.forgot-password-submit {
  border-radius: 12px;
  padding: 16px;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25, 118, 210, 0.3);
  }
}

// Hover effects
.hover-lift {
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-1px);
  }
}

// Animations
.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .forgot-password-container {
    padding: 0;
  }
  
  .forgot-password-header {
    .text-h3 {
      font-size: 1.8rem;
    }
    
    .text-h6 {
      font-size: 1rem;
    }
  }
  
  .forgot-password-input {
    .q-field__control {
      border-radius: 8px;
    }
  }
  
  .forgot-password-submit {
    border-radius: 8px;
    padding: 14px;
  }
}
</style>
<template>
  <div class="login-container">
    <!-- Header -->
    <div class="text-center q-mb-xl">
      <div class="login-header fade-in-up">
        <h1 class="text-h4 text-weight-bold theme-text-primary q-mb-sm">
          Selamat Datang Kembali
        </h1>
        <p class="text-h6 theme-text-secondary q-mb-none">
          Masuk ke akun Q-Pharmacy Anda
        </p>
      </div>
    </div>

    <!-- Form -->
    <q-form @submit="handleLogin" class="login-form fade-in-up">
      <div class="q-gutter-lg">
        <q-input
          v-model="form.email"
          type="email"
          label="Alamat Email"
          outlined
          :disable="authStore.loading"
          :rules="emailRules"
          class="login-input"
        >
          <template v-slot:prepend>
            <q-icon name="email" color="primary" />
          </template>
        </q-input>

        <q-input
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          label="Password"
          outlined
          :disable="authStore.loading"
          :rules="passwordRules"
          class="login-input"
        >
          <template v-slot:prepend>
            <q-icon name="lock" color="primary" />
          </template>
          <template v-slot:append>
            <q-icon
              :name="showPassword ? 'visibility_off' : 'visibility'"
              class="cursor-pointer text-grey-6 hover-primary"
              @click="showPassword = !showPassword"
            />
          </template>
        </q-input>

        <!-- Form Options -->
        <div class="row justify-between items-center q-mt-md">
          <q-checkbox
            v-model="form.remember"
            label="Ingat saya"
            :disable="authStore.loading"
            color="primary"
            class="text-weight-medium"
          />
          <q-btn
            flat
            no-caps
            color="primary"
            label="Lupa password?"
            class="text-weight-medium hover-lift"
            @click="$router.push('/auth/forgot-password')"
          />
        </div>
      </div>
      <div class="q-mt-md">
        <q-btn
          type="submit"
          label="Masuk ke Akun"
          color="primary"
          unelevated
          :loading="authStore.loading"
          :disable="!isFormValid"
          class="full-width q-mt-lg text-weight-bold login-submit"
        />
      </div>
    </q-form>

    <!-- Divider -->
    <div class="row items-center q-my-xl">
      <q-separator class="col" />
      <span class="q-px-md text-grey-6 text-weight-medium">atau</span>
      <q-separator class="col" />
    </div>

    <!-- Footer -->
    <div class="text-center fade-in-up">
      <p class="text-body1 theme-text-secondary q-mb-sm">
        Belum punya akun?
      </p>
      <q-btn
        outline
        no-caps
        color="primary"
        size="lg"
        class="full-width text-weight-bold q-px-xl hover-lift"
        label="Daftar Sekarang"
        @click="$router.push('/auth/register')"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const showPassword = ref(false)
const form = ref({
  email: '',
  password: '',
  remember: false
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

const passwordRules = [
  val => !!val || 'Password wajib diisi',
  val => val.length >= 6 || 'Password minimal 6 karakter'
]

// Computed properties
const isFormValid = computed(() => {
  return form.value.email && 
         form.value.password && 
         isValidEmail(form.value.email) &&
         form.value.password.length >= 6
})

const handleLogin = async () => {
  const result = await authStore.login({
    email: form.value.email,
    password: form.value.password,
    remember: form.value.remember
  })
  
  if (result.success) {
    // Redirect to app dashboard
    router.push('/app/dashboard')
    
  }
}
</script>

<style lang="scss" scoped>
// Login container styling
.login-container {
  width: 100%;
  max-width: 100%;
}

.login-header {
  margin-bottom: 1rem;
}

.login-form {
  margin-bottom: 1rem;
}

.login-input {
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

.login-submit {
  border-radius: 12px;
  padding: 16px;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25, 118, 210, 0.3);
  }
}

// Hover effects
.hover-primary {
  transition: color 0.3s ease;
  
  &:hover {
    color: var(--q-primary) !important;
  }
}

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
  .login-container {
    padding: 0;
  }
  
  .login-header {
    .text-h3 {
      font-size: 1.8rem;
    }
    
    .text-h6 {
      font-size: 1rem;
    }
  }
  
  .login-input {
    .q-field__control {
      border-radius: 8px;
    }
  }
  
  .login-submit {
    border-radius: 8px;
    padding: 14px;
  }
}
</style>
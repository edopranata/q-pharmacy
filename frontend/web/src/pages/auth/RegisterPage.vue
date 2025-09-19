<template>
  <div class="register-container">
    <!-- Header -->
    <div class="text-center q-mb-xl">
      <div class="register-header fade-in-up">
        <h1 class="text-h4 text-weight-bold theme-text-primary q-mb-sm">
          Bergabung dengan Q-Pharmacy
        </h1>
        <p class="text-h6 theme-text-secondary q-mb-none">
          Buat akun baru untuk memulai
        </p>
      </div>
    </div>

    <!-- Form -->
    <q-form @submit="handleRegister" class="register-form fade-in-up">
      <div class="q-gutter-lg">
        <!-- Personal Information -->
        <div class="form-section">
          <h3 class="text-h6 text-weight-bold theme-text-primary q-mb-md">
            Informasi Pribadi
          </h3>
          
          <div class="q-gutter-md q-mt-md">
            <q-input
              v-model="form.name"
              type="text"
              label="Nama Lengkap"
              outlined
              :disable="authStore.loading"
              :rules="nameRules"
              class="register-input"
    
            >
              <template v-slot:prepend>
                <q-icon name="person" color="primary" />
              </template>
            </q-input>

            <q-input
              v-model="form.email"
              type="email"
              label="Alamat Email"
              outlined
              :disable="authStore.loading"
              :rules="emailRules"
              class="register-input"
              size="lg"
            >
              <template v-slot:prepend>
                <q-icon name="email" color="primary" />
              </template>
            </q-input>

            <q-input
              v-model="form.phone"
              type="tel"
              label="Nomor Telepon"
              outlined
              :disable="authStore.loading"
              :rules="phoneRules"
              class="register-input"
              size="lg"
              hint="Contoh: 08123456789"
            >
              <template v-slot:prepend>
                <q-icon name="phone" color="primary" />
              </template>
            </q-input>
          </div>
        </div>

        <!-- Account Security -->
        <div class="form-section">
          <h3 class="text-h6 text-weight-bold theme-text-primary q-mb-md">
            Keamanan Akun
          </h3>
          
          <div class="q-gutter-md q-mt-md">
            <q-input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              label="Password"
              outlined
              :disable="authStore.loading"
              :rules="passwordRules"
              class="register-input"
              size="lg"
              hint="Minimal 8 karakter"
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

            <q-input
              v-model="form.password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              label="Konfirmasi Password"
              outlined
              :disable="authStore.loading"
              :rules="passwordConfirmRules"
              class="register-input"
              size="lg"
            >
              <template v-slot:prepend>
                <q-icon name="lock" color="primary" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="showPasswordConfirm ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer text-grey-6 hover-primary"
                  @click="showPasswordConfirm = !showPasswordConfirm"
                />
              </template>
            </q-input>

            <q-select
              v-model="form.role"
              :options="roleOptions"
              label="Peran dalam Sistem"
              outlined
              :disable="authStore.loading"
              :rules="roleRules"
              class="register-input"
              size="lg"
            >
              <template v-slot:prepend>
                <q-icon name="work" color="primary" />
              </template>
            </q-select>
          </div>
        </div>

        <!-- Terms Agreement -->
        <div class="form-section">
          <q-checkbox
            v-model="form.terms"
            :disable="authStore.loading"
            color="primary"
            class="terms-checkbox"
          >
            <span class="text-body1">
              Saya setuju dengan
              <a href="#" class="text-primary text-weight-medium hover-underline">Syarat & Ketentuan</a>
              dan
              <a href="#" class="text-primary text-weight-medium hover-underline">Kebijakan Privasi</a>
            </span>
          </q-checkbox>
        </div>

        <!-- Submit Button -->
        <div class="form-section">
          <q-btn
            type="submit"
            label="Buat Akun Sekarang"
            color="primary"
            unelevated
            size="lg"
            :loading="authStore.loading"
            :disable="!isFormValid"
            class="full-width q-mt-lg text-weight-bold register-submit"
          />
        </div>
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
        Sudah punya akun?
      </p>
      <q-btn
        outline
        no-caps
        color="primary"
        label="Masuk Sekarang"
        size="lg"
        class="full-width text-weight-bold q-px-xl hover-lift"
        @click="$router.push('/auth/login')"
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
const showPasswordConfirm = ref(false)
const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: '',
  terms: false
})

// Role options
const roleOptions = [
  { label: 'Administrator', value: 'admin' },
  { label: 'Kasir', value: 'kasir' }
]

// Validation helpers
const isValidEmail = (email) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailPattern.test(email)
}

const isValidPhone = (phone) => {
  const phonePattern = /^[0-9+\-\s()]{10,15}$/
  return phonePattern.test(phone)
}

// Form validation rules
const nameRules = [
  val => !!val || 'Nama lengkap wajib diisi',
  val => val.length >= 2 || 'Nama minimal 2 karakter'
]

const emailRules = [
  val => !!val || 'Email wajib diisi',
  val => isValidEmail(val) || 'Format email tidak valid'
]

const phoneRules = [
  val => !!val || 'Nomor telepon wajib diisi',
  val => isValidPhone(val) || 'Format nomor telepon tidak valid'
]

const passwordRules = [
  val => !!val || 'Password wajib diisi',
  val => val.length >= 8 || 'Password minimal 8 karakter',
  val => /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(val) || 'Password harus mengandung huruf besar, kecil, dan angka'
]

const passwordConfirmRules = [
  val => !!val || 'Konfirmasi password wajib diisi',
  val => val === form.value.password || 'Password tidak cocok'
]

const roleRules = [
  val => !!val || 'Peran wajib dipilih'
]

// Computed properties
const isFormValid = computed(() => {
  return form.value.name && 
         form.value.email && 
         form.value.phone &&
         form.value.password && 
         form.value.password_confirmation &&
         form.value.role &&
         form.value.terms &&
         isValidEmail(form.value.email) &&
         isValidPhone(form.value.phone) &&
         form.value.password.length >= 8 &&
         form.value.password === form.value.password_confirmation
})

const handleRegister = async () => {
  const result = await authStore.register({
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation,
    role: form.value.role
  })
  
  if (result.success) {
    // Redirect to dashboard
    router.push('/dashboard')
  }
}
</script>

<style lang="scss" scoped>
// Register container styling
.register-container {
  width: 100%;
  max-width: 100%;
}

.register-header {
  margin-bottom: 1rem;
}

.register-form {
  margin-bottom: 1rem;
}

.form-section {
  padding: 0.5rem 0;
  
  h3 {
    border-bottom: 2px solid var(--q-primary);
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }
}

.register-input {
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

.register-submit {
  border-radius: 12px;
  padding: 16px;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25, 118, 210, 0.3);
  }
}

.terms-checkbox {
  padding: 1rem;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.02);
  transition: all 0.3s ease;
  
  &:hover {
    background: rgba(0, 0, 0, 0.04);
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

.hover-underline {
  text-decoration: none;
  transition: all 0.3s ease;
  
  &:hover {
    text-decoration: underline;
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
  .register-container {
    padding: 0;
  }
  
  .register-header {
    .text-h3 {
      font-size: 1.8rem;
    }
    
    .text-h6 {
      font-size: 1rem;
    }
  }
  
  .form-section {
    padding: 0.5rem 0;
    
    h3 {
      font-size: 1.1rem;
    }
  }
  
  .register-input {
    .q-field__control {
      border-radius: 8px;
    }
  }
  
  .register-submit {
    border-radius: 8px;
    padding: 14px;
  }
  
  .terms-checkbox {
    padding: 0.75rem;
    border-radius: 6px;
  }
}
</style>
<template>
  <q-page class="q-pa-md">
    <!-- Page Header -->
    <div class="row q-mb-lg">
      <div class="col">
        <div class="text-h4 text-weight-bold text-grey-8">
          <q-icon name="person" class="q-mr-sm" />
          Profil Pengguna
        </div>
        <div class="text-subtitle1 text-grey-6">
          Kelola informasi profil dan pengaturan keamanan akun Anda
        </div>
      </div>
    </div>

    <div class="row q-col-gutter-lg">
      <!-- Profile Overview Card -->
      <div class="col-12 col-md-4">
        <q-card class="profile-overview-card">
          <q-card-section class="text-center q-pa-lg">
            <!-- Avatar Section -->
            <div class="avatar-section q-mb-md">
              <q-avatar 
                size="120px" 
                class="avatar-main"
                :class="{ 'avatar-loading': avatarLoading }"
              >
                <img 
                  :src="currentAvatar" 
                  alt="Profile Avatar"
                  @error="handleAvatarError"
                />
                
                <!-- Loading overlay -->
                <div v-if="avatarLoading" class="avatar-loading-overlay">
                  <q-spinner color="white" size="30px" />
                </div>
              </q-avatar>
              
              <!-- Avatar Actions -->
              <div class="avatar-actions q-mt-md">
                <q-btn
                  round
                  color="primary"
                  icon="camera_alt"
                  size="sm"
                  @click="triggerAvatarUpload"
                  :loading="avatarLoading"
                  class="q-mr-sm"
                >
                  <q-tooltip>Upload Avatar</q-tooltip>
                </q-btn>
                
                <q-btn
                  v-if="authStore.user?.avatar"
                  round
                  color="negative"
                  icon="delete"
                  size="sm"
                  @click="confirmDeleteAvatar"
                  :loading="avatarLoading"
                >
                  <q-tooltip>Hapus Avatar</q-tooltip>
                </q-btn>
              </div>
              
              <!-- Hidden file input -->
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                style="display: none"
                @change="handleAvatarUpload"
              />
            </div>

            <!-- User Info -->
            <div class="text-h5 text-weight-medium q-mb-xs">
              {{ authStore.user?.name || 'Nama Pengguna' }}
            </div>
            <div class="text-body2 text-grey-6 q-mb-sm">
              {{ authStore.user?.email || 'email@example.com' }}
            </div>
            
            <!-- User Status -->
            <q-chip 
              :color="userStatusColor" 
              text-color="white" 
              :icon="userStatusIcon"
              class="q-mt-sm"
            >
              {{ userStatusText }}
            </q-chip>
            
            <!-- Account Stats -->
            <div class="row q-mt-lg q-gutter-sm">
              <div class="col text-center">
                <div class="text-h6 text-weight-bold">
                  {{ formatDate(authStore.user?.created_at) }}
                </div>
                <div class="text-caption text-grey-6">Bergabung</div>
              </div>
              <div class="col text-center">
                <div class="text-h6 text-weight-bold">
                  {{ formatDate(authStore.user?.updated_at) }}
                </div>
                <div class="text-caption text-grey-6">Terakhir Update</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Profile Forms -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section class="q-pa-none">
            <q-tabs 
              v-model="activeTab" 
              class="text-primary"
              indicator-color="primary"
              align="left"
              narrow-indicator
            >
              <q-tab 
                name="profile" 
                icon="person" 
                label="Informasi Profil" 
                class="q-px-lg"
              />
              <q-tab 
                name="security" 
                icon="security" 
                label="Keamanan" 
                class="q-px-lg"
              />
            </q-tabs>
          </q-card-section>

          <q-separator />

          <q-card-section class="q-pa-lg">
            <q-tab-panels 
              v-model="activeTab" 
              animated 
              transition-prev="slide-right" 
              transition-next="slide-left"
            >
              <!-- Profile Information Tab -->
              <q-tab-panel name="profile" class="q-pa-none">
                <ProfileForm
                  :form-data="profileForm"
                  :loading="formLoading.profile"
                  :errors="formErrors.profile"
                  @submit="handleProfileUpdate"
                  @reset="resetProfileForm"
                />
              </q-tab-panel>

              <!-- Security Tab -->
              <q-tab-panel name="security" class="q-pa-none">
                <SecurityForm
                  :form-data="passwordForm"
                  :loading="formLoading.password"
                  :errors="formErrors.password"
                  :password-strength="passwordStrength"
                  @submit="handlePasswordChange"
                  @reset="resetPasswordForm"
                  @password-input="calculatePasswordStrength"
                />
              </q-tab-panel>
            </q-tab-panels>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Delete Avatar Confirmation Dialog -->
    <q-dialog v-model="showDeleteAvatarDialog">
      <q-card style="min-width: 300px">
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="negative" text-color="white" />
          <span class="q-ml-sm">Apakah Anda yakin ingin menghapus avatar?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn 
            flat 
            label="Hapus" 
            color="negative" 
            @click="deleteAvatar"
            :loading="avatarLoading"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from 'src/stores/auth'
import { Notify } from 'quasar'
import { date } from 'quasar'
import ProfileForm from 'src/components/profile/ProfileForm.vue'
import SecurityForm from 'src/components/profile/SecurityForm.vue'

/**
 * ProfilePage Component
 * 
 * A comprehensive profile management page that provides:
 * - User profile information display and editing
 * - Avatar management (upload, delete, preview)
 * - Password change functionality with strength validation
 * - Responsive design consistent with application theme
 * - Loading states and error handling
 * 
 * @component
 */

const authStore = useAuthStore()

// Reactive state
const activeTab = ref('profile')
const avatarLoading = ref(false)
const showDeleteAvatarDialog = ref(false)
const avatarInput = ref(null)

// Form data
const profileForm = ref({
  name: '',
  email: '',
  phone: '',
  bio: '',
  location: ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Loading states for different forms
const formLoading = ref({
  profile: false,
  password: false
})

// Form errors
const formErrors = ref({
  profile: {},
  password: {}
})

// Password strength tracking
const passwordStrength = ref({
  score: 0,
  feedback: '',
  color: 'grey'
})

// Avatar management
const currentAvatar = computed(() => {
  if (authStore.user?.avatar) {
    return authStore.user.avatar
  }
  // Generate avatar based on user name
  const name = authStore.user?.name || 'User'
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1976d2&color=fff&size=120`
})

// User status computed properties
const userStatusColor = computed(() => {
  if (authStore.user?.email_verified_at) {
    return 'positive'
  }
  return 'warning'
})

const userStatusIcon = computed(() => {
  if (authStore.user?.email_verified_at) {
    return 'verified'
  }
  return 'pending'
})

const userStatusText = computed(() => {
  if (authStore.user?.email_verified_at) {
    return 'Terverifikasi'
  }
  return 'Belum Verifikasi'
})

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return date.formatDate(dateString, 'DD MMM YYYY')
}

// Avatar management functions
const triggerAvatarUpload = () => {
  avatarInput.value?.click()
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // Validate file type
  if (!file.type.startsWith('image/')) {
    Notify.create({
      type: 'negative',
      message: 'File harus berupa gambar',
      position: 'top'
    })
    return
  }

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    Notify.create({
      type: 'negative',
      message: 'Ukuran file maksimal 2MB',
      position: 'top'
    })
    return
  }

  avatarLoading.value = true
  
  try {
    // Upload avatar using auth store (auth store handles notifications)
    const result = await authStore.uploadAvatar(file)
    
    if (!result.success) {
      throw new Error(result.message || 'Gagal mengupload avatar')
    }
    
  } catch (error) {
    // Only show notification if auth store didn't handle it
    if (!error.response) {
      Notify.create({
        type: 'negative',
        message: error.message || 'Gagal mengupload avatar',
        position: 'top'
      })
    }
  } finally {
    avatarLoading.value = false
    // Reset file input
    if (avatarInput.value) {
      avatarInput.value.value = ''
    }
  }
}

const confirmDeleteAvatar = () => {
  showDeleteAvatarDialog.value = true
}

const deleteAvatar = async () => {
  avatarLoading.value = true
  
  try {
    // Delete avatar using auth store (auth store handles notifications)
    const result = await authStore.deleteAvatar()
    
    if (!result.success) {
      throw new Error(result.message || 'Gagal menghapus avatar')
    }
    
  } catch (error) {
    // Only show notification if auth store didn't handle it
    if (!error.response) {
      Notify.create({
        type: 'negative',
        message: error.message || 'Gagal menghapus avatar',
        position: 'top'
      })
    }
  } finally {
    avatarLoading.value = false
    showDeleteAvatarDialog.value = false
  }
}

const handleAvatarError = () => {
  // Fallback to generated avatar if image fails to load
  console.warn('Avatar image failed to load, using fallback')
}

// Form handlers
const handleProfileUpdate = async (formData) => {
  formLoading.value.profile = true
  formErrors.value.profile = {}
  
  try {
    const result = await authStore.updateProfile(formData)
    
    if (result.success) {
      resetProfileForm()
    } else {
      // Handle validation errors
      if (result.errors) {
        formErrors.value.profile = result.errors
      }
    }
  } catch (error) {
    console.error('Profile update error:', error)
  } finally {
    formLoading.value.profile = false
  }
}

const handlePasswordChange = async (formData) => {
  formLoading.value.password = true
  formErrors.value.password = {}
  
  try {
    const result = await authStore.changePassword(formData)
    
    if (result.success) {
      resetPasswordForm()
    } else {
      // Handle validation errors
      if (result.errors) {
        formErrors.value.password = result.errors
      }
    }
  } catch (error) {
    console.error('Password change error:', error)
  } finally {
    formLoading.value.password = false
  }
}

const calculatePasswordStrength = (password) => {
  if (!password) {
    passwordStrength.value = { score: 0, feedback: '', color: 'grey', text: '' }
    return
  }

  let score = 0
  let feedback = []

  // Length check
  if (password.length >= 8) score += 1
  else feedback.push('Minimal 8 karakter')

  // Uppercase check
  if (/[A-Z]/.test(password)) score += 1
  else feedback.push('Sertakan huruf besar')

  // Lowercase check
  if (/[a-z]/.test(password)) score += 1
  else feedback.push('Sertakan huruf kecil')

  // Number check
  if (/\d/.test(password)) score += 1
  else feedback.push('Sertakan angka')

  // Special character check
  if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 1
  else feedback.push('Sertakan karakter khusus')

  // Determine strength
  let color, text
  if (score >= 5) {
    color = 'green'
    text = 'Sangat Kuat'
  } else if (score >= 4) {
    color = 'light-green'
    text = 'Kuat'
  } else if (score >= 3) {
    color = 'orange'
    text = 'Sedang'
  } else if (score >= 2) {
    color = 'deep-orange'
    text = 'Lemah'
  } else {
    color = 'red'
    text = 'Sangat Lemah'
  }

  passwordStrength.value = {
    score,
    feedback: feedback.join(', '),
    color,
    text
  }
}

// Form reset functions
const resetProfileForm = () => {
  profileForm.value = {
    name: authStore.user?.name || '',
    email: authStore.user?.email || '',
    phone: authStore.user?.phone || '',
    bio: authStore.user?.bio || '',
    location: authStore.user?.location || ''
  }
  formErrors.value.profile = {}
}

const resetPasswordForm = () => {
  passwordForm.value = {
    current_password: '',
    password: '',
    password_confirmation: ''
  }
  formErrors.value.password = {}
  passwordStrength.value = { score: 0, feedback: '', color: 'grey', text: '' }
}

// Lifecycle
onMounted(() => {
  resetProfileForm()
})
</script>

<style lang="scss" scoped>
.profile-overview-card {
  .avatar-section {
    position: relative;
    
    .avatar-main {
      border: 4px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      
      &.avatar-loading {
        opacity: 0.7;
      }
    }
    
    .avatar-loading-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 50%;
    }
    
    .avatar-actions {
      .q-btn {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }
    }
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .profile-overview-card {
    margin-bottom: 1rem;
  }
}
</style>
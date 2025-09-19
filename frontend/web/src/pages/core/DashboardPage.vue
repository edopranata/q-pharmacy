<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <!-- Welcome Card -->
      <div class="col-12">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h5">Selamat Datang, {{ authStore.user?.name }}!</div>
            <div class="text-subtitle2">{{ getCurrentGreeting() }}</div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Quick Stats -->
      <div class="col-12 col-md-6 col-lg-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Total Produk</div>
                <div class="text-h4 text-primary">{{ stats.totalProducts }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="inventory" size="3rem" color="primary" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Kategori</div>
                <div class="text-h4 text-green">{{ stats.totalCategories }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="category" size="3rem" color="green" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Supplier</div>
                <div class="text-h4 text-orange">{{ stats.totalSuppliers }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="business" size="3rem" color="orange" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Transaksi Hari Ini</div>
                <div class="text-h4 text-purple">{{ stats.todayTransactions }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="receipt" size="3rem" color="purple" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Recent Activities -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Aktivitas Terbaru</div>
            <q-list>
              <q-item v-for="activity in recentActivities" :key="activity.id">
                <q-item-section avatar>
                  <q-icon :name="activity.icon" :color="activity.color" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ activity.title }}</q-item-label>
                  <q-item-label caption>{{ activity.time }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <!-- System Info -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Informasi Sistem</div>
            <q-list>
              <q-item>
                <q-item-section>
                  <q-item-label>Role Anda</q-item-label>
                  <q-item-label caption>
                    <q-chip 
                      color="blue" 
                      text-color="white" 
                      size="sm"
                    >
                      User
                    </q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Last Login</q-item-label>
                  <q-item-label caption>{{ formatDate(new Date()) }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Versi Sistem</q-item-label>
                  <q-item-label caption>Q-Pharmacy v1.0.0</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from 'src/stores/auth'
const authStore = useAuthStore()

const stats = ref({
  totalProducts: 0,
  totalCategories: 0,
  totalSuppliers: 0,
  todayTransactions: 0
})

const recentActivities = ref([
  {
    id: 1,
    title: 'Login berhasil',
    time: 'Baru saja',
    icon: 'login',
    color: 'green'
  },
  {
    id: 2,
    title: 'Sistem dimulai',
    time: '5 menit yang lalu',
    icon: 'power_settings_new',
    color: 'blue'
  }
])

const getCurrentGreeting = () => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Selamat pagi! Semoga hari Anda produktif.'
  if (hour < 17) return 'Selamat siang! Tetap semangat bekerja.'
  return 'Selamat sore! Semoga hari Anda menyenangkan.'
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}



const loadStats = async () => {
  // TODO: Load actual stats from API
  stats.value = {
    totalProducts: 150,
    totalCategories: 12,
    totalSuppliers: 8,
    todayTransactions: 25
  }
}

onMounted(() => {
  loadStats()
})
</script>
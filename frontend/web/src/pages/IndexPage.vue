<template>
  <q-page>
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-to-br from-primary to-secondary text-white">
      <div class="layout-container">
        <div class="row items-center min-h-screen q-py-xl">
          <div class="col-12 col-md-6 q-pr-md">
            <div class="fade-in-left">
              <h1 class="text-h2 text-weight-bold q-mb-md">
                Kelola Apotek Anda dengan
                <span :class="themeStore.isCurrentlyDark ? '' : 'text-accent'">Q-Pharmacy</span>
              </h1>
              <p class="text-h6 text-weight-light q-mb-xl opacity-90">
                Sistem manajemen apotek modern yang membantu Anda mengelola inventori, 
                penjualan, dan operasional dengan mudah dan efisien.
              </p>
              <div class="q-gutter-md">
                <q-btn
                  unelevated
                  size="lg"
                  color="secondary"
                  no-caps
                  class="text-weight-bold q-px-xl"
                  to="/auth/register"
                >
                  Mulai Gratis
                </q-btn>
                <q-btn
                  outline
                  size="lg"
                  color="white"
                  no-caps
                  class="text-weight-medium q-px-xl"
                  @click="scrollToSection('features')"
                >
                  Lihat Fitur
                </q-btn>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 text-center">
            <div class="fade-in-right">
              <q-img
                src="/images/hero-dashboard.png"
                alt="Q-Pharmacy Dashboard"
                class="hero-image shadow-xl radius-lg"
                style="max-width: 100%; height: auto;"
                placeholder-src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5EYXNoYm9hcmQgUHJldmlldyA8L3RleHQ+PC9zdmc+"
              />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="layout-section bg-grey-1">
      <div class="layout-container">
        <div class="text-center q-mb-xl">
          <h2 class="text-h3 text-weight-bold text-primary q-mb-md">
            Fitur Unggulan
          </h2>
          <p class="text-h6 text-grey-7 max-w-2xl mx-auto">
            Solusi lengkap untuk mengelola semua aspek operasional apotek Anda
          </p>
        </div>

        <div class="row q-col-gutter-lg">
          <div
            v-for="(feature, index) in features"
            :key="index"
            class="col-12 col-sm-6 col-md-4"
          >
            <q-card
              class="feature-card layout-card hover-lift h-full"
              flat
              bordered
            >
              <q-card-section class="text-center">
                <q-avatar
                  size="80px"
                  :color="feature.color"
                  text-color="white"
                  class="q-mb-md"
                >
                  <q-icon :name="feature.icon" size="40px" />
                </q-avatar>
                <h3 class="text-h5 text-weight-bold q-mb-sm">
                  {{ feature.title }}
                </h3>
                <p class="text-grey-7">
                  {{ feature.description }}
                </p>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="layout-section bg-primary text-white">
      <div class="layout-container">
        <div class="row text-center q-gutter-y-lg">
          <div
            v-for="(stat, index) in statistics"
            :key="index"
            class="col-12 col-sm-6 col-md-3"
          >
            <div class="stat-item">
              <div class="text-h2 text-weight-bold q-mb-sm">
                {{ stat.value }}
              </div>
              <div class="text-h6 text-weight-medium opacity-90">
                {{ stat.label }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="layout-section">
      <div class="layout-container">
        <div class="row items-center q-gutter-y-xl">
          <div class="col-12 col-md-6">
            <q-img
              src="/images/benefits-illustration.png"
              alt="Benefits Illustration"
              class="radius-lg"
              placeholder-src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5CZW5lZml0cyBJbGx1c3RyYXRpb24gPC90ZXh0Pjwvc3ZnPg=="
            />
          </div>
          <div class="col-12 col-md-6 q-pl-md">
            <h2 class="text-h3 text-weight-bold text-primary q-mb-lg">
              Mengapa Memilih Q-Pharmacy?
            </h2>
            <div class="q-gutter-md">
              <div
                v-for="(benefit, index) in benefits"
                :key="index"
                class="flex items-start q-mb-lg"
              >
                <q-icon
                  :name="benefit.icon"
                  :color="benefit.color"
                  size="24px"
                  class="q-mr-md q-mt-xs"
                />
                <div>
                  <h4 class="text-h6 text-weight-bold q-mb-xs">
                    {{ benefit.title }}
                  </h4>
                  <p class="text-grey-7">
                    {{ benefit.description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="layout-section">
      <div class="layout-container">
        <div class="row items-center q-gutter-y-xl">
          <div class="col-12 col-md-6 q-pr-md">
            <h2 class="text-h3 text-weight-bold text-primary q-mb-lg">
              Tentang Q-Pharmacy
            </h2>
            <p class="text-h6 text-grey-7 q-mb-lg">
              Q-Pharmacy adalah sistem manajemen apotek yang dirancang khusus untuk 
              membantu apotek modern mengelola operasional mereka dengan lebih efisien.
            </p>
            <p class="text-body1 text-grey-7 q-mb-lg">
              Dengan teknologi terdepan dan antarmuka yang intuitif, Q-Pharmacy memungkinkan 
              Anda untuk fokus pada pelayanan pelanggan terbaik sambil sistem menangani 
              kompleksitas manajemen inventori, penjualan, dan pelaporan.
            </p>
            <q-btn
              unelevated
              color="primary"
              no-caps
              class="text-weight-medium q-px-lg"
              to="/auth/register"
            >
              Bergabung Sekarang
            </q-btn>
          </div>
          <div class="col-12 col-md-6">
            <q-img
              src="/images/about-illustration.png"
              alt="About Q-Pharmacy"
              class="radius-lg"
              placeholder-src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5BYm91dCBJbGx1c3RyYXRpb24gPC90ZXh0Pjwvc3ZnPg=="
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="layout-section">
      <div class="layout-container">
        <div class="text-center q-mb-xl">
          <h2 class="text-h3 text-weight-bold text-primary q-mb-md">
            Hubungi Kami
          </h2>
          <p class="text-h6 text-grey-7 max-w-2xl mx-auto">
            Punya pertanyaan? Tim kami siap membantu Anda memulai dengan Q-Pharmacy
          </p>
        </div>

        <div class="row q-col-gutter-lg">
          <div class="col-12 col-md-4">
            <q-card class="layout-card text-center h-full" flat bordered>
              <q-card-section>
                <q-avatar size="60px" color="primary" text-color="white" class="q-mb-md">
                  <q-icon name="phone" size="30px" />
                </q-avatar>
                <h4 class="text-h6 text-weight-bold q-mb-sm">Telepon</h4>
                <p class="text-grey-7">+62 21 1234 5678</p>
                <p class="text-grey-7">Senin - Jumat: 08:00 - 17:00</p>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-12 col-md-4">
            <q-card class="layout-card text-center h-full" flat bordered>
              <q-card-section>
                <q-avatar size="60px" color="secondary" text-color="white" class="q-mb-md">
                  <q-icon name="email" size="30px" />
                </q-avatar>
                <h4 class="text-h6 text-weight-bold q-mb-sm">Email</h4>
                <p class="text-grey-7">info@Q-Pharmacy.com</p>
                <p class="text-grey-7">support@Q-Pharmacy.com</p>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-12 col-md-4">
            <q-card class="layout-card text-center h-full" flat bordered>
              <q-card-section>
                <q-avatar size="60px" color="accent" text-color="primary" class="q-mb-md">
                  <q-icon name="place" size="30px" />
                </q-avatar>
                <h4 class="text-h6 text-weight-bold q-mb-sm">Alamat</h4>
                <p class="text-grey-7">Jl. Contoh No. 123</p>
                <p class="text-grey-7">Jakarta, Indonesia</p>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="layout-section bg-primary text-white">
      <div class="layout-container text-center">
        <h2 class="text-h3 text-weight-bold q-mb-md">
          Siap Memulai dengan Q-Pharmacy?
        </h2>
        <p class="text-h6 text-weight-light q-mb-xl opacity-90 text-center">
          Bergabunglah dengan ribuan apotek yang telah mempercayai Q-Pharmacy 
          untuk mengelola operasional mereka.
        </p>
        <div class="q-gutter-md">
          <q-btn
            unelevated
            size="lg"
            color="secondary"
            no-caps
            class="text-weight-bold q-px-xl"
            to="/auth/register"
          >
            Daftar Gratis
          </q-btn>
          <q-btn
            outline
            size="lg"
            color="white"
            no-caps
            class="text-weight-medium q-px-xl"
            to="/auth/login"
          >
            Masuk
          </q-btn>
        </div>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import {useThemeStore} from 'src/stores'

const themeStore = useThemeStore()

// Features data
const features = ref([
  {
    icon: 'inventory_2',
    title: 'Manajemen Inventori',
    description: 'Kelola stok obat dengan sistem tracking yang akurat dan real-time.',
    color: 'primary'
  },
  {
    icon: 'point_of_sale',
    title: 'Sistem POS',
    description: 'Proses transaksi penjualan yang cepat dan mudah dengan interface modern.',
    color: 'secondary'
  },
  {
    icon: 'analytics',
    title: 'Laporan & Analitik',
    description: 'Dashboard analitik komprehensif untuk insight bisnis yang mendalam.',
    color: 'accent'
  },
  {
    icon: 'group',
    title: 'Manajemen User',
    description: 'Kelola tim dan hak akses dengan sistem role-based yang fleksibel.',
    color: 'positive'
  },
  {
    icon: 'receipt_long',
    title: 'Riwayat Transaksi',
    description: 'Tracking lengkap semua transaksi dengan pencarian yang powerful.',
    color: 'info'
  },
  {
    icon: 'security',
    title: 'Keamanan Data',
    description: 'Perlindungan data tingkat enterprise dengan backup otomatis.',
    color: 'warning'
  }
])

// Statistics data
const statistics = ref([
  { value: '1000+', label: 'Apotek Terdaftar' },
  { value: '50K+', label: 'Transaksi Harian' },
  { value: '99.9%', label: 'Uptime Server' },
  { value: '24/7', label: 'Customer Support' }
])

// Benefits data
const benefits = ref([
  {
    icon: 'speed',
    title: 'Efisiensi Operasional',
    description: 'Otomatisasi proses bisnis untuk menghemat waktu dan mengurangi kesalahan manual.',
    color: 'primary'
  },
  {
    icon: 'trending_up',
    title: 'Peningkatan Penjualan',
    description: 'Analitik mendalam membantu mengoptimalkan strategi penjualan dan inventory.',
    color: 'positive'
  },
  {
    icon: 'verified_user',
    title: 'Kepatuhan Regulasi',
    description: 'Sistem yang sesuai dengan standar regulasi apotek dan farmasi Indonesia.',
    color: 'info'
  },
  {
    icon: 'support_agent',
    title: 'Dukungan 24/7',
    description: 'Tim support yang responsif siap membantu kapan saja Anda membutuhkan.',
    color: 'secondary'
  }
])

// Methods
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    })
  }
}
</script>

<style lang="scss" scoped>
// Hero section styles
.hero-section {
  background: linear-gradient(135deg, var(--q-primary) 0%, var(--q-secondary) 100%);
  position: relative;
  overflow: hidden;
  
  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
    pointer-events: none;
  }
}

.hero-image {
  transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
  transition: transform 0.3s ease;
  
  &:hover {
    transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
  }
}

// Feature cards
.feature-card {
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.08);
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  }
}

// Statistics section
.stat-item {
  padding: 2rem 1rem;
}

// Animations
.fade-in-left {
  animation: fadeInLeft 1s ease-out;
}

.fade-in-right {
  animation: fadeInRight 1s ease-out;
}

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .hero-section {
    .text-h2 {
      font-size: 2rem;
    }
    
    .text-h6 {
      font-size: 1.1rem;
    }
  }
  
  .hero-image {
    transform: none;
    margin-top: 2rem;
  }
  
  .stat-item {
    padding: 1rem;
  }
}

// Utility classes
.max-w-2xl {
  max-width: 42rem;
}

.h-full {
  height: 100%;
}

.min-h-screen {
  min-height: 100vh;
}
</style>

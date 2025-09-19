<template>
  <q-layout view="lHh Lpr lff">
    <!-- Header -->
    <q-header
      class="theme-bg-secondary theme-text-primary theme-transition shadow-md"
      height-hint="64"
    >
      <q-toolbar class="layout-container">
        <!-- Logo -->
        <q-toolbar-title class="flex items-center no-wrap">
          <q-avatar size="40px" class="q-mr-sm">
            <q-icon name="local_pharmacy" size="24px" color="primary" />
          </q-avatar>
          <div class="text-h6 text-weight-bold text-primary">
            Q-Pharmacy
          </div>
        </q-toolbar-title>

        <!-- Desktop Navigation -->
        <div class="desktop-only flex items-center q-gutter-lg">
          <q-btn
            flat
            no-caps
            class="text-weight-medium theme-text-primary"
            @click="scrollToSection('features')"
          >
            Fitur
          </q-btn>
          <q-btn
            flat
            no-caps
            class="text-weight-medium theme-text-primary"
            @click="scrollToSection('about')"
          >
            Tentang
          </q-btn>
          <q-btn
            flat
            no-caps
            class="text-weight-medium theme-text-primary"
            @click="scrollToSection('contact')"
          >
            Kontak
          </q-btn>
          <q-separator vertical class="q-mx-sm theme-border" />
          
          <!-- Theme Toggle -->
          <ToggleTheme />
          
          <q-btn
            outline
            no-caps
            color="primary"
            class="text-weight-medium"
            to="/auth/login"
          >
            Masuk
          </q-btn>
          <q-btn
            unelevated
            no-caps
            color="primary"
            class="text-weight-medium"
            to="/auth/register"
          >
            Daftar
          </q-btn>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-only flex items-center q-gutter-sm">
          <!-- Theme Toggle for Mobile -->
          <ToggleTheme />
          
          <!-- Mobile Menu Button -->
          <q-btn
            flat
            dense
            round
            icon="menu"
            class="theme-text-primary"
            @click="mobileMenuOpen = true"
          />
        </div>
      </q-toolbar>
    </q-header>

    <!-- Mobile Navigation Drawer -->
    <q-drawer
      v-model="mobileMenuOpen"
      side="right"
      overlay
      behavior="mobile"
      class="mobile-only theme-bg-secondary theme-transition"
      :width="280"
    >
      <q-list class="q-pa-md">
        <q-item-label header class="text-weight-bold text-primary">
          Menu
        </q-item-label>
        
        <q-item
          clickable
          v-ripple
          @click="scrollToSection('features'); mobileMenuOpen = false"
        >
          <q-item-section avatar>
            <q-icon name="star" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Fitur</q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          v-ripple
          @click="scrollToSection('about'); mobileMenuOpen = false"
        >
          <q-item-section avatar>
            <q-icon name="info" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Tentang</q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          v-ripple
          @click="scrollToSection('contact'); mobileMenuOpen = false"
        >
          <q-item-section avatar>
            <q-icon name="contact_mail" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Kontak</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md" />

        <q-item
          clickable
          v-ripple
          to="/auth/login"
        >
          <q-item-section avatar>
            <q-icon name="login" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Masuk</q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          v-ripple
          to="/auth/register"
        >
          <q-item-section avatar>
            <q-icon name="person_add" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Daftar</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- Main Content -->
    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Footer -->
    <q-footer class="theme-transition">
      <div class="layout-container">
        <div class="row q-py-xl q-col-gutter-y-lg">
          <!-- Company Info -->
          <div class="col-12 col-md-4">
            <div class="flex items-center q-mb-md">
              <q-avatar size="32px" class="q-mr-sm">
                <q-icon name="local_pharmacy" size="20px" color="white" />
              </q-avatar>
              <div class="text-h6 text-weight-bold">
                Q-Pharmacy
              </div>
            </div>
            <p class="text-grey-4 q-mb-md">
              Sistem manajemen apotek modern yang membantu mengelola inventori, 
              penjualan, dan operasional apotek dengan mudah dan efisien.
            </p>
            <div class="flex q-gutter-sm">
              <q-btn
                flat
                round
                icon="fab fa-facebook"
                size="sm"
                color="grey-4"
                class="hover-glow"
              />
              <q-btn
                flat
                round
                icon="fab fa-twitter"
                size="sm"
                color="grey-4"
                class="hover-glow"
              />
              <q-btn
                flat
                round
                icon="fab fa-instagram"
                size="sm"
                color="grey-4"
                class="hover-glow"
              />
              <q-btn
                flat
                round
                icon="fab fa-linkedin"
                size="sm"
                color="grey-4"
                class="hover-glow"
              />
            </div>
          </div>

          <!-- Quick Links -->
          <div class="col-12 col-sm-6 col-md-2">
            <div class="text-h6 text-weight-bold q-mb-md">
              Tautan Cepat
            </div>
            <q-list dense class="text-grey-4">
              <q-item
                clickable
                v-ripple
                @click="scrollToSection('features')"
                class="q-pa-none q-mb-xs"
              >
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    Fitur
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                @click="scrollToSection('about')"
                class="q-pa-none q-mb-xs"
              >
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    Tentang Kami
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                @click="scrollToSection('contact')"
                class="q-pa-none q-mb-xs"
              >
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    Kontak
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>

          <!-- Support -->
          <div class="col-12 col-sm-6 col-md-2">
            <div class="text-h6 text-weight-bold q-mb-md">
              Dukungan
            </div>
            <q-list dense class="text-grey-4">
              <q-item class="q-pa-none q-mb-xs">
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    Dokumentasi
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item class="q-pa-none q-mb-xs">
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    FAQ
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item class="q-pa-none q-mb-xs">
                <q-item-section>
                  <q-item-label class="cursor-pointer hover:text-white transition-colors">
                    Bantuan
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>

          <!-- Contact Info -->
          <div class="col-12 col-md-4">
            <div class="text-h6 text-weight-bold q-mb-md">
              Hubungi Kami
            </div>
            <div class="text-grey-4">
              <div class="flex items-center q-mb-sm">
                <q-icon name="place" class="q-mr-sm" />
                <span>Jl. Contoh No. 123, Jakarta, Indonesia</span>
              </div>
              <div class="flex items-center q-mb-sm">
                <q-icon name="phone" class="q-mr-sm" />
                <span>+62 21 1234 5678</span>
              </div>
              <div class="flex items-center q-mb-sm">
                <q-icon name="email" class="q-mr-sm" />
                <span>info@q-pharmacy.com</span>
              </div>
              <div class="flex items-center">
                <q-icon name="schedule" class="q-mr-sm" />
                <span>Senin - Jumat: 08:00 - 17:00</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Copyright -->
        <q-separator class="q-mb-md" color="grey-7" />
        <div class="row items-center justify-between q-pb-lg">
          <div class="text-grey-4">
            © {{ currentYear }} Q-Pharmacy. Semua hak dilindungi.
          </div>
          <div class="flex q-gutter-md text-grey-4">
            <span class="cursor-pointer hover:text-white transition-colors">
              Kebijakan Privasi
            </span>
            <span class="cursor-pointer hover:text-white transition-colors">
              Syarat & Ketentuan
            </span>
          </div>
        </div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { ref, computed } from 'vue'
import ToggleTheme from 'src/components/ToggleTheme.vue'

// Reactive data
const mobileMenuOpen = ref(false)

// Computed properties
const currentYear = computed(() => new Date().getFullYear())

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
// Component-specific styles using theme variables
.q-header {
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.q-toolbar {
  min-height: 64px;
}

.q-footer {
  .q-btn {
    transition: all 0.3s ease;
    
    &:hover {
      transform: translateY(-2px);
    }
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .q-toolbar {
    padding: 0 16px;
  }
  
  .layout-container {
    padding: 0 16px;
  }
}
</style>
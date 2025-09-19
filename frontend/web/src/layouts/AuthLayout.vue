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

        <q-space />
        
        <!-- Theme Toggle -->
        <ToggleTheme />
        
        <!-- Back to Home -->
        <q-btn
          flat
          no-caps
          color="primary"
          class="text-weight-medium q-ml-md"
          to="/"
          icon="home"
        >
          Beranda
        </q-btn>
      </q-toolbar>
    </q-header>

    <!-- Main Content -->
    <q-page-container>
      <div class="auth-container theme-bg-primary theme-transition">
        <div class="layout-container">
          <div class="row justify-center items-center min-h-screen q-py-xl">
            <!-- Right Form Container -->
            <q-card 
              class="auth-card theme-bg-secondary theme-transition shadow-xl"
              flat
            >
              <router-view />
            </q-card>
          </div>
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import ToggleTheme from 'src/components/ToggleTheme.vue'
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

.auth-container {
  min-height: 100vh;
  position: relative;
}

.auth-brand-section {
  padding: 2rem 0;
}

.auth-card {
  width: 100%;
  max-width: 420px;
  padding: 2rem;
  border-radius: 16px;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

// Responsive adjustments
@media (max-width: 1024px) {
  .auth-card {
    max-width: 100%;
    margin: 1rem;
  }
  
  .desktop-only {
    display: none;
  }
}

@media (max-width: 768px) {
  .q-toolbar {
    padding: 0 16px;
  }
  
  .layout-container {
    padding: 0 16px;
  }
  
  .auth-card {
    padding: 1.5rem;
    margin: 0.5rem;
  }
}

// Utility classes
.min-h-screen {
  min-height: 100vh;
}
</style>
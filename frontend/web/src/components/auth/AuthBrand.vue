<template>
  <div class="auth-brand">
    <div class="brand-logo">
      <q-icon name="local_pharmacy" size="64px" color="white" />
    </div>
    <h1 class="brand-title">Q-Pharmacy</h1>
    <p class="brand-subtitle">Sistem Manajemen Apotek Modern</p>
    <div class="brand-description">
      <p>Kelola apotek Anda dengan mudah dan efisien menggunakan sistem terintegrasi yang dirancang khusus untuk kebutuhan farmasi modern.</p>
    </div>
  </div>
</template>

<script setup>
// No props or logic needed for this static component
</script>

<style lang="scss" scoped>
.auth-brand {
  margin-bottom: var(--spacing-2xl);
}

.brand-logo {
  margin-bottom: var(--spacing-lg);
  display: flex;
  justify-content: center;
  
  @media (max-width: 768px) {
    justify-content: center;
  }
}

.brand-title {
  font-size: var(--font-size-3xl);
  font-weight: var(--font-weight-bold);
  margin: 0 0 var(--spacing-sm) 0;
  background: linear-gradient(45deg, var(--auth-background), #e2e8f0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: var(--line-height-tight);
  text-align: center;
  
  @media (max-width: 1024px) {
    font-size: var(--font-size-3xl);
  }
  
  @media (max-width: 768px) {
    font-size: var(--font-size-2xl);
  }
  
  @media (max-width: 480px) {
    font-size: var(--font-size-xl);
  }
}

.brand-subtitle {
  font-size: var(--font-size-lg);
  opacity: 0.9;
  margin: 0;
  line-height: var(--line-height-relaxed);
  font-weight: var(--font-weight-normal);
  text-align: center;
  color: var(--auth-background);
}

.brand-description {
  font-size: var(--font-size-base);
  color: rgba(255, 255, 255, 0.8);
  line-height: var(--line-height-relaxed);
  max-width: 500px;
  margin: var(--spacing-lg) auto 0;
  text-align: center;
  
  @media (max-width: 768px) {
    max-width: 100%;
    font-size: var(--font-size-sm);
  }
  
  p {
    margin: 0;
  }
}

@media (max-width: 768px) {
  .auth-brand {
    text-align: center;
    margin-bottom: var(--spacing-xl);
  }
}
</style>
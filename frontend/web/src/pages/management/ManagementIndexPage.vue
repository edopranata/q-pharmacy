<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Management Dashboard</h4>
        <p class="text-grey-6">Central hub for all management operations</p>
      </div>
    </div>

    <div class="row q-gutter-md">
      <!-- Master Data Section -->
      <div class="col-12 col-md-6 col-lg-4">
        <q-card class="interactive-hover" @click="$router.push('/app/master/categories')">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Master Data</div>
                <div class="text-caption text-grey-6">Categories, Suppliers, Units</div>
              </div>
              <div class="col-auto">
                <q-icon name="category" size="2rem" color="primary" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Inventory Section -->
      <div class="col-12 col-md-6 col-lg-4">
        <q-card class="interactive-hover" @click="$router.push('/app/products')">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Inventory</div>
                <div class="text-caption text-grey-6">Products, Stock Management</div>
              </div>
              <div class="col-auto">
                <q-icon name="inventory" size="2rem" color="green" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Reports Section -->
      <div class="col-12 col-md-6 col-lg-4">
        <q-card class="interactive-hover">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Reports</div>
                <div class="text-caption text-grey-6">Analytics & Insights</div>
              </div>
              <div class="col-auto">
                <q-icon name="assessment" size="2rem" color="orange" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
// Management Dashboard Page
</script>
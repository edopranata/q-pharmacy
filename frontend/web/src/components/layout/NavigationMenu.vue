<template>
  <q-scroll-area style="height: calc(100vh - 150px)">
    <q-list>
      <!-- Dashboard -->
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/dashboard'"
      >
        <q-item-section avatar>
          <q-icon name="dashboard" />
        </q-item-section>
        <q-item-section class="q-mini-drawer-hide">
          <q-item-label>Dashboard</q-item-label>
        </q-item-section>
        
        <q-tooltip 
          v-if="uiStore.miniState" 
          anchor="center right" 
          self="center left" 
          :offset="[10, 0]"
        >
          Dashboard
        </q-tooltip>
      </q-item>

      <q-separator />

       <!-- Master Data -->
       <q-expansion-item
         icon="storage"
         label="Master Data"
         v-model="uiStore.expandedMenus.masterData"
         @update:model-value="(val) => uiStore.setMenuExpansion('masterData', val)"
       >
         <template v-slot:header>
           <q-item-section avatar>
             <q-icon name="storage" />
           </q-item-section>
           <q-item-section class="q-mini-drawer-hide">
             Master Data
           </q-item-section>
         </template>
         <q-item 
           clickable 
           v-ripple 
           :to="'/app/master/categories'" 
           :inset-level="0.5"
         >
           <q-item-section avatar>
             <q-icon name="category" />
           </q-item-section>
           <q-item-section>
             <q-item-label>Daftar Kategori</q-item-label>
           </q-item-section>
         </q-item>

         <q-item 
           clickable 
           v-ripple 
           :to="'/app/master/suppliers'" 
           :inset-level="0.5"
         >
           <q-item-section avatar>
             <q-icon name="business" />
           </q-item-section>
           <q-item-section>
             <q-item-label>Daftar Supplier</q-item-label>
           </q-item-section>
         </q-item>

         <q-item 
           clickable 
           v-ripple 
           :to="'/app/master/units'" 
           :inset-level="0.5"
           class="nav-sub-item"
           active-class="nav-sub-item--active"
         >
           <q-item-section avatar class="nav-sub-item__icon">
             <q-icon name="straighten" />
           </q-item-section>
           <q-item-section class="nav-sub-item__content">
             <q-item-label class="nav-sub-item__label">Daftar Satuan</q-item-label>
           </q-item-section>
         </q-item>
       </q-expansion-item>

    <!-- Product Management -->
    <q-expansion-item
      icon="inventory"
      label="Produk"
      header-class="nav-expansion-header"
      class="nav-expansion"
      v-model="uiStore.expandedMenus.products"
      @update:model-value="(val) => uiStore.setMenuExpansion('products', val)"
    >
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/products'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="list" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Daftar Produk</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/products/pricing'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="attach_money" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Harga Produk</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>

    <!-- Inventory Management -->
    <q-expansion-item
      icon="warehouse"
      label="Inventory"
      header-class="nav-expansion-header"
      class="nav-expansion"
      v-model="uiStore.expandedMenus.inventory"
      @update:model-value="(val) => uiStore.setMenuExpansion('inventory', val)"
    >
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/inventories'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="inventory_2" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Stock Overview</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/inventories/stock-in'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="add_box" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Stock Masuk</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/inventories/stock-in/create'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="add" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Tambah Stock Masuk</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/inventories/stock-out'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="remove_circle" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Stock Keluar</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/inventories/adjustments'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="tune" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Penyesuaian Stock</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>

    <!-- Sales Management -->
    <q-expansion-item
      icon="point_of_sale"
      label="Penjualan"
      header-class="nav-expansion-header"
      class="nav-expansion"
      v-model="uiStore.expandedMenus.sales"
      @update:model-value="(val) => uiStore.setMenuExpansion('sales', val)"
    >
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/sells'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="receipt" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Daftar Penjualan</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/sells/pos'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="point_of_sale" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Point of Sale</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/sells/transactions'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="history" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Riwayat Transaksi</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>

    <!-- Reports & Analytics -->
    <q-expansion-item
      icon="assessment"
      label="Laporan"
      header-class="nav-expansion-header"
      class="nav-expansion"
      v-model="uiStore.expandedMenus.reports"
      @update:model-value="(val) => uiStore.setMenuExpansion('reports', val)"
    >
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/reports'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="assessment" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Dashboard Laporan</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/reports/sales'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="trending_up" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Laporan Penjualan</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/reports/inventory'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="bar_chart" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Laporan Inventory</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/reports/financial'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="account_balance" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Laporan Keuangan</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>

    <!-- User Management -->
    <q-expansion-item
      icon="people"
      label="Manajemen User"
      header-class="nav-expansion-header"
      class="nav-expansion"
      v-model="uiStore.expandedMenus.users"
      @update:model-value="(val) => uiStore.setMenuExpansion('users', val)"
    >
      <q-item 
        clickable 
        v-ripple 
        :to="'/app/management/users'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="person" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Daftar User</q-item-label>
        </q-item-section>
      </q-item>

      <q-item 
        clickable 
        v-ripple 
        :to="'/app/management/roles'" 
        :inset-level="0.5"
        class="nav-sub-item"
        active-class="nav-sub-item--active"
      >
        <q-item-section avatar class="nav-sub-item__icon">
          <q-icon name="admin_panel_settings" />
        </q-item-section>
        <q-item-section class="nav-sub-item__content">
          <q-item-label class="nav-sub-item__label">Role & Permission</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>

    <q-separator />

    <!-- Profile & Settings -->
    <q-item clickable v-ripple :to="'/app/profile'">
      <q-item-section avatar>
        <q-icon name="account_circle" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Profile</q-item-label>
      </q-item-section>
    </q-item>

    <q-item clickable v-ripple @click="handleLogout">
      <q-item-section avatar>
        <q-icon name="logout" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Logout</q-item-label>
      </q-item-section>
    </q-item>
    </q-list>
  </q-scroll-area>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore, useUIStore } from 'src/stores'
import { Notify } from 'quasar'

const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUIStore()

// Methods
const handleLogout = async () => {
  try {
    await authStore.logout()
    Notify.create({
      type: 'positive',
      message: 'Logged out successfully'
    })
    router.push('/auth/login')
  } catch {
    Notify.create({
      type: 'negative',
      message: 'Logout failed'
    })
  }
}
</script>

<style lang="scss" scoped>
// Navigation specific styles can be added here if needed
</style>
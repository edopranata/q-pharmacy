# Q-Pharmacy Component Style Guide

## Overview

Panduan ini menyediakan dokumentasi lengkap untuk semua komponen UI yang digunakan dalam aplikasi Q-Pharmacy. Setiap komponen dilengkapi dengan contoh penggunaan, variasi, dan best practices untuk memastikan konsistensi di seluruh aplikasi.

## Table of Contents

1. [Layout Components](#layout-components)
2. [Navigation Components](#navigation-components)
3. [Form Components](#form-components)
4. [Data Display Components](#data-display-components)
5. [Feedback Components](#feedback-components)
6. [Action Components](#action-components)
7. [Pharmacy-Specific Components](#pharmacy-specific-components)
8. [Custom Components](#custom-components)
9. [Component Guidelines](#component-guidelines)

## Layout Components

### QLayout

Layout utama aplikasi dengan header, drawer, dan content area.

```vue
<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Header -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="toggleLeftDrawer" />
        <q-toolbar-title>Q-Pharmacy</q-toolbar-title>
        <q-space />
        <q-btn flat round icon="notifications" />
        <q-btn flat round icon="account_circle" />
      </q-toolbar>
    </q-header>

    <!-- Left Drawer -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-grey-1"
      :width="280"
    >
      <q-scroll-area class="fit">
        <q-list>
          <!-- Navigation items -->
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
```

**Usage Guidelines:**
- Gunakan view="lHh Lpr lFf" untuk layout standar
- Header selalu elevated dengan bg-primary
- Drawer width 280px untuk navigasi yang nyaman
- Gunakan q-scroll-area untuk konten drawer yang panjang

### QPage

Container untuk konten halaman dengan padding yang konsisten.

```vue
<template>
  <!-- Standard page with padding -->
  <q-page class="q-pa-lg">
    <div class="page-header q-mb-xl">
      <h1 class="text-h4 text-weight-light q-ma-none">Page Title</h1>
      <p class="text-grey-7 q-ma-none">Page description</p>
    </div>
    
    <!-- Page content -->
  </q-page>
  
  <!-- Full-width page (for tables, etc.) -->
  <q-page class="q-pa-md">
    <!-- Content -->
  </q-page>
  
  <!-- Centered content page -->
  <q-page class="flex flex-center">
    <div class="q-pa-lg" style="max-width: 600px; width: 100%;">
      <!-- Centered content -->
    </div>
  </q-page>
</template>
```

**Padding Guidelines:**
- `q-pa-lg` (24px) untuk halaman standar
- `q-pa-md` (16px) untuk halaman dengan tabel lebar
- `q-pa-xl` (32px) untuk halaman dengan konten minimal

### QCard

Komponen card untuk mengelompokkan konten terkait.

```vue
<template>
  <!-- Basic card -->
  <q-card class="q-mb-lg">
    <q-card-section>
      <div class="text-h6">Card Title</div>
      <div class="text-subtitle2 text-grey-7">Card subtitle</div>
    </q-card-section>
    
    <q-card-section>
      Card content goes here.
    </q-card-section>
    
    <q-card-actions align="right">
      <q-btn flat label="Cancel" />
      <q-btn color="primary" label="Save" />
    </q-card-actions>
  </q-card>
  
  <!-- Medicine card with image -->
  <q-card class="medicine-card">
    <q-img 
      :src="medicine.image" 
      :alt="medicine.name"
      height="200px"
      class="medicine-image"
    />
    
    <q-card-section>
      <div class="text-h6">{{ medicine.name }}</div>
      <div class="text-subtitle2 text-grey-7">{{ medicine.category }}</div>
    </q-card-section>
    
    <q-card-section>
      <div class="row items-center">
        <div class="col">
          <div class="text-caption text-grey-6">Stock</div>
          <div class="text-h6">{{ medicine.stock }}</div>
        </div>
        <div class="col">
          <div class="text-caption text-grey-6">Price</div>
          <div class="text-h6 text-positive">{{ formatCurrency(medicine.price) }}</div>
        </div>
      </div>
    </q-card-section>
  </q-card>
  
  <!-- Metric card -->
  <q-card class="metric-card bg-primary text-white">
    <q-card-section>
      <div class="row items-center">
        <div class="col">
          <div class="text-h4 text-weight-light">{{ todaySales }}</div>
          <div class="text-subtitle2">Today's Sales</div>
        </div>
        <div class="col-auto">
          <q-icon name="trending_up" size="48px" />
        </div>
      </div>
    </q-card-section>
  </q-card>
</template>

<style scoped>
.medicine-card {
  max-width: 300px;
  transition: transform 0.2s;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
}

.metric-card {
  min-height: 120px;
}
</style>
```

## Navigation Components

### QList & QItem

Komponen untuk navigasi dan daftar item.

```vue
<template>
  <!-- Navigation list -->
  <q-list>
    <!-- Simple navigation item -->
    <q-item clickable v-ripple to="/dashboard">
      <q-item-section avatar>
        <q-icon name="dashboard" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Dashboard</q-item-label>
      </q-item-section>
    </q-item>
    
    <!-- Expandable navigation group -->
    <q-expansion-item 
      icon="inventory" 
      label="Inventory Management"
      :default-opened="$route.path.startsWith('/inventory')"
    >
      <q-item clickable v-ripple to="/inventory/medicines">
        <q-item-section avatar>
          <q-icon name="medication" />
        </q-item-section>
        <q-item-section>
          <q-item-label>Medicines</q-item-label>
        </q-item-section>
      </q-item>
      
      <q-item clickable v-ripple to="/inventory/categories">
        <q-item-section avatar>
          <q-icon name="category" />
        </q-item-section>
        <q-item-section>
          <q-item-label>Categories</q-item-label>
        </q-item-section>
      </q-item>
    </q-expansion-item>
    
    <!-- Item with badge -->
    <q-item clickable v-ripple to="/notifications">
      <q-item-section avatar>
        <q-icon name="notifications" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Notifications</q-item-label>
      </q-item-section>
      <q-item-section side>
        <q-badge color="negative" :label="unreadCount" />
      </q-item-section>
    </q-item>
  </q-list>
  
  <!-- Data list -->
  <q-list bordered separator>
    <q-item v-for="medicine in medicines" :key="medicine.id">
      <q-item-section avatar>
        <q-avatar>
          <img :src="medicine.image" :alt="medicine.name" />
        </q-avatar>
      </q-item-section>
      
      <q-item-section>
        <q-item-label>{{ medicine.name }}</q-item-label>
        <q-item-label caption>{{ medicine.category }}</q-item-label>
      </q-item-section>
      
      <q-item-section side>
        <q-item-label caption>Stock: {{ medicine.stock }}</q-item-label>
        <q-item-label>{{ formatCurrency(medicine.price) }}</q-item-label>
      </q-item-section>
      
      <q-item-section side>
        <q-btn flat round icon="more_vert">
          <q-menu>
            <q-list>
              <q-item clickable @click="edit(medicine)">
                <q-item-section>Edit</q-item-section>
              </q-item>
              <q-item clickable @click="delete(medicine)">
                <q-item-section>Delete</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-item-section>
    </q-item>
  </q-list>
</template>
```

### QTabs & QTabPanels

Komponen untuk navigasi tab.

```vue
<template>
  <div>
    <!-- Tab navigation -->
    <q-tabs 
      v-model="tab" 
      class="text-primary"
      active-color="primary"
      indicator-color="primary"
      align="left"
    >
      <q-tab name="details" icon="info" label="Details" />
      <q-tab name="stock" icon="inventory" label="Stock" />
      <q-tab name="history" icon="history" label="History" />
      <q-tab name="reports" icon="assessment" label="Reports" />
    </q-tabs>
    
    <q-separator />
    
    <!-- Tab content -->
    <q-tab-panels v-model="tab" animated>
      <q-tab-panel name="details" class="q-pa-lg">
        <div class="text-h6 q-mb-md">Medicine Details</div>
        <!-- Details content -->
      </q-tab-panel>
      
      <q-tab-panel name="stock" class="q-pa-lg">
        <div class="text-h6 q-mb-md">Stock Information</div>
        <!-- Stock content -->
      </q-tab-panel>
      
      <q-tab-panel name="history" class="q-pa-lg">
        <div class="text-h6 q-mb-md">Transaction History</div>
        <!-- History content -->
      </q-tab-panel>
      
      <q-tab-panel name="reports" class="q-pa-lg">
        <div class="text-h6 q-mb-md">Reports & Analytics</div>
        <!-- Reports content -->
      </q-tab-panel>
    </q-tab-panels>
  </div>
</template>
```

### QBreadcrumbs

Komponen untuk breadcrumb navigation.

```vue
<template>
  <q-breadcrumbs class="q-mb-lg">
    <q-breadcrumbs-el label="Home" icon="home" to="/" />
    <q-breadcrumbs-el label="Inventory" to="/inventory" />
    <q-breadcrumbs-el label="Medicines" to="/inventory/medicines" />
    <q-breadcrumbs-el :label="medicine.name" />
  </q-breadcrumbs>
</template>
```

## Form Components

### QInput

Komponen input untuk berbagai jenis data.

```vue
<template>
  <div class="q-gutter-md">
    <!-- Basic text input -->
    <q-input 
      v-model="form.name" 
      label="Medicine Name" 
      outlined
      :rules="[val => !!val || 'Name is required']"
    />
    
    <!-- Input with icon -->
    <q-input 
      v-model="search" 
      label="Search medicines" 
      outlined
      clearable
    >
      <template v-slot:prepend>
        <q-icon name="search" />
      </template>
    </q-input>
    
    <!-- Number input with prefix -->
    <q-input 
      v-model.number="form.price" 
      label="Price" 
      type="number"
      outlined
      prefix="Rp"
      :rules="[val => val > 0 || 'Price must be greater than 0']"
    />
    
    <!-- Textarea -->
    <q-input 
      v-model="form.description" 
      label="Description" 
      type="textarea"
      outlined
      rows="3"
      counter
      maxlength="500"
    />
    
    <!-- Password input -->
    <q-input 
      v-model="form.password" 
      label="Password" 
      :type="showPassword ? 'text' : 'password'"
      outlined
    >
      <template v-slot:append>
        <q-icon 
          :name="showPassword ? 'visibility_off' : 'visibility'"
          class="cursor-pointer"
          @click="showPassword = !showPassword"
        />
      </template>
    </q-input>
    
    <!-- Input with validation states -->
    <q-input 
      v-model="form.email" 
      label="Email" 
      type="email"
      outlined
      :error="!!errors.email"
      :error-message="errors.email"
      :rules="emailRules"
    />
  </div>
</template>
```

### QSelect

Komponen untuk dropdown selection.

```vue
<template>
  <div class="q-gutter-md">
    <!-- Basic select -->
    <q-select 
      v-model="form.category" 
      :options="categories" 
      label="Category" 
      outlined
      emit-value
      map-options
    />
    
    <!-- Multiple selection -->
    <q-select 
      v-model="form.tags" 
      :options="availableTags" 
      label="Tags" 
      outlined
      multiple
      use-chips
      stack-label
    />
    
    <!-- Searchable select -->
    <q-select 
      v-model="form.supplier" 
      :options="filteredSuppliers" 
      label="Supplier" 
      outlined
      use-input
      input-debounce="300"
      @filter="filterSuppliers"
      option-label="name"
      option-value="id"
    >
      <template v-slot:no-option>
        <q-item>
          <q-item-section class="text-grey">
            No results
          </q-item-section>
        </q-item>
      </template>
    </q-select>
    
    <!-- Select with custom options -->
    <q-select 
      v-model="form.status" 
      :options="statusOptions" 
      label="Status" 
      outlined
    >
      <template v-slot:option="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section avatar>
            <q-icon :name="scope.opt.icon" :color="scope.opt.color" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ scope.opt.label }}</q-item-label>
            <q-item-label caption>{{ scope.opt.description }}</q-item-label>
          </q-item-section>
        </q-item>
      </template>
    </q-select>
  </div>
</template>

<script>
export default {
  data() {
    return {
      statusOptions: [
        {
          label: 'Active',
          value: 'active',
          icon: 'check_circle',
          color: 'positive',
          description: 'Medicine is available for sale'
        },
        {
          label: 'Inactive',
          value: 'inactive',
          icon: 'pause_circle',
          color: 'grey',
          description: 'Medicine is temporarily unavailable'
        },
        {
          label: 'Discontinued',
          value: 'discontinued',
          icon: 'cancel',
          color: 'negative',
          description: 'Medicine is no longer available'
        }
      ]
    }
  }
}
</script>
```

### QCheckbox & QRadio

Komponen untuk pilihan boolean dan radio.

```vue
<template>
  <div class="q-gutter-md">
    <!-- Checkbox -->
    <q-checkbox 
      v-model="form.prescriptionRequired" 
      label="Prescription Required" 
    />
    
    <!-- Checkbox with description -->
    <q-checkbox 
      v-model="form.notifications" 
      label="Email Notifications"
    >
      <div class="text-caption text-grey-6 q-ml-sm">
        Receive email notifications for low stock alerts
      </div>
    </q-checkbox>
    
    <!-- Radio group -->
    <div class="text-subtitle2 q-mb-sm">Medicine Type</div>
    <q-radio 
      v-model="form.type" 
      val="tablet" 
      label="Tablet" 
    />
    <q-radio 
      v-model="form.type" 
      val="capsule" 
      label="Capsule" 
    />
    <q-radio 
      v-model="form.type" 
      val="syrup" 
      label="Syrup" 
    />
    <q-radio 
      v-model="form.type" 
      val="injection" 
      label="Injection" 
    />
    
    <!-- Toggle -->
    <q-toggle 
      v-model="form.active" 
      label="Active Status"
      color="positive"
    />
  </div>
</template>
```

### QDate & QTime

Komponen untuk input tanggal dan waktu.

```vue
<template>
  <div class="q-gutter-md">
    <!-- Date input -->
    <q-input 
      v-model="form.expiryDate" 
      label="Expiry Date" 
      outlined
      readonly
    >
      <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-date 
              v-model="form.expiryDate" 
              :options="dateOptions"
              @update:model-value="closePopup"
            >
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
    
    <!-- Time input -->
    <q-input 
      v-model="form.reminderTime" 
      label="Reminder Time" 
      outlined
      readonly
    >
      <template v-slot:append>
        <q-icon name="access_time" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-time 
              v-model="form.reminderTime" 
              format24h
              @update:model-value="closePopup"
            >
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-time>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
    
    <!-- Date range -->
    <q-input 
      v-model="dateRange" 
      label="Report Date Range" 
      outlined
      readonly
    >
      <template v-slot:append>
        <q-icon name="date_range" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-date 
              v-model="form.dateRange" 
              range
              @update:model-value="updateDateRange"
            >
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
  </div>
</template>
```

## Data Display Components

### QTable

Komponen tabel untuk menampilkan data.

```vue
<template>
  <q-table
    :rows="medicines"
    :columns="columns"
    row-key="id"
    :pagination="pagination"
    :loading="loading"
    :filter="filter"
    @request="onRequest"
    binary-state-sort
    flat
    bordered
  >
    <!-- Table header -->
    <template v-slot:top-left>
      <div class="text-h6">Medicine Inventory</div>
    </template>
    
    <template v-slot:top-right>
      <q-input 
        borderless 
        dense 
        debounce="300" 
        v-model="filter" 
        placeholder="Search medicines"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>
    
    <!-- Custom column: Image -->
    <template v-slot:body-cell-image="props">
      <q-td :props="props">
        <q-avatar size="40px">
          <img :src="props.row.image" :alt="props.row.name" />
        </q-avatar>
      </q-td>
    </template>
    
    <!-- Custom column: Status -->
    <template v-slot:body-cell-status="props">
      <q-td :props="props">
        <q-chip 
          :color="getStatusColor(props.value)" 
          text-color="white" 
          :icon="getStatusIcon(props.value)"
          size="sm"
        >
          {{ props.value }}
        </q-chip>
      </q-td>
    </template>
    
    <!-- Custom column: Stock -->
    <template v-slot:body-cell-stock="props">
      <q-td :props="props">
        <div class="row items-center">
          <span :class="getStockClass(props.value)">{{ props.value }}</span>
          <q-icon 
            v-if="props.value < 10" 
            name="warning" 
            color="warning" 
            class="q-ml-xs"
          />
        </div>
      </q-td>
    </template>
    
    <!-- Custom column: Price -->
    <template v-slot:body-cell-price="props">
      <q-td :props="props">
        <div class="text-weight-medium">
          {{ formatCurrency(props.value) }}
        </div>
      </q-td>
    </template>
    
    <!-- Custom column: Actions -->
    <template v-slot:body-cell-actions="props">
      <q-td :props="props">
        <q-btn 
          flat 
          round 
          icon="edit" 
          size="sm" 
          color="primary"
          @click="edit(props.row)"
        />
        <q-btn 
          flat 
          round 
          icon="delete" 
          size="sm" 
          color="negative"
          @click="confirmDelete(props.row)"
        />
        <q-btn 
          flat 
          round 
          icon="more_vert" 
          size="sm"
        >
          <q-menu>
            <q-list>
              <q-item clickable @click="duplicate(props.row)">
                <q-item-section>Duplicate</q-item-section>
              </q-item>
              <q-item clickable @click="viewHistory(props.row)">
                <q-item-section>View History</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-td>
    </template>
    
    <!-- No data state -->
    <template v-slot:no-data="{ icon, message, filter }">
      <div class="full-width row flex-center text-accent q-gutter-sm">
        <q-icon size="2em" name="sentiment_dissatisfied" />
        <span>
          Sorry, no medicines found
        </span>
      </div>
    </template>
  </q-table>
</template>

<script>
export default {
  data() {
    return {
      columns: [
        {
          name: 'image',
          label: '',
          field: 'image',
          align: 'center',
          sortable: false,
          style: 'width: 60px'
        },
        {
          name: 'name',
          required: true,
          label: 'Medicine Name',
          align: 'left',
          field: 'name',
          sortable: true
        },
        {
          name: 'category',
          label: 'Category',
          field: 'category',
          sortable: true
        },
        {
          name: 'stock',
          label: 'Stock',
          field: 'stock',
          sortable: true,
          align: 'center'
        },
        {
          name: 'price',
          label: 'Price',
          field: 'price',
          sortable: true,
          align: 'right'
        },
        {
          name: 'status',
          label: 'Status',
          field: 'status',
          sortable: true,
          align: 'center'
        },
        {
          name: 'actions',
          label: 'Actions',
          field: 'actions',
          align: 'center',
          sortable: false,
          style: 'width: 120px'
        }
      ]
    }
  },
  
  methods: {
    getStatusColor(status) {
      const colors = {
        active: 'positive',
        inactive: 'grey',
        discontinued: 'negative'
      }
      return colors[status] || 'grey'
    },
    
    getStatusIcon(status) {
      const icons = {
        active: 'check_circle',
        inactive: 'pause_circle',
        discontinued: 'cancel'
      }
      return icons[status] || 'help'
    },
    
    getStockClass(stock) {
      if (stock === 0) return 'text-negative text-weight-bold'
      if (stock < 10) return 'text-warning text-weight-medium'
      return 'text-positive'
    }
  }
}
</script>
```

## Feedback Components

### QDialog

Komponen untuk modal dan dialog.

```vue
<template>
  <div>
    <!-- Confirmation dialog -->
    <q-dialog v-model="showDeleteDialog" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Confirm Deletion</div>
        </q-card-section>
        
        <q-card-section class="q-pt-none">
          Are you sure you want to delete <strong>{{ selectedMedicine?.name }}</strong>? 
          This action cannot be undone.
        </q-card-section>
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup />
          <q-btn 
            flat 
            label="Delete" 
            color="negative" 
            @click="deleteMedicine" 
            :loading="deleting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Form dialog -->
    <q-dialog v-model="showFormDialog" @hide="resetForm">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} Medicine</div>
        </q-card-section>
        
        <q-card-section>
          <q-form @submit="saveMedicine" class="q-gutter-md">
            <q-input 
              v-model="form.name" 
              label="Medicine Name" 
              outlined
              :rules="[val => !!val || 'Name is required']"
            />
            
            <q-select 
              v-model="form.category" 
              :options="categories" 
              label="Category" 
              outlined
            />
            
            <div class="row q-gutter-md">
              <div class="col">
                <q-input 
                  v-model.number="form.price" 
                  label="Price" 
                  type="number"
                  outlined
                  prefix="Rp"
                />
              </div>
              <div class="col">
                <q-input 
                  v-model.number="form.stock" 
                  label="Stock" 
                  type="number"
                  outlined
                />
              </div>
            </div>
          </q-form>
        </q-card-section>
        
        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup />
          <q-btn 
            flat 
            label="Save" 
            color="primary" 
            @click="saveMedicine"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    
    <!-- Fullscreen dialog -->
    <q-dialog v-model="showReportDialog" full-width full-height>
      <q-card>
        <q-bar class="bg-primary text-white">
          <div class="text-h6">Sales Report</div>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup />
        </q-bar>
        
        <q-card-section>
          <!-- Report content -->
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
```

### QNotify

Sistem notifikasi untuk feedback pengguna.

```javascript
// Success notification
this.$q.notify({
  type: 'positive',
  message: 'Medicine saved successfully',
  position: 'top-right',
  timeout: 3000,
  actions: [
    { label: 'View', color: 'white', handler: () => this.viewMedicine() }
  ]
})

// Error notification
this.$q.notify({
  type: 'negative',
  message: 'Failed to save medicine. Please try again.',
  position: 'top-right',
  timeout: 5000,
  actions: [
    { label: 'Retry', color: 'white', handler: () => this.retry() },
    { label: 'Dismiss', color: 'white' }
  ]
})

// Warning notification
this.$q.notify({
  type: 'warning',
  message: 'Low stock alert: Only 5 units remaining',
  position: 'top-right',
  timeout: 0, // Persistent
  actions: [
    { label: 'Reorder', color: 'white', handler: () => this.reorder() },
    { label: 'Dismiss', color: 'white' }
  ]
})

// Info notification
this.$q.notify({
  type: 'info',
  message: 'System maintenance scheduled for tonight',
  position: 'top-right',
  timeout: 10000
})

// Custom notification
this.$q.notify({
  color: 'purple',
  textColor: 'white',
  icon: 'cloud_done',
  message: 'Data synchronized successfully',
  position: 'bottom-right'
})
```

### QBanner

Komponen untuk pesan penting dan alert.

```vue
<template>
  <div>
    <!-- Error banner -->
    <q-banner v-if="errors.length" class="text-white bg-negative q-mb-md">
      <template v-slot:avatar>
        <q-icon name="error" />
      </template>
      <div class="text-subtitle2">Please fix the following errors:</div>
      <ul class="q-ma-none q-pl-md">
        <li v-for="error in errors" :key="error">{{ error }}</li>
      </ul>
      <template v-slot:action>
        <q-btn flat label="Dismiss" @click="clearErrors" />
      </template>
    </q-banner>
    
    <!-- Warning banner -->
    <q-banner class="text-white bg-warning q-mb-md">
      <template v-slot:avatar>
        <q-icon name="warning" />
      </template>
      <strong>Low Stock Alert:</strong> {{ lowStockCount }} medicines are running low on stock.
      <template v-slot:action>
        <q-btn flat label="View Details" @click="viewLowStock" />
        <q-btn flat label="Dismiss" @click="dismissAlert" />
      </template>
    </q-banner>
    
    <!-- Info banner -->
    <q-banner class="text-white bg-info q-mb-md">
      <template v-slot:avatar>
        <q-icon name="info" />
      </template>
      System maintenance is scheduled for tonight from 2:00 AM to 4:00 AM.
      <template v-slot:action>
        <q-btn flat label="Learn More" @click="viewMaintenance" />
      </template>
    </q-banner>
    
    <!-- Success banner -->
    <q-banner class="text-white bg-positive q-mb-md">
      <template v-slot:avatar>
        <q-icon name="check_circle" />
      </template>
      Inventory sync completed successfully. {{ syncedCount }} items updated.
    </q-banner>
  </div>
</template>
```

## Action Components

### QBtn

Komponen button dengan berbagai variasi.

```vue
<template>
  <div class="q-gutter-md">
    <!-- Primary actions -->
    <q-btn color="primary" label="Save Medicine" icon="save" />
    <q-btn color="primary" label="Add New" icon="add" />
    
    <!-- Secondary actions -->
    <q-btn color="secondary" label="Export Data" icon="download" outline />
    <q-btn color="grey" label="Cancel" flat />
    
    <!-- Destructive actions -->
    <q-btn color="negative" label="Delete" icon="delete" />
    <q-btn color="negative" label="Remove All" icon="clear_all" outline />
    
    <!-- Icon buttons -->
    <q-btn icon="edit" round color="primary" />
    <q-btn icon="delete" round color="negative" />
    <q-btn icon="more_vert" round flat />
    
    <!-- Loading states -->
    <q-btn 
      :loading="saving" 
      label="Save" 
      color="primary"
      @click="save"
    >
      <template v-slot:loading>
        <q-spinner-hourglass class="on-left" />
        Saving...
      </template>
    </q-btn>
    
    <!-- Button with dropdown -->
    <q-btn-dropdown color="primary" label="Actions">
      <q-list>
        <q-item clickable v-close-popup @click="export">
          <q-item-section avatar>
            <q-icon name="download" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Export Data</q-item-label>
          </q-item-section>
        </q-item>
        
        <q-item clickable v-close-popup @click="import">
          <q-item-section avatar>
            <q-icon name="upload" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Import Data</q-item-label>
          </q-item-section>
        </q-item>
        
        <q-separator />
        
        <q-item clickable v-close-popup @click="settings">
          <q-item-section avatar>
            <q-icon name="settings" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Settings</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
    
    <!-- Floating action button -->
    <q-page-sticky position="bottom-right" :offset="[18, 18]">
      <q-btn fab icon="add" color="primary" @click="addMedicine" />
    </q-page-sticky>
  </div>
</template>
```

### QChip

Komponen untuk tags dan status indicators.

```vue
<template>
  <div class="q-gutter-sm">
    <!-- Status chips -->
    <q-chip 
      color="positive" 
      text-color="white" 
      icon="check_circle"
    >
      Active
    </q-chip>
    
    <q-chip 
      color="warning" 
      text-color="white" 
      icon="warning"
    >
      Low Stock
    </q-chip>
    
    <q-chip 
      color="negative" 
      text-color="white" 
      icon="cancel"
    >
      Out of Stock
    </q-chip>
    
    <!-- Category chips -->
    <q-chip 
      v-for="category in medicine.categories" 
      :key="category"
      color="primary" 
      text-color="white"
      removable
      @remove="removeCategory(category)"
    >
      {{ category }}
    </q-chip>
    
    <!-- Interactive chips -->
    <q-chip 
      clickable 
      color="grey-3" 
      text-color="grey-8"
      icon="add"
      @click="addCategory"
    >
      Add Category
    </q-chip>
  </div>
</template>
```

## Pharmacy-Specific Components

### Medicine Card

Komponen khusus untuk menampilkan informasi obat.

```vue
<template>
  <q-card class="medicine-card" :class="{ 'low-stock': medicine.stock < 10 }">
    <q-img 
      :src="medicine.image || '/default-medicine.png'" 
      :alt="medicine.name"
      height="200px"
      class="medicine-image"
    >
      <div class="absolute-top-right q-ma-sm">
        <q-chip 
          :color="getStockColor(medicine.stock)" 
          text-color="white" 
          size="sm"
        >
          {{ medicine.stock }} units
        </q-chip>
      </div>
    </q-img>
    
    <q-card-section>
      <div class="text-h6 text-weight-medium">{{ medicine.name }}</div>
      <div class="text-subtitle2 text-grey-7">{{ medicine.category }}</div>
      
      <div class="row items-center q-mt-md">
        <div class="col">
          <div class="text-caption text-grey-6">Price</div>
          <div class="text-h6 text-positive">{{ formatCurrency(medicine.price) }}</div>
        </div>
        <div class="col-auto">
          <q-rating 
            v-model="medicine.rating" 
            size="sm" 
            color="warning"
            readonly
          />
        </div>
      </div>
      
      <div v-if="medicine.expiryDate" class="q-mt-sm">
        <div class="text-caption text-grey-6">Expires</div>
        <div :class="getExpiryClass(medicine.expiryDate)">
          {{ formatDate(medicine.expiryDate) }}
        </div>
      </div>
    </q-card-section>
    
    <q-card-actions>
      <q-btn flat color="primary" icon="edit" @click="edit(medicine)">
        Edit
      </q-btn>
      <q-btn flat color="primary" icon="shopping_cart" @click="addToCart(medicine)">
        Add to Cart
      </q-btn>
      <q-space />
      <q-btn flat round icon="more_vert">
        <q-menu>
          <q-list>
            <q-item clickable @click="viewDetails(medicine)">
              <q-item-section>View Details</q-item-section>
            </q-item>
            <q-item clickable @click="duplicate(medicine)">
              <q-item-section>Duplicate</q-item-section>
            </q-item>
            <q-separator />
            <q-item clickable @click="delete(medicine)">
              <q-item-section class="text-negative">Delete</q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
    </q-card-actions>
  </q-card>
</template>

<style scoped>
.medicine-card {
  max-width: 300px;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  
  &.low-stock {
    border-left: 4px solid var(--q-warning);
  }
}

.medicine-image {
  position: relative;
}
</style>
```

### POS Cart Component

Komponen untuk keranjang Point of Sale.

```vue
<template>
  <q-card class="pos-cart">
    <q-card-section>
      <div class="text-h6 q-mb-md">Shopping Cart</div>
      
      <q-list v-if="cartItems.length" separator>
        <q-item v-for="item in cartItems" :key="item.id">
          <q-item-section avatar>
            <q-avatar size="40px">
              <img :src="item.image" :alt="item.name" />
            </q-avatar>
          </q-item-section>
          
          <q-item-section>
            <q-item-label>{{ item.name }}</q-item-label>
            <q-item-label caption>{{ formatCurrency(item.price) }} each</q-item-label>
          </q-item-section>
          
          <q-item-section side>
            <div class="row items-center q-gutter-xs">
              <q-btn 
                flat 
                round 
                icon="remove" 
                size="sm"
                @click="decreaseQuantity(item)"
                :disable="item.quantity <= 1"
              />
              <span class="text-weight-medium">{{ item.quantity }}</span>
              <q-btn 
                flat 
                round 
                icon="add" 
                size="sm"
                @click="increaseQuantity(item)"
                :disable="item.quantity >= item.stock"
              />
            </div>
          </q-item-section>
          
          <q-item-section side>
            <div class="text-weight-medium">
              {{ formatCurrency(item.price * item.quantity) }}
            </div>
            <q-btn 
              flat 
              round 
              icon="delete" 
              size="sm" 
              color="negative"
              @click="removeFromCart(item)"
            />
          </q-item-section>
        </q-item>
      </q-list>
      
      <div v-else class="text-center q-py-lg text-grey-6">
        <q-icon name="shopping_cart" size="48px" />
        <div class="q-mt-md">Cart is empty</div>
      </div>
    </q-card-section>
    
    <q-separator v-if="cartItems.length" />
    
    <q-card-section v-if="cartItems.length">
      <div class="row items-center justify-between q-mb-sm">
        <span>Subtotal:</span>
        <span class="text-weight-medium">{{ formatCurrency(subtotal) }}</span>
      </div>
      
      <div class="row items-center justify-between q-mb-sm">
        <span>Tax ({{ taxRate }}%):</span>
        <span class="text-weight-medium">{{ formatCurrency(tax) }}</span>
      </div>
      
      <div class="row items-center justify-between q-mb-md">
        <span>Discount:</span>
        <span class="text-weight-medium text-negative">-{{ formatCurrency(discount) }}</span>
      </div>
      
      <q-separator class="q-mb-md" />
      
      <div class="row items-center justify-between q-mb-lg">
        <span class="text-h6">Total:</span>
        <span class="text-h6 text-positive">{{ formatCurrency(total) }}</span>
      </div>
      
      <q-btn 
        color="primary" 
        label="Proceed to Checkout" 
        icon="payment"
        class="full-width"
        @click="checkout"
        :loading="processing"
      />
    </q-card-section>
  </q-card>
</template>
```

## Component Guidelines

### Naming Conventions

```javascript
// Component names should be PascalCase
MedicineCard.vue
POSCart.vue
InventoryTable.vue

// Props should be camelCase
props: {
  medicineData: Object,
  showActions: Boolean,
  isLoading: Boolean
}

// Events should be kebab-case
this.$emit('medicine-selected', medicine)
this.$emit('cart-updated', cartItems)
```

### Props Validation

```javascript
export default {
  props: {
    medicine: {
      type: Object,
      required: true,
      validator: (value) => {
        return value && value.id && value.name
      }
    },
    
    showActions: {
      type: Boolean,
      default: true
    },
    
    size: {
      type: String,
      default: 'medium',
      validator: (value) => {
        return ['small', 'medium', 'large'].includes(value)
      }
    }
  }
}
```

### Accessibility Guidelines

```vue
<template>
  <!-- Always provide proper labels -->
  <q-btn 
    icon="edit" 
    :aria-label="`Edit ${medicine.name}`"
    @click="edit(medicine)"
  />
  
  <!-- Use semantic HTML -->
  <table role="table" aria-label="Medicine inventory">
    <thead>
      <tr>
        <th scope="col">Medicine Name</th>
        <th scope="col">Stock</th>
      </tr>
    </thead>
  </table>
  
  <!-- Provide keyboard navigation -->
  <div 
    tabindex="0"
    @keydown.enter="selectMedicine"
    @keydown.space="selectMedicine"
  >
    <!-- Interactive content -->
  </div>
</template>
```

### Performance Best Practices

```vue
<template>
  <!-- Use v-show for frequently toggled elements -->
  <div v-show="isVisible">Frequently toggled content</div>
  
  <!-- Use v-if for conditionally rendered elements -->
  <div v-if="hasPermission">Admin only content</div>
  
  <!-- Use key for list items -->
  <q-item v-for="medicine in medicines" :key="medicine.id">
    {{ medicine.name }}
  </q-item>
  
  <!-- Lazy load images -->
  <q-img 
    :src="medicine.image" 
    loading="lazy"
    :alt="medicine.name"
  />
</template>

<script>
export default {
  // Use computed for derived data
  computed: {
    filteredMedicines() {
      return this.medicines.filter(m => 
        m.name.toLowerCase().includes(this.search.toLowerCase())
      )
    }
  },
  
  // Use watchers sparingly
  watch: {
    search: {
      handler: 'performSearch',
      immediate: true
    }
  }
}
</script>
```

## Cross-References

- [Design System](../../../docs/DESIGN_SYSTEM.md) - Color palette, typography, and spacing
- [UI/UX Guidelines](../../../docs/UI_UX_GUIDELINES.md) - User experience patterns
- [Accessibility Guidelines](../../../docs/ACCESSIBILITY.md) - Inclusive design practices
- [Quasar Components Documentation](https://quasar.dev/vue-components/)

## Maintenance

Komponen ini harus diperbarui secara berkala untuk:

1. **Konsistensi**: Memastikan semua komponen mengikuti design system
2. **Performance**: Optimasi berdasarkan usage patterns
3. **Accessibility**: Memenuhi standar WCAG terbaru
4. **Best Practices**: Mengikuti Vue.js dan Quasar best practices

---

**Last Updated**: September 2025  
**Version**: 0.0.1
**Maintainer**: Q-Pharmacy Development Team
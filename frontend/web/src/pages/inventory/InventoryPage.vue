<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Inventory Management</h4>
        <p class="text-grey-6">Manage your inventory, stock movements, and adjustments</p>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Total Products</div>
                <div class="text-h4 text-primary">{{ stats.totalProducts }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="inventory" size="2rem" color="primary" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Low Stock Items</div>
                <div class="text-h4 text-warning">{{ stats.lowStockItems }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="warning" size="2rem" color="warning" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Out of Stock</div>
                <div class="text-h4 text-negative">{{ stats.outOfStockItems }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="error" size="2rem" color="negative" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Total Value</div>
                <div class="text-h4 text-positive">{{ formatCurrency(stats.totalValue) }}</div>
              </div>
              <div class="col-auto">
                <q-icon name="attach_money" size="2rem" color="positive" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Inventory Actions -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-4">
        <q-card class="cursor-pointer" @click="$router.push('/app/inventories/stock-in')">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Stock In</div>
                <div class="text-caption text-grey-6">Add inventory to stock</div>
              </div>
              <div class="col-auto">
                <q-icon name="add_circle" size="2rem" color="positive" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-md-4">
        <q-card class="cursor-pointer" @click="$router.push('/app/inventories/stock-out')">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Stock Out</div>
                <div class="text-caption text-grey-6">Remove inventory from stock</div>
              </div>
              <div class="col-auto">
                <q-icon name="remove_circle" size="2rem" color="negative" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-md-4">
        <q-card class="cursor-pointer" @click="$router.push('/app/inventories/adjustments')">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6">Adjustments</div>
                <div class="text-caption text-grey-6">Adjust stock quantities</div>
              </div>
              <div class="col-auto">
                <q-icon name="tune" size="2rem" color="info" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Recent Stock Movements -->
    <q-card>
      <q-card-section>
        <div class="row items-center q-mb-md">
          <div class="col">
            <div class="text-h6">Recent Stock Movements</div>
          </div>
          <div class="col-auto">
            <q-btn color="primary" icon="history" label="View All" @click="$router.push('/app/products')" />
          </div>
        </div>
        
        <q-table
          :rows="recentMovements"
          :columns="movementColumns"
          row-key="id"
          flat
          :pagination="{ rowsPerPage: 5 }"
        >
          <template v-slot:body-cell-type="props">
            <q-td :props="props">
              <q-chip
                :color="getMovementColor(props.value)"
                text-color="white"
                size="sm"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>
          <template v-slot:body-cell-quantity="props">
            <q-td :props="props">
              <span :class="getQuantityClass(props.row.type)">
                {{ props.row.type === 'stock-out' ? '-' : '+' }}{{ props.value }}
              </span>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  totalProducts: 0,
  lowStockItems: 0,
  outOfStockItems: 0,
  totalValue: 0
})

const recentMovements = ref([
  {
    id: 1,
    product_name: 'Paracetamol 500mg',
    type: 'stock-in',
    quantity: 100,
    date: '2024-01-15',
    notes: 'Purchase from supplier'
  },
  {
    id: 2,
    product_name: 'Amoxicillin 250mg',
    type: 'stock-out',
    quantity: 50,
    date: '2024-01-15',
    notes: 'Sales transaction'
  },
  {
    id: 3,
    product_name: 'Vitamin C 1000mg',
    type: 'adjustment',
    quantity: 25,
    date: '2024-01-14',
    notes: 'Stock count adjustment'
  }
])

const movementColumns = [
  {
    name: 'product_name',
    label: 'Product',
    field: 'product_name',
    align: 'left'
  },
  {
    name: 'type',
    label: 'Type',
    field: 'type',
    align: 'center'
  },
  {
    name: 'quantity',
    label: 'Quantity',
    field: 'quantity',
    align: 'center'
  },
  {
    name: 'date',
    label: 'Date',
    field: 'date',
    align: 'left',
    format: val => new Date(val).toLocaleDateString()
  },
  {
    name: 'notes',
    label: 'Notes',
    field: 'notes',
    align: 'left'
  }
]

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount || 0)
}

const getMovementColor = (type) => {
  switch (type) {
    case 'stock-in': return 'positive'
    case 'stock-out': return 'negative'
    case 'adjustment': return 'info'
    default: return 'grey'
  }
}

const getQuantityClass = (type) => {
  switch (type) {
    case 'stock-in': return 'text-positive'
    case 'stock-out': return 'text-negative'
    case 'adjustment': return 'text-info'
    default: return ''
  }
}

const loadStats = async () => {
  // TODO: Load actual stats from API
  stats.value = {
    totalProducts: 150,
    lowStockItems: 12,
    outOfStockItems: 3,
    totalValue: 125000000
  }
}

onMounted(() => {
  loadStats()
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: scale(1.02);
}
</style>
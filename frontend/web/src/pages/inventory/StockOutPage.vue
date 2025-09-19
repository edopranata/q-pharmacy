<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Stock Out</h4>
        <p class="text-grey-6">Remove inventory from stock</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="remove"
          label="New Stock Out"
          @click="showAddDialog = true"
        />
      </div>
    </div>

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-12 col-md-3">
            <q-input
              v-model="localFilters.search"
              placeholder="Search stock out records..."
              outlined
              dense
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-md-2">
            <q-input
              v-model="localFilters.date_from"
              label="From Date"
              type="date"
              outlined
              dense
            />
          </div>
          <div class="col-12 col-md-2">
            <q-input
              v-model="localFilters.date_to"
              label="To Date"
              type="date"
              outlined
              dense
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="localFilters.reason"
              :options="reasonOptions"
              label="Reason"
              outlined
              dense
              clearable
            />
          </div>
          <div class="col-12 col-md-1">
            <q-btn
              color="primary"
              icon="refresh"
              label="Muat Ulang"
              outline
              @click="refreshData"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Stock Out Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="stockOuts"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-reason="props">
            <q-td :props="props">
              <q-chip
                :color="getReasonColor(props.value)"
                text-color="white"
                size="sm"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>
          <template v-slot:body-cell-total_cost="props">
            <q-td :props="props">
              {{ formatCurrency(props.value) }}
            </q-td>
          </template>
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                size="sm"
                icon="visibility"
                color="primary"
                @click="viewStockOut(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="edit"
                color="warning"
                @click="editStockOut(props.row)"
                :disable="props.row.reason === 'Sale'"
              >
                <q-tooltip>{{ props.row.reason === 'Sale' ? 'Cannot edit sales' : 'Edit' }}</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="deleteStockOut(props.row)"
                :disable="props.row.reason === 'Sale'"
              >
                <q-tooltip>{{ props.row.reason === 'Sale' ? 'Cannot delete sales' : 'Delete' }}</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Add/Edit Dialog -->
    <q-dialog v-model="showAddDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} Stock Out</div>
        </q-card-section>

        <q-card-section>
          <q-form>
            <q-select
              v-model="stockOutForm.product_id"
              :options="productOptions"
              option-value="id"
              option-label="name"
              label="Product"
              outlined
              required
              class="q-mb-md"
              @update:model-value="updateAvailableStock"
            />
            <div v-if="availableStock !== null" class="q-mb-md">
              <q-banner class="bg-info text-white">
                <template v-slot:avatar>
                  <q-icon name="info" />
                </template>
                Available Stock: {{ availableStock }} units
              </q-banner>
            </div>
            <q-input
              v-model.number="stockOutForm.quantity"
              label="Quantity"
              type="number"
              min="1"
              :max="availableStock"
              required
              outlined
              class="q-mb-md"
            />
            <q-select
              v-model="stockOutForm.reason"
              :options="reasonOptions"
              label="Reason"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model="stockOutForm.reference_number"
              label="Reference Number (Optional)"
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model="stockOutForm.notes"
              label="Notes (Optional)"
              type="textarea"
              outlined
              rows="3"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn color="primary" label="Save" @click="saveStockOut" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Stock Out Details</div>
        </q-card-section>

        <q-card-section v-if="selectedStockOut">
          <div class="row q-gutter-md">
            <div class="col-12">
              <q-item>
                <q-item-section>
                  <q-item-label>Product</q-item-label>
                  <q-item-label caption>{{ selectedStockOut.product_name }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Quantity</q-item-label>
                  <q-item-label caption>{{ selectedStockOut.quantity }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Reason</q-item-label>
                  <q-item-label caption>
                    <q-chip
                      :color="getReasonColor(selectedStockOut.reason)"
                      text-color="white"
                      size="sm"
                    >
                      {{ selectedStockOut.reason }}
                    </q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Unit Cost</q-item-label>
                  <q-item-label caption>{{ formatCurrency(selectedStockOut.unit_cost) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Total Cost</q-item-label>
                  <q-item-label caption>{{ formatCurrency(selectedStockOut.total_cost) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Date</q-item-label>
                  <q-item-label caption>{{ formatDate(selectedStockOut.created_at) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedStockOut.reference_number">
              <q-item>
                <q-item-section>
                  <q-item-label>Reference Number</q-item-label>
                  <q-item-label caption>{{ selectedStockOut.reference_number }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedStockOut.notes">
              <q-item>
                <q-item-section>
                  <q-item-label>Notes</q-item-label>
                  <q-item-label caption>{{ selectedStockOut.notes }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" @click="showViewDialog = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useProductStore } from 'src/stores/product'
import { debounce } from 'quasar'

const $q = useQuasar()
const productStore = useProductStore()

const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const loading = ref(false)
const selectedStockOut = ref(null)
const availableStock = ref(null)

const reasonOptions = [
  'Damaged',
  'Expired',
  'Lost',
  'Returned',
  'Transfer',
  'Sale',
  'Other'
]

const localFilters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  reason: null
})

const stockOutForm = reactive({
  id: null,
  product_id: null,
  quantity: 1,
  reason: null,
  reference_number: '',
  notes: ''
})

const stockOuts = ref([
  {
    id: 1,
    product_name: 'Paracetamol 500mg',
    quantity: 10,
    unit_cost: 5000,
    total_cost: 50000,
    reason: 'Expired',
    reference_number: 'EXP-2024-001',
    notes: 'Expired batch removed from inventory',
    created_at: '2024-01-15T10:00:00Z'
  },
  {
    id: 2,
    product_name: 'Amoxicillin 250mg',
    quantity: 5,
    unit_cost: 8000,
    total_cost: 40000,
    reason: 'Damaged',
    reference_number: 'DMG-2024-001',
    notes: 'Damaged during transport',
    created_at: '2024-01-14T14:30:00Z'
  },
  {
    id: 3,
    product_name: 'Vitamin C 1000mg',
    quantity: 2,
    unit_cost: 15000,
    total_cost: 30000,
    reason: 'Sale',
    reference_number: 'TXN-2024-001',
    notes: 'Sold to customer',
    created_at: '2024-01-13T09:15:00Z'
  }
])

const productOptions = computed(() => productStore.products)

const columns = [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    align: 'left',
    sortable: true
  },
  {
    name: 'product_name',
    label: 'Product',
    field: 'product_name',
    align: 'left',
    sortable: true
  },
  {
    name: 'quantity',
    label: 'Quantity',
    field: 'quantity',
    align: 'center',
    sortable: true
  },
  {
    name: 'reason',
    label: 'Reason',
    field: 'reason',
    align: 'center',
    sortable: true
  },
  {
    name: 'unit_cost',
    label: 'Unit Cost',
    field: 'unit_cost',
    align: 'right',
    format: val => formatCurrency(val)
  },
  {
    name: 'total_cost',
    label: 'Total Cost',
    field: 'total_cost',
    align: 'right'
  },
  {
    name: 'created_at',
    label: 'Date',
    field: 'created_at',
    align: 'left',
    format: val => formatDate(val)
  },
  {
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center'
  }
]

const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

const getReasonColor = (reason) => {
  const colors = {
    'Damaged': 'red',
    'Expired': 'orange',
    'Lost': 'purple',
    'Returned': 'blue',
    'Transfer': 'teal',
    'Sale': 'green',
    'Other': 'grey'
  }
  return colors[reason] || 'grey'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount || 0)
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

const updateAvailableStock = () => {
  if (stockOutForm.product_id) {
    // TODO: Get actual stock from API
    availableStock.value = Math.floor(Math.random() * 100) + 10
  } else {
    availableStock.value = null
  }
}

const loadStockOuts = async () => {
  loading.value = true
  try {
    // TODO: Load actual data from API
    await new Promise(resolve => setTimeout(resolve, 500))
    pagination.value.rowsNumber = stockOuts.value.length
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load stock out records'
    })
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  loadStockOuts()
}

// Watchers
watch(
  () => localFilters.search,
  debounce(() => {
    loadStockOuts()
  }, 300)
)

watch(
  () => localFilters.date_from,
  () => {
    loadStockOuts()
  }
)

watch(
  () => localFilters.date_to,
  () => {
    loadStockOuts()
  }
)

watch(
  () => localFilters.reason,
  () => {
    loadStockOuts()
  }
)

const onRequest = (props) => {
  pagination.value = props.pagination
  loadStockOuts()
}

const viewStockOut = (stockOut) => {
  selectedStockOut.value = stockOut
  showViewDialog.value = true
}

const editStockOut = (stockOut) => {
  if (stockOut.reason === 'Sale') {
    $q.notify({
      type: 'warning',
      message: 'Cannot edit stock out records from sales'
    })
    return
  }
  
  editMode.value = true
  stockOutForm.id = stockOut.id
  stockOutForm.product_id = stockOut.product_id
  stockOutForm.quantity = stockOut.quantity
  stockOutForm.reason = stockOut.reason
  stockOutForm.reference_number = stockOut.reference_number
  stockOutForm.notes = stockOut.notes
  updateAvailableStock()
  showAddDialog.value = true
}

const deleteStockOut = (stockOut) => {
  if (stockOut.reason === 'Sale') {
    $q.notify({
      type: 'warning',
      message: 'Cannot delete stock out records from sales'
    })
    return
  }
  
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete this stock out record for ${stockOut.product_name}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      // TODO: Delete from API
      const index = stockOuts.value.findIndex(item => item.id === stockOut.id)
      if (index > -1) {
        stockOuts.value.splice(index, 1)
      }
      $q.notify({
        type: 'positive',
        message: 'Stock out record deleted successfully'
      })
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to delete stock out record'
      })
    }
  })
}

const saveStockOut = async () => {
  if (stockOutForm.quantity > availableStock.value) {
    $q.notify({
      type: 'negative',
      message: 'Quantity cannot exceed available stock'
    })
    return
  }
  
  try {
    // TODO: Save to API
    const selectedProduct = productOptions.value.find(p => p.id === stockOutForm.product_id)
    const newStockOut = {
      id: editMode.value ? stockOutForm.id : Date.now(),
      product_name: selectedProduct?.name || 'Unknown Product',
      quantity: stockOutForm.quantity,
      unit_cost: selectedProduct?.cost || 0,
      total_cost: stockOutForm.quantity * (selectedProduct?.cost || 0),
      reason: stockOutForm.reason,
      reference_number: stockOutForm.reference_number,
      notes: stockOutForm.notes,
      created_at: new Date().toISOString()
    }

    if (editMode.value) {
      const index = stockOuts.value.findIndex(item => item.id === stockOutForm.id)
      if (index > -1) {
        stockOuts.value[index] = newStockOut
      }
    } else {
      stockOuts.value.unshift(newStockOut)
    }

    $q.notify({
      type: 'positive',
      message: `Stock out ${editMode.value ? 'updated' : 'created'} successfully`
    })

    closeDialog()
  } catch {
    $q.notify({
      type: 'negative',
      message: `Failed to ${editMode.value ? 'update' : 'create'} stock out record`
    })
  }
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  stockOutForm.id = null
  stockOutForm.product_id = null
  stockOutForm.quantity = 1
  stockOutForm.reason = null
  stockOutForm.reference_number = ''
  stockOutForm.notes = ''
  availableStock.value = null
}

const fetchProducts = async () => {
  try {
    await productStore.fetchProducts()
  } catch (error) {
    console.error('Error fetching products:', error.message)
  }
}

onMounted(() => {
  // Initialize local filters
  localFilters.search = ''
  localFilters.date_from = ''
  localFilters.date_to = ''
  localFilters.reason = null
  
  loadStockOuts()
  fetchProducts()
})
</script>
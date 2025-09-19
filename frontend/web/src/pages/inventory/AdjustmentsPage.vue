<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Stock Adjustments</h4>
        <p class="text-grey-6">Adjust inventory stock levels</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="secondary"
          icon="refresh"
          label="Muat Ulang"
          @click="refreshData"
          class="q-mr-sm"
        />
        <q-btn
          color="primary"
          icon="tune"
          label="New Adjustment"
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
              placeholder="Search adjustments..."
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
              v-model="localFilters.type"
              :options="typeOptions"
              label="Type"
              outlined
              dense
              clearable
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Adjustments Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="adjustments"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-type="props">
            <q-td :props="props">
              <q-chip
                :color="getTypeColor(props.value)"
                text-color="white"
                size="sm"
                :icon="getTypeIcon(props.value)"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>
          <template v-slot:body-cell-quantity_change="props">
            <q-td :props="props">
              <span :class="props.value > 0 ? 'text-positive' : 'text-negative'">
                {{ props.value > 0 ? '+' : '' }}{{ props.value }}
              </span>
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
                @click="viewAdjustment(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="edit"
                color="warning"
                @click="editAdjustment(props.row)"
              >
                <q-tooltip>Edit</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="deleteAdjustment(props.row)"
              >
                <q-tooltip>Delete</q-tooltip>
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
          <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} Stock Adjustment</div>
        </q-card-section>

        <q-card-section>
          <q-form>
            <q-select
              v-model="adjustmentForm.product_id"
              :options="productOptions"
              option-value="id"
              option-label="name"
              label="Product"
              outlined
              required
              class="q-mb-md"
              @update:model-value="updateCurrentStock"
            />
            <div v-if="currentStock !== null" class="q-mb-md">
              <q-banner class="bg-info text-white">
                <template v-slot:avatar>
                  <q-icon name="info" />
                </template>
                Current Stock: {{ currentStock }} units
              </q-banner>
            </div>
            <q-select
              v-model="adjustmentForm.type"
              :options="typeOptions"
              label="Adjustment Type"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="adjustmentForm.old_quantity"
              label="Current Quantity"
              type="number"
              outlined
              readonly
              class="q-mb-md"
            />
            <q-input
              v-model.number="adjustmentForm.new_quantity"
              label="New Quantity"
              type="number"
              min="0"
              required
              outlined
              class="q-mb-md"
              @update:model-value="calculateChange"
            />
            <q-input
              v-model.number="adjustmentForm.quantity_change"
              label="Quantity Change"
              type="number"
              outlined
              readonly
              class="q-mb-md"
              :class="adjustmentForm.quantity_change > 0 ? 'text-positive' : 'text-negative'"
            />
            <q-input
              v-model="adjustmentForm.reason"
              label="Reason"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model="adjustmentForm.reference_number"
              label="Reference Number (Optional)"
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model="adjustmentForm.notes"
              label="Notes (Optional)"
              type="textarea"
              outlined
              rows="3"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn color="primary" label="Save" @click="saveAdjustment" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Adjustment Details</div>
        </q-card-section>

        <q-card-section v-if="selectedAdjustment">
          <div class="row q-gutter-md">
            <div class="col-12">
              <q-item>
                <q-item-section>
                  <q-item-label>Product</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.product_name }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Type</q-item-label>
                  <q-item-label caption>
                    <q-chip
                      :color="getTypeColor(selectedAdjustment.type)"
                      text-color="white"
                      size="sm"
                      :icon="getTypeIcon(selectedAdjustment.type)"
                    >
                      {{ selectedAdjustment.type }}
                    </q-chip>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Quantity Change</q-item-label>
                  <q-item-label caption :class="selectedAdjustment.quantity_change > 0 ? 'text-positive' : 'text-negative'">
                    {{ selectedAdjustment.quantity_change > 0 ? '+' : '' }}{{ selectedAdjustment.quantity_change }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Old Quantity</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.old_quantity }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>New Quantity</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.new_quantity }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12">
              <q-item>
                <q-item-section>
                  <q-item-label>Reason</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.reason }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Date</q-item-label>
                  <q-item-label caption>{{ formatDate(selectedAdjustment.created_at) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedAdjustment.reference_number">
              <q-item>
                <q-item-section>
                  <q-item-label>Reference Number</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.reference_number }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedAdjustment.notes">
              <q-item>
                <q-item-section>
                  <q-item-label>Notes</q-item-label>
                  <q-item-label caption>{{ selectedAdjustment.notes }}</q-item-label>
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
import { useQuasar, debounce } from 'quasar'
import { useProductStore } from 'src/stores/product'

const $q = useQuasar()
const productStore = useProductStore()

const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const loading = ref(false)
const selectedAdjustment = ref(null)
const currentStock = ref(null)

const typeOptions = [
  'Stock Count',
  'Damage',
  'Loss',
  'Found',
  'Transfer',
  'Other'
]

const filters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  type: null
})

// Local filters for reactive UI
const localFilters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  type: null
})

// Watch for changes in local filters with debounce
watch(
  () => localFilters.search,
  debounce((newVal) => {
    filters.search = newVal
    loadAdjustments()
  }, 300)
)

watch(
  () => localFilters.date_from,
  (newVal) => {
    filters.date_from = newVal
    loadAdjustments()
  }
)

watch(
  () => localFilters.date_to,
  (newVal) => {
    filters.date_to = newVal
    loadAdjustments()
  }
)

watch(
  () => localFilters.type,
  (newVal) => {
    filters.type = newVal
    loadAdjustments()
  }
)

const adjustmentForm = reactive({
  id: null,
  product_id: null,
  type: null,
  old_quantity: 0,
  new_quantity: 0,
  quantity_change: 0,
  reason: '',
  reference_number: '',
  notes: ''
})

const adjustments = ref([
  {
    id: 1,
    product_name: 'Paracetamol 500mg',
    type: 'Stock Count',
    old_quantity: 95,
    new_quantity: 100,
    quantity_change: 5,
    reason: 'Physical count adjustment',
    reference_number: 'ADJ-2024-001',
    notes: 'Monthly stock count revealed discrepancy',
    created_at: '2024-01-15T10:00:00Z'
  },
  {
    id: 2,
    product_name: 'Amoxicillin 250mg',
    type: 'Damage',
    old_quantity: 200,
    new_quantity: 195,
    quantity_change: -5,
    reason: 'Damaged packaging',
    reference_number: 'ADJ-2024-002',
    notes: 'Damaged during handling',
    created_at: '2024-01-14T14:30:00Z'
  },
  {
    id: 3,
    product_name: 'Vitamin C 1000mg',
    type: 'Found',
    old_quantity: 48,
    new_quantity: 50,
    quantity_change: 2,
    reason: 'Found in storage',
    reference_number: 'ADJ-2024-003',
    notes: 'Found additional stock in back storage',
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
    name: 'type',
    label: 'Type',
    field: 'type',
    align: 'center',
    sortable: true
  },
  {
    name: 'old_quantity',
    label: 'Old Qty',
    field: 'old_quantity',
    align: 'center',
    sortable: true
  },
  {
    name: 'new_quantity',
    label: 'New Qty',
    field: 'new_quantity',
    align: 'center',
    sortable: true
  },
  {
    name: 'quantity_change',
    label: 'Change',
    field: 'quantity_change',
    align: 'center',
    sortable: true
  },
  {
    name: 'reason',
    label: 'Reason',
    field: 'reason',
    align: 'left'
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

const getTypeColor = (type) => {
  const colors = {
    'Stock Count': 'blue',
    'Damage': 'red',
    'Loss': 'purple',
    'Found': 'green',
    'Transfer': 'teal',
    'Other': 'grey'
  }
  return colors[type] || 'grey'
}

const getTypeIcon = (type) => {
  const icons = {
    'Stock Count': 'inventory',
    'Damage': 'broken_image',
    'Loss': 'remove',
    'Found': 'add',
    'Transfer': 'swap_horiz',
    'Other': 'more_horiz'
  }
  return icons[type] || 'more_horiz'
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

const updateCurrentStock = () => {
  if (adjustmentForm.product_id) {
    // TODO: Get actual stock from API
    currentStock.value = Math.floor(Math.random() * 100) + 10
    adjustmentForm.old_quantity = currentStock.value
    adjustmentForm.new_quantity = currentStock.value
    adjustmentForm.quantity_change = 0
  } else {
    currentStock.value = null
    adjustmentForm.old_quantity = 0
    adjustmentForm.new_quantity = 0
    adjustmentForm.quantity_change = 0
  }
}

const calculateChange = () => {
  adjustmentForm.quantity_change = adjustmentForm.new_quantity - adjustmentForm.old_quantity
}

const loadAdjustments = async () => {
  loading.value = true
  try {
    // TODO: Load actual data from API
    await new Promise(resolve => setTimeout(resolve, 500))
    pagination.value.rowsNumber = adjustments.value.length
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load adjustments'
    })
  } finally {
    loading.value = false
  }
}

const onRequest = (props) => {
  pagination.value = props.pagination
  loadAdjustments()
}

const viewAdjustment = (adjustment) => {
  selectedAdjustment.value = adjustment
  showViewDialog.value = true
}

const editAdjustment = (adjustment) => {
  editMode.value = true
  adjustmentForm.id = adjustment.id
  adjustmentForm.product_id = adjustment.product_id
  adjustmentForm.type = adjustment.type
  adjustmentForm.old_quantity = adjustment.old_quantity
  adjustmentForm.new_quantity = adjustment.new_quantity
  adjustmentForm.quantity_change = adjustment.quantity_change
  adjustmentForm.reason = adjustment.reason
  adjustmentForm.reference_number = adjustment.reference_number
  adjustmentForm.notes = adjustment.notes
  currentStock.value = adjustment.old_quantity
  showAddDialog.value = true
}

const deleteAdjustment = (adjustment) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete this adjustment for ${adjustment.product_name}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      // TODO: Delete from API
      const index = adjustments.value.findIndex(item => item.id === adjustment.id)
      if (index > -1) {
        adjustments.value.splice(index, 1)
      }
      $q.notify({
        type: 'positive',
        message: 'Adjustment deleted successfully'
      })
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to delete adjustment'
      })
    }
  })
}

const saveAdjustment = async () => {
  if (adjustmentForm.new_quantity < 0) {
    $q.notify({
      type: 'negative',
      message: 'New quantity cannot be negative'
    })
    return
  }
  
  try {
    // TODO: Save to API
    const selectedProduct = productOptions.value.find(p => p.id === adjustmentForm.product_id)
    const newAdjustment = {
      id: editMode.value ? adjustmentForm.id : Date.now(),
      product_name: selectedProduct?.name || 'Unknown Product',
      type: adjustmentForm.type,
      old_quantity: adjustmentForm.old_quantity,
      new_quantity: adjustmentForm.new_quantity,
      quantity_change: adjustmentForm.quantity_change,
      reason: adjustmentForm.reason,
      reference_number: adjustmentForm.reference_number,
      notes: adjustmentForm.notes,
      created_at: new Date().toISOString()
    }

    if (editMode.value) {
      const index = adjustments.value.findIndex(item => item.id === adjustmentForm.id)
      if (index > -1) {
        adjustments.value[index] = newAdjustment
      }
    } else {
      adjustments.value.unshift(newAdjustment)
    }

    $q.notify({
      type: 'positive',
      message: `Adjustment ${editMode.value ? 'updated' : 'created'} successfully`
    })

    closeDialog()
  } catch {
    $q.notify({
      type: 'negative',
      message: `Failed to ${editMode.value ? 'update' : 'create'} adjustment`
    })
  }
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  adjustmentForm.id = null
  adjustmentForm.product_id = null
  adjustmentForm.type = null
  adjustmentForm.old_quantity = 0
  adjustmentForm.new_quantity = 0
  adjustmentForm.quantity_change = 0
  adjustmentForm.reason = ''
  adjustmentForm.reference_number = ''
  adjustmentForm.notes = ''
  currentStock.value = null
}

const fetchProducts = async () => {
  try {
    await productStore.fetchProducts()
  } catch (error) {
    console.error('Error fetching products:', error.message)
  }
}

const refreshData = () => {
  // Reset local filters
  localFilters.search = ''
  localFilters.date_from = ''
  localFilters.date_to = ''
  localFilters.type = null
  
  // Reset store filters
  filters.search = ''
  filters.date_from = ''
  filters.date_to = ''
  filters.type = null
  
  loadAdjustments()
}

// Watch for changes in new_quantity to auto-calculate change
watch(() => adjustmentForm.new_quantity, calculateChange)

onMounted(() => {
  // Initialize local filters from store
  localFilters.search = filters.search
  localFilters.date_from = filters.date_from
  localFilters.date_to = filters.date_to
  localFilters.type = filters.type
  
  loadAdjustments()
  fetchProducts()
})
</script>
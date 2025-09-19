<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Stock In</h4>
        <p class="text-grey-6">Add inventory to stock</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="New Stock In"
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
              placeholder="Search stock in records..."
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
          <div class="col-auto">
            <q-btn
              color="secondary"
              icon="refresh"
              label="Muat Ulang"
              @click="refreshData"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Stock In Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="stockIns"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
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
                @click="viewStockIn(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="edit"
                color="warning"
                @click="editStockIn(props.row)"
              >
                <q-tooltip>Edit</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="deleteStockIn(props.row)"
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
          <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} Stock In</div>
        </q-card-section>

        <q-card-section>
          <q-form>
            <q-select
              v-model="stockInForm.product_id"
              :options="productOptions"
              option-value="id"
              option-label="name"
              label="Product"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="stockInForm.quantity"
              label="Quantity"
              type="number"
              min="1"
              required
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model.number="stockInForm.unit_cost"
              label="Unit Cost"
              type="number"
              step="0.01"
              required
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model="stockInForm.supplier"
              label="Supplier (Optional)"
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model="stockInForm.reference_number"
              label="Reference Number (Optional)"
              outlined
              class="q-mb-md"
            />
            <q-input
              v-model="stockInForm.notes"
              label="Notes (Optional)"
              type="textarea"
              outlined
              rows="3"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn color="primary" label="Save" @click="saveStockIn" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Stock In Details</div>
        </q-card-section>

        <q-card-section v-if="selectedStockIn">
          <div class="row q-gutter-md">
            <div class="col-12">
              <q-item>
                <q-item-section>
                  <q-item-label>Product</q-item-label>
                  <q-item-label caption>{{ selectedStockIn.product_name }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Quantity</q-item-label>
                  <q-item-label caption>{{ selectedStockIn.quantity }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Unit Cost</q-item-label>
                  <q-item-label caption>{{ formatCurrency(selectedStockIn.unit_cost) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Total Cost</q-item-label>
                  <q-item-label caption>{{ formatCurrency(selectedStockIn.total_cost) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-6">
              <q-item>
                <q-item-section>
                  <q-item-label>Date</q-item-label>
                  <q-item-label caption>{{ formatDate(selectedStockIn.created_at) }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedStockIn.supplier">
              <q-item>
                <q-item-section>
                  <q-item-label>Supplier</q-item-label>
                  <q-item-label caption>{{ selectedStockIn.supplier }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedStockIn.reference_number">
              <q-item>
                <q-item-section>
                  <q-item-label>Reference Number</q-item-label>
                  <q-item-label caption>{{ selectedStockIn.reference_number }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
            <div class="col-12" v-if="selectedStockIn.notes">
              <q-item>
                <q-item-section>
                  <q-item-label>Notes</q-item-label>
                  <q-item-label caption>{{ selectedStockIn.notes }}</q-item-label>
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

const $q = useQuasar()
const productStore = useProductStore()

const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const loading = ref(false)
const selectedStockIn = ref(null)

// Local filters for v-model (separated from store filters)
const localFilters = reactive({
  search: '',
  date_from: '',
  date_to: ''
})

// Watcher to update filters from local filters with debounce
watch(
  () => localFilters.search,
  () => {
    loadStockIns()
  },
  { debounce: 500 }
)

watch(
  () => localFilters.date_from,
  () => {
    loadStockIns()
  }
)

watch(
  () => localFilters.date_to,
  () => {
    loadStockIns()
  }
)

const stockInForm = reactive({
  id: null,
  product_id: null,
  quantity: 1,
  unit_cost: 0,
  supplier: '',
  reference_number: '',
  notes: ''
})

const stockIns = ref([
  {
    id: 1,
    product_name: 'Paracetamol 500mg',
    quantity: 100,
    unit_cost: 5000,
    total_cost: 500000,
    supplier: 'PT. Pharma Indonesia',
    reference_number: 'PO-2024-001',
    notes: 'Regular stock replenishment',
    created_at: '2024-01-15T10:00:00Z'
  },
  {
    id: 2,
    product_name: 'Amoxicillin 250mg',
    quantity: 200,
    unit_cost: 8000,
    total_cost: 1600000,
    supplier: 'CV. Medika Jaya',
    reference_number: 'PO-2024-002',
    notes: 'Emergency stock',
    created_at: '2024-01-14T14:30:00Z'
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
    name: 'supplier',
    label: 'Supplier',
    field: 'supplier',
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

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount || 0)
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

const loadStockIns = async () => {
  loading.value = true
  try {
    // TODO: Load actual data from API
    await new Promise(resolve => setTimeout(resolve, 500))
    pagination.value.rowsNumber = stockIns.value.length
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load stock in records'
    })
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  loadStockIns()
}

const onRequest = (props) => {
  pagination.value = props.pagination
  loadStockIns()
}

const viewStockIn = (stockIn) => {
  selectedStockIn.value = stockIn
  showViewDialog.value = true
}

const editStockIn = (stockIn) => {
  editMode.value = true
  stockInForm.id = stockIn.id
  stockInForm.product_id = stockIn.product_id
  stockInForm.quantity = stockIn.quantity
  stockInForm.unit_cost = stockIn.unit_cost
  stockInForm.supplier = stockIn.supplier
  stockInForm.reference_number = stockIn.reference_number
  stockInForm.notes = stockIn.notes
  showAddDialog.value = true
}

const deleteStockIn = (stockIn) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete this stock in record for ${stockIn.product_name}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      // TODO: Delete from API
      const index = stockIns.value.findIndex(item => item.id === stockIn.id)
      if (index > -1) {
        stockIns.value.splice(index, 1)
      }
      $q.notify({
        type: 'positive',
        message: 'Stock in record deleted successfully'
      })
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to delete stock in record'
      })
    }
  })
}

const saveStockIn = async () => {
  try {
    // TODO: Save to API
    const newStockIn = {
      id: editMode.value ? stockInForm.id : Date.now(),
      product_name: productOptions.value.find(p => p.id === stockInForm.product_id)?.name || 'Unknown Product',
      quantity: stockInForm.quantity,
      unit_cost: stockInForm.unit_cost,
      total_cost: stockInForm.quantity * stockInForm.unit_cost,
      supplier: stockInForm.supplier,
      reference_number: stockInForm.reference_number,
      notes: stockInForm.notes,
      created_at: new Date().toISOString()
    }

    if (editMode.value) {
      const index = stockIns.value.findIndex(item => item.id === stockInForm.id)
      if (index > -1) {
        stockIns.value[index] = newStockIn
      }
    } else {
      stockIns.value.unshift(newStockIn)
    }

    $q.notify({
      type: 'positive',
      message: `Stock in ${editMode.value ? 'updated' : 'created'} successfully`
    })

    closeDialog()
  } catch {
    $q.notify({
      type: 'negative',
      message: `Failed to ${editMode.value ? 'update' : 'create'} stock in record`
    })
  }
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  stockInForm.id = null
  stockInForm.product_id = null
  stockInForm.quantity = 1
  stockInForm.unit_cost = 0
  stockInForm.supplier = ''
  stockInForm.reference_number = ''
  stockInForm.notes = ''
}

const fetchProducts = async () => {
  try {
    await productStore.fetchProducts()
  } catch (error) {
    console.error('Error fetching products:', error.message)
  }
}

// Initialize local filters on mount
onMounted(() => {
  localFilters.search = ''
  localFilters.date_from = ''
  localFilters.date_to = ''
  loadStockIns()
  fetchProducts()
})
</script>
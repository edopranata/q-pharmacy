<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Sales Management</h4>
        <p class="text-grey-6">Manage sales transactions and orders</p>
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
          icon="add_shopping_cart"
          label="New Sale"
          @click="$router.push('/app/pos')"
        />
      </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-3">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h6">Today's Sales</div>
            <div class="text-h4">{{ formatCurrency(todaysSales) }}</div>
            <div class="text-caption">{{ todaysTransactions }} transactions</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-secondary text-white">
          <q-card-section>
            <div class="text-h6">This Week</div>
            <div class="text-h4">{{ formatCurrency(weekSales) }}</div>
            <div class="text-caption">{{ weekTransactions }} transactions</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-positive text-white">
          <q-card-section>
            <div class="text-h6">This Month</div>
            <div class="text-h4">{{ formatCurrency(monthSales) }}</div>
            <div class="text-caption">{{ monthTransactions }} transactions</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-info text-white">
          <q-card-section>
            <div class="text-h6">Average Sale</div>
            <div class="text-h4">{{ formatCurrency(averageSale) }}</div>
            <div class="text-caption">per transaction</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-12 col-md-3">
            <q-input
              v-model="localFilters.search"
              placeholder="Search transactions..."
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
              v-model="localFilters.status"
              :options="statusOptions"
              label="Status"
              outlined
              dense
              clearable
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="localFilters.payment_method"
              :options="paymentMethodOptions"
              label="Payment Method"
              outlined
              dense
              clearable
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Sales Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="sales"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip
                :color="getStatusColor(props.value)"
                text-color="white"
                size="sm"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>
          <template v-slot:body-cell-total_amount="props">
            <q-td :props="props">
              {{ formatCurrency(props.value) }}
            </q-td>
          </template>
          <template v-slot:body-cell-payment_method="props">
            <q-td :props="props">
              <q-chip
                :color="getPaymentMethodColor(props.value)"
                text-color="white"
                size="sm"
                :icon="getPaymentMethodIcon(props.value)"
              >
                {{ props.value }}
              </q-chip>
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
                @click="viewSale(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="print"
                color="secondary"
                @click="printReceipt(props.row)"
              >
                <q-tooltip>Print Receipt</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="assignment_return"
                color="warning"
                @click="processReturn(props.row)"
                :disable="props.row.status === 'Returned'"
              >
                <q-tooltip>{{ props.row.status === 'Returned' ? 'Already returned' : 'Process Return' }}</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- View Sale Dialog -->
    <q-dialog v-model="showViewDialog" maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Sale Details - #{{ selectedSale?.transaction_number }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showViewDialog = false" />
        </q-card-section>

        <q-card-section v-if="selectedSale">
          <div class="row q-gutter-lg">
            <!-- Sale Information -->
            <div class="col-12 col-md-4">
              <q-card>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Transaction Information</div>
                  <q-list>
                    <q-item>
                      <q-item-section>
                        <q-item-label>Transaction Number</q-item-label>
                        <q-item-label caption>{{ selectedSale.transaction_number }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>Date & Time</q-item-label>
                        <q-item-label caption>{{ formatDate(selectedSale.created_at) }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>Cashier</q-item-label>
                        <q-item-label caption>{{ selectedSale.cashier_name }}</q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>Status</q-item-label>
                        <q-item-label caption>
                          <q-chip
                            :color="getStatusColor(selectedSale.status)"
                            text-color="white"
                            size="sm"
                          >
                            {{ selectedSale.status }}
                          </q-chip>
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                    <q-item>
                      <q-item-section>
                        <q-item-label>Payment Method</q-item-label>
                        <q-item-label caption>
                          <q-chip
                            :color="getPaymentMethodColor(selectedSale.payment_method)"
                            text-color="white"
                            size="sm"
                            :icon="getPaymentMethodIcon(selectedSale.payment_method)"
                          >
                            {{ selectedSale.payment_method }}
                          </q-chip>
                        </q-item-label>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </q-card-section>
              </q-card>
            </div>

            <!-- Items -->
            <div class="col-12 col-md-8">
              <q-card>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Items</div>
                  <q-table
                    :rows="selectedSale.items"
                    :columns="itemColumns"
                    row-key="id"
                    flat
                    hide-pagination
                  >
                    <template v-slot:body-cell-unit_price="props">
                      <q-td :props="props">
                        {{ formatCurrency(props.value) }}
                      </q-td>
                    </template>
                    <template v-slot:body-cell-total_price="props">
                      <q-td :props="props">
                        {{ formatCurrency(props.value) }}
                      </q-td>
                    </template>
                  </q-table>
                </q-card-section>
              </q-card>

              <!-- Payment Summary -->
              <q-card class="q-mt-md">
                <q-card-section>
                  <div class="text-h6 q-mb-md">Payment Summary</div>
                  <div class="row q-gutter-md">
                    <div class="col">
                      <q-list>
                        <q-item>
                          <q-item-section>
                            <q-item-label>Subtotal</q-item-label>
                            <q-item-label caption>{{ formatCurrency(selectedSale.subtotal) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                        <q-item>
                          <q-item-section>
                            <q-item-label>Tax</q-item-label>
                            <q-item-label caption>{{ formatCurrency(selectedSale.tax_amount) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                        <q-item>
                          <q-item-section>
                            <q-item-label>Discount</q-item-label>
                            <q-item-label caption>{{ formatCurrency(selectedSale.discount_amount) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                        <q-separator />
                        <q-item>
                          <q-item-section>
                            <q-item-label class="text-h6">Total</q-item-label>
                            <q-item-label caption class="text-h6">{{ formatCurrency(selectedSale.total_amount) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                    </div>
                    <div class="col">
                      <q-list>
                        <q-item>
                          <q-item-section>
                            <q-item-label>Amount Paid</q-item-label>
                            <q-item-label caption>{{ formatCurrency(selectedSale.amount_paid) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                        <q-item>
                          <q-item-section>
                            <q-item-label>Change</q-item-label>
                            <q-item-label caption>{{ formatCurrency(selectedSale.change_amount) }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" @click="showViewDialog = false" />
          <q-btn color="secondary" icon="print" label="Print Receipt" @click="printReceipt(selectedSale)" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { debounce } from 'quasar'

const $q = useQuasar()

const showViewDialog = ref(false)
const loading = ref(false)
const selectedSale = ref(null)

const statusOptions = ['Completed', 'Pending', 'Cancelled', 'Returned']
const paymentMethodOptions = ['Cash', 'Credit Card', 'Debit Card', 'Digital Wallet', 'Bank Transfer']

const filters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  status: null,
  payment_method: null
})

// Local filters for reactive UI
const localFilters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  status: null,
  payment_method: null
})

// Watch for changes in local filters with debounce
watch(
  () => localFilters.search,
  debounce((newVal) => {
    filters.search = newVal
    loadSales()
  }, 300)
)

watch(
  () => localFilters.date_from,
  (newVal) => {
    filters.date_from = newVal
    loadSales()
  }
)

watch(
  () => localFilters.date_to,
  (newVal) => {
    filters.date_to = newVal
    loadSales()
  }
)

watch(
  () => localFilters.status,
  (newVal) => {
    filters.status = newVal
    loadSales()
  }
)

watch(
  () => localFilters.payment_method,
  (newVal) => {
    filters.payment_method = newVal
    loadSales()
  }
)

// Sample data
const todaysSales = ref(2500000)
const todaysTransactions = ref(45)
const weekSales = ref(15750000)
const weekTransactions = ref(287)
const monthSales = ref(67500000)
const monthTransactions = ref(1234)
const averageSale = ref(55000)

const sales = ref([
  {
    id: 1,
    transaction_number: 'TXN-2024-001',
    cashier_name: 'John Doe',
    total_amount: 125000,
    subtotal: 115000,
    tax_amount: 10000,
    discount_amount: 0,
    amount_paid: 125000,
    change_amount: 0,
    payment_method: 'Cash',
    status: 'Completed',
    created_at: '2024-01-15T10:30:00Z',
    items: [
      {
        id: 1,
        product_name: 'Paracetamol 500mg',
        quantity: 2,
        unit_price: 5000,
        total_price: 10000
      },
      {
        id: 2,
        product_name: 'Vitamin C 1000mg',
        quantity: 1,
        unit_price: 15000,
        total_price: 15000
      }
    ]
  },
  {
    id: 2,
    transaction_number: 'TXN-2024-002',
    cashier_name: 'Jane Smith',
    total_amount: 85000,
    subtotal: 78000,
    tax_amount: 7000,
    discount_amount: 5000,
    amount_paid: 100000,
    change_amount: 15000,
    payment_method: 'Credit Card',
    status: 'Completed',
    created_at: '2024-01-15T11:15:00Z',
    items: [
      {
        id: 1,
        product_name: 'Amoxicillin 250mg',
        quantity: 3,
        unit_price: 8000,
        total_price: 24000
      }
    ]
  }
])

const columns = [
  {
    name: 'transaction_number',
    label: 'Transaction #',
    field: 'transaction_number',
    align: 'left',
    sortable: true
  },
  {
    name: 'cashier_name',
    label: 'Cashier',
    field: 'cashier_name',
    align: 'left',
    sortable: true
  },
  {
    name: 'total_amount',
    label: 'Total Amount',
    field: 'total_amount',
    align: 'right',
    sortable: true
  },
  {
    name: 'payment_method',
    label: 'Payment',
    field: 'payment_method',
    align: 'center',
    sortable: true
  },
  {
    name: 'status',
    label: 'Status',
    field: 'status',
    align: 'center',
    sortable: true
  },
  {
    name: 'created_at',
    label: 'Date',
    field: 'created_at',
    align: 'left',
    format: val => formatDate(val),
    sortable: true
  },
  {
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center'
  }
]

const itemColumns = [
  {
    name: 'product_name',
    label: 'Product',
    field: 'product_name',
    align: 'left'
  },
  {
    name: 'quantity',
    label: 'Qty',
    field: 'quantity',
    align: 'center'
  },
  {
    name: 'unit_price',
    label: 'Unit Price',
    field: 'unit_price',
    align: 'right'
  },
  {
    name: 'total_price',
    label: 'Total',
    field: 'total_price',
    align: 'right'
  }
]

const pagination = ref({
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

const getStatusColor = (status) => {
  const colors = {
    'Completed': 'green',
    'Pending': 'orange',
    'Cancelled': 'red',
    'Returned': 'purple'
  }
  return colors[status] || 'grey'
}

const getPaymentMethodColor = (method) => {
  const colors = {
    'Cash': 'green',
    'Credit Card': 'blue',
    'Debit Card': 'purple',
    'Digital Wallet': 'orange',
    'Bank Transfer': 'teal'
  }
  return colors[method] || 'grey'
}

const getPaymentMethodIcon = (method) => {
  const icons = {
    'Cash': 'payments',
    'Credit Card': 'credit_card',
    'Debit Card': 'credit_card',
    'Digital Wallet': 'account_balance_wallet',
    'Bank Transfer': 'account_balance'
  }
  return icons[method] || 'payment'
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

const loadSales = async () => {
  loading.value = true
  try {
    // TODO: Load actual data from API
    await new Promise(resolve => setTimeout(resolve, 500))
    pagination.value.rowsNumber = sales.value.length
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load sales data'
    })
  } finally {
    loading.value = false
  }
}

const onRequest = (props) => {
  pagination.value = props.pagination
  loadSales()
}

const viewSale = (sale) => {
  selectedSale.value = sale
  showViewDialog.value = true
}

const printReceipt = (sale) => {
  // TODO: Implement receipt printing
  $q.notify({
    type: 'info',
    message: `Printing receipt for transaction ${sale.transaction_number}`
  })
}

const processReturn = (sale) => {
  $q.dialog({
    title: 'Process Return',
    message: `Are you sure you want to process a return for transaction ${sale.transaction_number}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      // TODO: Process return via API
      const index = sales.value.findIndex(item => item.id === sale.id)
      if (index > -1) {
        sales.value[index].status = 'Returned'
      }
      $q.notify({
        type: 'positive',
        message: 'Return processed successfully'
      })
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to process return'
      })
    }
  })
}

const refreshData = () => {
  // Reset local filters
  localFilters.search = ''
  localFilters.date_from = ''
  localFilters.date_to = ''
  localFilters.status = null
  localFilters.payment_method = null
  
  // Reset store filters
  filters.search = ''
  filters.date_from = ''
  filters.date_to = ''
  filters.status = null
  filters.payment_method = null
  
  loadSales()
}

onMounted(() => {
  // Initialize local filters from store
  localFilters.search = filters.search
  localFilters.date_from = filters.date_from
  localFilters.date_to = filters.date_to
  localFilters.status = filters.status
  localFilters.payment_method = filters.payment_method
  
  loadSales()
})
</script>
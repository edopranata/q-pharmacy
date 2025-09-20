<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Sales Management</h4>
        <p class="text-grey-6">Monitor and manage sales performance</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="point_of_sale"
          label="New Sale"
          to="/app/pos"
        />
      </div>
    </div>

    <!-- Sales Overview Cards -->
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
            <div class="text-caption">Per transaction</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Sales Chart -->
    <q-card class="q-mb-lg">
      <q-card-section>
        <div class="text-h6 q-mb-md">Sales Trend (Last 30 Days)</div>
        <div class="border-light" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 8px;">
          <div class="text-grey-6">
            <q-icon name="bar_chart" size="3rem" class="q-mb-md" />
            <div class="text-center">Chart will be displayed here</div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Quick Actions -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-4">
        <q-card class="interactive-hover" @click="$router.push('/app/pos')">
          <q-card-section class="text-center">
            <q-icon name="point_of_sale" size="3rem" color="primary" />
            <div class="text-h6 q-mt-md">Point of Sale</div>
            <div class="text-subtitle2">Process new sales</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card class="interactive-hover" @click="$router.push('/app/transactions')">
          <q-card-section class="text-center">
            <q-icon name="receipt_long" size="3rem" color="secondary" />
            <div class="text-h6 q-mt-md">Transactions</div>
            <div class="text-subtitle2">View transaction history</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card class="interactive-hover" @click="$router.push('/app/reports')">
          <q-card-section class="text-center">
            <q-icon name="assessment" size="3rem" color="positive" />
            <div class="text-h6 q-mt-md">Sales Reports</div>
            <div class="text-caption text-grey-6">Generate detailed reports</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Recent Transactions -->
    <q-card>
      <q-card-section>
        <div class="row items-center q-mb-md">
          <div class="col">
            <div class="text-h6">Recent Transactions</div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              color="primary"
              label="View All"
              @click="$router.push('/app/transactions')"
            />
          </div>
        </div>
        
        <q-table
          :rows="recentTransactions"
          :columns="transactionColumns"
          row-key="id"
          :pagination="{ rowsPerPage: 5 }"
          flat
          bordered
        >
          <template v-slot:body-cell-total="props">
            <q-td :props="props">
              <strong>{{ formatCurrency(props.value) }}</strong>
            </q-td>
          </template>
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
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                size="sm"
                icon="visibility"
                color="primary"
                @click="viewTransaction(props.row)"
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
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Top Products -->
    <q-card class="q-mt-lg">
      <q-card-section>
        <div class="text-h6 q-mb-md">Top Selling Products (This Month)</div>
        <q-list>
          <q-item v-for="product in topProducts" :key="product.id">
            <q-item-section avatar>
              <q-avatar>
                <img v-if="product.image" :src="product.image" />
                <q-icon v-else name="medication" />
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ product.name }}</q-item-label>
              <q-item-label caption>{{ product.category }}</q-item-label>
            </q-item-section>
            <q-item-section side>
              <div class="text-right">
                <div class="text-weight-bold">{{ product.quantity_sold }} sold</div>
                <div class="text-caption text-grey-6">{{ formatCurrency(product.revenue) }}</div>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>

    <!-- Transaction Detail Dialog -->
    <q-dialog v-model="showTransactionDialog">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">Transaction Details</div>
        </q-card-section>

        <q-card-section v-if="selectedTransaction">
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label>Transaction ID</q-item-label>
                <q-item-label caption>{{ selectedTransaction.id }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Date & Time</q-item-label>
                <q-item-label caption>{{ formatDateTime(selectedTransaction.created_at) }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Cashier</q-item-label>
                <q-item-label caption>{{ selectedTransaction.cashier }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Payment Method</q-item-label>
                <q-item-label caption>{{ selectedTransaction.payment_method }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Total Amount</q-item-label>
                <q-item-label caption>
                  <strong>{{ formatCurrency(selectedTransaction.total) }}</strong>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Status</q-item-label>
                <q-item-label caption>
                  <q-chip
                    :color="getStatusColor(selectedTransaction.status)"
                    text-color="white"
                    size="sm"
                  >
                    {{ selectedTransaction.status }}
                  </q-chip>
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" @click="showTransactionDialog = false" />
          <q-btn color="primary" icon="print" label="Print Receipt" @click="printReceipt(selectedTransaction)" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()

const showTransactionDialog = ref(false)
const selectedTransaction = ref(null)

// Sample data
const todaysSales = ref(2500000)
const todaysTransactions = ref(45)
const weekSales = ref(15750000)
const weekTransactions = ref(285)
const monthSales = ref(75000000)
const monthTransactions = ref(1250)
const averageSale = ref(60000)

const recentTransactions = ref([
  {
    id: 'TXN-2024-001',
    created_at: '2024-01-15T14:30:00Z',
    cashier: 'John Doe',
    payment_method: 'Cash',
    total: 125000,
    status: 'Completed'
  },
  {
    id: 'TXN-2024-002',
    created_at: '2024-01-15T14:25:00Z',
    cashier: 'Jane Smith',
    payment_method: 'Card',
    total: 89500,
    status: 'Completed'
  },
  {
    id: 'TXN-2024-003',
    created_at: '2024-01-15T14:20:00Z',
    cashier: 'Bob Wilson',
    payment_method: 'Digital Wallet',
    total: 156000,
    status: 'Completed'
  },
  {
    id: 'TXN-2024-004',
    created_at: '2024-01-15T14:15:00Z',
    cashier: 'Alice Brown',
    payment_method: 'Cash',
    total: 67500,
    status: 'Refunded'
  },
  {
    id: 'TXN-2024-005',
    created_at: '2024-01-15T14:10:00Z',
    cashier: 'John Doe',
    payment_method: 'Card',
    total: 234000,
    status: 'Completed'
  }
])

const topProducts = ref([
  {
    id: 1,
    name: 'Paracetamol 500mg',
    category: 'Pain Relief',
    quantity_sold: 245,
    revenue: 1225000,
    image: null
  },
  {
    id: 2,
    name: 'Vitamin C 1000mg',
    category: 'Supplements',
    quantity_sold: 189,
    revenue: 945000,
    image: null
  },
  {
    id: 3,
    name: 'Antacid Tablets',
    category: 'Digestive Health',
    quantity_sold: 156,
    revenue: 780000,
    image: null
  },
  {
    id: 4,
    name: 'Cough Syrup',
    category: 'Cold & Flu',
    quantity_sold: 134,
    revenue: 670000,
    image: null
  },
  {
    id: 5,
    name: 'Hand Sanitizer',
    category: 'Personal Care',
    quantity_sold: 298,
    revenue: 596000,
    image: null
  }
])

const transactionColumns = [
  {
    name: 'id',
    label: 'Transaction ID',
    field: 'id',
    align: 'left',
    sortable: true
  },
  {
    name: 'created_at',
    label: 'Date & Time',
    field: 'created_at',
    align: 'left',
    format: val => formatDateTime(val),
    sortable: true
  },
  {
    name: 'cashier',
    label: 'Cashier',
    field: 'cashier',
    align: 'left',
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
    name: 'total',
    label: 'Total',
    field: 'total',
    align: 'right',
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
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center'
  }
]

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID')
}

const getStatusColor = (status) => {
  const colors = {
    'Completed': 'green',
    'Pending': 'orange',
    'Refunded': 'red',
    'Cancelled': 'grey'
  }
  return colors[status] || 'grey'
}

const viewTransaction = (transaction) => {
  selectedTransaction.value = transaction
  showTransactionDialog.value = true
}

const printReceipt = (transaction) => {
  $q.notify({
    type: 'positive',
    message: `Printing receipt for transaction ${transaction.id}`
  })
}

const loadSalesData = async () => {
  try {
    // TODO: Load actual sales data from API
    await new Promise(resolve => setTimeout(resolve, 500))
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load sales data'
    })
  }
}

onMounted(() => {
  loadSalesData()
})
</script>
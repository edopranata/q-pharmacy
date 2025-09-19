<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Transaction History</h4>
        <p class="text-grey-6">View and manage transaction records</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="point_of_sale"
          label="New Sale"
          @click="$router.push('/kasir/pos')"
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
          <div class="col-12 col-md-1">
            <q-btn
              color="primary"
              icon="refresh"
              label="Muat Ulang"
              outline
              @click="refreshData"
            />
          </div>
          <div class="col-12 col-md-1">
            <q-btn
              color="secondary"
              icon="clear"
              label="Reset"
              outline
              @click="resetFilters"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Transactions Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="transactions"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-total="props">
            <q-td :props="props">
              {{ formatCurrency(props.value) }}
            </q-td>
          </template>
          
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-badge
                :color="getStatusColor(props.value)"
                :label="props.value"
              />
            </q-td>
          </template>
          
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                color="primary"
                icon="visibility"
                size="sm"
                @click="viewTransaction(props.row)"
              />
              <q-btn
                flat
                round
                color="secondary"
                icon="print"
                size="sm"
                @click="printReceipt(props.row)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Transaction Detail Dialog -->
    <q-dialog v-model="showDetailDialog" maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Transaction Detail #{{ selectedTransaction?.id }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showDetailDialog = false" />
        </q-card-section>

        <q-card-section v-if="selectedTransaction">
          <div class="row q-gutter-md">
            <div class="col-12 col-md-6">
              <q-card flat bordered>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Transaction Info</div>
                  <div class="row q-mb-sm">
                    <div class="col-4 text-weight-bold">ID:</div>
                    <div class="col-8">#{{ selectedTransaction.id }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-4 text-weight-bold">Date:</div>
                    <div class="col-8">{{ formatDate(selectedTransaction.created_at) }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-4 text-weight-bold">Customer:</div>
                    <div class="col-8">{{ selectedTransaction.customer_name }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-4 text-weight-bold">Status:</div>
                    <div class="col-8">
                      <q-badge
                        :color="getStatusColor(selectedTransaction.status)"
                        :label="selectedTransaction.status"
                      />
                    </div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-4 text-weight-bold">Cashier:</div>
                    <div class="col-8">{{ selectedTransaction.cashier?.name || 'N/A' }}</div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            
            <div class="col-12 col-md-6">
              <q-card flat bordered>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Payment Info</div>
                  <div class="row q-mb-sm">
                    <div class="col-6 text-weight-bold">Subtotal:</div>
                    <div class="col-6 text-right">{{ formatCurrency(selectedTransaction.subtotal) }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-6 text-weight-bold">Tax:</div>
                    <div class="col-6 text-right">{{ formatCurrency(selectedTransaction.tax) }}</div>
                  </div>
                  <div class="row q-mb-sm text-h6">
                    <div class="col-6 text-weight-bold">Total:</div>
                    <div class="col-6 text-right">{{ formatCurrency(selectedTransaction.total) }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-6 text-weight-bold">Payment:</div>
                    <div class="col-6 text-right">{{ formatCurrency(selectedTransaction.payment_amount) }}</div>
                  </div>
                  <div class="row q-mb-sm">
                    <div class="col-6 text-weight-bold">Change:</div>
                    <div class="col-6 text-right">{{ formatCurrency(selectedTransaction.change) }}</div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
          
          <!-- Transaction Items -->
          <q-card flat bordered class="q-mt-md">
            <q-card-section>
              <div class="text-h6 q-mb-md">Items</div>
              <q-table
                :rows="selectedTransaction.items || []"
                :columns="itemColumns"
                row-key="id"
                flat
                :pagination="{ rowsPerPage: 0 }"
              >
                <template v-slot:body-cell-price="props">
                  <q-td :props="props">
                    {{ formatCurrency(props.value) }}
                  </q-td>
                </template>
                <template v-slot:body-cell-total="props">
                  <q-td :props="props">
                    {{ formatCurrency(props.row.price * props.row.quantity) }}
                  </q-td>
                </template>
              </q-table>
            </q-card-section>
          </q-card>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn
            color="secondary"
            icon="print"
            label="Print Receipt"
            @click="printReceipt(selectedTransaction)"
          />
          <q-btn flat label="Close" @click="showDetailDialog = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref, onMounted, computed, watch, reactive } from 'vue'
import { useTransactionStore } from 'src/stores/transaction'
import { debounce } from 'quasar'

export default {
  name: 'TransactionsPage',
  setup() {
    const transactionStore = useTransactionStore()
    const showDetailDialog = ref(false)
    const selectedTransaction = ref(null)
    
    // Computed properties from store
    const transactions = computed(() => transactionStore.transactions)
    const loading = computed(() => transactionStore.loading)
    
    const localFilters = reactive({
      search: '',
      status: null,
      date_from: '',
      date_to: ''
    })

    const statusOptions = ['completed', 'pending', 'cancelled']

    const columns = [
      {
        name: 'id',
        label: 'ID',
        field: 'id',
        align: 'left',
        sortable: true
      },
      {
        name: 'created_at',
        label: 'Date',
        field: 'created_at',
        align: 'left',
        format: val => new Date(val).toLocaleDateString()
      },
      {
        name: 'customer_name',
        label: 'Customer',
        field: 'customer_name',
        align: 'left'
      },
      {
        name: 'total',
        label: 'Total',
        field: 'total',
        align: 'right'
      },
      {
        name: 'status',
        label: 'Status',
        field: 'status',
        align: 'center'
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
        name: 'price',
        label: 'Price',
        field: 'price',
        align: 'right'
      },
      {
        name: 'total',
        label: 'Total',
        field: 'total',
        align: 'right'
      }
    ]

    const pagination = computed({
      get: () => ({
        sortBy: transactionStore.pagination.sortBy || 'created_at',
        descending: transactionStore.pagination.descending !== undefined ? transactionStore.pagination.descending : true,
        page: transactionStore.pagination.page || 1,
        rowsPerPage: transactionStore.pagination.rowsPerPage || 10,
        rowsNumber: transactionStore.pagination.rowsNumber || 0
      }),
      set: (val) => {
        transactionStore.setPagination(val)
      }
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

    const getStatusColor = (status) => {
      switch (status) {
        case 'completed': return 'positive'
        case 'pending': return 'warning'
        case 'cancelled': return 'negative'
        default: return 'grey'
      }
    }

    const fetchTransactions = async () => {
      try {
        // Update store pagination if sorting has changed
        if (transactionStore.pagination.sortBy !== pagination.value.sortBy || 
            transactionStore.pagination.descending !== pagination.value.descending) {
          transactionStore.setPagination({
            ...transactionStore.pagination,
            sortBy: pagination.value.sortBy,
            descending: pagination.value.descending
          })
        }

        const params = {
          search: localFilters.search || undefined,
          status: localFilters.status || undefined,
          date_from: localFilters.date_from || undefined,
          date_to: localFilters.date_to || undefined,
          sort_by: transactionStore.pagination.sortBy,
          sort_order: transactionStore.pagination.descending ? 'desc' : 'asc'
        }
        
        await transactionStore.fetchTransactions(params)
      } catch (error) {
        console.error('Error fetching transactions:', error.message)
      }
    }

    const onRequest = (props) => {
      pagination.value = props.pagination
      fetchTransactions()
    }

    const viewTransaction = (transaction) => {
      selectedTransaction.value = transaction
      showDetailDialog.value = true
    }

    const printReceipt = (transaction) => {
      // Simple print functionality
      const printWindow = window.open('', '_blank')
      const receiptContent = `
        <html>
          <head>
            <title>Receipt #${transaction.id}</title>
            <style>
              body { font-family: monospace; font-size: 12px; }
              .center { text-align: center; }
              .right { text-align: right; }
              .line { border-bottom: 1px dashed #000; margin: 10px 0; }
            </style>
          </head>
          <body>
            <div class="center">
              <h2>Q-PHARMACY</h2>
              <p>Receipt #${transaction.id}</p>
              <p>${formatDate(transaction.created_at)}</p>
            </div>
            <div class="line"></div>
            <p>Customer: ${transaction.customer_name}</p>
            <p>Cashier: ${transaction.cashier?.name || 'N/A'}</p>
            <div class="line"></div>
            ${(transaction.items || []).map(item => `
              <div style="display: flex; justify-content: space-between;">
                <span>${item.product_name}</span>
                <span>${item.quantity} x ${formatCurrency(item.price)}</span>
              </div>
            `).join('')}
            <div class="line"></div>
            <div style="display: flex; justify-content: space-between;">
              <span>Subtotal:</span>
              <span>${formatCurrency(transaction.subtotal)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span>Tax:</span>
              <span>${formatCurrency(transaction.tax)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold;">
              <span>Total:</span>
              <span>${formatCurrency(transaction.total)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span>Payment:</span>
              <span>${formatCurrency(transaction.payment_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span>Change:</span>
              <span>${formatCurrency(transaction.change)}</span>
            </div>
            <div class="line"></div>
            <div class="center">
              <p>Thank you for your purchase!</p>
            </div>
          </body>
        </html>
      `
      printWindow.document.write(receiptContent)
      printWindow.document.close()
      printWindow.print()
    }

    const resetFilters = () => {
      localFilters.search = ''
      localFilters.status = null
      localFilters.date_from = ''
      localFilters.date_to = ''
      fetchTransactions()
    }

    const refreshData = () => {
      fetchTransactions()
    }

    // Watchers
    watch(
      () => localFilters.search,
      debounce(() => {
        fetchTransactions()
      }, 300)
    )

    watch(
      () => localFilters.status,
      () => {
        fetchTransactions()
      }
    )

    watch(
      () => localFilters.date_from,
      () => {
        fetchTransactions()
      }
    )

    watch(
      () => localFilters.date_to,
      () => {
        fetchTransactions()
      }
    )

    onMounted(() => {
      // Initialize local filters
      localFilters.search = ''
      localFilters.status = null
      localFilters.date_from = ''
      localFilters.date_to = ''
      
      fetchTransactions()
    })

    return {
      transactions,
      loading,
      showDetailDialog,
      selectedTransaction,
      localFilters,
      statusOptions,
      columns,
      itemColumns,
      pagination,
      formatCurrency,
      formatDate,
      getStatusColor,
      fetchTransactions,
      onRequest,
      viewTransaction,
      printReceipt,
      resetFilters,
      refreshData
    }
  }
}
</script>
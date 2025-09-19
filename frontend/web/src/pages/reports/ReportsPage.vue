<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Reports</h4>
        <p class="text-grey-6">Generate and view business reports</p>
      </div>
    </div>

    <!-- Report Categories -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-6 col-lg-3">
        <q-card class="cursor-pointer" @click="navigateToReport('sales')">
          <q-card-section class="text-center">
            <q-icon name="trending_up" size="3rem" color="primary" />
            <div class="text-h6 q-mt-md">Sales Reports</div>
            <div class="text-caption text-grey-6">Daily, weekly, monthly sales analysis</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <q-card class="cursor-pointer" @click="navigateToReport('inventory')">
          <q-card-section class="text-center">
            <q-icon name="inventory" size="3rem" color="secondary" />
            <div class="text-h6 q-mt-md">Inventory Reports</div>
            <div class="text-caption text-grey-6">Stock levels, movements, valuations</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <q-card class="cursor-pointer" @click="navigateToReport('financial')">
          <q-card-section class="text-center">
            <q-icon name="account_balance" size="3rem" color="positive" />
            <div class="text-h6 q-mt-md">Financial Reports</div>
            <div class="text-caption text-grey-6">Profit & loss, cash flow analysis</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <q-card class="cursor-pointer" @click="navigateToReport('products')">
          <q-card-section class="text-center">
            <q-icon name="medication" size="3rem" color="warning" />
            <div class="text-h6 q-mt-md">Product Reports</div>
            <div class="text-caption text-grey-6">Best sellers, slow movers, expiry</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Quick Reports -->
    <q-card class="q-mb-lg">
      <q-card-section>
        <div class="text-h6 q-mb-md">Quick Reports</div>
        <div class="row q-gutter-md">
          <div class="col-12 col-md-4">
            <q-select
              v-model="quickReport.type"
              :options="quickReportTypes"
              label="Report Type"
              outlined
              dense
            />
          </div>
          <div class="col-12 col-md-3">
            <q-select
              v-model="quickReport.period"
              :options="periodOptions"
              label="Period"
              outlined
              dense
            />
          </div>
          <div class="col-12 col-md-2">
            <q-input
              v-model="quickReport.date_from"
              label="From Date"
              type="date"
              outlined
              dense
              v-if="quickReport.period === 'Custom'"
            />
          </div>
          <div class="col-12 col-md-2">
            <q-input
              v-model="quickReport.date_to"
              label="To Date"
              type="date"
              outlined
              dense
              v-if="quickReport.period === 'Custom'"
            />
          </div>
          <div class="col-12 col-md-1">
            <q-btn
              color="primary"
              icon="play_arrow"
              label="Generate"
              @click="generateQuickReport"
              :loading="generatingReport"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Recent Reports -->
    <q-card>
      <q-card-section>
        <div class="text-h6 q-mb-md">Recent Reports</div>
        <q-table
          :rows="recentReports"
          :columns="reportColumns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-type="props">
            <q-td :props="props">
              <q-chip
                :color="getReportTypeColor(props.value)"
                text-color="white"
                size="sm"
                :icon="getReportTypeIcon(props.value)"
              >
                {{ props.value }}
              </q-chip>
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
                @click="viewReport(props.row)"
                :disable="props.row.status !== 'Completed'"
              >
                <q-tooltip>View Report</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="download"
                color="secondary"
                @click="downloadReport(props.row)"
                :disable="props.row.status !== 'Completed'"
              >
                <q-tooltip>Download</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="share"
                color="info"
                @click="shareReport(props.row)"
                :disable="props.row.status !== 'Completed'"
              >
                <q-tooltip>Share</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="deleteReport(props.row)"
              >
                <q-tooltip>Delete</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- View Report Dialog -->
    <q-dialog v-model="showViewDialog" maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ selectedReport?.name }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showViewDialog = false" />
        </q-card-section>

        <q-card-section v-if="selectedReport">
          <div class="text-center q-pa-xl">
            <q-icon name="description" size="4rem" color="grey-5" />
            <div class="text-h6 q-mt-md">Report Preview</div>
            <div class="text-caption text-grey-6">Report content would be displayed here</div>
            <div class="q-mt-lg">
              <q-btn color="primary" icon="download" label="Download PDF" class="q-mr-md" />
              <q-btn color="secondary" icon="table_chart" label="Export Excel" />
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
import { ref, reactive, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

const $q = useQuasar()
const router = useRouter()

const showViewDialog = ref(false)
const loading = ref(false)
const generatingReport = ref(false)
const selectedReport = ref(null)

const quickReportTypes = [
  'Sales Summary',
  'Inventory Status',
  'Top Products',
  'Low Stock Alert',
  'Expiry Report',
  'Financial Summary'
]

const periodOptions = [
  'Today',
  'Yesterday',
  'This Week',
  'Last Week',
  'This Month',
  'Last Month',
  'This Year',
  'Custom'
]

const quickReport = reactive({
  type: null,
  period: 'Today',
  date_from: '',
  date_to: ''
})

const recentReports = ref([
  {
    id: 1,
    name: 'Monthly Sales Report - January 2024',
    type: 'Sales',
    period: 'January 2024',
    status: 'Completed',
    generated_by: 'Admin',
    created_at: '2024-01-15T10:00:00Z',
    file_size: '2.5 MB'
  },
  {
    id: 2,
    name: 'Inventory Valuation Report',
    type: 'Inventory',
    period: 'As of Jan 15, 2024',
    status: 'Completed',
    generated_by: 'Manager',
    created_at: '2024-01-15T09:30:00Z',
    file_size: '1.8 MB'
  },
  {
    id: 3,
    name: 'Weekly Sales Analysis',
    type: 'Sales',
    period: 'Jan 8-14, 2024',
    status: 'Processing',
    generated_by: 'Admin',
    created_at: '2024-01-15T08:45:00Z',
    file_size: null
  },
  {
    id: 4,
    name: 'Product Performance Report',
    type: 'Products',
    period: 'Q4 2023',
    status: 'Completed',
    generated_by: 'Analyst',
    created_at: '2024-01-14T16:20:00Z',
    file_size: '3.2 MB'
  }
])

const reportColumns = [
  {
    name: 'name',
    label: 'Report Name',
    field: 'name',
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
    name: 'period',
    label: 'Period',
    field: 'period',
    align: 'left',
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
    name: 'generated_by',
    label: 'Generated By',
    field: 'generated_by',
    align: 'left',
    sortable: true
  },
  {
    name: 'created_at',
    label: 'Created',
    field: 'created_at',
    align: 'left',
    format: val => formatDate(val),
    sortable: true
  },
  {
    name: 'file_size',
    label: 'Size',
    field: 'file_size',
    align: 'right'
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

const getReportTypeColor = (type) => {
  const colors = {
    'Sales': 'primary',
    'Inventory': 'secondary',
    'Financial': 'positive',
    'Products': 'warning'
  }
  return colors[type] || 'grey'
}

const getReportTypeIcon = (type) => {
  const icons = {
    'Sales': 'trending_up',
    'Inventory': 'inventory',
    'Financial': 'account_balance',
    'Products': 'medication'
  }
  return icons[type] || 'description'
}

const getStatusColor = (status) => {
  const colors = {
    'Completed': 'green',
    'Processing': 'orange',
    'Failed': 'red',
    'Pending': 'blue'
  }
  return colors[status] || 'grey'
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

const navigateToReport = (type) => {
  switch (type) {
    case 'sales':
      router.push('/app/reports/sales')
      break
    case 'inventory':
      router.push('/app/reports/inventory')
      break
    case 'financial':
      router.push('/app/reports/financial')
      break
    case 'products':
      router.push('/app/reports/products')
      break
    default:
      $q.notify({
        type: 'info',
        message: `${type} reports coming soon`
      })
  }
}

const generateQuickReport = async () => {
  if (!quickReport.type) {
    $q.notify({
      type: 'warning',
      message: 'Please select a report type'
    })
    return
  }

  generatingReport.value = true
  try {
    // TODO: Generate report via API
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    const newReport = {
      id: Date.now(),
      name: `${quickReport.type} - ${quickReport.period}`,
      type: quickReport.type.split(' ')[0],
      period: quickReport.period,
      status: 'Completed',
      generated_by: 'Current User',
      created_at: new Date().toISOString(),
      file_size: '1.2 MB'
    }
    
    recentReports.value.unshift(newReport)
    
    $q.notify({
      type: 'positive',
      message: 'Report generated successfully'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to generate report'
    })
  } finally {
    generatingReport.value = false
  }
}

const loadReports = async () => {
  loading.value = true
  try {
    // TODO: Load actual data from API
    await new Promise(resolve => setTimeout(resolve, 500))
    pagination.value.rowsNumber = recentReports.value.length
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to load reports'
    })
  } finally {
    loading.value = false
  }
}

const onRequest = (props) => {
  pagination.value = props.pagination
  loadReports()
}

const viewReport = (report) => {
  selectedReport.value = report
  showViewDialog.value = true
}

const downloadReport = (report) => {
  $q.notify({
    type: 'info',
    message: `Downloading ${report.name}...`
  })
  // TODO: Implement actual download
}

const shareReport = (report) => {
  $q.notify({
    type: 'info',
    message: `Sharing ${report.name}...`
  })
  // TODO: Implement sharing functionality
}

const deleteReport = (report) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete "${report.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      // TODO: Delete from API
      const index = recentReports.value.findIndex(item => item.id === report.id)
      if (index > -1) {
        recentReports.value.splice(index, 1)
      }
      $q.notify({
        type: 'positive',
        message: 'Report deleted successfully'
      })
    } catch {
      $q.notify({
        type: 'negative',
        message: 'Failed to delete report'
      })
    }
  })
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}

.cursor-pointer:hover {
  transform: translateY(-2px);
}
</style>
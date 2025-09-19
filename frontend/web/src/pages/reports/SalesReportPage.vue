<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Sales Report</h4>
        <p class="text-grey-6">Analyze sales performance and trends</p>
      </div>
      <div class="col-auto">
        <q-btn color="primary" icon="download" label="Export" />
      </div>
    </div>

    <!-- Date Range Filter -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md items-center">
          <div class="col-auto">
            <q-input
              v-model="dateRange.from"
              type="date"
              label="From Date"
              outlined
              dense
            />
          </div>
          <div class="col-auto">
            <q-input
              v-model="dateRange.to"
              type="date"
              label="To Date"
              outlined
              dense
            />
          </div>
          <div class="col-auto">
            <q-btn color="primary" label="Filter" @click="loadReport" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Summary Cards -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="text-h6">Total Sales</div>
            <div class="text-h4 text-positive">{{ formatCurrency(summary.totalSales) }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="text-h6">Transactions</div>
            <div class="text-h4 text-primary">{{ summary.totalTransactions }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="text-h6">Average Sale</div>
            <div class="text-h4 text-info">{{ formatCurrency(summary.averageSale) }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card>
          <q-card-section>
            <div class="text-h6">Growth</div>
            <div class="text-h4 text-positive">+{{ summary.growth }}%</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Chart Section -->
    <q-card>
      <q-card-section>
        <div class="text-h6 q-mb-md">Sales Trend</div>
        <div class="chart-container" style="height: 300px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
          <div class="text-grey-6">Chart will be rendered here</div>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const dateRange = ref({
  from: '',
  to: ''
})

const summary = ref({
  totalSales: 0,
  totalTransactions: 0,
  averageSale: 0,
  growth: 0
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount)
}

const loadReport = () => {
  // Load report data based on date range
  console.log('Loading report for:', dateRange.value)
}

onMounted(() => {
  // Set default date range (last 30 days)
  const today = new Date()
  const lastMonth = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000)
  
  dateRange.value.to = today.toISOString().split('T')[0]
  dateRange.value.from = lastMonth.toISOString().split('T')[0]
  
  // Load initial data
  loadReport()
})
</script>
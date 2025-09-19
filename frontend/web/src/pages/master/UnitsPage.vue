<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md">
      <div class="col">
        <h4 class="q-my-none">Manajemen Satuan</h4>
        <p class="text-grey-6 q-mb-none">Kelola satuan ukuran produk</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Tambah Satuan"
          @click="showAddDialog = true"
        />
      </div>
    </div>

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input
              v-model="unitStore.filters.search"
              debounce="500"
              outlined
              dense
              placeholder="Cari satuan..."
              clearable
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-select
              v-model="unitStore.filters.status"
              outlined
              dense
              :options="statusOptions"
              label="Status"
              clearable
              emit-value
              map-options
            />
          </div>
          <div class="col-auto">
            <q-btn
              color="secondary"
              icon="refresh"
              label="Muat Ulang"
              @click="loadUnits"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Data Table -->
    <q-table
      ref="unitTable"
      :rows="units"
      :columns="columns"
      :loading="loading"
      v-model:pagination="pagination"
      @request="onRequest"
      row-key="id"
      binary-state-sort
      :rows-per-page-options="[10, 15, 25, 50]"
      :filter="filterTrigger"
    >
      <template v-slot:body-cell-symbol="props">
        <q-td :props="props">
          <q-chip
            v-if="props.value"
            color="blue"
            text-color="white"
            dense
          >
            {{ props.value }}
          </q-chip>
          <span v-else class="text-grey-6">-</span>
        </q-td>
      </template>

      <template v-slot:body-cell-is_active="props">
        <q-td :props="props">
          <q-chip
            :color="props.value ? 'green' : 'red'"
            text-color="white"
            dense
          >
            {{ props.value ? 'Aktif' : 'Tidak Aktif' }}
          </q-chip>
        </q-td>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props">

          <q-btn
            flat
            round
            color="blue"
            icon="edit"
            size="sm"
            @click="editUnit(props.row)"
          >
            <q-tooltip>Edit</q-tooltip>
          </q-btn>
          <q-btn
            flat
            round
            color="blue"
            icon="visibility"
            size="sm"
            @click="viewDetail(props.row)"
          >
            <q-tooltip>View Details</q-tooltip>
          </q-btn>
          <q-btn
            flat
            round
            color="red"
            icon="delete"
            size="sm"
            @click="deleteUnit(props.row)"
          >
            <q-tooltip>Delete</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>

    <!-- Add/Edit Dialog -->
    <q-dialog v-model="showAddDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit Unit' : 'Add Unit' }}</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="saveUnit">
            <div class="row q-col-gutter-md">
              <div class="col">
                <q-input
                  v-model="unitForm.name"
                  outlined
                  label="Name *"
                  :rules="[val => !!val || 'Name is required']"
                />
              </div>
              <div class="col">
                <q-input
                  v-model="unitForm.code"
                  outlined
                  label="Code *"
                  :rules="[val => !!val || 'Code is required']"
                />
              </div>
              <div class="col">
                <q-input
                  v-model="unitForm.symbol"
                  outlined
                  label="Symbol"
                  hint="e.g., kg, ml, pcs"
                />
              </div>
            </div>
            
            <q-input
              class="q-mt-sm"
              v-model="unitForm.description"
              outlined
              type="textarea"
              label="Description"
              rows="3"
            />
            
            <q-toggle
              v-model="unitForm.is_active"
              label="Active"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn
            color="primary"
            label="Save"
            @click="saveUnit"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Unit Details</div>
        </q-card-section>

        <q-card-section class="q-pt-none" v-if="selectedUnit">
          <div class="q-gutter-md">
            <div class="row">
              <div class="col-4 text-weight-medium">Name:</div>
              <div class="col">{{ selectedUnit.name }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-medium">Code:</div>
              <div class="col">{{ selectedUnit.code }}</div>
            </div>
            <div class="row" v-if="selectedUnit.symbol">
              <div class="col-4 text-weight-medium">Symbol:</div>
              <div class="col">
                <q-chip
                  color="blue"
                  text-color="white"
                  dense
                >
                  {{ selectedUnit.symbol }}
                </q-chip>
              </div>
            </div>
            <div class="row" v-if="selectedUnit.description">
              <div class="col-4 text-weight-medium">Description:</div>
              <div class="col">{{ selectedUnit.description }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-medium">Status:</div>
              <div class="col">
                <q-chip
                  :color="selectedUnit.is_active ? 'green' : 'red'"
                  text-color="white"
                  dense
                >
                  {{ selectedUnit.is_active ? 'Active' : 'Inactive' }}
                </q-chip>
              </div>
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
import { useUnitStore } from 'src/stores/unit'
import { Notify, Dialog } from 'quasar'

// Reactive data
const unitStore = useUnitStore()
const saving = ref(false)

// Computed properties from store
const units = computed(() => unitStore.units)
const loading = computed(() => unitStore.loading)
const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const selectedUnit = ref(null)
const unitTable = ref(null)

// Local filters for v-model (separated from store filters)
const filterTrigger = ref(null)

// Watcher to update store filters from local filters with debounce
watch(
  () => unitStore.filters,
  () => {
    filterTrigger.value = String(Date.now())
  },
  { deep: true }
)

const unitForm = reactive({
  id: null,
  name: '',
  code: '',
  symbol: '',
  description: '',
  is_active: true
})

// Menggunakan pagination dari store dengan fallback untuk UI
const pagination = computed({
  get: () => ({
    sortBy: unitStore.pagination.sortBy,
    descending: unitStore.pagination.descending,
    page: unitStore.pagination.page,
    rowsPerPage: unitStore.pagination.rowsPerPage,
    rowsNumber: unitStore.pagination.rowsNumber
  }),
  set: (val) => {
    unitStore.setPagination(val)
  }
})

const statusOptions = [
  { label: 'Aktif', value: true },
  { label: 'Tidak Aktif', value: false }
]

const columns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left',
    field: 'name',
    sortable: true
  },
  {
    name: 'code',
    label: 'Code',
    align: 'left',
    field: 'code',
    sortable: true
  },
  {
    name: 'symbol',
    label: 'Symbol',
    align: 'center',
    field: 'symbol'
  },
  {
    name: 'description',
    label: 'Description',
    align: 'left',
    field: 'description'
  },
  {
    name: 'is_active',
    label: 'Status',
    align: 'center',
    field: 'is_active',
    sortable: true
  },
  {
    name: 'actions',
    label: 'Actions',
    align: 'center'
  }
]

// Methods
const loadUnits = async (props = {}) => {
  try {
    const { page = pagination.value.page, rowsPerPage = pagination.value.rowsPerPage, sortBy, descending } = props.pagination || {}
    
    // Update store pagination jika ada perubahan sorting
    if (sortBy !== undefined) {
      unitStore.setPagination({
        ...unitStore.pagination,
        sortBy,
        descending,
        page
      })
    } else {
      unitStore.setPagination({
        ...unitStore.pagination,
        page,
        rowsPerPage
      })
    }
    
    const params = {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy || unitStore.pagination.sortBy,
      sort_order: (descending !== undefined ? descending : unitStore.pagination.descending) ? 'desc' : 'asc'
    }
    
    // Add search filter
    if (unitStore.filters.search && unitStore.filters.search.trim()) {
      params.search = unitStore.filters.search.trim()
    }
    
    // Add status filter
    if (unitStore.filters.status !== null && unitStore.filters.status !== undefined) {
      params.is_active = unitStore.filters.status.value
    }

    await unitStore.fetchUnits(params)
  } catch (error) {
    Notify.create({
      type: 'negative',
      message: error.message || 'Gagal memuat data satuan'
    })
  }
}

const onRequest = (props) => {
  loadUnits(props)
}

const editUnit = (unit) => {
  editMode.value = true
  unitForm.id = unit.id
  unitForm.name = unit.name
  unitForm.code = unit.code
  unitForm.symbol = unit.symbol || ''
  unitForm.description = unit.description || ''
  unitForm.is_active = unit.is_active
  showAddDialog.value = true
}

const saveUnit = async () => {
  saving.value = true
  try {
    const data = {
      name: unitForm.name,
      code: unitForm.code,
      symbol: unitForm.symbol,
      description: unitForm.description,
      is_active: unitForm.is_active
    }
    
    if (editMode.value) {
      await unitStore.updateUnit(unitForm.id, data)
      Notify.create({
        type: 'positive',
        message: 'Unit updated successfully'
      })
    } else {
      await unitStore.createUnit(data)
      Notify.create({
        type: 'positive',
        message: 'Unit created successfully'
      })
    }
    
    // Refresh data setelah save
    loadUnits()
    closeDialog()
  } catch (error) {
    Notify.create({
      type: 'negative',
      message: error.message || 'Failed to save unit'
    })
  } finally {
    saving.value = false
  }
}

const viewDetail = (unit) => {
  selectedUnit.value = unit
  showViewDialog.value = true
}

const deleteUnit = (unit) => {
  Dialog.create({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete unit "${unit.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await unitStore.deleteUnit(unit.id)
      Notify.create({
        type: 'positive',
        message: 'Unit deleted successfully'
      })
    } catch (error) {
      Notify.create({
        type: 'negative',
        message: error.message || 'Failed to delete unit'
      })
    }
  })
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  unitForm.id = null
  unitForm.name = ''
  unitForm.code = ''
  unitForm.symbol = ''
  unitForm.description = ''
  unitForm.is_active = true
}

// Lifecycle
onMounted(() => {
  loadUnits()
})
</script>

<style scoped>
.q-table {
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
}
</style>
<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md">
      <div class="col">
        <h4 class="q-my-none">Manajemen Supplier</h4>
        <p class="text-grey-6 q-mb-none">Kelola data supplier produk</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Tambah Supplier"
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
              v-model="supplierStore.filters.search"
              debounce="500"
              outlined
              dense
              placeholder="Cari supplier..."
              clearable
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-select
              v-model="supplierStore.filters.status"
              outlined
              dense
              :options="statusOptions"
              label="Status"
              clearable
              emit-value
              map-options
            />
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-input
              v-model="supplierStore.filters.city"
              outlined
              dense
              placeholder="Filter berdasarkan kota"
              clearable
            />
          </div>
          <div class="col-auto">
            <q-btn
              color="secondary"
              icon="refresh"
              label="Muat Ulang"
              @click="loadSuppliers"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Data Table -->
    <q-table
      ref="supplierTable"
      :rows="suppliers"
      :columns="columns"
      :loading="loading"
      v-model:pagination="pagination"
      @request="onRequest"
      row-key="id"
      binary-state-sort
      :rows-per-page-options="[10, 15, 25, 50]"
      :filter="filterTrigger"
    >
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

      <template v-slot:body-cell-contact="props">
        <q-td :props="props">
          <div v-if="props.row.contact_person">
            <div class="text-weight-medium">{{ props.row.contact_person }}</div>
            <div class="text-caption text-grey-6">{{ props.row.phone }}</div>
          </div>
          <span v-else class="text-grey-6">-</span>
        </q-td>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props">
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
            color="blue"
            icon="edit"
            size="sm"
            @click="editSupplier(props.row)"
          >
            <q-tooltip>Edit</q-tooltip>
          </q-btn>
          <q-btn
            flat
            round
            color="red"
            icon="delete"
            size="sm"
            @click="deleteSupplier(props.row)"
          >
            <q-tooltip>Hapus</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>



    <!-- Add/Edit Dialog -->
    <q-dialog v-model="showAddDialog" persistent>
      <q-card style="min-width: 600px; max-width: 800px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit Supplier' : 'Add Supplier' }}</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="saveSupplier">
            <div class="row q-col-gutter-md">
              <div class="col-8">
                <q-input
                  v-model="supplierForm.name"
                  outlined
                  label="Name *"
                  :rules="[val => !!val || 'Name is required']"
                />
              </div>
              <div class="col-4">
                <q-input
                  v-model="supplierForm.code"
                  outlined
                  label="Code *"
                  :rules="[val => !!val || 'Code is required']"
                />
              </div>
            </div>
            
            <div class="row q-col-gutter-md">
              <div class="col-8">
                <q-input
                  v-model="supplierForm.contact_person"
                  outlined
                  label="Contact Person"
                />
              </div>
              <div class="col-4">
                <q-input
                  v-model="supplierForm.phone"
                  outlined
                  label="Phone"
                />
              </div>
            </div>
            
            <div class="row q-col-gutter-md q-mt-sm">
              <div class="col-12">
                <q-input
                  v-model="supplierForm.email"
                  outlined
                  type="email"
                  label="Email"
                />
              </div>
              <div class="col-12">
                <q-input
                  v-model="supplierForm.address"
                  outlined
                  type="textarea"
                  label="Address"
                  rows="2"
                />
              </div>
            </div>
            
            <div class="row q-col-gutter-md q-mt-sm">
              <div class="col-4">
                <q-input
                  v-model="supplierForm.city"
                  outlined
                  label="City"
                />
              </div>
              <div class="col-4">
                <q-input
                  v-model="supplierForm.province"
                  outlined
                  label="Province"
                />
              </div>
              <div class="col-4">
                <q-input
                  v-model="supplierForm.postal_code"
                  outlined
                  label="Postal Code"
                />
              </div>
            </div>
            
            <div class="row q-col-gutter-md q-mt-sm">
              <div class="col-12">
                <q-input
                  v-model="supplierForm.description"
                  outlined
                  type="textarea"
                  label="Description"
                  rows="3"
                />
              </div>
            </div>
            
            <q-toggle
              v-model="supplierForm.is_active"
              label="Active"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn
            color="primary"
            label="Save"
            @click="saveSupplier"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">Supplier Details</div>
        </q-card-section>

        <q-card-section class="q-pt-none" v-if="selectedSupplier">
          <div class="q-gutter-md">
            <div class="row">
              <div class="col-4 text-weight-medium">Name:</div>
              <div class="col">{{ selectedSupplier.name }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-medium">Code:</div>
              <div class="col">{{ selectedSupplier.code }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.contact_person">
              <div class="col-4 text-weight-medium">Contact Person:</div>
              <div class="col">{{ selectedSupplier.contact_person }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.phone">
              <div class="col-4 text-weight-medium">Phone:</div>
              <div class="col">{{ selectedSupplier.phone }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.email">
              <div class="col-4 text-weight-medium">Email:</div>
              <div class="col">{{ selectedSupplier.email }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.address">
              <div class="col-4 text-weight-medium">Address:</div>
              <div class="col">{{ selectedSupplier.address }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.city">
              <div class="col-4 text-weight-medium">City:</div>
              <div class="col">{{ selectedSupplier.city }}, {{ selectedSupplier.province }} {{ selectedSupplier.postal_code }}</div>
            </div>
            <div class="row" v-if="selectedSupplier.description">
              <div class="col-4 text-weight-medium">Description:</div>
              <div class="col">{{ selectedSupplier.description }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-medium">Status:</div>
              <div class="col">
                <q-chip
                  :color="selectedSupplier.is_active ? 'green' : 'red'"
                  text-color="white"
                  dense
                >
                  {{ selectedSupplier.is_active ? 'Active' : 'Inactive' }}
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
import { useSupplierStore } from 'src/stores/supplier'
import { Notify, Dialog } from 'quasar'

// Reactive data
const supplierStore = useSupplierStore()
const saving = ref(false)

// Computed properties from store
const suppliers = computed(() => supplierStore.suppliers)
const loading = computed(() => supplierStore.loading)
const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const selectedSupplier = ref(null)
const supplierTable = ref(null)

// Filter trigger to force table refresh
const filterTrigger = ref(null)

// Watcher to update store filters from local filters with debounce
watch(
  () => supplierStore.filters,
  () => {
    filterTrigger.value = String(Date.now())
  },
  { deep: true}
)

const supplierForm = reactive({
  id: null,
  name: '',
  code: '',
  contact_person: '',
  phone: '',
  email: '',
  address: '',
  city: '',
  province: '',
  postal_code: '',
  description: '',
  is_active: true
})

// Menggunakan pagination dari store dengan fallback untuk UI
const pagination = computed({
  get: () => ({
    sortBy: supplierStore.pagination.sortBy,
    descending: supplierStore.pagination.descending,
    page: supplierStore.pagination.page,
    rowsPerPage: supplierStore.pagination.rowsPerPage,
    rowsNumber: supplierStore.pagination.rowsNumber
  }),
  set: (val) => {
    supplierStore.setPagination(val)
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
    name: 'contact',
    label: 'Contact',
    align: 'left'
  },
  {
    name: 'city',
    label: 'City',
    align: 'left',
    field: 'city',
    sortable: true
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
const loadSuppliers = async (props = {}) => {
  try {
    const { page = pagination.value.page, rowsPerPage = pagination.value.rowsPerPage, sortBy, descending } = props.pagination || {}
    
    // Update store pagination jika ada perubahan sorting
    if (sortBy !== undefined) {
      supplierStore.setPagination({
        ...supplierStore.pagination,
        sortBy,
        descending,
        page
      })
    } else {
      supplierStore.setPagination({
        ...supplierStore.pagination,
        page,
        rowsPerPage
      })
    }
    
    const params = {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy || supplierStore.pagination.sortBy,
      sort_order: (descending !== undefined ? descending : supplierStore.pagination.descending) ? 'desc' : 'asc'
    }
    
    // Add search filter
    if (supplierStore.filters.search && supplierStore.filters.search.trim()) {
      params.search = supplierStore.filters.search.trim()
    }
    
    // Add status filter
    if (supplierStore.filters.status !== null && supplierStore.filters.status !== undefined) {
      params.is_active = supplierStore.filters.status.value
    }

    // Add city filter
    if (supplierStore.filters.city && supplierStore.filters.city.trim()) {
      params.city = supplierStore.filters.city.trim()
    }

    await supplierStore.fetchSuppliers(params)
  } catch (error) {
    Notify.create({
      type: 'negative',
      message: error.message || 'Gagal memuat data supplier'
    })
  }
}

const onRequest = (props) => {
  loadSuppliers(props)
}

const editSupplier = (supplier) => {
  editMode.value = true
  supplierForm.id = supplier.id
  supplierForm.name = supplier.name
  supplierForm.code = supplier.code
  supplierForm.contact_person = supplier.contact_person || ''
  supplierForm.phone = supplier.phone || ''
  supplierForm.email = supplier.email || ''
  supplierForm.address = supplier.address || ''
  supplierForm.city = supplier.city || ''
  supplierForm.province = supplier.province || ''
  supplierForm.postal_code = supplier.postal_code || ''
  supplierForm.description = supplier.description || ''
  supplierForm.is_active = supplier.is_active
  showAddDialog.value = true
}

const saveSupplier = async () => {
  saving.value = true
  try {
    const data = {
      name: supplierForm.name,
      code: supplierForm.code,
      contact_person: supplierForm.contact_person,
      phone: supplierForm.phone,
      email: supplierForm.email,
      address: supplierForm.address,
      city: supplierForm.city,
      province: supplierForm.province,
      postal_code: supplierForm.postal_code,
      description: supplierForm.description,
      is_active: supplierForm.is_active
    }
    
    if (editMode.value) {
      await supplierStore.updateSupplier(supplierForm.id, data)
      Notify.create({
        type: 'positive',
        message: 'Supplier berhasil diperbarui'
      })
    } else {
      await supplierStore.createSupplier(data)
      Notify.create({
        type: 'positive',
        message: 'Supplier berhasil ditambahkan'
      })
    }

    // Refresh data setelah save
    loadSuppliers()
    closeDialog()
  } catch (error) {
    Notify.create({
      type: 'negative',
      message: error.message || 'Gagal menyimpan supplier'
    })
  } finally {
    saving.value = false
  }
}

const viewDetail = (supplier) => {
  selectedSupplier.value = supplier
  showViewDialog.value = true
}

const deleteSupplier = (supplier) => {
  Dialog.create({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete supplier "${supplier.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await supplierStore.deleteSupplier(supplier.id)
      Notify.create({
        type: 'positive',
        message: 'Supplier deleted successfully'
      })

      loadSuppliers()
    } catch (error) {
      Notify.create({
        type: 'negative',
        message: error.message || 'Failed to delete supplier'
      })
    }
  })
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  supplierForm.id = null
  supplierForm.name = ''
  supplierForm.code = ''
  supplierForm.contact_person = ''
  supplierForm.phone = ''
  supplierForm.email = ''
  supplierForm.address = ''
  supplierForm.city = ''
  supplierForm.province = ''
  supplierForm.postal_code = ''
  supplierForm.description = ''
  supplierForm.is_active = true
}

// Lifecycle
onMounted(() => {
  loadSuppliers()
})
</script>

<style scoped>
.q-table {
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
}
</style>
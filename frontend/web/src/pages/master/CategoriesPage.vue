<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md">
      <div class="col">
        <h4 class="q-my-none">Categories Management</h4>
        <p class="text-grey-6 q-mb-none">Manage product categories</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Add Category"
          @click="showAddDialog = true"
        />
      </div>
    </div>

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input
              v-model="categoryStore.filters.search"
              debounce="500"
              outlined
              dense
              placeholder="Cari kategori..."
              clearable
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-select
              v-model="categoryStore.filters.status"
              outlined
              dense
              :options="statusOptions"
              label="Status"
              clearable
            />
          </div>
          <div class="col-auto">
            <q-btn
              color="secondary"
              icon="refresh"
              label="Refresh"
              @click="loadCategories"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Data Table -->
    <q-table
      class="table-elevated"
      ref="categoryTable"
      :rows="categories"
      :columns="columns"
      :loading="loading"
      v-model:pagination="pagination"
      @request="onRequest"
      row-key="id"
      binary-state-sort
      :rows-per-page-options="[10, 25, 50, 100]"
      :filter="filterTrigger"
    >
      <template v-slot:body-cell-is_active="props">
        <q-td :props="props">
          <q-chip
            :color="props.value ? 'green' : 'red'"
            text-color="white"
            dense
          >
            {{ props.value ? 'Active' : 'Inactive' }}
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
            @click="editCategory(props.row)"
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
            @click="deleteCategory(props.row)"
          >
            <q-tooltip>Delete</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>

    <!-- View Detail Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">Category Details</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div v-if="selectedCategory" class="q-gutter-md">
            <div class="row">
              <div class="col-4 text-weight-bold">Name:</div>
              <div class="col-8">{{ selectedCategory.name }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-bold">Code:</div>
              <div class="col-8">{{ selectedCategory.code }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-bold">Description:</div>
              <div class="col-8">{{ selectedCategory.description || '-' }}</div>
            </div>
            <div class="row">
              <div class="col-4 text-weight-bold">Status:</div>
              <div class="col-8">
                <q-chip
                  :color="selectedCategory.is_active ? 'green' : 'red'"
                  text-color="white"
                  dense
                >
                  {{ selectedCategory.is_active ? 'Active' : 'Inactive' }}
                </q-chip>
              </div>
            </div>
            <div class="row" v-if="selectedCategory.created_at">
              <div class="col-4 text-weight-bold">Created At:</div>
              <div class="col-8">{{ new Date(selectedCategory.created_at).toLocaleString() }}</div>
            </div>
            <div class="row" v-if="selectedCategory.updated_at">
              <div class="col-4 text-weight-bold">Updated At:</div>
              <div class="col-8">{{ new Date(selectedCategory.updated_at).toLocaleString() }}</div>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" @click="showViewDialog = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Add/Edit Dialog -->
    <q-dialog v-model="showAddDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit Category' : 'Add Category' }}</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="saveCategory" class="q-gutter-md">
            <q-input
              v-model="categoryForm.name"
              outlined
              debounce="500"
              label="Name *"
              :rules="[val => !!val || 'Name is required']"
            />
            
            <q-input
              v-model="categoryForm.code"
              outlined
              label="Code *"
              :rules="[val => !!val || 'Code is required']"
            />
            
            <q-input
              v-model="categoryForm.description"
              outlined
              type="textarea"
              label="Description"
              rows="3"
            />
            
            <q-toggle
              v-model="categoryForm.is_active"
              label="Active"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn
            color="primary"
            label="Save"
            @click="saveCategory"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useCategoryStore } from 'src/stores/category'
import { Dialog } from 'quasar'

// Reactive data
const categoryStore = useCategoryStore()
const saving = ref(false)

// Computed properties from store
const categories = computed(() => categoryStore.categories)
const loading = computed(() => categoryStore.loading)
const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const selectedCategory = ref(null)
const categoryTable = ref(null)

// Local filters for v-model (separated from store filters)
const filterTrigger = ref(null)

// Watcher to update filterTrigger when store filters change
watch(
  () => categoryStore.filters,
  () => {
    filterTrigger.value = String(Date.now())
  },
  { deep: true }
)

const categoryForm = reactive({
  id: null,
  name: '',
  code: '',
  description: '',
  is_active: true
})

// Menggunakan pagination dari store dengan fallback untuk UI
const pagination = computed({
  get: () => {
    const storePagination = categoryStore.pagination
    return {
      sortBy: storePagination.sortBy,
      descending: storePagination.descending,
      page: storePagination.page,
      rowsPerPage: storePagination.rowsPerPage,
      rowsNumber: storePagination.rowsNumber
    }
  },
  set: (val) => {
    categoryStore.setPagination(val)
  }
})

const statusOptions = [
  { label: 'Active', value: true },
  { label: 'Inactive', value: false }
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
const loadCategories = async (props = {}) => {
  try {
    const { page = pagination.value.page, rowsPerPage = pagination.value.rowsPerPage, sortBy, descending } = props.pagination || {}
    
    // Update store pagination jika ada perubahan sorting
    if (sortBy !== undefined) {
      categoryStore.setPagination({
        ...categoryStore.pagination,
        sortBy,
        descending,
        page
      })
    } else {
      categoryStore.setPagination({
        ...categoryStore.pagination,
        page,
        rowsPerPage
      })
    }
    
    const params = {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy || categoryStore.pagination.sortBy,
      sort_order: (descending !== undefined ? descending : categoryStore.pagination.descending) ? 'desc' : 'asc'
    }
    
    // Add search filter
    if (categoryStore.filters.search && categoryStore.filters.search.trim()) {
      params.search = categoryStore.filters.search.trim()
    }
    
    // Add status filter
    if (categoryStore.filters.status !== null && categoryStore.filters.status !== undefined) {
      params.is_active = categoryStore.filters.status.value
    }
    
    await categoryStore.fetchCategories(params)
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

const onRequest = (props) => {
  loadCategories(props)
}

const editCategory = (category) => {
  editMode.value = true
  categoryForm.id = category.id
  categoryForm.name = category.name
  categoryForm.code = category.code
  categoryForm.description = category.description || ''
  categoryForm.is_active = category.is_active
  showAddDialog.value = true
}

const saveCategory = async () => {
  saving.value = true
  try {
    const data = {
      name: categoryForm.name,
      code: categoryForm.code,
      description: categoryForm.description,
      is_active: categoryForm.is_active
    }
    
    if (editMode.value) {
      await categoryStore.updateCategory(categoryForm.id, data)
    } else {
      await categoryStore.createCategory(data)
    }
    closeDialog()
    loadCategories()
  } catch (error) {
    console.error('Error saving category:', error)
  } finally {
    saving.value = false
  }
}

const viewDetail = (category) => {
  selectedCategory.value = category
  showViewDialog.value = true
}

const deleteCategory = (category) => {
  Dialog.create({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete category "${category.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await categoryStore.deleteCategory(category.id)
    } catch (error) {
      console.error('Error deleting category:', error)
    }
  })
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  categoryForm.id = null
  categoryForm.name = ''
  categoryForm.code = ''
  categoryForm.description = ''
  categoryForm.is_active = true
}

// Lifecycle
onMounted(() => {
  loadCategories()
})
</script>
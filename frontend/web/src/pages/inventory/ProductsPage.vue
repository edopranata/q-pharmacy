<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md">
      <div class="col">
        <h4 class="q-my-none">Products Management</h4>
        <p class="text-grey-6 q-mb-none">Kelola produk inventori Anda</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Tambah Produk"
          @click="showAddDialog = true"
        />
      </div>
    </div>

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-input
              v-model="localFilters.search"
              outlined
              dense
              placeholder="Cari produk..."
              clearable
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-select
              v-model="localFilters.category"
              outlined
              dense
              :options="categoryOptions"
              option-label="name"
              option-value="id"
              emit-value
              map-options
              label="Kategori"
              clearable
            />
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <q-select
              v-model="localFilters.status"
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
              @click="refreshData"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card>
      <q-card-section>
        <q-table
          :rows="products"
          :columns="columns"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
          row-key="id"
          binary-state-sort
          :rows-per-page-options="[10, 25, 50, 100]"
          :filter="productStore.filters.search"
          :filter-method="() => {}"
        >
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                color="primary"
                icon="edit"
                size="sm"
                @click="editProduct(props.row)"
              />
              <q-btn
                flat
                round
                color="negative"
                icon="delete"
                size="sm"
                @click="deleteProduct(props.row)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Add/Edit Product Dialog -->
    <q-dialog v-model="showAddDialog">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit Product' : 'Add New Product' }}</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="saveProduct">
            <q-input
              v-model="productForm.name"
              label="Product Name"
              required
              class="q-mb-md"
            />
            <q-input
              v-model="productForm.description"
              label="Description"
              type="textarea"
              class="q-mb-md"
            />
            <q-select
              v-model="productForm.category_id"
              :options="categoryOptions"
              option-value="id"
              option-label="name"
              label="Category"
              required
              class="q-mb-md"
            />
            <q-select
              v-model="productForm.supplier_id"
              :options="supplierOptions"
              option-value="id"
              option-label="name"
              label="Supplier"
              required
              class="q-mb-md"
            />
            <q-select
              v-model="productForm.unit_id"
              :options="unitOptions"
              option-value="id"
              option-label="name"
              label="Unit"
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="productForm.purchase_price"
              label="Purchase Price"
              type="number"
              step="0.01"
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="productForm.selling_price"
              label="Selling Price"
              type="number"
              step="0.01"
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="productForm.stock"
              label="Stock"
              type="number"
              required
              class="q-mb-md"
            />
            <q-input
              v-model.number="productForm.min_stock"
              label="Minimum Stock"
              type="number"
              class="q-mb-md"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="showAddDialog = false" />
          <q-btn color="primary" label="Save" @click="saveProduct" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useProductStore } from 'src/stores/product'
import { useCategoryStore } from 'src/stores/category'
import { useSupplierStore } from 'src/stores/supplier'
import { useUnitStore } from 'src/stores/unit'

const $q = useQuasar()
const productStore = useProductStore()
const categoryStore = useCategoryStore()
const supplierStore = useSupplierStore()
const unitStore = useUnitStore()

const showAddDialog = ref(false)
const editMode = ref(false)

// Local filters for v-model (separated from store filters)
const localFilters = reactive({
  search: '',
  category: null,
  status: null
})

// Watcher to update store filters from local filters with debounce
watch(
  () => localFilters.search,
  (newValue) => {
    productStore.filters.search = newValue
    loadProducts()
  },
  { debounce: 500 }
)

watch(
  () => localFilters.category,
  (newValue) => {
    productStore.filters.category = newValue
    loadProducts()
  }
)

watch(
  () => localFilters.status,
  (newValue) => {
    productStore.filters.status = newValue
    loadProducts()
  }
)

// Initialize local filters from store on mount
onMounted(() => {
  localFilters.search = productStore.filters.search || ''
  localFilters.category = productStore.filters.category || null
  localFilters.status = productStore.filters.status || null
  loadProducts()
  fetchCategories()
  fetchSuppliers()
  fetchUnits()
})

const productForm = reactive({
  id: null,
  name: '',
  description: '',
  sku: '',
  barcode: '',
  category_id: null,
  unit_id: null,
  purchase_price: 0,
  selling_price: 0,
  stock: 0,
  min_stock: 0,
  is_active: true
})

// Computed properties from stores
const products = computed(() => productStore.products)
const loading = computed(() => productStore.loading)
const categoryOptions = computed(() => categoryStore.categories)
const supplierOptions = computed(() => supplierStore.suppliers)
const unitOptions = computed(() => unitStore.units)

const statusOptions = [
  { label: 'Aktif', value: true },
  { label: 'Tidak Aktif', value: false }
]

    const columns = [
      {
        name: 'id',
        label: 'ID',
        field: 'id',
        align: 'left',
        sortable: true
      },
      {
        name: 'name',
        label: 'Name',
        field: 'name',
        align: 'left',
        sortable: true
      },
      {
        name: 'category',
        label: 'Category',
        field: row => row.category?.name || '-',
        align: 'left',
        sortable: true
      },
      {
        name: 'supplier',
        label: 'Supplier',
        field: row => row.supplier?.name || '-',
        align: 'left',
        sortable: true
      },
      {
        name: 'unit',
        label: 'Unit',
        field: row => row.unit?.name || '-',
        align: 'left'
      },
      {
        name: 'purchase_price',
        label: 'Purchase Price',
        field: 'purchase_price',
        align: 'right',
        format: val => `Rp ${val?.toLocaleString() || 0}`
      },
      {
        name: 'selling_price',
        label: 'Selling Price',
        field: 'selling_price',
        align: 'right',
        format: val => `Rp ${val?.toLocaleString() || 0}`
      },
      {
        name: 'stock',
        label: 'Stock',
        field: 'stock',
        align: 'right'
      },
      {
        name: 'actions',
        label: 'Actions',
        field: 'actions',
        align: 'center'
      }
    ]

    // Menggunakan pagination dari store dengan fallback untuk UI
    const pagination = computed({
      get: () => {
        const storePagination = productStore.pagination
        return {
          sortBy: storePagination.sortBy,
          descending: storePagination.descending,
          page: storePagination.page,
          rowsPerPage: storePagination.rowsPerPage,
          rowsNumber: storePagination.rowsNumber
        }
      },
      set: (val) => {
        productStore.setPagination(val)
      }
    })

const loadProducts = async (props = {}) => {
  try {
    const { page = pagination.value.page, rowsPerPage = pagination.value.rowsPerPage, sortBy, descending } = props.pagination || {}
    
    // Update store pagination jika ada perubahan sorting
    if (sortBy !== undefined) {
      productStore.setPagination({
        ...productStore.pagination,
        sortBy,
        descending,
        page
      })
    } else {
      productStore.setPagination({
        ...productStore.pagination,
        page,
        rowsPerPage
      })
    }
    
    const params = {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy || productStore.pagination.sortBy,
      sort_order: (descending !== undefined ? descending : productStore.pagination.descending) ? 'desc' : 'asc'
    }
    
    // Add search filter
    if (productStore.filters.search && productStore.filters.search.trim()) {
      params.search = productStore.filters.search.trim()
    }
    
    // Add category filter
    if (productStore.filters.category) {
      params.category_id = productStore.filters.category
    }
    
    // Add status filter
    if (productStore.filters.status !== null && productStore.filters.status !== undefined) {
      params.is_active = productStore.filters.status
    }
    
    await productStore.fetchProducts(params)
  } catch (error) {
    console.error('Error loading products:', error)
    $q.notify({
      type: 'negative',
      message: 'Gagal memuat data produk'
    })
  }
}

const refreshData = () => {
  localFilters.search = ''
  localFilters.category = null
  localFilters.status = null
  productStore.filters.search = ''
  productStore.filters.category = null
  productStore.filters.status = null
  loadProducts()
}

const fetchCategories = async () => {
  try {
    await categoryStore.fetchCategories()
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to fetch categories'
    })
  }
}

const fetchSuppliers = async () => {
  try {
    await supplierStore.fetchSuppliers()
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to fetch suppliers'
    })
  }
}

const fetchUnits = async () => {
  try {
    await unitStore.fetchUnits()
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Failed to fetch units'
    })
  }
}

const onRequest = (props) => {
  loadProducts(props)
}

const saveProduct = async () => {
  try {
    const data = {
      name: productForm.name,
      description: productForm.description,
      sku: productForm.sku,
      barcode: productForm.barcode,
      category_id: productForm.category_id,
      unit_id: productForm.unit_id,
      purchase_price: productForm.purchase_price,
      selling_price: productForm.selling_price,
      stock: productForm.stock,
      min_stock: productForm.min_stock,
      is_active: productForm.is_active
    }
    
    if (editMode.value) {
      await productStore.updateProduct(productForm.id, data)
      $q.notify({
        type: 'positive',
        message: 'Produk berhasil diperbarui'
      })
    } else {
      await productStore.createProduct(data)
      $q.notify({
        type: 'positive',
        message: 'Produk berhasil ditambahkan'
      })
    }
    
    closeDialog()
    loadProducts()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.message || 'Gagal menyimpan produk'
    })
  }
}

    const deleteProduct = async (product) => {
  $q.dialog({
    title: 'Confirm',
    message: `Are you sure you want to delete product "${product.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await productStore.deleteProduct(product.id)
      $q.notify({
        type: 'positive',
        message: 'Product deleted successfully'
      })
      loadProducts()
    } catch (error) {
      $q.notify({
        type: 'negative',
        message: error.message || 'Failed to delete product'
      })
    }
  })
}

const editProduct = (product) => {
  editMode.value = true
  productForm.id = product.id
  productForm.name = product.name
  productForm.description = product.description || ''
  productForm.sku = product.sku || ''
  productForm.barcode = product.barcode || ''
  productForm.category_id = product.category_id
  productForm.unit_id = product.unit_id
  productForm.purchase_price = product.purchase_price
  productForm.selling_price = product.selling_price
  productForm.stock = product.stock
  productForm.min_stock = product.min_stock
  productForm.is_active = product.is_active
  showAddDialog.value = true
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  resetForm()
}

const resetForm = () => {
  productForm.id = null
  productForm.name = ''
  productForm.description = ''
  productForm.sku = ''
  productForm.barcode = ''
  productForm.category_id = null
  productForm.unit_id = null
  productForm.purchase_price = 0
  productForm.selling_price = 0
  productForm.stock = 0
  productForm.min_stock = 0
  productForm.is_active = true
}

// onMounted already handled above with localFilters initialization
</script>
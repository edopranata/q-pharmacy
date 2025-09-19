<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">Role & Permission Management</h4>
        <p class="text-grey-6">Kelola role dan permission pengguna sistem</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="admin_panel_settings"
          label="Tambah Role"
          @click="openAddDialog"
          :disable="!hasPermission('app.management.roles.store')"
        />
      </div>
    </div>

    <!-- Role Statistics -->
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-md-3">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h6">Total Roles</div>
            <div class="text-h4">{{ totalRoles }}</div>
            <div class="text-caption">Roles dalam sistem</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-secondary text-white">
          <q-card-section>
            <div class="text-h6">Active Roles</div>
            <div class="text-h4">{{ activeRoles }}</div>
            <div class="text-caption">Roles yang digunakan</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-positive text-white">
          <q-card-section>
            <div class="text-h6">Total Permissions</div>
            <div class="text-h4">{{ totalPermissions }}</div>
            <div class="text-caption">Permissions tersedia</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-info text-white">
          <q-card-section>
            <div class="text-h6">Assigned Users</div>
            <div class="text-h4">{{ totalAssignedUsers }}</div>
            <div class="text-caption">Users dengan role</div>
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
              debounce="500"
              v-model="localFilters.search"
              placeholder="Cari role..."
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
              v-model="localFilters.hasUsers"
              :options="userFilterOptions"
              label="Status Pengguna"
              outlined
              dense
              clearable
            />
          </div>
          <div class="col-12 col-md-2">
            <q-select
              v-model="localFilters.permissionCount"
              :options="permissionFilterOptions"
              label="Jumlah Permission"
              outlined
              dense
              clearable
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

    <!-- Roles Table -->
    <q-card>
      <q-card-section>

        <q-table
          :rows="roles"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-permissions="props">
            <q-td :props="props">
              <div class="q-gutter-xs">
                <q-chip
                  v-for="permission in props.row.permissions.slice(0, 3)"
                  :key="permission.id"
                  size="sm"
                  color="blue-grey-2"
                  text-color="blue-grey-8"
                >
                  {{ permission.name }}
                </q-chip>
                <q-chip
                  v-if="props.row.permissions.length > 3"
                  size="sm"
                  color="grey-3"
                  text-color="grey-7"
                >
                  +{{ props.row.permissions.length - 3 }} lainnya
                </q-chip>
                <span v-if="!props.row.permissions || props.row.permissions.length === 0" class="text-grey-6">No Permissions</span>
              </div>
            </q-td>
          </template>

          <template v-slot:body-cell-users_count="props">
            <q-td :props="props">
              <q-badge 
                :color="props.row.users_count > 0 ? 'primary' : 'grey'" 
                :label="props.row.users_count" 
              />
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
                @click="viewRole(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="edit"
                color="warning"
                @click="editRole(props.row)"
                :disable="!hasPermission('app.management.roles.update')"
              >
                <q-tooltip>Edit Role</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="confirmDelete(props.row)"
                :disable="!hasPermission('app.management.roles.destroy') || props.row.name === 'Super Admin'"
              >
                <q-tooltip>{{ props.row.name === 'Super Admin' ? 'Cannot delete super admin' : 'Hapus Role' }}</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Create/Edit Role Dialog -->
    <q-dialog v-model="showCreateDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editingRole ? 'Edit Role' : 'Tambah Role Baru' }}</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveRole" class="q-gutter-md">
            <q-input
              v-model="roleForm.name"
              label="Nama Role"
              outlined
              :rules="[val => !!val || 'Nama role wajib diisi']"
            />

            <q-input
              v-model="roleForm.description"
              label="Deskripsi"
              outlined
              type="textarea"
              rows="3"
            />

            <div class="text-subtitle2 q-mb-sm">Permissions</div>
            <div class="row q-gutter-sm">
              <div
                v-for="permission in availablePermissions"
                :key="permission.id"
                class="col-12 col-sm-6 col-md-4"
              >
                <q-checkbox
                  v-model="roleForm.permissions"
                  :val="permission.id"
                  :label="permission.name"
                  dense
                />
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" @click="closeDialog" />
          <q-btn
            color="primary"
            label="Simpan"
            @click="saveRole"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View Role Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">Detail Role</div>
        </q-card-section>

        <q-card-section v-if="selectedRole">
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label>Nama Role</q-item-label>
                <q-item-label caption>{{ selectedRole.name }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Deskripsi</q-item-label>
                <q-item-label caption>{{ selectedRole.description || 'Tidak ada deskripsi' }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Jumlah Pengguna</q-item-label>
                <q-item-label caption>
                  <q-badge 
                    :color="selectedRole.users_count > 0 ? 'primary' : 'grey'" 
                    :label="selectedRole.users_count + ' pengguna'" 
                  />
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Permissions ({{ selectedRole.permissions?.length || 0 }})</q-item-label>
                <q-item-label caption>
                  <div class="q-gutter-xs q-mt-sm">
                    <q-chip
                      v-for="permission in selectedRole.permissions || []"
                      :key="permission.id"
                      size="sm"
                      color="blue-grey-2"
                      text-color="blue-grey-8"
                    >
                      {{ permission.name }}
                    </q-chip>
                    <span v-if="!selectedRole.permissions || selectedRole.permissions.length === 0" class="text-grey-6">Tidak ada permissions</span>
                  </div>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item v-if="selectedRole.created_at">
              <q-item-section>
                <q-item-label>Dibuat</q-item-label>
                <q-item-label caption>{{ formatDate(selectedRole.created_at) }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item v-if="selectedRole.updated_at">
              <q-item-section>
                <q-item-label>Terakhir Diperbarui</q-item-label>
                <q-item-label caption>{{ formatDate(selectedRole.updated_at) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Tutup" @click="showViewDialog = false" />
          <q-btn 
            color="primary" 
            icon="edit" 
            label="Edit" 
            @click="editRoleFromView" 
            :disable="!hasPermission('app.management.roles.update')"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="showDeleteDialog" persistent>
      <q-card>
        <q-card-section>
          <div class="text-h6">Konfirmasi Hapus</div>
        </q-card-section>

        <q-card-section>
          Apakah Anda yakin ingin menghapus role "{{ roleToDelete?.name }}"?
          <br><br>
          <q-banner class="bg-orange-1 text-orange-8" rounded>
            <template v-slot:avatar>
              <q-icon name="warning" color="orange" />
            </template>
            Role yang dihapus tidak dapat dikembalikan dan akan mempengaruhi {{ roleToDelete?.users_count }} pengguna.
          </q-banner>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" @click="showDeleteDialog = false" />
          <q-btn
            color="red"
            label="Hapus"
            @click="deleteRole"
            :loading="deleting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth'
import { roleService } from 'src/services'

const $q = useQuasar()
const authStore = useAuthStore()

// Reactive data
const roles = ref([])
const availablePermissions = ref([])
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const showCreateDialog = ref(false)
const showViewDialog = ref(false)
const showDeleteDialog = ref(false)
const editingRole = ref(null)
const selectedRole = ref(null)
const roleToDelete = ref(null)

// Local filters for v-model (separated from store filters)
const localFilters = reactive({
  search: '',
  hasUsers: null,
  permissionCount: null
})

// Filter options
const userFilterOptions = [
  { label: 'Dengan Pengguna', value: 'with_users' },
  { label: 'Tanpa Pengguna', value: 'without_users' }
]

const permissionFilterOptions = [
  { label: 'Banyak (>10)', value: 'many' },
  { label: 'Sedang (5-10)', value: 'medium' },
  { label: 'Sedikit (<5)', value: 'few' }
]

// Role Statistics
const roleStats = ref({
  total_roles: 0,
  active_roles: 0,
  total_permissions: 0,
  total_assigned_users: 0
})

// Form data
const roleForm = ref({
  name: '',
  description: '',
  permissions: []
})

// Table configuration
const columns = [
  {
    name: 'name',
    label: 'Nama Role',
    align: 'left',
    field: 'name',
    sortable: true
  },
  {
    name: 'description',
    label: 'Deskripsi',
    align: 'left',
    field: 'description'
  },
  {
    name: 'permissions',
    label: 'Permissions',
    align: 'left',
    field: 'permissions'
  },
  {
    name: 'users_count',
    label: 'Jumlah User',
    align: 'center',
    field: 'users_count',
    sortable: true
  },
  {
    name: 'actions',
    label: 'Aksi',
    align: 'center'
  }
]

const pagination = ref({
  sortBy: 'name',
  descending: false,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

// Computed properties
const totalRoles = computed(() => roleStats.value.total_roles)
const activeRoles = computed(() => roleStats.value.active_roles)
const totalPermissions = computed(() => roleStats.value.total_permissions)
const totalAssignedUsers = computed(() => roleStats.value.total_assigned_users)

const hasPermission = computed(() => {
  return (permission) => authStore.hasPermission(permission)
})

// Watcher to update filters from local filters with debounce
watch(
  () => localFilters.search,
  () => {
    pagination.value.page = 1
    fetchRoles()
  },
  { debounce: 500 }
)

watch(
  () => localFilters.hasUsers,
  () => {
    pagination.value.page = 1
    fetchRoles()
  }
)

watch(
  () => localFilters.permissionCount,
  () => {
    pagination.value.page = 1
    fetchRoles()
  }
)

// Methods
const fetchRoles = async () => {
  try {
    loading.value = true
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage,
      search: localFilters.search,
      sort_by: pagination.value.sortBy,
      sort_order: pagination.value.descending ? 'desc' : 'asc',
      has_users: localFilters.hasUsers,
      permission_count: localFilters.permissionCount
    }
    
    const response = await roleService.getRoles(params)
    
    // Handle the response structure from backend
    if (response.data && response.data.success) {
      roles.value = response.data.data.data
      pagination.value.rowsNumber = response.data.data.total
      pagination.value.page = response.data.data.current_page
      pagination.value.rowsPerPage = response.data.data.per_page
    } else {
      throw new Error('Invalid response format')
    }
  } catch (error) {
    console.error('Error loading roles:', error)
    $q.notify({
      type: 'negative',
      message: 'Gagal memuat data role',
      caption: error.response?.data?.message || error.message
    })
  } finally {
    loading.value = false
  }
}

const fetchRoleStats = async () => {
  try {
    const response = await roleService.getRoleStats()
    if (response.data && response.data.success) {
      roleStats.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading role stats:', error)
  }
}

const fetchPermissions = async () => {
  try {
    const response = await roleService.getPermissions()
    if (response.data && response.data.success) {
      availablePermissions.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching permissions:', error)
  }
}

const viewRole = (role) => {
  selectedRole.value = role
  showViewDialog.value = true
}

const openAddDialog = () => {
  editingRole.value = null
  roleForm.value = {
    name: '',
    description: '',
    permissions: []
  }
  showCreateDialog.value = true
}

const editRole = (role) => {
  editingRole.value = role
  roleForm.value = {
    name: role.name,
    description: role.description || '',
    permissions: role.permissions.map(p => p.id)
  }
  showCreateDialog.value = true
}

const editRoleFromView = () => {
  showViewDialog.value = false
  editRole(selectedRole.value)
}

const refreshData = async () => {
  await Promise.all([fetchRoles(), fetchRoleStats()])
  $q.notify({
    type: 'positive',
    message: 'Data berhasil dimuat ulang'
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID')
}

const saveRole = async () => {
  try {
    saving.value = true
    
    // Convert permission IDs to permission names for backend
    const permissionNames = roleForm.value.permissions.map(permId => {
      const permission = availablePermissions.value.find(p => p.id === permId)
      return permission ? permission.name : null
    }).filter(Boolean)
    
    const roleData = {
      ...roleForm.value,
      permissions: permissionNames
    }
    
    if (editingRole.value) {
      await roleService.updateRole(editingRole.value.id, roleData)
      $q.notify({
        type: 'positive',
        message: 'Role berhasil diperbarui'
      })
    } else {
      await roleService.createRole(roleData)
      $q.notify({
        type: 'positive',
        message: 'Role berhasil dibuat'
      })
    }
    
    closeDialog()
    await fetchRoles()
    await fetchRoleStats()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Gagal menyimpan role',
      caption: error.response?.data?.message || error.message
    })
  } finally {
    saving.value = false
  }
}

const confirmDelete = (role) => {
  roleToDelete.value = role
  showDeleteDialog.value = true
}

const deleteRole = async () => {
  try {
    deleting.value = true
    await roleService.deleteRole(roleToDelete.value.id)
    
    $q.notify({
      type: 'positive',
      message: 'Role berhasil dihapus'
    })
    
    showDeleteDialog.value = false
    await fetchRoles()
    await fetchRoleStats()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Gagal menghapus role',
      caption: error.response?.data?.message || error.message
    })
  } finally {
    deleting.value = false
  }
}

const closeDialog = () => {
  showCreateDialog.value = false
  editingRole.value = null
  roleForm.value = {
    name: '',
    description: '',
    permissions: []
  }
}

const onRequest = (props) => {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  pagination.value.page = page
  pagination.value.rowsPerPage = rowsPerPage
  pagination.value.sortBy = sortBy
  pagination.value.descending = descending
  fetchRoles()
}

// Lifecycle
onMounted(() => {
  fetchRoles()
  fetchPermissions()
  fetchRoleStats()
})
</script>

<style scoped>
.q-table {
  box-shadow: none;
}
</style>
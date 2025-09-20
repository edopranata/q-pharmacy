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

    <!-- Statistics Cards -->
    <RoleStatsCard 
      :stats="roleStats"
      :loading="statsLoading"
      :error="statsError"
      @retry="retryLoadStats"
    />

    <!-- Filters -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-12 col-md-4">
            <q-input
              debounce="300"
              :model-value="filter.search"
              @update:model-value="roleStore.setFilter('search', $event)"
              placeholder="Cari role berdasarkan nama atau deskripsi..."
              outlined
              dense
              clearable
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-md-3">
            <q-select
              :model-value="filter.status"
              @update:model-value="roleStore.setFilter('status', $event)"
              :options="statusOptions"
              placeholder="Filter Status"
              outlined
              dense
              clearable
              emit-value
              map-options
            />
          </div>
          <div class="col-12 col-md-3">
            <q-select
              :model-value="filter.permissions_count"
              @update:model-value="roleStore.setFilter('permissions_count', $event)"
              :options="permissionCountOptions"
              placeholder="Filter Permissions"
              outlined
              dense
              clearable
              emit-value
              map-options
            />
          </div>
          <div class="col-12 col-md-2">
            <q-btn
              color="primary"
              icon="refresh"
              label="Refresh"
              @click="refreshData"
              :loading="loading"
              dense
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Roles Table -->
    <q-card>
      <q-card-section>

        <q-table
          class="table-clean"
          :rows="roles"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
          binary-state-sort
          :rows-per-page-options="[10, 25, 50, 100]"
        >
          <template v-slot:body-cell-permissions_count="props">
            <q-td :props="props">
              <q-badge 
                :color="props.row.permissions_count > 0 ? 'blue' : 'grey'" 
                :label="props.row.permissions_count || 0" 
              />
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
              <div class="q-gutter-xs">
                <q-btn
                  flat
                  dense
                  round
                  color="blue"
                  icon="visibility"
                  size="sm"
                  @click="viewRole(props.row)"
                  :disable="!hasPermission('app.management.roles.show')"
                >
                  <q-tooltip>Lihat Detail</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  dense
                  round
                  color="orange"
                  icon="edit"
                  size="sm"
                  @click="editRole(props.row)"
                  :disable="!hasPermission('app.management.roles.update')"
                >
                  <q-tooltip>Edit Role</q-tooltip>
                </q-btn>
                <q-btn
                  flat
                  dense
                  round
                  color="red"
                  icon="delete"
                  size="sm"
                  @click="confirmDelete(props.row)"
                  :disable="!hasPermission('app.management.roles.delete') || props.row.name === 'Super Admin'"
                >
                  <q-tooltip>Hapus Role</q-tooltip>
                </q-btn>
              </div>
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
              label="Nama Role *"
              outlined
              required
              :rules="[
                val => !!val || 'Nama role wajib diisi',
                val => val.length >= 3 || 'Nama role minimal 3 karakter',
                val => val.length <= 50 || 'Nama role maksimal 50 karakter'
              ]"
              counter
              maxlength="50"
            />

            <q-input
              v-model="roleForm.description"
              label="Deskripsi"
              outlined
              type="textarea"
              rows="3"
              :rules="[
                val => !val || val.length <= 255 || 'Deskripsi maksimal 255 karakter'
              ]"
              counter
              maxlength="255"
            />

            <div class="q-mb-md">
              <div class="text-subtitle2 q-mb-sm">Permissions *</div>
              <div class="text-caption text-grey-6 q-mb-md">Pilih permissions yang akan diberikan untuk role ini</div>
              
              <q-card flat bordered class="q-pa-md" style="max-height: 400px; overflow-y: auto;">
                <!-- Permissions grouped by category -->
                <div v-if="permissionsByCategory && Object.keys(permissionsByCategory).length > 0">
                  <div 
                    v-for="(permissions, category) in permissionsByCategory" 
                    :key="category"
                    class="q-mb-lg"
                  >
                    <!-- Category Header -->
                    <div class="row items-center q-mb-sm">
                      <div class="col">
                        <div class="text-weight-medium text-primary">{{ category }}</div>
                        <div class="text-caption text-grey-6">{{ permissions.length }} permissions</div>
                      </div>
                      <div class="col-auto">
                        <q-btn
                          flat
                          dense
                          size="sm"
                          :label="isAllCategorySelected(category) ? 'Unselect All' : 'Select All'"
                          :color="isAllCategorySelected(category) ? 'negative' : 'positive'"
                          @click="toggleCategorySelection(category)"
                        />
                      </div>
                    </div>
                    
                    <!-- Permissions in category -->
                    <div class="row q-gutter-sm">
                      <div
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="col-12"
                      >
                        <q-checkbox
                          v-model="roleForm.permissions"
                          :val="permission.id"
                          dense
                          class="full-width"
                        >
                          <div class="q-ml-sm">
                            <div class="text-body2">{{ formatPermissionName(permission.name) }}</div>
                            <div class="text-caption text-grey-6">{{ permission.description }}</div>
                          </div>
                        </q-checkbox>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Fallback for flat permissions (backward compatibility) -->
                <div v-else-if="availablePermissions && availablePermissions.length > 0" class="row q-gutter-sm">
                  <div
                    v-for="permission in availablePermissions"
                    :key="permission.id"
                    class="col-12 col-sm-6"
                  >
                    <q-checkbox
                      v-model="roleForm.permissions"
                      :val="permission.id"
                      :label="permission.name"
                      dense
                      class="full-width"
                    />
                  </div>
                </div>
                
                <!-- No permissions available -->
                <div v-else class="text-center text-grey-6 q-pa-md">
                  <q-icon name="security" size="48px" class="q-mb-sm" />
                  <div>Tidak ada permissions tersedia</div>
                  <div class="text-caption">Pastikan backend sudah mengembalikan data permissions</div>
                </div>
              </q-card>
              
              <div class="text-caption text-grey-6 q-mt-sm">
                {{ roleForm.permissions?.length || 0 }} permissions dipilih dari {{ availablePermissions?.length || 0 }} total permissions
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
                  <div class="q-mt-sm">
                    <q-card flat bordered class="q-pa-md" style="max-height: 300px; overflow-y: auto;" v-if="selectedRole.permissions && selectedRole.permissions.length > 0">
                      <!-- Permissions grouped by category -->
                      <div v-if="selectedRolePermissionsByCategory && Object.keys(selectedRolePermissionsByCategory).length > 0">
                        <div 
                          v-for="(permissions, category) in selectedRolePermissionsByCategory" 
                          :key="category"
                          class="q-mb-lg"
                        >
                          <!-- Category Header -->
                          <div class="row items-center q-mb-sm">
                            <div class="col">
                              <div class="text-weight-medium text-primary">{{ category }}</div>
                              <div class="text-caption text-grey-6">{{ permissions.length }} permissions</div>
                            </div>
                          </div>
                          
                          <!-- Permissions in category -->
                          <div class="row q-gutter-sm">
                            <div
                              v-for="permission in permissions"
                              :key="permission.id"
                              class="col-12"
                            >
                              <div class="q-pa-sm bg-blue-grey-1 rounded-borders">
                                <div class="text-body2 text-weight-medium">{{ formatPermissionName(permission.name) }}</div>
                                <div class="text-caption text-grey-6" v-if="permission.description">{{ permission.description }}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Fallback for flat permissions (backward compatibility) -->
                      <div v-else class="q-gutter-xs">
                        <q-chip
                          v-for="permission in selectedRole.permissions"
                          :key="permission.id"
                          size="sm"
                          color="blue-grey-2"
                          text-color="blue-grey-8"
                        >
                          {{ formatPermissionName(permission.name) }}
                        </q-chip>
                      </div>
                    </q-card>
                    
                    <!-- No permissions -->
                    <div v-else class="text-center text-grey-6 q-pa-md">
                      <q-icon name="security" size="32px" class="q-mb-sm" />
                      <div>Tidak ada permissions</div>
                    </div>
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
import { ref, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth'
import { useRoleStore } from 'src/stores/role'
import RoleStatsCard from 'src/components/RoleStatsCard.vue'

const $q = useQuasar()
const authStore = useAuthStore()
const roleStore = useRoleStore()

// Local reactive data for UI state
const showCreateDialog = ref(false)
const showViewDialog = ref(false)
const showDeleteDialog = ref(false)
const editingRole = ref(null)
const selectedRole = ref(null)
const roleToDelete = ref(null)

// Filter options
const statusOptions = [
  { label: 'Aktif', value: 'active' },
  { label: 'Tidak Aktif', value: 'inactive' }
]

const permissionCountOptions = [
  { label: 'Tanpa Permission', value: '0' },
  { label: '1-5 Permissions', value: '1-5' },
  { label: '6-10 Permissions', value: '6-10' },
  { label: '10+ Permissions', value: '10+' }
]

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
    name: 'permissions_count',
    label: 'Total Permissions',
    align: 'center',
    field: 'permissions_count',
    sortable: true
  },
  {
    name: 'users_count',
    label: 'Total User',
    align: 'center',
    field: 'users_count',
    sortable: true
  },
  {
    name: 'actions',
    label: 'Actions',
    align: 'center'
  }
]

// Computed properties from store
const roles = computed(() => roleStore.roles)
const availablePermissions = computed(() => roleStore.availablePermissions)
const loading = computed(() => roleStore.loading)
const saving = computed(() => roleStore.saving)
const deleting = computed(() => roleStore.deleting)
const filter = computed(() => roleStore.filters)
const pagination = computed(() => roleStore.pagination)
const roleStats = computed(() => roleStore.stats)
const statsLoading = computed(() => roleStore.statsLoading)
const statsError = computed(() => roleStore.statsError)

const hasPermission = computed(() => {
  return (permission) => authStore.hasPermission(permission)
})

// Group permissions by category
const permissionsByCategory = computed(() => {
  if (!availablePermissions.value || availablePermissions.value.length === 0) {
    return {}
  }
  
  const grouped = {}
  availablePermissions.value.forEach(permission => {
    const category = permission.category || 'Uncategorized'
    if (!grouped[category]) {
      grouped[category] = []
    }
    grouped[category].push(permission)
  })
  
  // Sort categories alphabetically
  const sortedGrouped = {}
  Object.keys(grouped).sort().forEach(key => {
    sortedGrouped[key] = grouped[key].sort((a, b) => a.name.localeCompare(b.name))
  })
  
  return sortedGrouped
})

// Group selected role permissions by category for view dialog
const selectedRolePermissionsByCategory = computed(() => {
  if (!selectedRole.value || !selectedRole.value.permissions || selectedRole.value.permissions.length === 0) {
    return {}
  }
  
  const grouped = {}
  selectedRole.value.permissions.forEach(permission => {
    // Try to get category from permission itself first
    let category = permission.category
    
    // If no category, try to find it from availablePermissions
    if (!category && availablePermissions.value) {
      const matchedPermission = availablePermissions.value.find(p => p.id === permission.id || p.name === permission.name)
      category = matchedPermission?.category || 'Uncategorized'
    }
    
    // Fallback to Uncategorized
    if (!category) {
      category = 'Uncategorized'
    }
    
    if (!grouped[category]) {
      grouped[category] = []
    }
    grouped[category].push(permission)
  })
  
  // Sort categories alphabetically
  const sortedGrouped = {}
  Object.keys(grouped).sort().forEach(key => {
    sortedGrouped[key] = grouped[key].sort((a, b) => a.name.localeCompare(b.name))
  })
  
  return sortedGrouped
})

// Watcher to update filters with debounce
watch(
  () => filter.value.search,
  () => {
    roleStore.setPagination({ page: 1 })
    roleStore.fetchRoles()
  },
  { debounce: 500 }
)

watch(
  () => filter.value.status,
  () => {
    roleStore.setPagination({ page: 1 })
    roleStore.fetchRoles()
  }
)

watch(
  () => filter.value.permissions_count,
  () => {
    roleStore.setPagination({ page: 1 })
    roleStore.fetchRoles()
  }
)

// Methods using store actions
const retryLoadStats = () => roleStore.fetchStats()

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
    permissions: role.permissions ? role.permissions.map(p => p.id) : []
  }
  showCreateDialog.value = true
}

const editRoleFromView = () => {
  if (selectedRole.value) {
    editRole(selectedRole.value)
    showViewDialog.value = false
  }
}

// Helper methods for permissions UI
const isAllCategorySelected = (category) => {
  const categoryPermissions = permissionsByCategory.value[category] || []
  if (categoryPermissions.length === 0) return false
  
  return categoryPermissions.every(permission => 
    roleForm.value.permissions.includes(permission.id)
  )
}

const toggleCategorySelection = (category) => {
  const categoryPermissions = permissionsByCategory.value[category] || []
  const isAllSelected = isAllCategorySelected(category)
  
  if (isAllSelected) {
    // Remove all permissions from this category
    categoryPermissions.forEach(permission => {
      const index = roleForm.value.permissions.indexOf(permission.id)
      if (index > -1) {
        roleForm.value.permissions.splice(index, 1)
      }
    })
  } else {
    // Add all permissions from this category
    categoryPermissions.forEach(permission => {
      if (!roleForm.value.permissions.includes(permission.id)) {
        roleForm.value.permissions.push(permission.id)
      }
    })
  }
}

const formatPermissionName = (permissionName) => {
  // Convert "app.management.users.index" to "Users Index"
  if (!permissionName) return ''
  
  const parts = permissionName.split('.')
  if (parts.length >= 3) {
    const module = parts[parts.length - 2] // e.g., "users"
    const action = parts[parts.length - 1] // e.g., "index"
    
    // Capitalize and format
    const formattedModule = module.charAt(0).toUpperCase() + module.slice(1)
    const formattedAction = action.charAt(0).toUpperCase() + action.slice(1)
    
    return `${formattedModule} ${formattedAction}`
  }
  
  return permissionName
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID')
}

const refreshData = async () => {
  await Promise.all([
    roleStore.fetchRoles(),
    roleStore.fetchStats()
  ])
}

const saveRole = async () => {
  // Validation
  if (!roleForm.value.name || !roleForm.value.name.trim()) {
    $q.notify({
      type: 'warning',
      message: 'Nama role wajib diisi'
    })
    return
  }
  
  if (roleForm.value.name.trim().length < 3) {
    $q.notify({
      type: 'warning',
      message: 'Nama role minimal 3 karakter'
    })
    return
  }
  
  if (!roleForm.value.permissions || roleForm.value.permissions.length === 0) {
    $q.notify({
      type: 'warning',
      message: 'Pilih minimal satu permission'
    })
    return
  }
  
  try {
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
      await roleStore.updateRole(editingRole.value.id, roleData)
      $q.notify({
        type: 'positive',
        message: 'Role berhasil diperbarui'
      })
    } else {
      await roleStore.createRole(roleData)
      $q.notify({
        type: 'positive',
        message: 'Role berhasil dibuat'
      })
    }
    
    closeDialog()
    await refreshData()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Gagal menyimpan role',
      caption: error.response?.data?.message || error.message
    })
  }
}

const confirmDelete = (role) => {
  roleToDelete.value = role
  showDeleteDialog.value = true
}

const deleteRole = async () => {
  try {
    await roleStore.deleteRole(roleToDelete.value.id)
    
    $q.notify({
      type: 'positive',
      message: 'Role berhasil dihapus'
    })
    
    showDeleteDialog.value = false
    await refreshData()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Gagal menghapus role',
      caption: error.response?.data?.message || error.message
    })
  }
}

const closeDialog = () => {
  showCreateDialog.value = false
  showViewDialog.value = false
  editingRole.value = null
  selectedRole.value = null
  roleForm.value = {
    name: '',
    description: '',
    permissions: []
  }
}

const onRequest = (props) => {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  roleStore.setPagination({
    page,
    rowsPerPage,
    sortBy,
    descending
  })
  roleStore.fetchRoles()
}

// Lifecycle
onMounted(() => {
  roleStore.fetchRoles()
  roleStore.fetchPermissions()
  roleStore.fetchStats()
})
</script>
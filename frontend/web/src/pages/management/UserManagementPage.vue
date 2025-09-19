<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-lg">
      <div class="col">
        <h4 class="q-my-none">User Management</h4>
        <p class="text-grey-6">Manage system users and permissions</p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="person_add"
          label="Add User"
          @click="openAddDialog"
        />
      </div>
    </div>

    <!-- User Statistics -->
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-md-3">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h6">Total Users</div>
            <div class="text-h4">{{ totalUsers }}</div>
            <div class="text-caption">Active system users</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-secondary text-white">
          <q-card-section>
            <div class="text-h6">Active Users</div>
            <div class="text-h4">{{ activeUsers }}</div>
            <div class="text-caption">Currently active</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-positive text-white">
          <q-card-section>
            <div class="text-h6">Admins</div>
            <div class="text-h4">{{ adminUsers }}</div>
            <div class="text-caption">Administrator accounts</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-3">
        <q-card class="bg-info text-white">
          <q-card-section>
            <div class="text-h6">Cashiers</div>
            <div class="text-h4">{{ cashierUsers }}</div>
            <div class="text-caption">Cashier accounts</div>
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
              placeholder="Search users..."
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
              v-model="localFilters.role"
              :options="roleOptions"
              label="Role"
              outlined
              dense
              clearable
              use-input
              input-debounce="500"
              @filter="filterRoles"
              @input-value="setRoleModel"
              option-value="name"
              option-label="label"
              emit-value
              map-options
            />
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

    <!-- Users Table -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="users"
          :columns="columns"
          row-key="id"
          :loading="loading"
          v-model:pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-avatar="props">
            <q-td :props="props">
              <q-avatar size="40px">
                <img :src="getUserAvatarUrl(props.row)" alt="User Avatar" />
              </q-avatar>
            </q-td>
          </template>
          <template v-slot:body-cell-roles="props">
            <q-td :props="props">
              <div class="q-gutter-xs">
                <q-chip
                  v-for="role in props.row.roles || []"
                  :key="role.id"
                  :color="getRoleColor(role.name)"
                  text-color="white"
                  :icon="getRoleIcon(role.name)"
                  size="sm"
                >
                  {{ role.name }}
                </q-chip>
                <span v-if="!props.row.roles || props.row.roles.length === 0" class="text-grey-6">No Roles</span>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-status="props">
              <q-td :props="props">
                <q-chip
                  :color="props.row.email_verified_at ? 'green' : 'red'"
                  text-color="white"
                  size="sm"
                >
                  {{ props.row.email_verified_at ? 'Active' : 'Inactive' }}
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
                @click="viewUser(props.row)"
              >
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="edit"
                color="warning"
                @click="editUser(props.row)"
              >
                <q-tooltip>Edit User</q-tooltip>
              </q-btn>
              <q-btn
                v-if="!props.row.email_verified_at"
                flat
                round
                size="sm"
                :icon="props.row.status === 'Active' ? 'block' : 'check_circle'"
                :color="props.row.status === 'Active' ? 'negative' : 'positive'"
                @click="toggleUserStatus(props.row)"
              >
                <q-tooltip>{{ props.row.status === 'Active' ? 'Deactivate' : 'Activate' }} User</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="lock_reset"
                color="info"
                @click="resetPassword(props.row)"
              >
                <q-tooltip>Reset Password</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                size="sm"
                icon="delete"
                color="negative"
                @click="deleteUser(props.row)"
                :disable="props.row.role === 'Super Admin'"
              >
                <q-tooltip>{{ props.row.role === 'Super Admin' ? 'Cannot delete super admin' : 'Delete User' }}</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Add/Edit User Dialog -->
    <q-dialog v-model="showAddDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit' : 'Add' }} User</div>
        </q-card-section>

        <q-card-section>
          <q-form>
            <q-input
              v-model="userForm.name"
              label="Full Name"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model="userForm.email"
              label="Email"
              type="email"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-model="userForm.phone"
              label="Phone Number"
              outlined
              class="q-mb-md"
            />
            <div class="row q-col-gutter-sm q-mb-md">
              <div class="col">
                <q-select
                  v-model="userForm.roles"
                  :options="formRoleOptions"
                  label="Roles"
                  multiple
                  use-chips
                  emit-value
                  map-options
                  option-value="value"
                  option-label="label"
                  use-input
                  input-debounce="500"
                  @filter="filterFormRoles"
                  @input-value="setFormRoleModel"
                  clearable
                  :rules="[
                        val => val && val.length > 0 || 'Minimal satu role harus dipilih',
                        val => val && val.length <= 5 || 'Maksimal 5 role dapat dipilih'
                      ]"
                  outlined
                  required
                  :loading="formRoleLoading"
                >
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        Tidak ada role yang ditemukan
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>
              <div class="col-auto">
                <q-btn
                  icon="refresh"
                  color="grey-6"
                  flat
                  round
                  dense
                  @click="refreshFormRoleOptions"
                  :loading="formRoleLoading"
                >
                  <q-tooltip>Refresh daftar role</q-tooltip>
                </q-btn>
              </div>
            </div>
            <q-select
              v-model="userForm.status"
              :options="statusOptions"
              label="Status"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-if="!editMode"
              v-model="userForm.password"
              label="Password"
              type="password"
              outlined
              required
              class="q-mb-md"
            />
            <q-input
              v-if="!editMode"
              v-model="userForm.password_confirmation"
              label="Confirm Password"
              type="password"
              outlined
              required
              class="q-mb-md"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" @click="closeDialog" />
          <q-btn color="primary" label="Save" @click="saveUser" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- View User Dialog -->
    <q-dialog v-model="showViewDialog">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">User Details</div>
        </q-card-section>

        <q-card-section v-if="selectedUser">
          <div class="text-center q-mb-lg">
            <q-avatar size="80px">
              <img :src="getUserAvatarUrl(selectedUser)" alt="User Avatar" />
            </q-avatar>
            <div class="text-h6 q-mt-md">{{ selectedUser.name }}</div>
            <div class="q-gutter-xs">
              <q-chip
                v-for="role in selectedUser.roles || []"
                :key="role.id"
                :color="getRoleColor(role.name)"
                text-color="white"
                :icon="getRoleIcon(role.name)"
                size="sm"
              >
                {{ role.name }}
              </q-chip>
            </div>
          </div>
          
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label>Email</q-item-label>
                <q-item-label caption>{{ selectedUser.email }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Phone</q-item-label>
                <q-item-label caption>{{ selectedUser.phone || 'Not provided' }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Status</q-item-label>
                <q-item-label caption>
                  <q-chip
                    :color="selectedUser.email_verified_at ? 'green' : 'red'"
                    text-color="white"
                    size="sm"
                  >
                    {{ selectedUser.email_verified_at ? 'Active' : 'Inactive' }}
                  </q-chip>
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Created</q-item-label>
                <q-item-label caption>{{ DateUtils.formatDateLong(selectedUser.created_at) }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>Last Login</q-item-label>
                <q-item-label caption>{{ selectedUser.last_login ? DateUtils.formatDateLong(selectedUser.last_login) : 'Never' }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" @click="showViewDialog = false" />
          <q-btn color="primary" icon="edit" label="Edit" @click="editUserFromView" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useUserStore } from 'src/stores'
import { DateUtils } from 'src/utils'

const $q = useQuasar()
const userStore = useUserStore()

const showAddDialog = ref(false)
const showViewDialog = ref(false)
const editMode = ref(false)
const selectedUser = ref(null)

const roleOptions = ref([])
const statusOptions = ['Active', 'Inactive']

// Local filters for v-model (separated from store filters)
const localFilters = reactive({
  search: '',
  role: null,
  status: null
})

// Watcher to update filters from local filters with debounce
watch(
  () => localFilters.search,
  () => {
    updateFiltersAndLoad()
  },
  { debounce: 500 }
)

watch(
  () => localFilters.role,
  () => {
    updateFiltersAndLoad()
  }
)

watch(
  () => localFilters.status,
  () => {
    updateFiltersAndLoad()
  }
)

const userForm = reactive({
  id: null,
  name: '',
  email: '',
  phone: '',
  roles: [],
  status: 'Active',
  password: '',
  password_confirmation: ''
})

// Watcher for form role changes to validate in real-time
watch(() => userForm.roles, (newRoles) => {
  if (newRoles && newRoles.length > 5) {
    $q.notify({
      type: 'warning',
      message: 'Maksimal 5 role dapat dipilih',
      timeout: 2000
    })
    // Remove excess roles
    userForm.roles = newRoles.slice(0, 5)
  }
}, { deep: true })

const formRoleOptions = ref([])
const formRoleLoading = ref(false)

// Computed properties from store
const users = computed(() => userStore.users)
const loading = computed(() => userStore.loading)
const pagination = computed({
  get: () => userStore.pagination,
  set: (value) => userStore.setPagination(value)
})
const userStats = computed(() => userStore.userStats)

const totalUsers = computed(() => userStats.value.total_users || 0)
const activeUsers = computed(() => userStats.value.active_users || 0)
const adminUsers = computed(() => userStats.value.admin_users || 0)
const cashierUsers = computed(() => userStats.value.cashier_users || 0)

// Avatar URL helper
const getUserAvatarUrl = (user) => {
  if (user?.avatar) {
    return user.avatar
  }
  // Generate avatar based on user name
  const name = user?.name || 'User'
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1976d2&color=fff&size=80`
}

const columns = [
  {
    name: 'avatar',
    label: '',
    field: 'avatar',
    align: 'center'
  },
  {
    name: 'name',
    label: 'Name',
    field: 'name',
    align: 'left',
    sortable: true
  },
  {
    name: 'email',
    label: 'Email',
    field: 'email',
    align: 'left',
    sortable: true
  },
  {
    name: 'phone',
    label: 'Phone',
    field: 'phone',
    align: 'left'
  },
  {
    name: 'role',
    label: 'Roles',
    field: 'roles',
    align: 'center',
    sortable: false,
    format: (val) => val && val.length > 0 ? val.map(role => role.name).join(', ') : 'No Roles'
  },
  {
    name: 'status',
    label: 'Status',
    field: 'email_verified_at',
    align: 'center',
    sortable: true,
    format: (val) => val ? 'Active' : 'Inactive'
  },
  {
    name: 'last_login',
    label: 'Last Login',
    field: 'last_login',
    align: 'left',
    sortable: true,
    format: val => val ? DateUtils.formatDateLong(val) : 'Never'
  },
  {
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center'
  }
]

const getRoleColor = (role) => {
  const colors = {
    'Super Admin': 'red',
    'Admin': 'purple',
    'Manager': 'blue',
    'Cashier': 'green',
    'Staff': 'orange'
  }
  return colors[role] || 'grey'
}

const getRoleIcon = (role) => {
  const icons = {
    'super Admin': 'admin_panel_settings',
    'admin': 'manage_accounts',
    'manager': 'supervisor_account',
    'cashier': 'point_of_sale',
    'staff': 'person'
  }
  return icons[role] || 'person'
}





const updateFiltersAndLoad = () => {
  userStore.setFilters({
    search: localFilters.search,
    role: localFilters.role,
    status: localFilters.status
  })
  loadUsers()
}

const loadUsers = async () => {
  try {
    await userStore.fetchUsers()
  } catch (error) {
    console.error('Error loading users:', error)
    $q.notify({
      type: 'negative',
      message: 'Failed to load users'
    })
  }
}

const loadUserStats = async () => {
  try {
    await userStore.fetchUserStats()
  } catch (error) {
    console.error('Error loading user stats:', error)
    $q.notify({
      type: 'negative',
      message: 'Failed to load user statistics'
    })
  }
}

const refreshData = () => {
  loadUsers()
  loadUserStats()
}

const onRequest = (props) => {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  userStore.setPagination({
    page,
    rowsPerPage,
    sortBy,
    descending
  })
  loadUsers()
}

const viewUser = (user) => {
  selectedUser.value = user
  showViewDialog.value = true
}

const openAddDialog = async () => {
  editMode.value = false
  closeDialog() // Reset form
  // Load form role options when opening dialog (10 initial data)
  formRoleOptions.value = await loadFormRoleOptions('', 10)
  showAddDialog.value = true
}

const editUser = async (user) => {
  editMode.value = true
  userForm.id = user.id
  userForm.name = user.name
  userForm.email = user.email
  userForm.phone = user.phone
  userForm.status = user.email_verified_at ? 'Active' : 'Inactive'
  userForm.password = ''
  userForm.password_confirmation = ''
  
  // Load form role options when opening edit dialog (10 initial data)
  formRoleOptions.value = await loadFormRoleOptions('', 10)
  
  // Map user roles to role IDs for q-select
  if (user.roles && user.roles.length > 0) {
    userForm.roles = user.roles.map(userRole => {
      const option = formRoleOptions.value.find(option => option.name === userRole.name)
      return option ? option.value : null
    }).filter(id => id !== null)
  } else {
    userForm.roles = []
  }
  
  showAddDialog.value = true
}

const editUserFromView = () => {
  showViewDialog.value = false
  editUser(selectedUser.value)
}

const toggleUserStatus = (user) => {
  const newStatus = user.status === 'active' ? 'inactive' : 'active'
  const action = newStatus === 'active' ? 'activate' : 'deactivate'
  
  $q.dialog({
    title: 'Confirm Status Change',
    message: `Are you sure you want to ${action} ${user.name}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await userStore.toggleUserStatus(user.id, newStatus)
      $q.notify({
        type: 'positive',
        message: `User ${action}d successfully`
      })
    } catch (error) {
      console.error('Error toggling user status:', error)
      $q.notify({
        type: 'negative',
        message: `Failed to ${action} user`
      })
    }
  })
}

const resetPassword = (user) => {
  $q.dialog({
    title: 'Reset Password',
    message: `Are you sure you want to reset the password for ${user.name}? A new temporary password will be sent to their email.`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await userStore.resetPassword(user.id)
      $q.notify({
        type: 'positive',
        message: 'Password reset email sent successfully'
      })
    } catch (error) {
      console.error('Error resetting password:', error)
      $q.notify({
        type: 'negative',
        message: 'Failed to reset password'
      })
    }
  })
}

const deleteUser = (user) => {
  if (user.role === 'Super Admin') {
    $q.notify({
      type: 'warning',
      message: 'Cannot delete super admin user'
    })
    return
  }
  
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete ${user.name}? This action cannot be undone.`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await userStore.deleteUser(user.id)
      $q.notify({
        type: 'positive',
        message: 'User deleted successfully'
      })
    } catch (error) {
      console.error('Error deleting user:', error)
      $q.notify({
        type: 'negative',
        message: 'Failed to delete user'
      })
    }
  })
}

const saveUser = async () => {
  // Validation - check fields that exist in userForm
  if (!userForm.name || !userForm.email) {
    $q.notify({
      type: 'warning',
      message: 'Please fill in all required fields (name and email)'
    })
    return
  }
  
  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(userForm.email)) {
    $q.notify({
      type: 'warning',
      message: 'Please enter a valid email address'
    })
    return
  }
  
  if (!userForm.roles || userForm.roles.length === 0) {
    $q.notify({
      type: 'warning',
      message: 'Please select at least one role'
    })
    return
  }
  
  if (!editMode.value && !userForm.password) {
    $q.notify({
      type: 'warning',
      message: 'Password is required for new users'
    })
    return
  }
  
  if (userForm.password && userForm.password !== userForm.password_confirmation) {
    $q.notify({
      type: 'warning',
      message: 'Passwords do not match'
    })
    return
  }
  
  // Validate password length for new users
  if (!editMode.value && userForm.password && userForm.password.length < 8) {
    $q.notify({
      type: 'warning',
      message: 'Password must be at least 8 characters long'
    })
    return
  }
  
  try {
    // Transform role IDs to role names for backend
    console.log('userForm.roles:', userForm.roles)
    console.log('formRoleOptions.value:', formRoleOptions.value)
    
    const roleNames = userForm.roles.map(roleId => {
      const role = formRoleOptions.value.find(option => option.value === roleId)
      console.log(`Mapping roleId ${roleId} to role:`, role)
      return role ? role.name : null
    }).filter(name => name !== null)
    
    console.log('Final roleNames:', roleNames)
    
    // Prepare data according to backend API requirements
    const userData = {
      name: userForm.name,
      email: userForm.email,
      phone: userForm.phone || '',
      roles: roleNames // Backend expects array of role names
    }
    
    // Add password fields only if password is provided
    if (userForm.password) {
      userData.password = userForm.password
      userData.password_confirmation = userForm.password_confirmation
    }

    if (editMode.value) {
      await userStore.updateUser(userForm.id, userData)
    } else {
      await userStore.createUser(userData)
    }
    $q.notify({
      type: 'positive',
      message: `User ${editMode.value ? 'updated' : 'created'} successfully`
    })

    closeDialog()
  } catch (error) {
    console.error('Error saving user:', error)
    
    // Handle validation errors from backend
    if (error.response && error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat()
      $q.notify({
        type: 'negative',
        message: errorMessages.join(', ')
      })
    } else {
      $q.notify({
        type: 'negative',
        message: `Failed to ${editMode.value ? 'update' : 'create'} user`
      })
    }
  }
}

const closeDialog = () => {
  showAddDialog.value = false
  editMode.value = false
  userForm.id = null
  userForm.name = ''
  userForm.email = ''
  userForm.phone = ''
  userForm.roles = []
  userForm.status = 'Active'
  userForm.password = ''
  userForm.password_confirmation = ''
}

// Load role options for filter dropdown
const loadRoleOptions = async (search = '', limit = 10) => {
  try {
    await userStore.fetchAvailableRoles()
    const roles = userStore.availableRoles.filter(role => 
      !search || role.label.toLowerCase().includes(search.toLowerCase())
    ).slice(0, limit)
    return roles.map(role => ({
      label: role.label,
      value: role.value
    }))
  } catch (error) {
    console.error('Error loading role options:', error)
    return []
  }
}

// Filter roles for dropdown search
const filterRoles = async (val, update, abort) => {
  try {
    const roles = await loadRoleOptions(val, 10)
    update(() => {
      roleOptions.value = roles
    })
  } catch (error) {
    console.error('Error filtering roles:', error)
    abort()
  }
}

// Set role model for input
const setRoleModel = () => {
  // This function is called when user types in the input
  // We don't need to do anything special here as the filtering is handled by filterRoles
}

// Load role options for form dropdown
const loadFormRoleOptions = async (search = '', limit = 10) => {
  try {
    formRoleLoading.value = true
    await userStore.fetchAvailableRoles()
    const roles = userStore.availableRoles.filter(role => 
      !search || role.label.toLowerCase().includes(search.toLowerCase())
    ).slice(0, limit)
    return roles.map(role => ({
      label: role.label,
      value: role.value,
      name: role.name // Include name field for role mapping
    }))
  } catch (error) {
    console.error('Error loading form role options:', error)
    return []
  } finally {
    formRoleLoading.value = false
  }
}

// Filter roles for form dropdown search
const filterFormRoles = async (val, update, abort) => {
  try {
    const roles = await loadFormRoleOptions(val, 10)
    update(() => {
      formRoleOptions.value = roles
    })
  } catch (error) {
    console.error('Error filtering form roles:', error)
    abort()
  }
}

// Set form role model for input
const setFormRoleModel = () => {
  // This function is called when user types in the input
  // We don't need to do anything special here as the filtering is handled by filterFormRoles
}

// Refresh form role options manually
const refreshFormRoleOptions = async () => {
  try {
    formRoleLoading.value = true
    formRoleOptions.value = await loadFormRoleOptions('', 10)
    $q.notify({
      type: 'positive',
      message: 'Daftar role berhasil diperbarui',
      timeout: 1500
    })
  } catch (error) {
    console.error('Error refreshing form role options:', error)
    $q.notify({
      type: 'negative',
      message: 'Gagal memperbarui daftar role',
      timeout: 2000
    })
  } finally {
    formRoleLoading.value = false
  }
}

// Load roles for user form (all roles)
const loadRoles = async () => {
  try {
    await userStore.fetchAvailableRoles()
  } catch (error) {
    console.error('Error loading roles:', error)
  }
}

// Initialize local filters on mount
onMounted(async () => {
  localFilters.search = ''
  localFilters.role = null
  localFilters.status = null
  
  // Load initial role options for filter (10 items)
  roleOptions.value = await loadRoleOptions('', 10)
  
  // Don't load form role options on mount - only when dialog opens
  
  await loadRoles()
  await loadUsers()
  await loadUserStats()
})
</script>
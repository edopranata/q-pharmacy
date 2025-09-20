<template>
  <q-page class="q-pa-md">
    <!-- Header -->
    <div class="row items-center justify-between q-mb-lg">
      <div>
        <h4 class="text-h4 q-my-none">Daftar Permission</h4>
        <p class="text-grey-6 q-mb-none">
          Tampilan lengkap seluruh permission yang tersedia dalam sistem
        </p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="totalPermissions"
          label="Total Permission"
          icon="security"
          variant="primary"
          :loading="loading"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="userPermissionsCount"
          label="Permission Saya"
          icon="verified_user"
          variant="success"
          :loading="loading"
        />
      </div>
      
      <div class="col-12 col-sm-6 col-md-4">
        <StatsCard
          :value="totalCategories"
          label="Kategori"
          icon="category"
          variant="info"
          :loading="loading"
        />
      </div>
    </div>

    <!-- Search and Filter -->
    <q-card class="q-mb-lg">
      <q-card-section>
        <div class="row q-col-gutter-md items-center">
          <div class="col-12 col-md-4">
            <q-input
              v-model="searchQuery"
              placeholder="Cari permission..."
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
              v-model="selectedCategory"
              :options="categoryOptions"
              placeholder="Filter kategori"
              outlined
              dense
              clearable
              emit-value
              map-options
            />
          </div>
          
          <div class="col-12 col-md-3">
            <q-select
              v-model="permissionFilter"
              :options="permissionFilterOptions"
              placeholder="Filter permission"
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
              outline
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Legend -->
    <q-card class="q-mb-lg">
      <q-card-section>
        <div class="text-subtitle2 q-mb-sm">Keterangan:</div>
        <div class="row q-col-gutter-md">
          <div class="flex items-center">
            <q-icon name="check_circle" color="green" size="sm" class="q-mr-xs" />
            <span class="text-caption">Permission yang Anda miliki</span>
          </div>
          <div class="flex items-center">
            <q-icon name="radio_button_unchecked" color="grey-5" size="sm" class="q-mr-xs" />
            <span class="text-caption">Permission yang tidak Anda miliki</span>
          </div>
          <div class="flex items-center">
            <q-icon name="security" color="blue" size="sm" class="q-mr-xs" />
            <span class="text-caption">Permission sistem</span>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Loading State -->
    <div v-if="loading" class="text-center q-pa-xl">
      <q-spinner-dots size="50px" color="primary" />
      <div class="q-mt-md">Memuat data permission...</div>
    </div>

    <!-- Permission Categories -->
    <div v-else-if="filteredPermissionsByCategory && Object.keys(filteredPermissionsByCategory).length > 0">
      <div
        v-for="(permissions, category) in filteredPermissionsByCategory"
        :key="category"
        class="q-mb-lg"
      >
        <q-card>
          <!-- Category Header -->
          <q-card-section class="bg-grey-2 text-dark" :class="$q.dark.isActive ? 'bg-grey-9 text-white' : 'bg-grey-2 text-dark'">
            <div class="row items-center justify-between">
              <div class="flex items-center">
                <q-icon 
                  :name="getCategoryIcon(category)" 
                  :color="getCategoryColor(category)" 
                  size="md" 
                  class="q-mr-md" 
                />
                <div>
                  <div class="text-h6 text-weight-medium">{{ category }}</div>
                  <div class="text-caption" :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-6'">
                    {{ permissions.length }} permission{{ permissions.length > 1 ? 's' : '' }} 
                    • {{ getUserPermissionsInCategory(permissions).length }} yang Anda miliki
                  </div>
                </div>
              </div>
              
              <!-- Category Stats -->
              <div class="flex items-center q-gutter-sm">
                <q-chip 
                  size="sm" 
                  :color="getCategoryColor(category)" 
                  :text-color="$q.dark.isActive ? 'white' : 'white'"
                  icon="security"
                  :outline="$q.dark.isActive"
                >
                  {{ permissions.length }}
                </q-chip>
                <q-chip 
                  size="sm" 
                  color="positive" 
                  :text-color="$q.dark.isActive ? 'white' : 'white'"
                  icon="check_circle"
                  :outline="$q.dark.isActive"
                >
                  {{ getUserPermissionsInCategory(permissions).length }}
                </q-chip>
              </div>
            </div>
          </q-card-section>

          <!-- Permissions List -->
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div
                v-for="permission in permissions"
                :key="permission.id"
                class="col-12 col-sm-6 col-md-4 col-lg-3"
              >
                <q-card 
                  :class="[
                    'permission-card interactive-hover transition-all',
                    hasUserPermission(permission) 
                      ? ($q.dark.isActive ? 'bg-green-10 border-green text-white' : 'bg-green-1 border-green text-dark')
                      : ($q.dark.isActive ? 'bg-grey-9 border-grey text-white' : 'bg-grey-1 border-grey text-dark')
                  ]"
                  @click="showPermissionDetails(permission)"
                >
                  <q-card-section class="q-pa-md">
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <!-- Permission Status Icon -->
                        <div class="flex items-center q-mb-sm">
                          <q-icon 
                            :name="hasUserPermission(permission) ? 'check_circle' : 'radio_button_unchecked'"
                            :color="hasUserPermission(permission) ? 'green' : 'grey-5'"
                            size="sm"
                            class="q-mr-xs"
                          />
                          <q-icon 
                            name="security" 
                            color="blue" 
                            size="xs" 
                          />
                        </div>
                        
                        <!-- Permission Name -->
                        <div class="text-body2 text-weight-medium q-mb-xs">
                          {{ formatPermissionName(permission.name) }}
                        </div>
                        
                        <!-- Permission Description -->
                        <div 
                          v-if="permission.description" 
                          class="text-caption q-mb-sm"
                          :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-6'"
                        >
                          {{ permission.description }}
                        </div>
                        
                        <!-- Permission Name (Technical) -->
                        <div class="text-caption font-mono" :class="$q.dark.isActive ? 'text-grey-5' : 'text-grey-5'">
                          {{ permission.name }}
                        </div>
                      </div>
                      
                      <!-- Action Button -->
                      <q-btn
                        size="sm"
                        round
                        flat
                        icon="info"
                        :color="$q.dark.isActive ? 'grey-4' : 'grey-6'"
                        @click.stop="showPermissionDetails(permission)"
                      >
                        <q-tooltip>Detail Permission</q-tooltip>
                      </q-btn>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center q-pa-xl">
      <q-icon name="security" size="80px" :color="$q.dark.isActive ? 'grey-6' : 'grey-4'" />
      <div class="text-h6 q-mt-md" :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-6'">
        {{ searchQuery || selectedCategory ? 'Tidak ada permission yang sesuai filter' : 'Tidak ada permission tersedia' }}
      </div>
      <div class="text-body2 q-mt-sm" :class="$q.dark.isActive ? 'text-grey-5' : 'text-grey-5'">
        {{ searchQuery || selectedCategory ? 'Coba ubah kriteria pencarian atau filter' : 'Pastikan backend sudah mengembalikan data permission' }}
      </div>
      <q-btn
        v-if="searchQuery || selectedCategory"
        color="primary"
        label="Reset Filter"
        @click="resetFilters"
        class="q-mt-md"
        outline
      />
    </div>

    <!-- Permission Detail Dialog -->
    <q-dialog v-model="showDetailDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Detail Permission</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedPermission">
          <div class="q-gutter-md">
            <!-- Status -->
            <div class="flex items-center">
              <q-icon 
                :name="hasUserPermission(selectedPermission) ? 'check_circle' : 'radio_button_unchecked'"
                :color="hasUserPermission(selectedPermission) ? 'green' : 'grey-5'"
                size="md"
                class="q-mr-md"
              />
              <div>
                <div class="text-weight-medium">
                  {{ hasUserPermission(selectedPermission) ? 'Anda memiliki permission ini' : 'Anda tidak memiliki permission ini' }}
                </div>
                <div class="text-caption text-grey-6">Status akses permission</div>
              </div>
            </div>

            <q-separator />

            <!-- Permission Info -->
            <div>
              <div class="text-subtitle2 q-mb-sm">Informasi Permission</div>
              <q-list dense>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Nama Teknis</q-item-label>
                    <q-item-label class="font-mono">{{ selectedPermission.name }}</q-item-label>
                  </q-item-section>
                </q-item>
                
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Nama Tampilan</q-item-label>
                    <q-item-label>{{ formatPermissionName(selectedPermission.name) }}</q-item-label>
                  </q-item-section>
                </q-item>
                
                <q-item v-if="selectedPermission.description">
                  <q-item-section>
                    <q-item-label caption>Deskripsi</q-item-label>
                    <q-item-label>{{ selectedPermission.description }}</q-item-label>
                  </q-item-section>
                </q-item>
                
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Kategori</q-item-label>
                    <q-item-label>{{ selectedPermission.category || 'Uncategorized' }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Tutup" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoleStore } from 'src/stores/role'
import { useAuthStore } from 'src/stores/auth'
import StatsCard from 'src/components/common/StatsCard.vue'

// Stores
const roleStore = useRoleStore()
const authStore = useAuthStore()

// Reactive data
const searchQuery = ref('')
const selectedCategory = ref(null)
const permissionFilter = ref(null)
const showDetailDialog = ref(false)
const selectedPermission = ref(null)

// Computed properties
const loading = computed(() => roleStore.loading)
const availablePermissions = computed(() => roleStore.availablePermissions)
const userPermissions = computed(() => authStore.userPermissions)

// Group permissions by category
const permissionsByCategory = computed(() => {
  const permissions = availablePermissions.value
  if (!permissions || permissions.length === 0) {
    return {}
  }
  
  const grouped = {}
  permissions.forEach(permission => {
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

// Filtered permissions based on search and filters
const filteredPermissionsByCategory = computed(() => {
  const categorizedPermissions = permissionsByCategory.value
  let filtered = { ...categorizedPermissions }
  
  // Filter by category
  const selectedCat = selectedCategory.value
  if (selectedCat) {
    filtered = { [selectedCat]: filtered[selectedCat] || [] }
  }
  
  // Filter by search query
  const query = searchQuery.value
  if (query) {
    const searchTerm = query.toLowerCase()
    const newFiltered = {}
    
    Object.keys(filtered).forEach(category => {
      const matchingPermissions = filtered[category].filter(permission => 
        permission.name.toLowerCase().includes(searchTerm) ||
        (permission.description && permission.description.toLowerCase().includes(searchTerm)) ||
        formatPermissionName(permission.name).toLowerCase().includes(searchTerm)
      )
      
      if (matchingPermissions.length > 0) {
        newFiltered[category] = matchingPermissions
      }
    })
    
    filtered = newFiltered
  }
  
  // Filter by permission status
  const permFilter = permissionFilter.value
  if (permFilter) {
    const newFiltered = {}
    
    Object.keys(filtered).forEach(category => {
      let matchingPermissions = []
      
      if (permFilter === 'owned') {
        matchingPermissions = filtered[category].filter(permission => hasUserPermission(permission))
      } else if (permFilter === 'not_owned') {
        matchingPermissions = filtered[category].filter(permission => !hasUserPermission(permission))
      }
      
      if (matchingPermissions.length > 0) {
        newFiltered[category] = matchingPermissions
      }
    })
    
    filtered = newFiltered
  }
  
  return filtered
})

// Stats
const totalPermissions = computed(() => {
  const permissions = availablePermissions.value
  return permissions?.length || 0
})

const userPermissionsCount = computed(() => {
  const userPerms = userPermissions.value
  const availablePerms = availablePermissions.value
  
  if (!userPerms || !availablePerms) return 0
  
  return availablePerms.filter(permission => hasUserPermission(permission)).length
})

const totalCategories = computed(() => {
  const categories = permissionsByCategory.value
  return Object.keys(categories).length
})

// Filter options
const categoryOptions = computed(() => {
  const categorizedPerms = permissionsByCategory.value
  const categories = Object.keys(categorizedPerms).map(category => ({
    label: `${category} (${categorizedPerms[category].length})`,
    value: category
  }))
  
  return [
    { label: 'Semua Kategori', value: null },
    ...categories
  ]
})

const permissionFilterOptions = [
  { label: 'Semua Permission', value: null },
  { label: 'Permission yang Saya Miliki', value: 'owned' },
  { label: 'Permission yang Tidak Saya Miliki', value: 'not_owned' }
]

// Methods
const hasUserPermission = (permission) => {
  if (!userPermissions.value) return false
  
  // Check if user has this permission (by name)
  if (Array.isArray(userPermissions.value)) {
    return userPermissions.value.some(userPerm => 
      (typeof userPerm === 'string' && userPerm === permission.name) ||
      (typeof userPerm === 'object' && userPerm.name === permission.name)
    )
  }
  
  return authStore.hasPermission(permission.name)
}

const getUserPermissionsInCategory = (permissions) => {
  return permissions.filter(permission => hasUserPermission(permission))
}

const getCategoryIcon = (category) => {
  const iconMap = {
    'Management': 'admin_panel_settings',
    'Users': 'people',
    'Roles': 'security',
    'Inventory': 'inventory',
    'Products': 'shopping_bag',
    'Sales': 'point_of_sale',
    'Reports': 'assessment',
    'Master': 'settings',
    'POS': 'store',
    'Uncategorized': 'help_outline'
  }
  
  return iconMap[category] || 'security'
}

const getCategoryColor = (category) => {
  const colorMap = {
    'Management': 'blue',
    'Users': 'green',
    'Roles': 'purple',
    'Inventory': 'orange',
    'Products': 'teal',
    'Sales': 'pink',
    'Reports': 'indigo',
    'Master': 'brown',
    'POS': 'red',
    'Uncategorized': 'grey'
  }
  
  return colorMap[category] || 'blue'
}

const formatPermissionName = (permissionName) => {
  if (!permissionName) return ''
  
  const parts = permissionName.split('.')
  if (parts.length >= 3) {
    const module = parts[parts.length - 2]
    const action = parts[parts.length - 1]
    
    const formattedModule = module.charAt(0).toUpperCase() + module.slice(1)
    const formattedAction = action.charAt(0).toUpperCase() + action.slice(1)
    
    return `${formattedModule} ${formattedAction}`
  }
  
  return permissionName
}

const showPermissionDetails = (permission) => {
  selectedPermission.value = permission
  showDetailDialog.value = true
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = null
  permissionFilter.value = null
}

const refreshData = async () => {
  await roleStore.fetchAvailablePermissions()
}

// Lifecycle
onMounted(async () => {
  await refreshData()
})
</script>
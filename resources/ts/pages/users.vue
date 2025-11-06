<script setup lang="ts">
import { userService } from '@/services/userService'
import type { CreateUserPayload } from '@/services/userService'
import AddNewUserDrawer from './AddNewUserDrawer.vue'
import type { UserProperties } from './users'

// 👉 Store
const searchQuery = ref('')
const selectedRole = ref()
const selectedStatus = ref()

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()
const selectedRows = ref<number[]>([])

// Update data table options
const updateOptions = (options: any) => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
}

// Headers
const headers = [
  { title: 'User', key: 'user' },
  { title: 'Role', key: 'role' },
  { title: 'Phone', key: 'phone' },
  { title: 'Mobile', key: 'mobile' },
  { title: 'Status', key: 'status' },
  { title: 'Actions', key: 'actions', sortable: false },
]

// 👉 Fetching users
const users = ref<UserProperties[]>([])
const totalUsers = ref(0)
const loading = ref(false)

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await userService.getUsers(page.value, itemsPerPage.value)
    users.value = response.data || []
    totalUsers.value = response.meta?.total || (response.data?.length ?? 0)
  } catch (error) {
    console.error('Error fetching users:', error)
    users.value = []
    totalUsers.value = 0
  } finally {
    loading.value = false
  }
}

// Initial fetch
await fetchUsers()

// Watch for changes
watch([page, itemsPerPage], () => {
  fetchUsers()
})

// 👉 search filters
const roles = [
  { title: 'Admin', value: 'admin' },
  { title: 'User', value: 'user' },
]

const status = [
  { title: 'Pending', value: 'pending' },
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' },
]

const resolveUserRoleVariant = (role?: string | null) => {
  if (!role) return { color: 'primary', icon: 'tabler-user' }

  const roleLowerCase = role.toLowerCase()

  if (roleLowerCase === 'admin')
    return { color: 'primary', icon: 'tabler-crown' }
  if (roleLowerCase === 'user')
    return { color: 'success', icon: 'tabler-user' }

  return { color: 'primary', icon: 'tabler-user' }
}

const resolveUserStatusVariant = (stat: string) => {
  const statLowerCase = stat.toLowerCase()
  if (statLowerCase === 'pending')
    return 'warning'
  if (statLowerCase === 'active')
    return 'success'
  if (statLowerCase === 'inactive')
    return 'secondary'

  return 'primary'
}

const isAddNewUserDrawerVisible = ref(false)

// 👉 Add new user
const addNewUser = async (userData: CreateUserPayload) => {
  try {
    await userService.createUser(userData)
    // Refetch Users
    await fetchUsers()
  } catch (error) {
    console.error('Error creating user:', error)
  }
}

// 👉 Delete user
const deleteUser = async (id: number) => {
  try {
    await userService.deleteUser(id)

    // Delete from selectedRows
    const index = selectedRows.value.findIndex(row => row === id)
    if (index !== -1)
      selectedRows.value.splice(index, 1)

    // refetch Users
    await fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
  }
}

const widgetData = ref([
  { title: 'Total Users', value: '0', change: 0, desc: 'All registered users', icon: 'tabler-users', iconColor: 'primary' },
  { title: 'Admin Users', value: '0', change: 0, desc: 'Administrator accounts', icon: 'tabler-user-plus', iconColor: 'error' },
  { title: 'Active Users', value: '0', change: 0, desc: 'Currently active', icon: 'tabler-user-check', iconColor: 'success' },
  { title: 'Pending Users', value: '0', change: 0, desc: 'Awaiting approval', icon: 'tabler-user-search', iconColor: 'warning' },
])

// Update widget data based on users
watchEffect(() => {
  widgetData.value[0].value = totalUsers.value.toString()
  const activeCount = users.value.filter(u => u.status === 'active').length
  const pendingCount = users.value.filter(u => u.status === 'pending').length
  const adminCount = users.value.filter(u => u.role === 'admin').length

  widgetData.value[1].value = adminCount.toString()
  widgetData.value[2].value = activeCount.toString()
  widgetData.value[3].value = pendingCount.toString()
})
</script>

<template>
  <section>
    <!-- 👉 Widgets -->
    <div class="d-flex mb-6">
      <VRow>
        <template
          v-for="(data, id) in widgetData"
          :key="id"
        >
          <VCol
            cols="12"
            md="3"
            sm="6"
          >
            <VCard>
              <VCardText>
                <div class="d-flex justify-space-between">
                  <div class="d-flex flex-column gap-y-1">
                    <div class="text-body-1 text-high-emphasis">
                      {{ data.title }}
                    </div>
                    <div class="d-flex gap-x-2 align-center">
                      <h4 class="text-h4">
                        {{ data.value }}
                      </h4>
                      <div
                        class="text-base"
                        :class="data.change > 0 ? 'text-success' : 'text-error'"
                      >
                        ({{ prefixWithPlus(data.change) }}%)
                      </div>
                    </div>
                    <div class="text-sm">
                      {{ data.desc }}
                    </div>
                  </div>
                  <VAvatar
                    :color="data.iconColor"
                    variant="tonal"
                    rounded
                    size="42"
                  >
                    <VIcon
                      :icon="data.icon"
                      size="26"
                    />
                  </VAvatar>
                </div>
              </VCardText>
            </VCard>
          </VCol>
        </template>
      </VRow>
    </div>

    <VCard class="mb-6">
      <VCardItem class="pb-4">
        <VCardTitle>Filters</VCardTitle>
      </VCardItem>

      <VCardText>
        <VRow>
          <!-- 👉 Select Role -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppSelect
              v-model="selectedRole"
              placeholder="Select Role"
              :items="roles"
              clearable
              clear-icon="tabler-x"
            />
          </VCol>
          <!-- 👉 Select Status -->
          <VCol
            cols="12"
            sm="6"
          >
            <AppSelect
              v-model="selectedStatus"
              placeholder="Select Status"
              :items="status"
              clearable
              clear-icon="tabler-x"
            />
          </VCol>
        </VRow>
      </VCardText>

      <VDivider />

      <VCardText class="d-flex flex-wrap gap-4">
        <div class="me-3 d-flex gap-3">
          <AppSelect
            :model-value="itemsPerPage"
            :items="[
              { value: 10, title: '10' },
              { value: 25, title: '25' },
              { value: 50, title: '50' },
              { value: 100, title: '100' },
              { value: -1, title: 'All' },
            ]"
            style="inline-size: 6.25rem;"
            @update:model-value="itemsPerPage = parseInt($event, 10)"
          />
        </div>
        <VSpacer />

        <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div style="inline-size: 15.625rem;">
            <AppTextField
              v-model="searchQuery"
              placeholder="Search User"
            />
          </div>

          <!-- 👉 Export button -->
          <VBtn
            variant="tonal"
            color="secondary"
            prepend-icon="tabler-upload"
          >
            Export
          </VBtn>

          <!-- 👉 Add user button -->
          <VBtn
            prepend-icon="tabler-plus"
            @click="isAddNewUserDrawerVisible = true"
          >
            Add New User
          </VBtn>
        </div>
      </VCardText>

      <VDivider />

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:model-value="selectedRows"
        v-model:page="page"
        :items="users"
        item-value="id"
        :items-length="totalUsers"
        :headers="headers"
        class="text-no-wrap"
        show-select
        @update:options="updateOptions"
      >
        <!-- User -->
        <template #item.user="{ item }">
          <div class="d-flex align-center gap-x-4">
            <VAvatar
              size="34"
              :variant="!item.avatar ? 'tonal' : undefined"
              :color="!item.avatar ? resolveUserRoleVariant(item.role).color : undefined"
            >
              <VImg
                v-if="item.avatar"
                :src="item.avatar"
              />
              <span v-else>{{ avatarText(item.name) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <h6 class="text-base font-weight-medium">
                {{ item.name }}
              </h6>
              <div class="text-sm">
                {{ item.email }}
              </div>
            </div>
          </div>
        </template>

        <!-- 👉 Role -->
        <template #item.role="{ item }">
          <div class="d-flex align-center gap-x-2">
            <VIcon
              :size="22"
              :icon="resolveUserRoleVariant(item.role).icon"
              :color="resolveUserRoleVariant(item.role).color"
            />

            <div class="text-capitalize text-high-emphasis text-body-1">
              {{ item.role || 'N/A' }}
            </div>
          </div>
        </template>

        <!-- Phone -->
        <template #item.phone="{ item }">
          <div class="text-body-1 text-high-emphasis">
            {{ item.phone || 'N/A' }}
          </div>
        </template>

        <!-- Mobile -->
        <template #item.mobile="{ item }">
          <div class="text-body-1 text-high-emphasis">
            {{ item.mobile || 'N/A' }}
          </div>
        </template>

        <!-- Status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveUserStatusVariant(item.status)"
            size="small"
            label
            class="text-capitalize"
          >
            {{ item.status }}
          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="deleteUser(item.id)">
            <VIcon icon="tabler-trash" />
          </IconBtn>

          <IconBtn>
            <VIcon icon="tabler-eye" />
          </IconBtn>

          <VBtn
            icon
            variant="text"
            color="medium-emphasis"
          >
            <VIcon icon="tabler-dots-vertical" />
            <VMenu activator="parent">
              <VList>
                <VListItem :to="{ name: 'apps-user-view-id', params: { id: item.id } }">
                  <template #prepend>
                    <VIcon icon="tabler-eye" />
                  </template>

                  <VListItemTitle>View</VListItemTitle>
                </VListItem>

                <VListItem link>
                  <template #prepend>
                    <VIcon icon="tabler-pencil" />
                  </template>
                  <VListItemTitle>Edit</VListItemTitle>
                </VListItem>

                <VListItem @click="deleteUser(item.id)">
                  <template #prepend>
                    <VIcon icon="tabler-trash" />
                  </template>
                  <VListItemTitle>Delete</VListItemTitle>
                </VListItem>
              </VList>
            </VMenu>
          </VBtn>
        </template>

        <!-- pagination -->
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="totalUsers"
          />
        </template>
      </VDataTableServer>
      <!-- SECTION -->
    </VCard>
    <!-- 👉 Add New User -->
    <AddNewUserDrawer
      v-model:is-drawer-open="isAddNewUserDrawerVisible"
      @user-data="addNewUser"
    />
  </section>
</template>

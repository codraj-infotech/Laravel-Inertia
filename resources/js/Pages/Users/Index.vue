<template>
  <AuthenticatedLayout active-key="users">
    <div class="page-header">
      <div class="page-header-content">
        <div>
          <h1 class="page-title">Users Management</h1>
          <a-breadcrumb>
            <a-breadcrumb-item>
              <HomeOutlined />
            </a-breadcrumb-item>
            <a-breadcrumb-item>Users</a-breadcrumb-item>
          </a-breadcrumb>
        </div>
        <a-button type="primary" size="large" @click="showCreateModal">
          <template #icon>
            <PlusOutlined />
          </template>
          Add User
        </a-button>
      </div>
    </div>

    <div class="container">
      <a-card :bordered="false" class="users-card">
        <!-- Search and Filters -->
        <div class="table-toolbar">
          <a-input-search
            v-model:value="searchText"
            placeholder="Search by name or email..."
            style="width: 300px"
            allow-clear
          >
            <template #prefix>
              <SearchOutlined />
            </template>
          </a-input-search>
        </div>

        <!-- Users Table -->
        <a-table
          :columns="columns"
          :data-source="usersData.data"
          :pagination="paginationConfig"
          :loading="loading"
          @change="handleTableChange"
          row-key="id"
          class="users-table"
          :scroll="{ x: 1200 }"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'name'">
              <div style="display: flex; align-items: center; gap: 12px;">
                <a-avatar
                  v-if="record.profile_image"
                  :src="record.profile_image"
                  :size="40"
                />
                <a-avatar
                  v-else
                  :style="{ backgroundColor: getAvatarColor(record.name) }"
                  :size="40"
                >
                  {{ getUserInitials(record.name) }}
                </a-avatar>
                <div>
                  <div style="font-weight: 500;">{{ record.name }}</div>
                  <div style="font-size: 12px; color: #8c8c8c;">ID: {{ record.id }}</div>
                </div>
              </div>
            </template>

            <template v-if="column.key === 'contact'">
              <div>
                <div style="margin-bottom: 4px;">
                  <MailOutlined style="margin-right: 6px; color: #1890ff;" />
                  <span>{{ record.email }}</span>
                </div>
                <div v-if="record.phone" style="font-size: 12px; color: #8c8c8c;">
                  <PhoneOutlined style="margin-right: 6px;" />
                  <span>{{ record.phone }}</span>
                </div>
              </div>
            </template>

            <template v-if="column.key === 'gender'">
              <a-tag v-if="record.gender" :color="getGenderColor(record.gender)">
                {{ capitalizeFirst(record.gender) }}
              </a-tag>
              <span v-else style="color: #8c8c8c;">-</span>
            </template>

            <template v-if="column.key === 'date_of_birth'">
              <span v-if="record.date_of_birth">{{ formatDate(record.date_of_birth) }}</span>
              <span v-else style="color: #8c8c8c;">-</span>
            </template>

            <template v-if="column.key === 'created_at'">
              {{ formatDate(record.created_at) }}
            </template>

            <template v-if="column.key === 'actions'">
              <a-space>
                <a-button type="link" size="small" @click="showEditDrawer(record)">
                  <template #icon>
                    <EditOutlined />
                  </template>
                  Edit
                </a-button>
                <a-popconfirm
                  title="Are you sure you want to delete this user?"
                  ok-text="Yes"
                  cancel-text="No"
                  @confirm="deleteUser(record.id)"
                >
                  <a-button type="link" danger size="small">
                    <template #icon>
                      <DeleteOutlined />
                    </template>
                    Delete
                  </a-button>
                </a-popconfirm>
              </a-space>
            </template>
          </template>
        </a-table>
      </a-card>
    </div>

    <!-- Create User Modal -->
    <UserCreate
      v-model:visible="createModalVisible"
      @success="handleSuccess"
    />

    <!-- Edit User Drawer -->
    <UserEdit
      v-model:visible="editDrawerVisible"
      :user="selectedUser"
      @success="handleSuccess"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserCreate from './UserCreate.vue';
import UserEdit from './UserEdit.vue';
import { userApi } from '@/services/api';
import {
  HomeOutlined,
  PlusOutlined,
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  MailOutlined,
  PhoneOutlined,
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const page = usePage();

// Local state for users data
const usersData = ref({
  data: [],
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
});

const getUserInitials = (name) => {
  if (!name) return 'U';
  const names = name.split(' ');
  if (names.length === 1) return names[0].charAt(0).toUpperCase();
  return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = ['#f56a00', '#7265e6', '#ffbf00', '#00a2ae', '#667eea', '#764ba2'];
  const index = name.length % colors.length;
  return colors[index];
};

const getGenderColor = (gender) => {
  const colors = {
    male: 'blue',
    female: 'pink',
    other: 'purple',
  };
  return colors[gender] || 'default';
};

const capitalizeFirst = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
};

// Table columns
const columns = [
  {
    title: 'User',
    dataIndex: 'name',
    key: 'name',
    sorter: true,
    width: 250,
  },
  {
    title: 'Contact',
    key: 'contact',
    width: 280,
  },
  {
    title: 'Gender',
    dataIndex: 'gender',
    key: 'gender',
    width: 100,
  },
  {
    title: 'Date of Birth',
    dataIndex: 'date_of_birth',
    key: 'date_of_birth',
    width: 150,
  },
  {
    title: 'Created At',
    dataIndex: 'created_at',
    key: 'created_at',
    sorter: true,
    width: 150,
  },
  {
    title: 'Actions',
    key: 'actions',
    width: 180,
    fixed: 'right',
  },
];

// Search with debounce
const searchText = ref('');
const loading = ref(false);
const sortField = ref('created_at');
const sortOrder = ref('desc');
let searchTimeout = null;

// Watch for search text changes and debounce
watch(searchText, (newValue) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchUsers();
  }, 500); // 500ms debounce
});

// Fetch users from API
const fetchUsers = async () => {
  loading.value = true;
  try {
    const params = {
      page: usersData.value.current_page,
      limit: usersData.value.per_page,
    };

    // Add search filter
    if (searchText.value) {
      params.filter = `name:${searchText.value},email:${searchText.value},phone:${searchText.value}`;
    }

    // Add sorting
    if (sortField.value) {
      params.sort = sortOrder.value === 'asc' ? sortField.value : `-${sortField.value}`;
    }

    const response = await userApi.getAll(params);

    // Update users data from API response
    usersData.value = {
      data: response.data.data || [],
      current_page: response.data.meta?.current_page || 1,
      per_page: response.data.meta?.per_page || 10,
      total: response.data.meta?.total || 0,
      last_page: response.data.meta?.last_page || 1,
    };
  } catch (error) {
    console.error('Error fetching users:', error);
    message.error('Failed to fetch users');
  } finally {
    loading.value = false;
  }
};

// Pagination
const paginationConfig = computed(() => ({
  current: usersData.value.current_page,
  pageSize: usersData.value.per_page,
  total: usersData.value.total,
  showSizeChanger: true,
  showQuickJumper: true,
  showTotal: (total) => `Total ${total} users`,
  pageSizeOptions: ['10', '20', '50', '100'],
}));

const handleTableChange = (pagination, filters, sorter) => {
  // Update current page and page size
  usersData.value.current_page = pagination.current;
  usersData.value.per_page = pagination.pageSize;

  // Handle sorting
  if (sorter && sorter.column && sorter.order) {
    sortField.value = sorter.field;
    sortOrder.value = sorter.order === 'ascend' ? 'asc' : 'desc';
  }

  // Fetch users with new parameters
  fetchUsers();
};

// Create Modal
const createModalVisible = ref(false);

const showCreateModal = () => {
  createModalVisible.value = true;
};

// Edit Drawer
const editDrawerVisible = ref(false);
const selectedUser = ref(null);

const showEditDrawer = (record) => {
  selectedUser.value = record;
  editDrawerVisible.value = true;
};

// Delete User
const deleteUser = async (id) => {
  try {
    await userApi.delete(id);
    message.success('User deleted successfully!');
    fetchUsers();
  } catch (error) {
    console.error('Error deleting user:', error);
    if (error.response?.data?.message) {
      message.error(error.response.data.message);
    } else {
      message.error('Failed to delete user');
    }
  }
};

// Success Handler
const handleSuccess = () => {
  fetchUsers();
};

// Format Date
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

// Load users on mount
onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.page-header {
  background: #fff;
  padding: 24px;
  margin-bottom: 24px;
  border-bottom: 1px solid #f0f0f0;
}

.page-header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: #1a1a1a;
}

.container {
  padding: 0 24px 24px 24px;
}

.users-card {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.table-toolbar {
  margin-bottom: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.users-table {
  margin-top: 16px;
}

@media (max-width: 768px) {
  .container {
    padding: 0 16px 16px 16px;
  }

  .page-header {
    padding: 16px;
  }

  .page-header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .page-title {
    font-size: 20px;
  }

  .table-toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .table-toolbar .ant-input-search {
    width: 100% !important;
  }
}
</style>

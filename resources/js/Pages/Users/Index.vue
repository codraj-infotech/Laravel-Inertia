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
          :data-source="users.data"
          :pagination="paginationConfig"
          :loading="loading"
          @change="handleTableChange"
          row-key="id"
          class="users-table"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'name'">
              <a-avatar :style="{ backgroundColor: getAvatarColor(record.name), marginRight: '8px' }">
                {{ getUserInitials(record.name) }}
              </a-avatar>
              <span>{{ record.name }}</span>
            </template>

            <template v-if="column.key === 'email'">
              <a-tag color="blue">{{ record.email }}</a-tag>
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
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserCreate from '@/Components/UserCreate.vue';
import UserEdit from '@/Components/UserEdit.vue';
import {
  HomeOutlined,
  PlusOutlined,
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const page = usePage();
const props = defineProps({
  users: Object,
  filters: Object,
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

// Table columns
const columns = [
  {
    title: 'Name',
    dataIndex: 'name',
    key: 'name',
    sorter: true,
  },
  {
    title: 'Email',
    dataIndex: 'email',
    key: 'email',
    sorter: true,
  },
  {
    title: 'Created At',
    dataIndex: 'created_at',
    key: 'created_at',
    sorter: true,
  },
  {
    title: 'Actions',
    key: 'actions',
    width: 200,
  },
];

// Search with debounce
const searchText = ref(props.filters?.search || '');
const loading = ref(false);
let searchTimeout = null;

// Watch for search text changes and debounce
watch(searchText, (newValue) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 500); // 500ms debounce
});

const performSearch = () => {
  router.get('/users', {
    search: searchText.value,
    page: 1,
    sort_field: props.filters?.sort_field,
    sort_order: props.filters?.sort_order,
  }, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false,
  });
};

// Pagination
const paginationConfig = computed(() => ({
  current: props.users.current_page,
  pageSize: props.users.per_page,
  total: props.users.total,
  showSizeChanger: true,
  showQuickJumper: true,
  showTotal: (total) => `Total ${total} users`,
  pageSizeOptions: ['10', '20', '50', '100'],
}));

const handleTableChange = (pagination, filters, sorter) => {
  let sortField = props.filters?.sort_field || 'created_at';
  let sortOrder = props.filters?.sort_order || 'desc';

  // Handle sorting
  if (sorter && sorter.column && sorter.order) {
    sortField = sorter.field;
    sortOrder = sorter.order === 'ascend' ? 'asc' : 'desc';
  }

  router.get('/users', {
    page: pagination.current,
    per_page: pagination.pageSize,
    search: searchText.value,
    sort_field: sortField,
    sort_order: sortOrder,
  }, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false,
  });
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
const deleteUser = (id) => {
  router.delete(`/users/${id}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      message.success('User deleted successfully!');
    },
    onError: (errors) => {
      Object.keys(errors).forEach(key => {
        message.error(errors[key]);
      });
    },
  });
};

// Success Handler
const handleSuccess = () => {
  // The page will automatically reload via Inertia
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

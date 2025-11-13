<template>
  <a-layout class="dashboard-layout">
    <!-- Left Sidebar -->
    <a-layout-sider
      v-model:collapsed="collapsed"
      :trigger="null"
      collapsible
      :width="250"
      class="sidebar"
      breakpoint="lg"
      @breakpoint="onBreakpoint"
    >
      <div class="logo">
        <AppstoreOutlined class="logo-icon" />
        <span v-if="!collapsed" class="logo-text">Laravel Inertia</span>
      </div>

      <a-menu
        v-model:selectedKeys="selectedKeys"
        theme="dark"
        mode="inline"
        class="sidebar-menu"
      >
        <a-menu-item key="dashboard" @click="navigateTo('/dashboard')">
          <template #icon>
            <DashboardOutlined />
          </template>
          <span>Dashboard</span>
        </a-menu-item>

        <a-menu-item key="users" @click="navigateTo('/users')">
          <template #icon>
            <UserOutlined />
          </template>
          <span>Users</span>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>

    <!-- Right Side Layout -->
    <a-layout :style="{ marginLeft: collapsed ? '80px' : '250px' }" class="main-layout">
      <!-- Header -->
      <a-layout-header class="header">
        <div class="header-left">
          <a-button
            type="text"
            class="trigger"
            @click="toggleCollapsed"
            :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
          >
            <MenuUnfoldOutlined v-if="collapsed" />
            <MenuFoldOutlined v-else />
          </a-button>
        </div>

        <div class="header-right">
          <a-space :size="16">
            <a-badge :count="5">
              <a-button type="text" shape="circle">
                <template #icon>
                  <BellOutlined />
                </template>
              </a-button>
            </a-badge>

            <a-dropdown>
              <a-button type="text" class="user-button">
                <a-avatar :size="32" style="background-color: #667eea">
                  {{ getUserInitials(user.name) }}
                </a-avatar>
                <span class="user-name">{{ user.name }}</span>
                <DownOutlined />
              </a-button>
              <template #overlay>
                <a-menu>
                  <a-menu-item key="profile">
                    <UserOutlined />
                    Profile
                  </a-menu-item>
                  <a-menu-item key="account">
                    <SettingOutlined />
                    Account Settings
                  </a-menu-item>
                  <a-menu-divider />
                  <a-menu-item key="logout" @click="logout">
                    <LogoutOutlined />
                    Logout
                  </a-menu-item>
                </a-menu>
              </template>
            </a-dropdown>
          </a-space>
        </div>
      </a-layout-header>

      <!-- Content -->
      <a-layout-content class="content">
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
                @search="handleSearch"
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
                    <a-button type="link" size="small" @click="showEditModal(record)">
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
      </a-layout-content>

      <!-- Footer -->
      <a-layout-footer class="footer">
        Laravel Inertia with Ant Design Vue - {{ new Date().getFullYear() }}
      </a-layout-footer>
    </a-layout>

    <!-- Create/Edit User Modal -->
    <a-modal
      v-model:open="modalVisible"
      :title="isEditing ? 'Edit User' : 'Create New User'"
      :confirm-loading="modalLoading"
      @ok="handleModalOk"
      @cancel="handleModalCancel"
      width="600px"
    >
      <a-form
        ref="formRef"
        :model="formState"
        :rules="formRules"
        layout="vertical"
      >
        <a-form-item label="Name" name="name">
          <a-input v-model:value="formState.name" placeholder="Enter user name">
            <template #prefix>
              <UserOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item label="Email" name="email">
          <a-input v-model:value="formState.email" placeholder="Enter email address">
            <template #prefix>
              <MailOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item :label="isEditing ? 'New Password (optional)' : 'Password'" name="password">
          <a-input-password v-model:value="formState.password" :placeholder="isEditing ? 'Leave blank to keep current password' : 'Enter password'">
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item label="Confirm Password" name="password_confirmation">
          <a-input-password v-model:value="formState.password_confirmation" placeholder="Confirm password">
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>
      </a-form>
    </a-modal>
  </a-layout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
  AppstoreOutlined,
  DashboardOutlined,
  UserOutlined,
  DownOutlined,
  LogoutOutlined,
  SettingOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  BellOutlined,
  HomeOutlined,
  PlusOutlined,
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  MailOutlined,
  LockOutlined,
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const page = usePage();
const props = defineProps({
  users: Object,
  filters: Object,
});

const user = computed(() => page.props.auth?.user || { name: 'User', email: 'user@example.com' });

// Sidebar state
const getInitialCollapsedState = () => {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('sidebar-collapsed');
    return stored ? JSON.parse(stored) : false;
  }
  return false;
};

const collapsed = ref(getInitialCollapsedState());
const selectedKeys = ref(['users']);

watch(collapsed, (newValue) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('sidebar-collapsed', JSON.stringify(newValue));
  }
});

const toggleCollapsed = () => {
  collapsed.value = !collapsed.value;
};

const onBreakpoint = (broken) => {
  collapsed.value = broken;
};

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

const logout = () => {
  router.post('/logout');
};

const navigateTo = (url) => {
  router.visit(url);
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

// Search and filters
const searchText = ref(props.filters?.search || '');
const loading = ref(false);

const handleSearch = () => {
  router.get('/users', {
    search: searchText.value,
    page: 1,
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
  router.get('/users', {
    page: pagination.current,
    per_page: pagination.pageSize,
    search: searchText.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false,
  });
};

// Modal and form
const modalVisible = ref(false);
const modalLoading = ref(false);
const isEditing = ref(false);
const formRef = ref();
const formState = reactive({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const formRules = computed(() => ({
  name: [
    { required: true, message: 'Please input user name!', trigger: 'blur' },
  ],
  email: [
    { required: true, message: 'Please input email!', trigger: 'blur' },
    { type: 'email', message: 'Please enter a valid email!', trigger: 'blur' },
  ],
  password: [
    { required: !isEditing.value, message: 'Please input password!', trigger: 'blur' },
    { min: 8, message: 'Password must be at least 8 characters!', trigger: 'blur' },
  ],
  password_confirmation: [
    { required: formState.password !== '', message: 'Please confirm password!', trigger: 'blur' },
    {
      validator: (rule, value) => {
        if (value !== formState.password) {
          return Promise.reject('Passwords do not match!');
        }
        return Promise.resolve();
      },
      trigger: 'blur',
    },
  ],
}));

const showCreateModal = () => {
  isEditing.value = false;
  formState.id = null;
  formState.name = '';
  formState.email = '';
  formState.password = '';
  formState.password_confirmation = '';
  modalVisible.value = true;
};

const showEditModal = (record) => {
  isEditing.value = true;
  formState.id = record.id;
  formState.name = record.name;
  formState.email = record.email;
  formState.password = '';
  formState.password_confirmation = '';
  modalVisible.value = true;
};

const handleModalOk = async () => {
  try {
    await formRef.value.validate();
    modalLoading.value = true;

    const url = isEditing.value ? `/users/${formState.id}` : '/users';
    const method = isEditing.value ? 'put' : 'post';

    router[method](url, formState, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        message.success(isEditing.value ? 'User updated successfully!' : 'User created successfully!');
        modalVisible.value = false;
        resetForm();
      },
      onError: (errors) => {
        Object.keys(errors).forEach(key => {
          message.error(errors[key]);
        });
      },
      onFinish: () => {
        modalLoading.value = false;
      },
    });
  } catch (error) {
    console.log('Validation failed:', error);
  }
};

const handleModalCancel = () => {
  modalVisible.value = false;
  resetForm();
};

const resetForm = () => {
  formState.id = null;
  formState.name = '';
  formState.email = '';
  formState.password = '';
  formState.password_confirmation = '';
  if (formRef.value) {
    formRef.value.clearValidate();
  }
};

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

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

onMounted(() => {
  collapsed.value = getInitialCollapsedState();
});
</script>

<style scoped>
/* Import Dashboard styles */
.dashboard-layout {
  min-height: 100vh;
}

.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  z-index: 1001;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.logo {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-size: 18px;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.logo-icon {
  font-size: 28px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.logo-text {
  white-space: nowrap;
  overflow: hidden;
  opacity: 1;
  transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-menu {
  margin-top: 8px;
}

:deep(.ant-layout-sider) {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

:deep(.ant-menu-item),
:deep(.ant-menu-submenu) {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  padding: 0 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-left {
  display: flex;
  align-items: center;
}

.trigger {
  font-size: 20px;
  padding: 8px 16px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.trigger:hover {
  color: #667eea;
  background: rgba(102, 126, 234, 0.1);
  transform: scale(1.05);
}

.trigger:active {
  transform: scale(0.95);
}

.header-right {
  display: flex;
  align-items: center;
}

.user-button {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 4px 12px;
  height: auto;
}

.user-name {
  font-weight: 500;
  margin-left: 4px;
}

.content {
  padding: 0;
  background: #f0f2f5;
  min-height: calc(100vh - 64px - 70px);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

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

.footer {
  text-align: center;
  background: #fff;
  border-top: 1px solid #f0f0f0;
  padding: 16px 50px;
}

.main-layout {
  transition: margin-left 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.ant-layout) {
  background: #f0f2f5;
}

:deep(.ant-layout-sider-children) {
  display: flex;
  flex-direction: column;
}

@media (max-width: 991px) {
  .user-name {
    display: none;
  }

  .sidebar {
    position: fixed !important;
    z-index: 1002;
  }

  .main-layout {
    margin-left: 0 !important;
  }
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

  .trigger {
    padding: 8px 12px;
  }

  .header {
    padding: 0 16px;
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

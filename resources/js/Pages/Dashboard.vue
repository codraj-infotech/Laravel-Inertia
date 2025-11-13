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
        <a-menu-item key="dashboard">
          <template #icon>
            <DashboardOutlined />
          </template>
          <span>Dashboard</span>
        </a-menu-item>

        <a-menu-item key="users">
          <template #icon>
            <UserOutlined />
          </template>
          <span>Users</span>
        </a-menu-item>

        <a-menu-item key="analytics">
          <template #icon>
            <BarChartOutlined />
          </template>
          <span>Analytics</span>
        </a-menu-item>

        <a-sub-menu key="content">
          <template #icon>
            <FileTextOutlined />
          </template>
          <template #title>Content</template>
          <a-menu-item key="posts">Posts</a-menu-item>
          <a-menu-item key="pages">Pages</a-menu-item>
          <a-menu-item key="media">Media</a-menu-item>
        </a-sub-menu>

        <a-sub-menu key="settings-menu">
          <template #icon>
            <SettingOutlined />
          </template>
          <template #title>Settings</template>
          <a-menu-item key="general">General</a-menu-item>
          <a-menu-item key="security">Security</a-menu-item>
          <a-menu-item key="notifications">Notifications</a-menu-item>
        </a-sub-menu>

        <a-menu-item key="reports">
          <template #icon>
            <PieChartOutlined />
          </template>
          <span>Reports</span>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>

    <!-- Right Side Layout -->
    <a-layout>
      <!-- Header -->
      <a-layout-header class="header">
        <div class="header-left">
          <a-button
            type="text"
            class="trigger"
            @click="toggleCollapsed"
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
          <h1 class="page-title">Dashboard</h1>
          <a-breadcrumb>
            <a-breadcrumb-item>
              <HomeOutlined />
            </a-breadcrumb-item>
            <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
          </a-breadcrumb>
        </div>

        <div class="container">
          <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="12" :lg="6">
              <a-card class="stat-card" :bordered="false">
                <a-statistic
                  title="Total Users"
                  :value="1234"
                  :value-style="{ color: '#3f8600' }"
                >
                  <template #prefix>
                    <UserOutlined />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>

            <a-col :xs="24" :sm="12" :lg="6">
              <a-card class="stat-card" :bordered="false">
                <a-statistic
                  title="Active Sessions"
                  :value="567"
                  :value-style="{ color: '#1890ff' }"
                >
                  <template #prefix>
                    <TeamOutlined />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>

            <a-col :xs="24" :sm="12" :lg="6">
              <a-card class="stat-card" :bordered="false">
                <a-statistic
                  title="Revenue"
                  :value="93450"
                  prefix="$"
                  :value-style="{ color: '#cf1322' }"
                >
                  <template #prefix>
                    <DollarOutlined />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>

            <a-col :xs="24" :sm="12" :lg="6">
              <a-card class="stat-card" :bordered="false">
                <a-statistic
                  title="Success Rate"
                  :value="98.5"
                  :precision="1"
                  suffix="%"
                  :value-style="{ color: '#722ed1' }"
                >
                  <template #prefix>
                    <RiseOutlined />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>
          </a-row>

          <a-row :gutter="[16, 16]" style="margin-top: 24px">
            <a-col :xs="24" :lg="16">
              <a-card title="Welcome to Your Dashboard" :bordered="false" class="welcome-card">
                <p>You are successfully logged in! This is your dashboard where you can manage your application.</p>

                <a-divider />

                <h3>Quick Actions</h3>
                <a-space :size="12" wrap>
                  <a-button type="primary">
                    <template #icon>
                      <PlusOutlined />
                    </template>
                    Create New Item
                  </a-button>
                  <a-button>
                    <template #icon>
                      <SettingOutlined />
                    </template>
                    Settings
                  </a-button>
                  <a-button>
                    <template #icon>
                      <FileTextOutlined />
                    </template>
                    View Reports
                  </a-button>
                </a-space>
              </a-card>
            </a-col>

            <a-col :xs="24" :lg="8">
              <a-card title="Recent Activity" :bordered="false">
                <a-timeline>
                  <a-timeline-item color="green">
                    <p>You logged in successfully</p>
                    <small>Just now</small>
                  </a-timeline-item>
                  <a-timeline-item color="blue">
                    <p>Account created</p>
                    <small>Today</small>
                  </a-timeline-item>
                  <a-timeline-item color="gray">
                    <p>Profile updated</p>
                    <small>2 days ago</small>
                  </a-timeline-item>
                </a-timeline>
              </a-card>
            </a-col>
          </a-row>
        </div>
      </a-layout-content>

      <!-- Footer -->
      <a-layout-footer class="footer">
        Laravel Inertia with Ant Design Vue - {{ new Date().getFullYear() }}
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
  AppstoreOutlined,
  DashboardOutlined,
  UserOutlined,
  DownOutlined,
  LogoutOutlined,
  TeamOutlined,
  RiseOutlined,
  PlusOutlined,
  SettingOutlined,
  FileTextOutlined,
  BarChartOutlined,
  PieChartOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  BellOutlined,
  HomeOutlined,
  DollarOutlined,
} from '@ant-design/icons-vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'User', email: 'user@example.com' });

const collapsed = ref(false);
const selectedKeys = ref(['dashboard']);

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

const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  z-index: 1001;
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
  transition: all 0.3s;
}

.logo-icon {
  font-size: 28px;
}

.logo-text {
  white-space: nowrap;
  overflow: hidden;
}

.sidebar-menu {
  margin-top: 8px;
}

/* Header Styles */
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
  font-size: 18px;
  padding: 0 24px;
  cursor: pointer;
  transition: color 0.3s;
}

.trigger:hover {
  color: #667eea;
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

/* Content Styles */
.content {
  padding: 0;
  background: #f0f2f5;
  min-height: calc(100vh - 64px - 70px);
}

.page-header {
  background: #fff;
  padding: 24px;
  margin-bottom: 24px;
  border-bottom: 1px solid #f0f0f0;
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

.stat-card {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
  transition: all 0.3s;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.welcome-card {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.welcome-card h3 {
  margin-top: 16px;
  margin-bottom: 16px;
  font-size: 16px;
  font-weight: 600;
}

/* Footer Styles */
.footer {
  text-align: center;
  background: #fff;
  border-top: 1px solid #f0f0f0;
  padding: 16px 50px;
}

/* Responsive Styles */
@media (max-width: 991px) {
  .user-name {
    display: none;
  }

  .sidebar {
    position: fixed !important;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 16px 16px 16px;
  }

  .page-header {
    padding: 16px;
  }

  .page-title {
    font-size: 20px;
  }

  .trigger {
    padding: 0 12px;
  }

  .header {
    padding: 0 16px;
  }
}

/* Ant Design Layout adjustments */
:deep(.ant-layout) {
  background: #f0f2f5;
}

:deep(.ant-layout-sider-children) {
  display: flex;
  flex-direction: column;
}
</style>

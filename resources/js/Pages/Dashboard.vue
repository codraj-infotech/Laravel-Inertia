<template>
  <a-layout class="dashboard-layout">
    <a-layout-header class="header">
      <div class="logo">
        <AppstoreOutlined />
        <span>Laravel Inertia</span>
      </div>
      <div class="user-menu">
        <a-dropdown>
          <a-button type="text" class="user-button">
            <UserOutlined />
            <span class="user-name">{{ user.name }}</span>
            <DownOutlined />
          </a-button>
          <template #overlay>
            <a-menu>
              <a-menu-item key="profile">
                <UserOutlined />
                Profile
              </a-menu-item>
              <a-menu-divider />
              <a-menu-item key="logout" @click="logout">
                <LogoutOutlined />
                Logout
              </a-menu-item>
            </a-menu>
          </template>
        </a-dropdown>
      </div>
    </a-layout-header>

    <a-layout-content class="content">
      <div class="container">
        <a-row :gutter="[16, 16]">
          <a-col :xs="24" :sm="24" :md="8">
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

          <a-col :xs="24" :sm="24" :md="8">
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

          <a-col :xs="24" :sm="24" :md="8">
            <a-card class="stat-card" :bordered="false">
              <a-statistic
                title="Success Rate"
                :value="98.5"
                :precision="1"
                suffix="%"
                :value-style="{ color: '#cf1322' }"
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
              <a-space direction="vertical" :size="12" style="width: 100%">
                <a-button type="primary" block>
                  <PlusOutlined /> Create New Item
                </a-button>
                <a-button block>
                  <SettingOutlined /> Settings
                </a-button>
                <a-button block>
                  <FileTextOutlined /> View Reports
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
              </a-timeline>
            </a-card>
          </a-col>
        </a-row>
      </div>
    </a-layout-content>

    <a-layout-footer class="footer">
      Laravel Inertia with Ant Design Vue - {{ new Date().getFullYear() }}
    </a-layout-footer>
  </a-layout>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
  AppstoreOutlined,
  UserOutlined,
  DownOutlined,
  LogoutOutlined,
  TeamOutlined,
  RiseOutlined,
  PlusOutlined,
  SettingOutlined,
  FileTextOutlined,
} from '@ant-design/icons-vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'User', email: 'user@example.com' });

const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
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

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 600;
  color: #667eea;
}

.logo :deep(.anticon) {
  font-size: 24px;
}

.user-menu {
  display: flex;
  align-items: center;
}

.user-button {
  display: flex;
  align-items: center;
  gap: 8px;
  color: rgba(0, 0, 0, 0.85);
}

.user-name {
  margin: 0 4px;
  font-weight: 500;
}

.content {
  padding: 24px;
  background: #f0f2f5;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.stat-card {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
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

.footer {
  text-align: center;
  background: #fff;
  border-top: 1px solid #f0f0f0;
}

@media (max-width: 768px) {
  .user-name {
    display: none;
  }

  .content {
    padding: 16px;
  }
}
</style>

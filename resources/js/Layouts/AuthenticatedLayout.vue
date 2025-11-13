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
    <a-layout :style="{ marginLeft: collapsed ? '80px' : '250px' }" class="main-layout">
      <!-- Header -->
      <a-layout-header class="header">
        <div class="header-left">
          <!-- Hamburger Menu Button -->
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

      <!-- Content Slot -->
      <a-layout-content class="content">
        <slot />
      </a-layout-content>

      <!-- Footer -->
      <a-layout-footer class="footer">
        Laravel Inertia with Ant Design Vue - {{ new Date().getFullYear() }}
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
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
  BarChartOutlined,
  PieChartOutlined,
  FileTextOutlined,
} from '@ant-design/icons-vue';

const props = defineProps({
  activeKey: {
    type: String,
    default: 'dashboard',
  },
});

const page = usePage();
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
const selectedKeys = ref([props.activeKey]);

watch(collapsed, (newValue) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('sidebar-collapsed', JSON.stringify(newValue));
  }
});

watch(() => props.activeKey, (newValue) => {
  selectedKeys.value = [newValue];
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

const logout = () => {
  router.post('/logout');
};

const navigateTo = (url) => {
  router.visit(url);
};

onMounted(() => {
  collapsed.value = getInitialCollapsedState();
});
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

:deep(.ant-menu-item-icon) {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Content Styles */
.content {
  padding: 0;
  background: #f0f2f5;
  min-height: calc(100vh - 64px - 70px);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Footer Styles */
.footer {
  text-align: center;
  background: #fff;
  border-top: 1px solid #f0f0f0;
  padding: 16px 50px;
}

/* Main Layout - Right side content */
.main-layout {
  transition: margin-left 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Ant Design Layout adjustments */
:deep(.ant-layout) {
  background: #f0f2f5;
}

:deep(.ant-layout-sider-children) {
  display: flex;
  flex-direction: column;
}

/* Responsive Styles */
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

  :deep(.ant-layout-sider-collapsed) {
    transform: translateX(-100%);
  }
}

@media (max-width: 768px) {
  .trigger {
    padding: 8px 12px;
  }

  .header {
    padding: 0 16px;
  }
}
</style>

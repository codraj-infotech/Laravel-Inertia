<template>
  <AuthenticatedLayout active-key="dashboard">
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
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  UserOutlined,
  TeamOutlined,
  RiseOutlined,
  PlusOutlined,
  SettingOutlined,
  FileTextOutlined,
  HomeOutlined,
  DollarOutlined,
} from '@ant-design/icons-vue';
</script>

<style scoped>
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
}
</style>

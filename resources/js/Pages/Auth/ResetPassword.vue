<template>
  <div class="auth-container">
    <a-card class="auth-card" :bordered="false">
      <div class="auth-header">
        <h1>Reset Password</h1>
        <p>Enter your new password</p>
      </div>

      <a-form
        :model="form"
        @finish="submit"
        layout="vertical"
        autocomplete="off"
      >
        <a-form-item
          label="Email"
          name="email"
          :rules="[
            { required: true, message: 'Please input your email!' },
            { type: 'email', message: 'Please enter a valid email!' }
          ]"
          :validate-status="errors.email ? 'error' : ''"
          :help="errors.email"
        >
          <a-input
            v-model:value="form.email"
            size="large"
            placeholder="Enter your email"
          >
            <template #prefix>
              <MailOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item
          label="New Password"
          name="password"
          :rules="[
            { required: true, message: 'Please input your password!' },
            { min: 8, message: 'Password must be at least 8 characters!' }
          ]"
          :validate-status="errors.password ? 'error' : ''"
          :help="errors.password"
        >
          <a-input-password
            v-model:value="form.password"
            size="large"
            placeholder="Enter your new password"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item
          label="Confirm Password"
          name="password_confirmation"
          :rules="[
            { required: true, message: 'Please confirm your password!' },
            { validator: validatePasswordConfirm }
          ]"
          :validate-status="errors.password_confirmation ? 'error' : ''"
          :help="errors.password_confirmation"
        >
          <a-input-password
            v-model:value="form.password_confirmation"
            size="large"
            placeholder="Confirm your new password"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item>
          <a-button
            type="primary"
            html-type="submit"
            size="large"
            block
            :loading="processing"
          >
            Reset Password
          </a-button>
        </a-form-item>

        <div class="auth-footer">
          <a :href="route('login')" class="back-link">
            <ArrowLeftOutlined /> Back to login
          </a>
        </div>
      </a-form>
    </a-card>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { MailOutlined, LockOutlined, ArrowLeftOutlined } from '@ant-design/icons-vue';

const page = usePage();
const props = defineProps({
  email: String,
  token: String,
});

const form = reactive({
  token: props.token,
  email: props.email || '',
  password: '',
  password_confirmation: '',
});

const processing = computed(() => page.props.processing || false);
const errors = computed(() => page.props.errors || {});

const validatePasswordConfirm = async (rule, value) => {
  if (value !== form.password) {
    return Promise.reject('Passwords do not match!');
  }
  return Promise.resolve();
};

const submit = () => {
  router.post('/reset-password', form);
};

const route = (name) => {
  const routes = {
    'login': '/login',
  };
  return routes[name] || '/';
};
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.auth-card {
  width: 100%;
  max-width: 450px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}

.auth-header {
  text-align: center;
  margin-bottom: 32px;
}

.auth-header h1 {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #1a1a1a;
}

.auth-header p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.auth-footer {
  text-align: center;
  margin-top: 16px;
}

.back-link {
  color: #667eea;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.back-link:hover {
  color: #764ba2;
}
</style>

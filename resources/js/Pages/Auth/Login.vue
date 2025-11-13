<template>
  <div class="auth-container">
    <a-card class="auth-card" :bordered="false">
      <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your account</p>
      </div>

      <a-alert
        v-if="statusMessage"
        :message="statusMessage"
        type="success"
        show-icon
        closable
        class="mb-4"
      />

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
          label="Password"
          name="password"
          :rules="[{ required: true, message: 'Please input your password!' }]"
          :validate-status="errors.password ? 'error' : ''"
          :help="errors.password"
        >
          <a-input-password
            v-model:value="form.password"
            size="large"
            placeholder="Enter your password"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item>
          <div class="form-options">
            <a-checkbox v-model:checked="form.remember">
              Remember me
            </a-checkbox>
            <a :href="route('password.request')" class="forgot-link">
              Forgot password?
            </a>
          </div>
        </a-form-item>

        <a-form-item>
          <a-button
            type="primary"
            html-type="submit"
            size="large"
            block
            :loading="processing"
          >
            Sign in
          </a-button>
        </a-form-item>

        <div class="auth-footer">
          <span>Don't have an account?</span>
          <a :href="route('register')" class="register-link">Sign up</a>
        </div>
      </a-form>
    </a-card>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { MailOutlined, LockOutlined } from '@ant-design/icons-vue';

const page = usePage();
const props = defineProps({
  status: String,
});

const form = reactive({
  email: '',
  password: '',
  remember: false,
});

const processing = computed(() => page.props.processing || false);
const errors = computed(() => page.props.errors || {});
const statusMessage = computed(() => props.status);

const submit = () => {
  router.post('/login', form);
};

const route = (name) => {
  const routes = {
    'password.request': '/forgot-password',
    'register': '/register',
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

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-link {
  color: #667eea;
  font-size: 14px;
}

.forgot-link:hover {
  color: #764ba2;
}

.auth-footer {
  text-align: center;
  margin-top: 16px;
  font-size: 14px;
  color: #666;
}

.register-link {
  margin-left: 8px;
  color: #667eea;
  font-weight: 500;
}

.register-link:hover {
  color: #764ba2;
}

.mb-4 {
  margin-bottom: 16px;
}
</style>

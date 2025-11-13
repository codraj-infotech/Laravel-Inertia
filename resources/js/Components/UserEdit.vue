<template>
  <a-drawer
    v-model:open="open"
    title="Edit User"
    placement="right"
    width="500"
    :closable="true"
    @close="handleCancel"
  >
    <a-form
      ref="formRef"
      :model="formState"
      :rules="formRules"
      layout="vertical"
    >
      <a-form-item label="Name" name="name">
        <a-input v-model:value="formState.name" placeholder="Enter user name" size="large">
          <template #prefix>
            <UserOutlined />
          </template>
        </a-input>
      </a-form-item>

      <a-form-item label="Email" name="email">
        <a-input v-model:value="formState.email" placeholder="Enter email address" size="large">
          <template #prefix>
            <MailOutlined />
          </template>
        </a-input>
      </a-form-item>

      <a-divider>Change Password (Optional)</a-divider>

      <a-alert
        message="Leave password fields blank to keep the current password"
        type="info"
        show-icon
        style="margin-bottom: 16px"
      />

      <a-form-item label="New Password" name="password">
        <a-input-password
          v-model:value="formState.password"
          placeholder="Leave blank to keep current password"
          size="large"
        >
          <template #prefix>
            <LockOutlined />
          </template>
        </a-input-password>
      </a-form-item>

      <a-form-item label="Confirm Password" name="password_confirmation">
        <a-input-password
          v-model:value="formState.password_confirmation"
          placeholder="Confirm new password"
          size="large"
        >
          <template #prefix>
            <LockOutlined />
          </template>
        </a-input-password>
      </a-form-item>
    </a-form>

    <template #footer>
      <div style="display: flex; gap: 8px; justify-content: flex-end;">
        <a-button @click="handleCancel" size="large">Cancel</a-button>
        <a-button type="primary" @click="handleSubmit" :loading="loading" size="large">
          Update User
        </a-button>
      </div>
    </template>
  </a-drawer>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import {
  UserOutlined,
  MailOutlined,
  LockOutlined,
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  user: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['update:visible', 'success']);

const open = ref(props.visible);
const loading = ref(false);
const formRef = ref();
const formState = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const formRules = {
  name: [
    { required: true, message: 'Please input user name!', trigger: 'blur' },
  ],
  email: [
    { required: true, message: 'Please input email!', trigger: 'blur' },
    { type: 'email', message: 'Please enter a valid email!', trigger: 'blur' },
  ],
  password: [
    { min: 8, message: 'Password must be at least 8 characters!', trigger: 'blur' },
  ],
  password_confirmation: [
    {
      validator: (rule, value) => {
        if (formState.password && value !== formState.password) {
          return Promise.reject('Passwords do not match!');
        }
        return Promise.resolve();
      },
      trigger: 'blur',
    },
  ],
};

watch(() => props.visible, (newVal) => {
  open.value = newVal;
  if (newVal && props.user) {
    formState.name = props.user.name;
    formState.email = props.user.email;
    formState.password = '';
    formState.password_confirmation = '';
  }
});

watch(open, (newVal) => {
  emit('update:visible', newVal);
});

const handleSubmit = async () => {
  if (!props.user) return;

  try {
    await formRef.value.validate();
    loading.value = true;

    router.put(`/users/${props.user.id}`, formState, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        message.success('User updated successfully!');
        open.value = false;
        emit('success');
      },
      onError: (errors) => {
        Object.keys(errors).forEach(key => {
          message.error(errors[key]);
        });
      },
      onFinish: () => {
        loading.value = false;
      },
    });
  } catch (error) {
    console.log('Validation failed:', error);
  }
};

const handleCancel = () => {
  open.value = false;
  resetForm();
};

const resetForm = () => {
  formState.password = '';
  formState.password_confirmation = '';
  if (formRef.value) {
    formRef.value.clearValidate();
  }
};
</script>

<style scoped>
:deep(.ant-drawer-body) {
  padding-bottom: 80px;
}
</style>

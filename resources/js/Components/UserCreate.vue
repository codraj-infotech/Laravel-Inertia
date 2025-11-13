<template>
  <a-modal
    v-model:open="open"
    title="Create New User"
    :confirm-loading="loading"
    @ok="handleSubmit"
    @cancel="handleCancel"
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

      <a-form-item label="Password" name="password">
        <a-input-password v-model:value="formState.password" placeholder="Enter password">
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
    { required: true, message: 'Please input password!', trigger: 'blur' },
    { min: 8, message: 'Password must be at least 8 characters!', trigger: 'blur' },
  ],
  password_confirmation: [
    { required: true, message: 'Please confirm password!', trigger: 'blur' },
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
};

watch(() => props.visible, (newVal) => {
  open.value = newVal;
  if (newVal) {
    resetForm();
  }
});

watch(open, (newVal) => {
  emit('update:visible', newVal);
});

const handleSubmit = async () => {
  try {
    await formRef.value.validate();
    loading.value = true;

    router.post('/users', formState, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        message.success('User created successfully!');
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
};

const resetForm = () => {
  formState.name = '';
  formState.email = '';
  formState.password = '';
  formState.password_confirmation = '';
  if (formRef.value) {
    formRef.value.clearValidate();
  }
};
</script>

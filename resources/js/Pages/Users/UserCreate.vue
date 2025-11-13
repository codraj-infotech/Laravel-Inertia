<template>
  <a-modal
    v-model:open="open"
    title="Create New User"
    :confirm-loading="loading"
    @ok="handleSubmit"
    @cancel="handleCancel"
    width="700px"
  >
    <a-form
      ref="formRef"
      :model="formState"
      :rules="formRules"
      layout="vertical"
    >
      <a-row :gutter="16">
        <a-col :span="24">
          <a-form-item label="Profile Image URL" name="profile_image">
            <a-input v-model:value="formState.profile_image" placeholder="Enter profile image URL">
              <template #prefix>
                <PictureOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Name" name="name">
            <a-input v-model:value="formState.name" placeholder="Enter user name">
              <template #prefix>
                <UserOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Email" name="email">
            <a-input v-model:value="formState.email" placeholder="Enter email address">
              <template #prefix>
                <MailOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Phone" name="phone">
            <a-input v-model:value="formState.phone" placeholder="Enter phone number">
              <template #prefix>
                <PhoneOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Date of Birth" name="date_of_birth">
            <a-date-picker
              v-model:value="formState.date_of_birth"
              format="YYYY-MM-DD"
              placeholder="Select date of birth"
              style="width: 100%"
            />
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Gender" name="gender">
            <a-select v-model:value="formState.gender" placeholder="Select gender">
              <a-select-option value="male">Male</a-select-option>
              <a-select-option value="female">Female</a-select-option>
              <a-select-option value="other">Other</a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <a-col :span="24">
          <a-form-item label="Address" name="address">
            <a-textarea
              v-model:value="formState.address"
              placeholder="Enter address"
              :rows="3"
            />
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Password" name="password">
            <a-input-password v-model:value="formState.password" placeholder="Enter password">
              <template #prefix>
                <LockOutlined />
              </template>
            </a-input-password>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Confirm Password" name="password_confirmation">
            <a-input-password v-model:value="formState.password_confirmation" placeholder="Confirm password">
              <template #prefix>
                <LockOutlined />
              </template>
            </a-input-password>
          </a-form-item>
        </a-col>
      </a-row>
    </a-form>
  </a-modal>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import dayjs from 'dayjs';
import { userApi } from '@/services/api';
import {
  UserOutlined,
  MailOutlined,
  LockOutlined,
  PhoneOutlined,
  PictureOutlined,
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
  profile_image: '',
  date_of_birth: null,
  gender: undefined,
  phone: '',
  address: '',
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

    // Format date_of_birth to YYYY-MM-DD string
    const data = {
      ...formState,
      date_of_birth: formState.date_of_birth ? dayjs(formState.date_of_birth).format('YYYY-MM-DD') : null,
    };

    // Use API instead of Inertia router
    await userApi.create(data);

    message.success('User created successfully!');
    open.value = false;
    emit('success');
  } catch (error) {
    console.error('Error creating user:', error);
    if (error.response?.data?.errors) {
      // Handle Laravel validation errors
      Object.keys(error.response.data.errors).forEach(key => {
        message.error(error.response.data.errors[key][0]);
      });
    } else if (error.response?.data?.message) {
      message.error(error.response.data.message);
    } else {
      message.error('Failed to create user');
    }
  } finally {
    loading.value = false;
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
  formState.profile_image = '';
  formState.date_of_birth = null;
  formState.gender = undefined;
  formState.phone = '';
  formState.address = '';
  if (formRef.value) {
    formRef.value.clearValidate();
  }
};
</script>

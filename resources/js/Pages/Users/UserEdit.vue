<template>
  <a-drawer
    v-model:open="open"
    title="Edit User"
    placement="right"
    width="650"
    :closable="true"
    @close="handleCancel"
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
            <a-input v-model:value="formState.profile_image" placeholder="Enter profile image URL" size="large">
              <template #prefix>
                <PictureOutlined />
              </template>
            </a-input>
            <div v-if="formState.profile_image" style="margin-top: 8px;">
              <a-image :src="formState.profile_image" :width="100" :height="100" style="border-radius: 50%; object-fit: cover;" />
            </div>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Name" name="name">
            <a-input v-model:value="formState.name" placeholder="Enter user name" size="large">
              <template #prefix>
                <UserOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Email" name="email">
            <a-input v-model:value="formState.email" placeholder="Enter email address" size="large">
              <template #prefix>
                <MailOutlined />
              </template>
            </a-input>
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Phone" name="phone">
            <a-input v-model:value="formState.phone" placeholder="Enter phone number" size="large">
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
              size="large"
              style="width: 100%"
            />
          </a-form-item>
        </a-col>

        <a-col :span="12">
          <a-form-item label="Gender" name="gender">
            <a-select v-model:value="formState.gender" placeholder="Select gender" size="large">
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
              size="large"
            />
          </a-form-item>
        </a-col>
      </a-row>

      <a-divider>Change Password (Optional)</a-divider>

      <a-alert
        message="Leave password fields blank to keep the current password"
        type="info"
        show-icon
        style="margin-bottom: 16px"
      />

      <a-row :gutter="16">
        <a-col :span="12">
          <a-form-item label="New Password" name="password">
            <a-input-password
              v-model:value="formState.password"
              placeholder="Leave blank to keep current"
              size="large"
            >
              <template #prefix>
                <LockOutlined />
              </template>
            </a-input-password>
          </a-form-item>
        </a-col>

        <a-col :span="12">
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
        </a-col>
      </a-row>
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
import dayjs from 'dayjs';
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
    formState.profile_image = props.user.profile_image || '';
    formState.date_of_birth = props.user.date_of_birth ? dayjs(props.user.date_of_birth) : null;
    formState.gender = props.user.gender || undefined;
    formState.phone = props.user.phone || '';
    formState.address = props.user.address || '';
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

    // Format date_of_birth to YYYY-MM-DD string
    const data = {
      ...formState,
      date_of_birth: formState.date_of_birth ? dayjs(formState.date_of_birth).format('YYYY-MM-DD') : null,
    };

    router.put(`/users/${props.user.id}`, data, {
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

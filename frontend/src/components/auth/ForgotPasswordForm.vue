<template>
  <q-form @submit="forgotPassword()">
    <div class="forgot-password-container column justify-between">
      <div class="justify-center text-center">
        <h1 class="forgot-password-title">
          {{ t('forgot_password') }}
        </h1>
      </div>
      <div>
        <q-input
          v-model="formData.email"
          :label="t('email')"
          type="email"
          dense
          hide-bottom-space
          :rules="[ val => val && val.length > 0 || t('mandatory_completion')]"
        />
      </div>
      <div class="text-right padding q-pa-sm">
        <q-btn
          class="q-ma-sm"
          :label="t('to_go_back')"
          flat
          :disable="loading"
          :loading="loading"
          @click="emit('hide-forgot-password')"
        />
        <q-btn
          type="submit"
          class="q-ma-sm"
          text-color="white"
          color="primary"
          :disable="loading"
          :loading="loading"
          :label="t('send')"
        />
      </div>
    </div>
  </q-form>
</template>

<script setup>
import { ref } from 'vue';
import { postForgotPassword } from 'src/services/login/login-api.js';
import { t } from 'src/services/utils/i18n';
import { Notify } from 'quasar';

const emit = defineEmits([ 'hide-forgot-password' ]);

const formData = ref({});
const loading = ref(false);

async function forgotPassword() {
  loading.value = true;

  try {
    const { email } = formData.value;
    const { data } = await postForgotPassword(email);

    Notify.create({
      message: data.message || t('password_recovery_sent_message'),
      type: 'positive',
    });

    loading.value = false;

    emit('hide-forgot-password');
  } catch (error) {
    const message = error.response.data.message;

    Notify.create({
      message: message || t('unexpected_error'),
      type: 'negative',
    });

    loading.value = false;
  }
}
</script>

<style scoped>
.forgot-password-container {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 50vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.forgot-password-title {
  font-size: 2rem;
  line-height: 1rem;
}
</style>

<template>
  <q-form @submit="sendPasswordSetting()">
    <div class="password-setting-container column justify-between">
      <div class="justify-center text-center">
        <h5>
          {{ t('password_setting') }}
        </h5>
      </div>

      <q-input
        v-model="formData.password"
        :label="t('password')"
        :type="passwordHidden ? 'password' : 'text'"
        dense
        hide-bottom-space
        :rules="[
            val => val && val.length > 0 || t('mandatory_completion'),
            val => val && val.length >= 4 || t('password_must_have_least_characters', {val: 4}),
          ]"
        lazy-rules
      >
        <template v-slot:append>
          <q-icon
            :name="passwordHidden ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            @click="passwordHidden = !passwordHidden"
          />
        </template>
      </q-input>

      <q-linear-progress
        :value="passwordSecurityLevel"
        :color="passwordSecurityLevelColor(passwordSecurityLevel)"
        class="q-mt-sm"
        track-color="transparent"
      />

      <q-input
        v-model="formData.password_confirmation"
        :label="t('confirm_password')"
        :type="passwordConfirmHidden ? 'password' : 'text'"
        dense
        hide-bottom-space
        :rules="[
            val => val && val.length > 0 || t('mandatory_completion'),
            val => val === formData.password || t('passwords_do_not_match')
          ]"
        lazy-rules
      >
        <template v-slot:append>
          <q-icon
            :name="passwordConfirmHidden ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            @click="passwordConfirmHidden = !passwordConfirmHidden"
          />
        </template>
      </q-input>

      <div class="text-right padding q-pa-sm">
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
import { computed, ref } from 'vue';
import { Notify } from 'quasar';
import { t } from 'src/services/utils/i18n';
import { setPassword } from 'src/services/auth/auth-api';
import { useRouter } from 'vue-router';
import { formatResponseError } from 'src/services/utils/error-formatter';

const router = useRouter();

const loading = ref(false);
const passwordHidden = ref(true);
const passwordConfirmHidden = ref(true);
const formData = ref({
  password: '',
  password_confirmation: '',
});

const props = defineProps({
  token: {
    required: true,
  },
});

const passwordSecurityLevel = computed(() => {
  if (!formData.value.password?.length) {
    return 0;
  }

  const validations = [
    formData.value.password.length >= 6,
    formData.value.password.length >= 10,
    /[a-z]/.test(formData.value.password),
    /[A-Z]/.test(formData.value.password),
    /[1-9]/.test(formData.value.password),
    /[!@#$%^&*()_+\-=\[\]{};:"',.<>?/|\\`~]+/.test(formData.value.password),
  ];

  const passedValidations = validations.filter((validated) => validated).length;

  return passedValidations / validations.length;
});

function passwordSecurityLevelColor(level) {
  if (+level < 0.25) {
    return 'negative';
  }

  if (+level < 0.50) {
    return 'warning';
  }

  if (+level < 0.75) {
    return 'teal';
  }

  return 'green';
}

async function sendPasswordSetting() {
  loading.value = true;

  try {
    const { data } = await setPassword(formData.value, props.token);

    Notify.create({
      type: 'positive',
      message: data.message || t('password_set_successfully'),
    });

    router.push({ name: 'login' });
  } catch (error) {
    Notify.create({
      type: 'negative',
      message: formatResponseError(error) || t('unexpected_error'),
    });
  }

  loading.value = false;
}
</script>

<style scoped>
.password-setting-container {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 50vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}
</style>

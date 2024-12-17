<template>
  <q-form @submit="login()">
    <div class="login-container column justify-between">
      <div class="col-12 text-center q-pa-md text-h6">
        {{ t('welcome') }}
      </div>
      <div class="q-mt-xl">
        <q-input
          v-model="formData.email"
          :label="t('email')"
          type="email"
          dense
          hide-bottom-space
          :rules="[ val => val && val.length > 0 || t('mandatory_completion')]"
        />
      </div>
      <div>
        <q-input
          v-model="formData.password"
          :type="passwordHidden ? 'password' : 'text'"
          :label="t('password')"
          dense
          hide-bottom-space
          :rules="[ val => val && val.length > 0 || t('mandatory_completion')]"
        >
          <template v-slot:append>
            <q-icon
              :name="passwordHidden ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="passwordHidden = !passwordHidden"
            />
          </template>
        </q-input>
      </div>

      <div class="row q-mt-lg">
        <q-btn
          type="submit"
          color="primary"
          class="col-xs-12"
          :label="t('login')"
        />

        <div class="col-xs-12 q-mt-lg">
          <a
            class="text-primary cursor-pointer"
            style="text-decoration: underline"
            @click="emit('show-forgot-password')"
          >
            {{ t('forgot_password') }}
          </a>
        </div>
        <div class="col-xs-12 q-mt-sm">
          {{ t('do_not_have_an_account') }}
          <router-link :to="{ name: 'register' }" class="text-primary">
            {{ t('register_action') }}
          </router-link>
        </div>
      </div>
    </div>
  </q-form>
</template>

<script setup>
import { loginUser } from 'src/services/auth/auth-api.js';
import { onMounted, ref, reactive } from 'vue';
import { loadLoggedUser, loggedUser, resetLoggedUser } from 'boot/user';
import { useRouter } from 'vue-router';
import { Notify, Loading } from 'quasar';
import { t } from 'src/services/utils/i18n';

const emit = defineEmits([ 'show-forgot-password' ]);
const router = useRouter();

const formData = reactive({
  email: null,
  password: null,
});

const passwordHidden = ref(true);

onMounted(() => {
  if (loggedUser.id) {
    resetLoggedUser();
  }
});

const login = async function () {
  formData.email = formData.email.toLowerCase();
  Loading.show();
  try {
    const loginInfo = await loginUser(formData);
    localStorage.setItem('isUserLogged', 'true');
    localStorage.setItem('userToken', loginInfo.data.token);
    await loadLoggedUser();
    Loading.hide();
    router.push({ name: 'dashboard' });
  } catch (error) {
    Loading.hide();
    const unauthorizedAccess = error.response?.status === 403;
    const unauthorizedMessage = error.response?.data?.message;
    const errorMessage = unauthorizedAccess && unauthorizedMessage ? unauthorizedMessage : t('unexpected_error');
    Notify.create({
      message: errorMessage,
      type: 'negative',
    });
  }
};
</script>

<style scoped>
.login-container {
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

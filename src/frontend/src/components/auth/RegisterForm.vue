<template>
  <div class="q-pa-md">
    <q-form @submit="sendRegister()" class="row justify-center q-mt-lg">
      <q-card class="col-xs-12 col-sm-9 col-md-6 col-lg-4 col-xl-3">
        <q-card-section>
          <h4 class="text-center">
            {{ t('register_label') }}
          </h4>

          <div class="q-pt-lg">
            <div class="text-bold">{{ t('contact_and_login_info') }}</div>
            <q-input
              v-model="formData.name"
              :label="t('your_name')+' *'"
              dense
              hide-bottom-space
              :rules="[val => !!val || t('mandatory_completion')]"
            />
            <q-input
              v-model="formData.whatsapp"
              :label="t('whatsapp')"
              :mask="formData.whatsapp?.length > 14 ? '(##) #####-####' : '(##) ####-#####'"
              dense
              hide-bottom-space
            />
            <q-input
              v-model="formData.email"
              :label="t('email')+' *'"
              type="email"
              dense
              hide-bottom-space
              :rules="[val => !!val || t('mandatory_completion')]"
            />
            <q-input
              v-model="formData.password"
              :type="passwordHidden ? 'password' : 'text'"
              :label="t('password')+' *'"
              dense
              hide-bottom-space
              :rules="[val => !!val || t('mandatory_completion')]"
            >
              <template v-slot:append>
                <q-icon
                  :name="passwordHidden ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="passwordHidden = !passwordHidden"
                />
              </template>
            </q-input>
            <q-input
              v-model="formData.password_confirmation"
              :label="t('confirm_password')+' *'"
              :type="passwordHidden ? 'password' : 'text'"
              dense
              hide-bottom-space
              :rules="[
                val => val && val.length > 0 || t('mandatory_completion'),
                val => val === formData.password || t('incorrect_password_confirmation')
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
          </div>

          <div class="row q-mt-lg">
            <q-btn
              type="submit"
              color="primary"
              class="col-xs-12"
              :label="t('create_account')"
            />
            <div class="col-xs-12 q-mt-sm">
              {{ t('already_have_an_account') }}
              <router-link :to="{ name: 'login' }" class="text-primary">
                {{ t('do_login') }}
              </router-link>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { Notify, Loading } from 'quasar';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { t } from 'src/services/utils/i18n';
import { register } from 'src/services/auth/auth-api';
import { loadLoggedUser } from 'boot/user';

const router = useRouter();

const formData = reactive({
  name: null,
  email: null,
  phone: null,
  password: null,
  password_confirmation: null,
});

const passwordHidden = ref(true);

const sendRegister = async function () {
  Loading.show();

  try {
    formData.email = formData.email?.toLowerCase();

    const response = await register(formData);

    localStorage.setItem('isUserLogged', 'true');
    localStorage.setItem('userToken', response.data.token);
    await loadLoggedUser();

    Loading.hide();

    router.push({ name: 'dashboard' });
  } catch (error) {
    Loading.hide();

    Notify.create({
      message: formatResponseError(error) || t('failed_to_register'),
      type: 'negative',
    });
  }
};
</script>

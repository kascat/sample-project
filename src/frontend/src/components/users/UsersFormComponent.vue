<template>
  <q-card>
    <q-card-section>
      <q-form
        ref="userForm"
        @submit="submitUser()"
      >
        <div class="row q-col-gutter-md">
          <q-input
            v-model="user.name"
            :label="t('name') + ' *'"
            hide-bottom-space
            dense
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
            :rules="[val => !!val || t('mandatory_completion')]"
          />

          <q-input
            v-model="user.email"
            :label="t('email') + ' *'"
            hide-bottom-space
            dense
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
            :rules="[val => !!val || t('mandatory_completion')]"
          />

          <q-input
            v-model="user.phone"
            :label="t('whatsapp_phone')"
            :mask="user.phone?.length > 14 ? '(##) #####-####' : '(##) ####-#####'"
            hide-bottom-space
            dense
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
          />

          <q-select
            v-model="user.permission_id"
            :label="t('permission') + ' *'"
            map-options
            emit-value
            hide-bottom-space
            :options="permissionsOptions"
            :option-label="opt => opt.name || (opt.id && '-') || user.permission?.name || '-'"
            option-value="id"
            dense
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
            :rules="[val => !!val || t('mandatory_completion')]"
            @filter="filterPermissions"
          />

          <q-select
            v-model="user.role"
            :label="t('role') + ' *'"
            map-options
            emit-value
            hide-bottom-space
            :options="rolesOptions"
            option-label="label"
            option-value="value"
            dense
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
            :rules="[val => !!val || t('mandatory_completion')]"
          />

          <q-input
            v-model="user.login_time"
            :label="t('access_time_after_login')"
            suffix="Min"
            hide-bottom-space
            clearable
            dense
            outlined
            type="number"
            min="0"
            step="1"
            class="col-xs-12 col-md-4 col-xl-3"
          >
            <template v-slot:append>
              <q-icon
                name="o_info"
                class="cursor-pointer"
              />
              <q-tooltip :offset="[5, 5]">
                {{ t('login_time_info') }}
              </q-tooltip>
            </template>
          </q-input>

          <q-input
            v-model="user.expires_in"
            :label="t('limit_access_date')"
            mask="##/##/#### ##:##"
            hide-bottom-space
            dense
            clearable
            outlined
            class="col-xs-12 col-md-4 col-xl-3"
          >
            <template v-slot:prepend>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-date v-model="user.expires_in" mask="DD/MM/YYYY HH:mm">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup :label="t('close')" color="primary" flat/>
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>

            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-time v-model="user.expires_in" mask="DD/MM/YYYY HH:mm" format24h>
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup :label="t('close')" color="primary" flat/>
                    </div>
                  </q-time>
                </q-popup-proxy>
              </q-icon>

              <div class="row">
                <q-icon
                  name="o_info"
                  class="cursor-pointer"
                />
                <q-tooltip :offset="[5, 5]">
                  {{ t('expires_in_info') }}
                </q-tooltip>
              </div>
            </template>
          </q-input>

          <div class="col-xs-12 col-md-6 row items-center">
            <q-btn
              outline
              :label="t('set_password')"
              icon="lock_open"
              color="positive"
              type="button"
            >
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-card style="width: 660px; max-width: 90vw;">
                  <q-card-section>
                    <div class="text-h6 q-mb-lg">
                      {{ t('user_password') }}
                    </div>
                    <div class="text-deep-orange text-bold">
                      {{ t('user_password_input_notice') }}
                    </div>
                    <q-input
                      v-model="user.password"
                      :label="t('password')"
                      outlined
                      clearable
                      dense
                      lazy-rules
                      hide-bottom-space
                      :rules="[val => val && val.length >= 4 || t('user_password_rule')]"
                    />
                    <div class="q-mt-sm col text-right">
                      <q-btn
                        :label="t('close')"
                        flat
                        color="primary"
                        type="button"
                        v-close-popup
                      />
                    </div>
                  </q-card-section>
                </q-card>
              </q-popup-proxy>
            </q-btn>

            <q-chip
              v-if="user.password"
              color="positive"
              text-color="white"
              :label="t('password_set')"
            />

            <div v-if="!props.formItemId" class="row items-center">
              <q-icon
                name="o_info"
                size="sm"
                color="deep-orange"
                class="cursor-pointer"
              />
              <q-tooltip :offset="[5, 5]">
                {{ t('user_password_notice') }}
              </q-tooltip>
            </div>
          </div>
        </div>

        <div class="q-mt-sm text-right">
          <q-btn
            outline
            :label="t('save')"
            icon="save"
            type="submit"
            color="primary"
            :disable="saving"
            :loading="saving"
          />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { createUser, updateUser, getUser } from 'src/services/user/user-api';
import { getPermissions } from 'src/services/permission/permission-api';
import { Notify, Loading } from 'quasar';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { ROLES } from 'src/constants/user_roles';
import { t } from 'src/services/utils/i18n';

const router = useRouter();

const props = defineProps({
  formItemId: {
    required: true,
  },
});

const userForm = ref(null);

const user = ref({
  name: null,
  email: null,
  phone: null,
  role: null,
  permission_id: null,
  password: null,
});

const permissionsOptions = ref([]);
const saving = ref(false);

const rolesOptions = Object.values(ROLES).map((role) => ({
  label: t(`user.role.${role}`),
  value: role,
}));

onMounted(async () => {
  if (props.formItemId) {
    await loadUser(props.formItemId);
  }
});

async function submitUser() {
  saving.value = true;
  try {
    const validated = await userForm.value.validate();
    if (validated) {
      const userToSave = { ...user.value };

      if (!props.formItemId) {
        await createUser(userToSave);
      } else {
        await updateUser(props.formItemId, userToSave);
      }

      Notify.create({
        message: t('saved_successfully'),
        type: 'positive',
      });

      router.push({ name: 'users' });
    }
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || t('failed_to_save'),
      type: 'negative',
    });
  }
  saving.value = false;
}

async function loadUser(id) {
  Loading.show();
  try {
    user.value = await getUser(id, {
      with: [ 'permission' ],
    });
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  Loading.hide();
}

async function filterPermissions(val, update, abort) {
  try {
    permissionsOptions.value = await getPermissions({
      name: val,
      rowsPerPage: 100,
    });
    update();
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
    abort();
  }
}
</script>

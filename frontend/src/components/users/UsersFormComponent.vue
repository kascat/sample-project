<template>
  <q-btn
    color="primary"
    icon="arrow_back"
    dense
    outline
    rounded
    :to="{ name: 'users' }"
  >
    <q-tooltip :offset="[5, 5]">
      {{ t('to_go_back') }}
    </q-tooltip>
  </q-btn>
  <h4 class="q-mt-lg">{{ t('system_user') }}</h4>
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
      <q-select
        v-model="user.permission_id"
        :label="t('permission') + ' *'"
        map-options
        emit-value
        hide-bottom-space
        clearable
        :options="permissionsOptions"
        :option-label="opt => opt.name || user.permission?.name"
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
        clearable
        :options="rolesOptions"
        option-label="label"
        option-value="value"
        dense
        outlined
        class="col-xs-12 col-md-4 col-xl-3"
        :rules="[val => !!val || t('mandatory_completion')]"
      />

      <!--      TODO: funcionalidade de "bloqueio automático" inativa-->
      <!--      <q-input-->
      <!--        v-if="ROLES.ADMIN !== user.role"-->
      <!--        v-model="user.login_time"-->
      <!--        :label="t('usage_time')"-->
      <!--        suffix="Min"-->
      <!--        hide-bottom-space-->
      <!--        clearable-->
      <!--        dense-->
      <!--        outlined-->
      <!--        class="col-xs-12 col-md-4 col-xl-3"-->
      <!--      />-->
      <!--      <q-input-->
      <!--        v-if="ROLES.ADMIN !== user.role"-->
      <!--        v-model="user.expires_in"-->
      <!--        :label="t('accessible_until')"-->
      <!--        mask="##/##/#### ##:##"-->
      <!--        hide-bottom-space-->
      <!--        dense-->
      <!--        clearable-->
      <!--        outlined-->
      <!--        class="col-xs-12 col-md-4 col-xl-3"-->
      <!--      >-->
      <!--        <template v-slot:append>-->
      <!--          <q-icon-->
      <!--            :name="'event' + 'access_time'"-->
      <!--            class="cursor-pointer q-ml-md q-mr-md"-->
      <!--            @click="showDateTimeModal = true"-->
      <!--          />-->
      <!--        </template>-->
      <!--      </q-input>-->
    </div>

    <div class="q-mt-sm row q-col-gutter-md">
      <div
        v-if="user.role !== ROLES.ADMIN"
        class="col-xs-12 col-md-6"
      >
        <q-btn
          outline
          :label="t('set_password')"
          icon="lock_open"
          color="positive"
          type="button"
          @click="showPasswordModal = true"
        />
        <q-chip
          v-if="user.password"
          color="positive"
          text-color="white"
          :label="t('password_set')"
        />
        <div class="text-deep-orange text-bold" v-if="!$route.params.id">
          {{ t('user_password_notice') }}
        </div>
      </div>
      <div class="col text-right">
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
    </div>

    <q-dialog v-model="showDateTimeModal">
      <q-card style="width: 645px; max-width: 80vw;">
        <div class="text-h6 q-ma-md">
          {{ t('accessible_until') }}
        </div>
        <q-card-section>
          <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-py-xs q-mb-lg q-mr-md">
              <q-date
                v-model="user.expires_in"
                mask="DD/MM/YYYY HH:mm"
              />
            </div>
            <div class="col">
              <q-time
                v-model="user.expires_in"
                mask="DD/MM/YYYY HH:mm"
                format24h
              />
            </div>
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn
            :label="t('close')"
            dense
            outline
            color="negative"
            @click="showDateTimeModal = false"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showPasswordModal">
      <q-card style="width: 660px; max-width: 80vw;">
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
            :rules="[val => val && val.length >= 6 || t('user_password_role')]"
          />
        </q-card-section>
        <q-card-actions>
          <div class="q-ma-sm col text-right">
            <q-btn
              :label="t('close')"
              dense
              outline
              color="negative"
              type="button"
              @click="showPasswordModal = false"
            />
          </div>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-form>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { createUser, updateUser, getUser } from 'src/services/user/user-api';
import { getPermissions } from 'src/services/permission/permission-api';
import { Notify, Loading } from 'quasar';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { formatDateBR } from 'src/services/utils/date';
import { ROLES } from 'src/constants/user_roles';
import { t } from 'src/services/utils/i18n';

const router = useRouter();
const route = useRoute();

const userForm = ref(null);

const user = ref({
  name: null,
  email: null,
  role: null,
  permission_id: null,
  password: null,
});

const showDateTimeModal = ref(false);
const showPasswordModal = ref(false);
const permissionsOptions = ref([]);
const saving = ref(false);

const rolesOptions = Object.values(ROLES).map((role) => ({
  label: t(`user.role.${role}`),
  value: role,
}));

onMounted(async () => {
  if (route.params.id) {
    await loadUser(route.params.id);
  }
});

async function submitUser() {
  saving.value = true;
  try {
    const validated = await userForm.value.validate();
    if (validated) {
      if (user.value.role === ROLES.ADMIN) {
        user.value.password = null;
        user.value.expires_in = null;
        user.value.login_time = null;
      }

      const userToSave = { ...user.value };

      if (!route.params.id) {
        await createUser(userToSave);
      } else {
        await updateUser(route.params.id, userToSave);
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
    const response = await getUser(id, {
      with: [ 'permission' ],
    });
    response.expires_in = formatDateBR(response.expires_in);
    user.value = response;
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

<style scoped>
.q-date__content {
  width: auto;
  min-width: 200px;
}

.q-date {
  width: auto;
  min-width: 200px;
}

.q-time__content {
  width: auto;
  min-width: 200px;
}

.q-time {
  width: 440px;
  min-width: 200px;
}
</style>

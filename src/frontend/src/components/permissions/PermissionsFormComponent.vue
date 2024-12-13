<template>
  <q-btn
    color="primary"
    icon="arrow_back"
    dense
    outline
    rounded
    :to="{ name: 'permissions' }"
  >
    <q-tooltip :offset="[5, 5]">
      {{ t('to_go_back') }}
    </q-tooltip>
  </q-btn>
  <h4 class="q-mt-lg">{{ t('permission') }}</h4>
  <q-form
    ref="permissionForm"
    @submit="submitFunction()"
  >
    <div class="row q-col-gutter-md">
      <div class="col-xs-12 col-md-6">
        <q-input
          :label="t('name')"
          v-model="permission.name"
          dense
          outlined
          :rules="[val => !!val || t('mandatory_completion')]"
        />
      </div>
      <div class="col-xs-12 col-md-6">
        <q-checkbox
          v-model="permission.default"
          :label="t('default_permission_to_new_accounts')"
        />
      </div>
    </div>
    <q-separator />
    <div>
      <q-checkbox
        :loading="loading"
        class="q-mr-md q-mb-md"
        v-for="permission in permissionsOptions"
        :key="permission"
        v-model="abilitiesCache"
        :val="permission"
        :label="t(`permissions.${permission}`)"
        color="positive"
      />
    </div>
    <div class="text-right">
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
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  createPermission,
  updatePermission,
  getPermission,
  getAllPermission,
} from 'src/services/permission/permission-api';
import { loggedUser } from 'boot/user';
import { Notify, Loading } from 'quasar';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { t } from 'src/services/utils/i18n';

const permission = ref({
  name: null,
  default: false,
  abilities: [],
});

let abilitiesCache = ref([]);
let saving = ref(false);
let loading = ref(false);

const permissionsOptions = ref([]);
const permissionForm = ref(null);
const router = useRouter();
const route = useRoute();

onMounted(async () => {
  if (route.params.id) {
    await getPermissionFunction();
  }

  searchAllPermissionsOptions();
});

async function submitFunction() {
  saving.value = true;
  try {
    const validated = permissionForm.value.validate();
    if (validated) {
      permission.value.abilities = abilitiesCache;
      const permissionToSave = { ...permission.value };
      !route.params.id ? await createPermission(permissionToSave) : await updatePermission(route.params.id, permissionToSave);

      Notify.create({
        message: t('saved_successfully'),
        type: 'positive',
      });

      await router.push({ name: 'permissions' });
      if (loggedUser?.permission_id === permission.value?.id) {
        router.go();
      }
    }
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || t('failed_to_save'),
      type: 'negative',
    });
  }
  saving.value = false;
}

async function getPermissionFunction() {
  Loading.show();
  try {
    const response = await getPermission(route.params.id);
    abilitiesCache.value = response.abilities || [];
    permission.value = response;
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  Loading.hide();
}

async function searchAllPermissionsOptions() {
  loading.value = true;
  try {
    permissionsOptions.value = await getAllPermission();
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  loading.value = false;
}

</script>

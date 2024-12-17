<template>
  <q-card>
    <q-card-section>
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

        <q-separator/>

        <div>
          <q-checkbox
            :loading="loading"
            class="q-mr-md q-mb-md"
            v-for="ability in abilityOptions"
            :key="ability"
            v-model="permission.abilities"
            :val="ability"
            :label="t(`permissions.${ability}`)"
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
    </q-card-section>
  </q-card>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  createPermission,
  updatePermission,
  getPermission,
  getAbilities,
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

const saving = ref(false);
const loading = ref(false);

const abilityOptions = ref([]);
const permissionForm = ref(null);

const router = useRouter();

const props = defineProps({
  formItemId: {
    required: true,
  },
});

onMounted(async () => {
  if (props.formItemId) {
    await loadPermission(props.formItemId);
  }

  searchAllPermissionsOptions();
});

async function submitFunction() {
  saving.value = true;
  try {
    const validated = permissionForm.value.validate();
    if (validated) {
      const permissionToSave = { ...permission.value };

      if (!props.formItemId) {
        await createPermission(permissionToSave)
      } else {
        await updatePermission(props.formItemId, permissionToSave)
      }

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

async function loadPermission(id) {
  Loading.show();
  try {
    const response = await getPermission(id);
    response.abilities = response.abilities || [];
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
    abilityOptions.value = await getAbilities();
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  loading.value = false;
}
</script>

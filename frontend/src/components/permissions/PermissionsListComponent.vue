<template>
  <q-table
    :title="t('permissions_label')"
    :rows="permissionsData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    :loading-label="t('loading')"
    :no-results-label="t('no_results')"
    :no-data-label="t('no_results')"
    binary-state-sort
    @request="getPermissionsFunction"
  >
    <template v-slot:top-right>
      <q-btn
        icon="add"
        :label="t('register')"
        color="primary"
        outline
        :to="{ name: 'permissions_create' }"
      />
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td key="actions" :props="props">
        <q-btn-group outline>
          <q-btn
            outline
            color="primary"
            icon="edit"
            :to="{ name: 'permissions_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              {{ t('update') }}
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="negative"
            icon="delete"
            :loading="removingId === props.row.id"
            :disable="removingId === props.row.id"
            @click="destroyPermissionFunction(props.row.id)"
          >
            <q-tooltip>
              {{ t('remove') }}
            </q-tooltip>
          </q-btn>
        </q-btn-group>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { getPermissions, destroyPermission } from 'src/services/permission/permission-api';
import { Notify, Dialog } from 'quasar';
import { t } from 'src/services/utils/i18n';

let permissionsData = ref([]);
let loading = ref(false);
let removingId = ref(null);

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
});

const columns = [
  {
    name: 'name',
    label: t('name'),
    align: 'left',
    field: 'name',
    format: val => val || t('ni'),
  },
  {
    name: 'actions',
    align: 'center',
    label: t('actions'),
    field: 'id',
    sortable: false,
  },
];

onMounted(async () => {
  await getPermissionsFunction();
});

async function getPermissionsFunction(props) {
  loading.value = true;
  try {
    mainPagination.value = props?.pagination || mainPagination.value;
    permissionsData.value = await getPermissions(mainPagination.value);
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  loading.value = false;
}

async function destroyPermissionFunction(id) {
  Dialog.create({
    title: t('warning'),
    message: t('confirm_remove'),
    cancel: true,
  }).onOk(async () => {
    removingId.value = id;
    try {
      await destroyPermission(id);
      getPermissionsFunction();

      Notify.create({
        message: t('removed_successfully'),
        type: 'positive',
      });
    } catch (e) {
      Notify.create({
        message: t('failed_to_remove'),
        type: 'negative',
      });
    }
    removingId.value = null;
  });
}
</script>

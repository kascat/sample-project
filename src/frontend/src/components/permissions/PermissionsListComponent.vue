<template>
  <q-table
    :rows="permissionsData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    :loading-label="t('loading')"
    :no-results-label="t('no_results')"
    :no-data-label="t('no_results')"
    binary-state-sort
    @request="fetchPermissions"
  >
    <template v-slot:top>
      <div class="full-width">
        <div class="row q-pt-sm q-col-gutter-md">
          <q-input
            v-model="mainPagination.name"
            :label="t('name')"
            class="col-xs-12 col-md-4"
            outlined
            dense
            debounce="500"
            @update:model-value="fetchPermissions()"
          />
        </div>
      </div>
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
    field: val => val,
    format: val => {
      const name = val.name || t('ni')
      const defaultMarc = val.default ? ('(' + t('default_permission') + ')') : ''

      return `${name} ${defaultMarc}`
    },
  },
  {
    name: 'abilities',
    label: t('permissions_label'),
    align: 'left',
    field: 'abilities',
    format: val => val?.length || 0,
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
  await fetchPermissions();
});

async function fetchPermissions(props) {
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
      fetchPermissions();

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

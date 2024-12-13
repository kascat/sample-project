<template>
  <q-table
    :title="t('users')"
    :rows="usersData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    :loading-label="t('loading')"
    :no-results-label="t('no_results')"
    :no-data-label="t('no_results')"
    binary-state-sort
    @request="fetchUsers"
  >
    <template v-slot:top>
      <div class="full-width">
        <div class="row">
          <div class="col">
            <h6 class="q-mt-none q-mb-none text-weight-regular">
              {{ t('users') }}
            </h6>
          </div>
          <div class="col">
            <q-btn
              icon="add"
              class="float-right"
              :label="t('register')"
              color="primary"
              outline
              :to="{ name: 'users_create' }"
            />
          </div>
        </div>
        <div class="row q-mt-md q-col-gutter-md">
          <q-input
            v-model="mainPagination.name"
            :label="t('name')"
            class="col-xs-12 col-md-4"
            outlined
            dense
            debounce="500"
            @update:model-value="fetchUsers()"
          />
          <q-input
            v-model="mainPagination.email"
            :label="t('email')"
            class="col-xs-12 col-md-4"
            outlined
            dense
            debounce="500"
            @update:model-value="fetchUsers()"
          />
          <q-select
            v-model="mainPagination.role"
            :label="t('role')"
            map-options
            emit-value
            hide-bottom-space
            clearable
            :options="rolesOptions"
            option-label="label"
            option-value="value"
            dense
            outlined
            class="col-xs-12 col-md-4"
            @update:model-value="fetchUsers()"
          />
        </div>
      </div>
    </template>

    <template v-slot:body-cell-user="props">
      <q-td key="user" :props="props">
        <q-item class="q-pa-none">
          <q-item-section>
            <q-item-label>{{ props.row.name }}</q-item-label>
            <q-item-label caption>{{ props.row.email }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-td>
    </template>

    <template v-slot:body-cell-role="props">
      <q-td key="role" :props="props">
        <q-item class="q-pa-none">
          <q-item-section>
            <q-item-label>{{ t(`user.role.${props.row.role}`) }}</q-item-label>
            <q-item-label caption>{{ formatDatetimeBR(props.row.created_at) }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-td>
    </template>

    <template v-slot:body-cell-status="props">
      <q-td key="status" :props="props">
        <q-chip
          text-color="white"
          :label="t(`user.status.${props.row.status}`)"
          :color="props.row.status === 'pending_password' ? 'warning' : props.row.status === 'active' ? 'positive' : 'negative'"
        />
      </q-td>
    </template>

    <template v-slot:body-cell-actions="props">
      <q-td key="actions" :props="props">
        <q-btn-group outline>
          <q-btn
            v-if="loggedUser.id !== props.row.id"
            outline
            :color="userIsBlocked(props.row) ? 'positive' : 'negative'"
            :icon="userIsBlocked(props.row) ? 'check' : 'close'"
            @click="changeStatus(props.row)"
            :loading="changingStatus"
          >
            <q-tooltip>
              {{ userIsBlocked(props.row) ? t('activate') : t('block') }}
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="primary"
            icon="edit"
            :to="{ name: 'users_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              {{ t('update') }}
            </q-tooltip>
          </q-btn>
          <q-btn
            v-if="loggedUser.id !== props.row.id"
            outline
            color="negative"
            icon="delete"
            :loading="removingId === props.row.id"
            :disable="removingId === props.row.id"
            @click="deleteUser(props.row.id)"
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
import { loggedUser } from 'src/boot/user';
import { getUsers, destroyUser, updateUser } from 'src/services/user/user-api';
import { t } from 'src/services/utils/i18n';
import { Notify, Dialog } from 'quasar';
import { ROLES } from 'src/constants/user_roles';
import { formatDatetimeBR } from 'src/services/utils/date';

const usersData = ref([]);
const loading = ref(false);
const removingId = ref(null);
const changingStatus = ref(false);

const rolesOptions = Object.values(ROLES).map((role) => ({
  label: t(`user.role.${role}`),
  value: role,
}));

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
});

const columns = [
  {
    name: 'user',
    label: t('name'),
    align: 'left',
    field: 'name',
  },
  {
    name: 'role',
    label: t('role'),
    align: 'left',
    field: 'role',
    format: val => t(`user.role.${val}`),
  },
  {
    name: 'status',
    label: t('status'),
    align: 'left',
    field: 'status',
    format: val => t(`user.status.${val}`),
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
  await fetchUsers();
});

async function fetchUsers(props) {
  loading.value = true;
  try {
    mainPagination.value = props?.pagination || mainPagination.value;
    usersData.value = await getUsers(mainPagination.value);
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  loading.value = false;
}

function userIsBlocked(user) {
  return [ 'blocked' ].includes(user.status);
}

function changeStatus(user) {
  Dialog.create({
    title: t('warning'),
    message: t('confirm_this_action'),
    cancel: true,
  }).onOk(async () => {
    changingStatus.value = true;
    try {
      await updateUser(user.id, { status: userIsBlocked(user) ? 'active' : 'blocked' });
      fetchUsers();

      Notify.create({
        message: t('action_completed'),
        type: 'positive',
      });
    } catch (e) {
      Notify.create({
        message: t('operation_failure'),
        type: 'negative',
      });
    }
    changingStatus.value = false;
  });
}

function deleteUser(id) {
  Dialog.create({
    title: t('warning'),
    message: t('confirm_remove'),
    cancel: true,
  }).onOk(async () => {
    removingId.value = id;
    try {
      await destroyUser(id);
      fetchUsers();

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

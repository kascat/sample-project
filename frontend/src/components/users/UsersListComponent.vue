<template>
  <q-table
    title="Usuários"
    :rows="usersData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    loading-label="Carregando..."
    no-results-label="Nenhum usuário encontrado"
    no-data-label="Nenhum usuário encontrado"
    binary-state-sort
    @request="getUsersFunction"
  >
    <template v-slot:top-right>
      <q-btn
        icon="add"
        label="Cadastrar"
        color="primary"
        outline
        :to="{ name: 'users_create' }"
      />
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
            :color="isBlocked(props.row) ? 'positive' : 'negative'"
            :icon="isBlocked(props.row) ? 'check' : 'close'"
            @click="changeStatus(props.row)"
            :loading="changingStatus"
          >
            <q-tooltip>
              {{ isBlocked(props.row) ? 'Ativar' : 'Bloquear' }}
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="primary"
            icon="edit"
            :to="{ name: 'users_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              Editar
            </q-tooltip>
          </q-btn>
          <q-btn
            v-if="loggedUser.id !== props.row.id"
            outline
            color="negative"
            icon="delete"
            :loading="removing"
            :disable="removing"
            @click="destroyUserFunction(props.row.id)"
          >
            <q-tooltip>
              Excluir
            </q-tooltip>
          </q-btn>
        </q-btn-group>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { loggedUser } from 'src/boot/user'
import { getUsers, destroyUser, updateUser } from 'src/services/user/user-api'
import { t } from 'src/services/utils/i18n'
import { Notify, Dialog } from 'quasar'

let usersData = ref([])
let loading = ref(false)
let removing = ref(false)
let changingStatus = ref(false)

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
})

const columns = [
  {
    name: 'name',
    label: 'Nome',
    align: 'left',
    field: 'name',
    format: val => val || 'N/I',
  },
  {
    name: 'email',
    label: 'E-mail',
    align: 'left',
    field: 'email',
    format: val => val || 'N/I',
  },
  {
    name: 'role',
    label: 'Tipo',
    align: 'left',
    field: 'role',
    format: val => t(`user.role.${val}`),
  },
  {
    name: 'status',
    label: 'Status',
    align: 'left',
    field: 'status',
    format: val => t(`user.status.${val}`),
  },
  {
    name: 'actions',
    align: 'center',
    label: 'Ações',
    field: 'id',
    sortable: false
  },
]

onMounted(async () => {
  await getUsersFunction()
})

async function getUsersFunction (props) {
  loading.value = true
  try {
    mainPagination.value = props?.pagination || mainPagination.value
    usersData.value = await getUsers(mainPagination.value)
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar usuários',
      type: 'negative'
    })
  }
  loading.value = false
}

function isBlocked (user) {
  return ['blocked', 'blocked_by_time'].includes(user.status)
}

function changeStatus (user) {
  Dialog.create({
    title: 'Atenção!',
    message: `Tem certeza que deseja ${isBlocked(user) ? 'ativar' : 'bloquear'} este usuário?`,
    cancel: true,
  }).onOk(async () => {
    changingStatus.value = true
    try {
      await updateUser(user.id, { status: isBlocked(user) ? 'active' : 'blocked' })
      getUsersFunction()

      Notify.create({
        message: `Usuário ${isBlocked(user) ? 'ativo' : 'bloqueado'} com sucesso!`,
        type: 'positive'
      })
    } catch (e) {
      Notify.create({
        message: `Falha ao ${isBlocked(user) ? 'ativar' : 'bloquear'} usuário!`,
        type: 'negative'
      })
    }
    changingStatus.value = false
  })
}

function destroyUserFunction (client) {
  Dialog.create({
    title: 'Atenção!',
    message: 'Tem certeza que deseja excluir este usuário?',
    cancel: true,
  }).onOk(async () => {
    removing.value = true
    try {
      await destroyUser(client)
      getUsersFunction()

      Notify.create({
        message: 'Usuário excluído com sucesso!',
        type: 'positive'
      })
    } catch (e) {
      Notify.create({
        message: 'Falha ao excluir usuário!',
        type: 'negative'
      })
    }
    removing.value = false
  })
}
</script>

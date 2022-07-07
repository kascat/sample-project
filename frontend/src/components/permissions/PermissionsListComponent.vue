<template>
  <q-table
    title="Permissões"
    :rows="permissionsData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    loading-label="Carregando..."
    no-results-label="Nenhuma permissão encontrada"
    no-data-label="Nenhuma permissão encontrada"
    binary-state-sort
    @request="getPermissionsFunction"
  >
    <template v-slot:top-right>
      <q-btn
        icon="add"
        label="Cadastrar"
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
              Editar
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="negative"
            icon="delete"
            :loading="removing"
            :disable="removing"
            @click="destroyPermissionFunction(props.row.id)"
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
import { getPermissions, destroyPermission } from 'src/services/permission/permission-api'
import {Notify, Dialog} from 'quasar'

let permissionsData = ref([])
let loading = ref(false)
let removing = ref(false)

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
    name: 'actions',
    align: 'center',
    label: 'Ações',
    field: 'id',
    sortable: false
  },
]

onMounted(async () => {
  await getPermissionsFunction()
})

async function getPermissionsFunction(props) {
  loading.value = true
  try {
    mainPagination.value = props?.pagination || mainPagination.value
    permissionsData.value = await getPermissions(mainPagination.value)
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar permissões!',
      type: 'negative'
    })
  }
  loading.value = false
}

async function destroyPermissionFunction(permission) {
  Dialog.create({
    title: 'Atenção!',
    message: 'Tem certeza que deseja excluir esta permissão?',
    cancel: true,
  }).onOk(async () => {
    removing.value = true
    try {
      await destroyPermission(permission)
      getPermissionsFunction()

      Notify.create({
        message: 'Permissão excluída com sucesso!',
        type: 'positive'
      })
    } catch (e) {
      Notify.create({
        message: 'Falha ao excluir permissão!',
        type: 'negative'
      })
    }
    removing.value = false
  })
}
</script>

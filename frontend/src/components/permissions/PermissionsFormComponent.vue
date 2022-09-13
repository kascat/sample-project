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
      Voltar
    </q-tooltip>
  </q-btn>
  <h4 class="q-mt-lg" v-if="!route.params.id">Criar permissão</h4>
  <h4 class="q-mt-lg" v-else>Editar permissão</h4>
  <q-form
    ref="permissionForm"
    @submit="submitFunction()"
  >
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 q-mr-md">
        <q-input
          label="Nome"
          v-model="permission.name"
          dense
          outlined
          color="primary"
          :rules="[val => !!val || 'Preenchimento obrigatório']"
        />
      </div>
    </div>
    <div>
      <q-checkbox
        :loading="loadingPermissions"
        class="q-mr-md q-mb-md"
        v-for="permission in permissionsOptions"
        :key="permission"
        v-model="abilitiesCache"
        :val="permission"
        :label="t(`permissions.${permission}`)"
        color="positive"
      />
    </div>
    <div align="right">
      <q-btn
        outline
        label="Salvar"
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
import { onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import {
  createPermission,
  updatePermission,
  getPermission,
  getAllPermission
} from 'src/services/permission/permission-api'
import { loggedUser } from 'boot/user'
import { Notify, Loading } from 'quasar'
import { formatResponseError } from "src/services/utils/error-formatter";
import { t } from 'src/services/utils/i18n'

const permission = ref({
  name: '',
  abilities: []
})

let abilitiesCache = ref([])
let saving = ref(false)
let loadingPermissions = ref(false)

const permissionsOptions = ref([])
const permissionForm = ref(null)
const router = useRouter()
const route = useRoute()

onMounted(async () => {
  if (route.params.id) {
    await getPermissionFunction()
  }

  searchAllPermissionsOptions()
})

async function submitFunction() {
  saving.value = true
  try {
    const validated = permissionForm.value.validate()
    if (validated) {
      permission.value.abilities = abilitiesCache
      const permissionToSave = { ...permission.value }
      !route.params.id ? await createPermission(permissionToSave) : await updatePermission(route.params.id, permissionToSave)

      Notify.create({
        message: !route.params.id ? 'Permissão criada com sucesso!' : 'Permissão editada com sucesso!',
        type: 'positive'
      })

      await router.push({ name: 'permissions' })
      if (loggedUser?.permission_id === permission.value?.id) {
        router.go()
      }
    }
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || 'Falha ao salvar permissão',
      type: 'negative'
    })
  }
  saving.value = false
}

async function getPermissionFunction() {
  Loading.show()
  try {
    const response = await getPermission(route.params.id)
    abilitiesCache.value = response.abilities || []
    permission.value = response
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar permissão!',
      type: 'negative'
    })
  }
  Loading.hide()
}
async function searchAllPermissionsOptions() {
  loadingPermissions.value = true
  try {
    const result = await getAllPermission()
    permissionsOptions.value = result

  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar permissões!',
      type: 'negative'
    })
  }
  loadingPermissions.value = false
}

</script>

<style scoped>

</style>

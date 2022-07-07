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
      Voltar
    </q-tooltip>
  </q-btn>
  <h4 class="q-mt-lg" v-if="!route.params.id">Criar usuário</h4>
  <h4 class="q-mt-lg" v-else>Editar usuário</h4>
  <q-form
    ref="userForm"
    @submit="submitUser()"
  >
    <div>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-py-xs q-mr-md q-mb-lg">
          <q-input
            label="Nome"
            v-model="user.name"
            hide-bottom-space
            dense
            outlined
            :rules="[val => !!val || 'Preenchimento obrigatório']"
          />
        </div>
        <div class="col q-mb-lg">
          <q-input
            label="E-mail"
            v-model="user.email"
            hide-bottom-space
            dense
            outlined
            :rules="[val => !!val || 'Preenchimento obrigatório']"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-py-xs q-mb-lg q-mr-md">
          <q-select
            label="Perfil"
            map-options
            emit-value
            hide-bottom-space
            clearable
            v-model="user.role"
            :options="rolesOptions"
            option-label="label"
            option-value="value"
            dense
            outlined
            :rules="[val => !!val || 'Preenchimento obrigatório']"
          />
        </div>
        <div class="col q-mb-lg">
          <q-select
            label="Permissão"
            v-model="user.permission_id"
            map-options
            emit-value
            hide-bottom-space
            clearable
            :options="permissionsOptions"
            :option-label="opt => opt.name || user.permission?.name || 'N/I'"
            option-value="id"
            dense
            outlined
            :loading="loadingPermissions"
            :rules="[val => !!val || 'Preenchimento obrigatório']"
            @filter="filterPermissions"
          />
        </div>
      </div>
      <div
        v-if="user.role === 'member'"
        class="row"
      >
        <div class="col-xs-12 col-sm-12 col-md-6 col-py-xs q-mb-lg q-mr-md">
          <q-input
            label="Tempo de login"
            suffix="mins"
            v-model="user.login_time"
            hide-bottom-space
            clearable
            dense
            outlined
          />
        </div>
        <div class="col q-mb-lg">
          <q-input
            label="Acessível até"
            v-model="user.expires_in"
            mask="##/##/#### ##:##"
            hide-bottom-space
            dense
            clearable
            outlined
          >
            <template v-slot:append>
              <q-icon
                :name="'event' + 'access_time'"
                class="cursor-pointer q-ml-md q-mr-md"
                @click="openDateTimeModal = true"
              />
            </template>
          </q-input>
        </div>
      </div>
    </div>
    <q-dialog v-model="openDateTimeModal">
      <q-card style="width: 645px; max-width: 80vw;">
        <div class="text-h6 q-ma-md">
          Acessível até:
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
            label="Fechar"
            dense
            outline
            color="negative"
            @click="openDateTimeModal = false"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog v-model="openPasswordModal">
      <q-card style="width: 660px; max-width: 80vw;">
        <div class="text-h6 q-ma-md">
          Defina sua senha:
        </div>
        <q-card-section>
          <div class="col-xs-12 col-sm-12 col-md-6 col-py-xs q-mb-lg q-mr-md">
            <div class="text-deep-orange text-bold">
              Aviso: Após o salvamento do usuário esta senha não será mais exibida.
            </div>
            <q-input
              v-model="user.password"
              label="Senha"
              outlined
              clearable
              dense
              hide-bottom-space
              :rules="[
                val => val && val.length >= 6 || 'A senha deve ter no mínimo 6 caracteres'
              ]"
              lazy-rules
            />
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn
            label="Fechar"
            dense
            outline
            color="negative"
            type="button"
            @click="openPasswordModal = false"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <div
      v-if="user.role === 'member'"
      align="left"
    >
      <q-btn
        outline
        label="Definir senha"
        icon="lock_open"
        color="positive"
        type="button"
        @click="openPasswordModal = true"
      />
      <q-chip
        v-if="user.password"
        color="positive"
        text-color="white"
        label="Senha definida"
      />
      <div class="text-deep-orange text-bold" v-if="!$route.params.id">
        Aviso: caso a senha não seja definida será enviado um e-mail para definição da senha.
      </div>
    </div>
    <div
      v-if="route.params.id"
      align="row" style="margin: 10px">
        <q-btn
          outline
          label="Enviar assinatura"
          icon="o_upload"
          color="primary"
          @click="showUpload = true"
        />
        <span
          class="q-mt-lg"
          style="margin: 10px; color: green"
          v-if="userSignature">
          Assinatura cadastrada
        </span>
        <span
          class="q-mt-lg"
          style="margin: 10px; color: #C10015"
          v-else>
          Não tem assinatura cadastrada
        </span>
    </div>
    <div>
      <q-uploader
        v-if="showUpload"
        ref="attachmentUploader"
        dense
        hide-bottom-space
        class="row"
        max-file-size="3000000"
        batch
        :url="`${uploadURL}/media`"
        :headers="[{name: 'Authorization', value: userToken()}]"
        label="Clique para selecionar ou arraste arquivos aqui"
        no-thumbnails
        auto-upload
        color="primary"
        accept=".jpg,.png,.jpeg,.gif"
        :form-fields="() => [
          {name: 'subject_id', value: user.id},
          {name: 'media_type', value: 'user_signature'}
        ]"
        @start="() => closeLabel = 'Cancelar'"
        @uploaded="onUploadAttachments"
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
import { createUser, updateUser, getUser } from 'src/services/user/user-api'
import { getPermissions } from 'src/services/permission/permission-api'
import { Notify, Loading } from 'quasar'
import { formatResponseError } from "src/services/utils/error-formatter";
import { formatDateBR } from 'src/services/utils/date'
import { userToken } from "src/services/utils/local-storage";
import { getMedia } from "src/services/media/media-api";

const router = useRouter()
const route = useRoute()
const  uploadURL = process.env.API_URL
let openDateTimeModal = ref(false)
let openPasswordModal = ref(false)
let showUpload = ref(false)

const rolesOptions = [
  {
    label: 'Administrador',
    value: 'admin'
  },
  {
    label: 'Membro',
    value: 'member'
  },
]

let user = ref({
  name: '',
  email: null,
  role: '',
  permission_id: null,
  password: null,
})

let permissionsOptions = ref([])
let loadingPermissions = ref(false)
let saving = ref(false)
let userSignature = ref(null)

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
})

const userForm = ref(null)

onMounted(async () => {
  if (route.params.id) {
    await getUserFunction()
  }
})

async function getSignatureFunction(userId){
  try {
    let result = await getMedia({
      media_type: 'user_signature',
      subject_id: userId
    })
    userSignature.value = result[0]
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || 'Falha ao carregar assinatura',
      type: 'negative'
    })
  }
}

async function onUploadAttachments(){
  showUpload.value = false
  getSignatureFunction(user.value.id)
}

async function submitUser() {
  saving.value = true
  try {
    const validated = userForm.value.validate()
    if (validated) {
      if (user.value.role !== 'member') {
        user.value.password = null
        user.value.expires_in = null
        user.value.login_time = null
      }
      const userToSave = { ...user.value }
      !route.params.id ? await createUser(userToSave) : await updateUser(route.params.id, userToSave)

      Notify.create({
        message: !route.params.id ? 'Usuário criado com sucesso!' : 'Usuário editado com sucesso!',
        type: 'positive'
      })

      router.push({ name: 'users' })
    }
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || 'Falha ao salvar usuário',
      type: 'negative'
    })
  }
  saving.value = false
}

async function getUserFunction() {
  Loading.show()
  try {
    const response = await getUser(route.params.id, {
      with: ['permission']
    })
    response.expires_in = formatDateBR(response.expires_in)
    user.value = response
    getSignatureFunction(user.value.id)
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar usuário!',
      type: 'negative'
    })
  }
  Loading.hide()
}

async function filterPermissions(val, update, abort) {
  loadingPermissions.value = true
  try {
    const result = await getPermissions({
      name: val,
      rowsPerPage: 25,
    })
    permissionsOptions.value = result
    update()
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar permissões!',
      type: 'negative'
    })
    abort()
  }
  loadingPermissions.value = false
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

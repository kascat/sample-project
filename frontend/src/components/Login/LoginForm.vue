<template>
  <q-form @submit="login()">
    <div class="login-container column justify-between">
      <div class="justify-center text-center">
        <h1 class="login-title">
          Bem-vindo
        </h1>
      </div>
      <div>
        <q-input
          v-model="formData.email"
          label="E-mail"
          type="email"
          dense
          hide-bottom-space
          :rules="[ val => val && val.length > 0 || 'O E-mail é obrigatório']"
        />
      </div>
      <div>
        <q-input
          v-model="formData.password"
          :type="passwordHidden ? 'password' : 'text'"
          label="Senha"
          dense
          hide-bottom-space
          :rules="[ val => val && val.length > 0 || 'A senha é obrigatória']"
        >
          <template v-slot:append>
            <q-icon
              :name="passwordHidden ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="passwordHidden = !passwordHidden"
            />
          </template>
        </q-input>
      </div>
      <div>
        <a
          class="forgot-password-text"
          @click="$emit('show-forgot-password')"
        >
          Esqueci minha senha
        </a>
      </div>
      <div class="text-right padding q-pa-sm">
        <q-btn
          type="submit"
          class="q-ma-sm"
          color="primary"
          outline
          label="Logar"
        />
      </div>
    </div>
  </q-form>
</template>

<script setup>
import { postLoginUser } from 'src/services/login/login-api.js'
import { onMounted, ref, reactive } from "vue"
import { loadLoggedUser, loggedUser, resetLoggedUser } from "boot/user"
import { useRouter } from "vue-router"
import { Notify, Loading } from "quasar"

const router = useRouter()

const formData = reactive({
  email: null,
  password: null
})

let passwordHidden = ref(true)

onMounted(() => {
  if (loggedUser.id) {
    resetLoggedUser()
  }
})

const login = async function () {
  formData.email = formData.email.toLowerCase()
  Loading.show()
  try {
    const loginInfo = await postLoginUser(formData)
    Loading.hide()
    localStorage.setItem('isUserLogged', 'true')
    localStorage.setItem('userToken', loginInfo.data.token)
    await loadLoggedUser()
    router.push({ name: 'dashboard' })
  } catch (error) {
    Loading.hide()
    const unauthorizedAccess = error.response?.status === 403
    const unauthorizedMessage = error.response?.data?.message
    const errorMessage = unauthorizedAccess && unauthorizedMessage ? unauthorizedMessage : 'Erro ao efetuar login'
    Notify.create({
      message: errorMessage,
      type: 'negative'
    })
  }
}
</script>

<style scoped>
.login-container {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 50vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.login-title {
  font-size: 2rem;
  line-height: 1rem;
}

.forgot-password-text {
  color: #1976d2;
  text-decoration: none;
  cursor: pointer;
}

.forgot-password-text:link {
  color: #1976d2;
}
</style>

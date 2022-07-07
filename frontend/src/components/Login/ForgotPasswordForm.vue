<template>
  <q-form @submit="forgotPassword()">
    <div class="forgot-password-container column justify-between">
      <div class="justify-center text-center">
        <h1 class="forgot-password-title">
          Esqueci minha senha
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
      <div class="text-right padding q-pa-sm">
        <q-btn
          class="q-ma-sm"
          label="Voltar"
          flat
          :disable="loading"
          :loading="loading"
          @click="$emit('hide-forgot-password')"
        />
        <q-btn
          type="submit"
          class="q-ma-sm"
          text-color="white"
          color="primary"
          :disable="loading"
          :loading="loading"
          label="Enviar"
        />
      </div>
    </div>
  </q-form>
</template>

<script>
import { postForgotPassword } from 'src/services/login/login-api.js'

export default {
  data () {
    return {
      formData: {},
      loading: false
    }
  },
  methods: {
    forgotPassword: async function () {
      this.loading = true
      try {
        const { email } = this.formData
        const { data } = await postForgotPassword(email)
        this.$q.notify({
          type: 'positive',
          message: data.message || 'Solicitação de recuperação de senha enviada com sucesso, verifique seu e-mail.',
        })
        this.loading = false
        this.$emit('hide-forgot-password')
      } catch (error) {
        const message = error.response.data.message
        this.$q.notify({
          type: 'negative',
          message: message || 'Não foi possivel solicitar recuperação de senha',
        })
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.forgot-password-container {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 50vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.forgot-password-title {
  font-size: 2rem;
  line-height: 1rem;
}
</style>

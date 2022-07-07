<template>
  <q-form @submit="resetPassword()">
    <div class="reset-password-container column justify-between">
      <div class="justify-center text-center">
        <h1 class="reset-password-title">
          Alterar senha
        </h1>
      </div>
      <div>
        <q-input
          v-model="formData.password"
          label="Senha"
          :type="passwordHidden ? 'password' : 'text'"
          dense
          hide-bottom-space
          :rules="[
            val => val && val.length > 0 || 'A senha é obrigatória',
            isValidPassword
          ]"
          lazy-rules
        >
          <template v-slot:append>
            <q-icon
              :name="passwordHidden ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="passwordHidden = !passwordHidden"
            />
          </template>
        </q-input>
        <div
          v-if="formData.password.length > 0"
          style="font-size:.7rem"
        >
          <div :class="atLeastOneLowerCaseCharacter ? 'valid' : 'invalid'">
            A senha deve conter ao menos um caractere minúsculo.
          </div>
          <div :class="atLeastOneUpperCaseCharacter ? 'valid' : 'invalid'">
            A senha deve conter ao menos um caractere maiúsculo
          </div>
          <div :class="atLeastOneSpecialCharacter ? 'valid' : 'invalid'">
            A senha deve conter ao menos um caractere especial. Ex: @ $ # ! % ( ) ^ ~ { } + - * / . ? & &lt; &gt;
          </div>
          <div :class="atLeastOneNumber ? 'valid' : 'invalid'">
            A senha deve conter ao menos um número.
          </div>
          <div :class="moreThan8Characters ? 'valid' : 'invalid'">
            A senha deve conter ao menos 8 caracteres.
          </div>
        </div>
      </div>
      <div>
        <q-input
          v-model="formData.password_confirmation"
          label="Confirmar Senha"
          :type="passwordConfirmHidden ? 'password' : 'text'"
          dense
          hide-bottom-space
          :rules="[
            val => val && val.length > 0 || 'A confirmação da senha é obrigatória',
            val => val === formData.password || 'A senha e a confirmação de senha devem ser iguais'
          ]"
          lazy-rules
        >
          <template v-slot:append>
            <q-icon
              :name="passwordConfirmHidden ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="passwordConfirmHidden = !passwordConfirmHidden"
            />
          </template>
        </q-input>
      </div>
      <div class="text-right padding q-pa-sm">
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
import { postResetPassword } from 'src/services/login/login-api.js'

export default {
  data () {
    return {
      formData: {
        password: '',
        password_confirmation: ''
      },
      loading: false,
      passwordHidden: true,
      passwordConfirmHidden: true
    }
  },
  computed: {
    moreThan8Characters: function () {
      return this.formData.password.length >= 8
    },
    atLeastOneLowerCaseCharacter: function () {
      return /[a-z]/.test(this.formData.password)
    },
    atLeastOneNumber: function () {
      return /[1-9]/.test(this.formData.password)
    },
    atLeastOneUpperCaseCharacter: function () {
      return /[A-Z]/.test(this.formData.password)
    },
    atLeastOneSpecialCharacter: function () {
      return /[@$#!%()^~{}+\-*/.?&<>]+/.test(this.formData.password)
    }
  },
  methods: {
    resetPassword: async function () {
      this.loading = true
      try {
        const { data } = await postResetPassword(this.formData, this.$route.params.token)
        this.$q.notify({
          type: 'positive',
          message: data.message || 'Senha alterada com sucesso',
        })
        this.loading = false

        this.$router.push({ name: 'login' })
      } catch (error) {
        const message = error.response.data.message
        this.$q.notify({
          type: 'negative',
          message: message || 'Não foi possivel definir sua senha',
        })
        this.loading = false
      }
    },
    isValidPassword: (val) => {
      return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$#!%()^~{}+\-*/.?&<>])[A-Za-z\d@$#!%()^~{}+\-*/.?&<>]{8,}$/.test(val) || 'A senha deve conter no mínimo oito caracteres, com pelo menos um número e um caractere especial!'
    }
  }
}
</script>

<style scoped>
.valid {
  color: green;
}
.valid::before {
  content: "✔";
}
.invalid {
  color: #c10015;
}
.reset-password-container {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 50vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.reset-password-title {
  font-size: 2rem;
  line-height: 1rem;
}
</style>

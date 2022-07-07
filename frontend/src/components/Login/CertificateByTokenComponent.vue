<template>
  <div class="login-page row items-center justify-center">
    <div class="col-xs-6">
      <div class="row items-center justify-center">
        <div
          v-if="!certificateData"
          class="image-container">
          <img
            src="~assets/logo.jpeg"
            alt="Logo image"
            style="max-height:70px;"
          >
        </div>
        <q-form @submit="certificateByTokenForm">
          <div
            v-if="!certificateData"
            class="certificate-validate column justify-between"
          >
            <div class="justify-center text-center">
              <h1 class="validate-title">
                Validação de certificado
              </h1>
            </div>
            <div>
              <q-input
                v-model="formToken"
                label="Token"
                type="token"
                dense
                hide-bottom-space
                :rules="[ val => val && val.length > 0 || 'O Token é obrigatório']"
              />
            </div>
            <div class="text-right padding q-pa-sm">
              <q-btn
                type="submit"
                class="q-ma-sm"
                color="primary"
                outline
                label="Buscar"
                :loading="loading"
                @click="getCertificateByTokenFunction()"
              />
            </div>
          </div>
          <certificate-by-token-list-component
            v-else
            :certificate="certificateData"
            @submit="setNullCertificateValues()"
          />
        </q-form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import { useRoute } from "vue-router"
import { getCertificateByToken } from "src/services/certificate/certificate-api";
import { Notify } from "quasar";
import { formatResponseError } from "src/services/utils/error-formatter";
import CertificateByTokenListComponent from "components/Login/CertificateByTokenListComponent";

const route = useRoute()
let loading = ref(false)
const formToken = ref(null)

const certificateData = ref(null)

onMounted(async () => {
  formToken.value = route.query.token
  if (formToken.value) {
    await getCertificateByTokenFunction()
  }
})

async function setNullCertificateValues() {
  certificateData.value = null
  formToken.value = null
}

async function getCertificateByTokenFunction() {
  loading.value = true

  try {
    const result = await getCertificateByToken(formToken.value)
    certificateData.value = result
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || 'Falha ao buscar o certificado',
      type: 'negative'
    })
  }
  loading.value = false
}
</script>

<style scoped>
.certificate-validate {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 30vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.validate-title {
  font-size: 2rem;
  line-height: 2rem;
}

.login-page {
  min-height: 100vh;
  min-width: 100vw;
  padding: 20px 20px 5px 20px;
  background: rgb(0, 102, 153);
  background: radial-gradient(#9bc43e 0%, #0c5ca9 100%);
  display: flex;
}

.image-container {
  background: #092f09;
  border-radius: 15px;
  margin-bottom: 15px;
  padding: 10px;
  z-index: 2;
}
</style>

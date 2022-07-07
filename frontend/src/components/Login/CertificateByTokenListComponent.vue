<template>
  <div class="login-page row items-center justify-center">
    <div class="col-xs-11">
      <div class="row items-center justify-center">
        <div class="image-container">
          <img
            src="~assets/logo.jpeg"
            alt="Logo image"
            style="max-height:60px;"
          >
        </div>
      <div class="certificate-validate column justify-between">
        <div class="justify-center text-center">
          <h1 class="login-title">
            Certificado N° {{ certificate.simpleValues['numero-certificado'] }}
          </h1>
        </div>
        <q-separator color="primary" size="3px"/>
        <div class="row q-pa-lg">
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Tipo de tratamento: {{ certificate.simpleValues['tipo-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Sequência de tratamento: {{ certificate.simpleValues['sequencia-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Data do tratamento: {{ certificate.simpleValues['data-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Hora do tratamento: {{ certificate.simpleValues['hora-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Hora inical do tratamento: {{ certificate.simpleValues['hora-inicio-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Hora final do tratamento: {{ certificate.simpleValues['hora-final-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Tempo de tratamento: {{ certificate.simpleValues['duracao-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Temperatura de tratamento: {{ certificate.simpleValues['temperatura-trat'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Nome do cliente: {{ certificate.simpleValues['nome-cliente'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            CPF/CNPJ: {{ certificate.simpleValues['documento-cliente'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Telefone: {{ certificate.simpleValues['telefone-cliente'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            CEP: {{ certificate.simpleValues['cep-cliente'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Endereço: {{ certificate.simpleValues['endereco-cliente'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Nome importador: {{ certificate.simpleValues['nome-importador'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Endereço importador: {{ certificate.simpleValues['endereco-importador'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Local de origem: {{ certificate.simpleValues['local-origem'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Local de destino: {{ certificate.simpleValues['local-destino'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Peso bruto: {{ certificate.simpleValues['peso-bruto'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Peso líquido: {{ certificate.simpleValues['peso-liquido'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Meio de transporte: {{ certificate.simpleValues['meio-transporte'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            B.L./Fatura: {{ certificate.simpleValues['fatura'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Número e descrição volume: {{ certificate.simpleValues['numero-descricao-volume'] }}
          </div>
          <div class="col-12 col-md-4 q-py-xs q-px-sm">
            Marcas distintivas: {{ certificate.simpleValues['marcas-distintivas'] }}
          </div>
        </div>
        <q-separator/>
        <div class="row q-pa-lg">
          <q-table
            title="Produtos do certificado"
            :rows="certificate.blockValues['produtos']"
            :columns="columns"
            row-key="id"
            :loading="loading"
            loading-label="Carregando..."
            no-results-label="Nenhum produto encontrado"
            no-data-label="Nenhum produto encontrado"
            binary-state-sort
            flat
            :pagination="{ rowsPerPage: 0 }"
            hide-pagination
          />
        </div>
        <q-separator color="primary" size="3px"/>
        <div class="text-center q-my-md">
          <q-img
            :src="certificate.imageValues['assinatura-tecnico']"
            style="width: 8cm; border-bottom: 1px solid black"
          />
          <br> {{ certificate.simpleValues['nome-tecnico'] }} - {{ certificate.simpleValues['registro-tecnico'] }}
        </div>
        <div align="right" style="margin: 10px">
          <q-btn
            color="primary"
            label="Procurar outro certificado"
            @click="$emit('submit', props.certificado)"
          />
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>

const props = defineProps({
  certificate: {
    type: Object,
    required: true
  }
})
const columns = [
  {
    name: 'product',
    label: 'Produto',
    align: 'left',
    field: row => row.produto,
  },
  {
    name: 'quantidade',
    label: 'Quantidade',
    align: 'center',
    field: row => row.quantidade,
  },
  {
    name: 'volume',
    label: 'Volume',
    align: 'center',
    field: row => row.volume,
    format: val => val ? `${val} M³` : 'N/I',
  },
]

</script>

<style scoped>
.certificate-validate {
  padding: 20px;
  border-radius: 10px;
  background: white;
  min-height: 20vh;
  display: flex;
  -webkit-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 4px 15px 0px rgba(0, 0, 0, 0.75);
}

.login-title {
  font-size: 2rem;
  line-height: 1rem;
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

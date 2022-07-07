import axios from 'axios'

export const locationFromZipCode = async (zipCode) => {
  const axiosClient = axios.create()
  delete axiosClient.defaults.headers.common.Authorization

  try {
    const { data } = await axiosClient.get(`https://viacep.com.br/ws/${zipCode}/json/`)

    if ('erro' in data) {
      throw new Error('CEP não encontrado')
    }

    return {
      zipcode: data.cep,
      city: data.localidade,
      state: data.uf,
      street: data.logradouro,
      neighborhood: data.bairro,
      complement: data.complemento,
      areacode: data.ddd,
      gia: data.gia, // ICMS (somente SP)
      ibge: data.ibge,
      siafi: data.siafi
    }
  } catch (e) {
    throw new Error(e)
  }
}

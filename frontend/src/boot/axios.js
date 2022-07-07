import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { resetUserInLocalStorage } from './user'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: process.env.API_URL })

api.interceptors.request.use(
  function (config) {
    config.headers.Accept = 'application/json'
    config.headers['Content-Type'] = 'application/json'

    const token = localStorage.getItem('userToken')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }

    return config
  },
  error => {
    return Promise.reject(error)
  }
)

api.interceptors.response.use(
  response => {
    return response
  },
  error => {
    const isUserLogged = localStorage.getItem('isUserLogged')
    const userToken = localStorage.getItem('userToken')

    if (
      error.response?.status === 401 &&
      (isUserLogged || userToken)
    ) {
      resetUserInLocalStorage()
      window.location.reload()
    }
    return Promise.reject(error)
  }
)

export default boot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

const get = async (path, params) => {
  return api.get(path, { params })
}
const show = async (path, params) => {
  return api.get(path, { params })
}
const post = async (path, params) => {
  return api.post(path, params)
}
const put = async (path, params) => {
  return api.put(path, params)
}
const destroy = async path => {
  return api.delete(path)
}


export {
  api,
  get,
  show,
  post,
  put,
  destroy
}

import { api, post } from 'boot/axios'

export const postLoginUser = async (data) => {
  return await post('/login', data)
}

export const postForgotPassword = async (email) => {
  return await post('/forgot-password', { email })
}

export const postResetPassword = async (passwords, token) => {
  return await api.post('/reset-password', passwords, { headers: { Authorization: `Bearer ${token}` } })
}

export const postLogoutUser = async () => {
  return await post('/logout',[])
}

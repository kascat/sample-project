import { get, post, put, destroy } from 'boot/axios'

export const getUsers = async (params = { page: '', rowsPerPage: 0 }) => {
  params.perPage = params.rowsPerPage || 0
  const { data } = await get('/users', params)
  params.rowsNumber = data.total
  return data.data
}

export const getUser = async (id, params) => {
  const { data } = await get(`/users/${id}`, params)
  return data
}

export const createUser = async user => {
  const { data } = await post('/users', user)
  return data
}

export const updateUser = async (id, user) => {
  const { data } = await put(`/users/${id}`, user)
  return data
}

export const destroyUser = async id => {
  await destroy(`/users/${id}`)
}

export const getLoggedUser = async () => {
  const { data } = await get('/users/logged-user')
  return data
}

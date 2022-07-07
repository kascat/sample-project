import { get, post, put, destroy } from 'boot/axios'

export const getPermissions = async (params = { page: '', rowsPerPage: 0 }) => {
  params.perPage = params.rowsPerPage || 0
  const { data } = await get('/permissions', params)
  params.rowsNumber = data.total
  return data.data
}

export const getPermission = async (id, params) => {
  const { data } = await get(`/permissions/${id}`, params)
  return data
}

export const createPermission = async permission => {
  const { data } = await post('/permissions', permission)
  return data
}

export const updatePermission = async (id, permission) => {
  const { data } = await put(`/permissions/${id}`, permission)
  return data
}

export const destroyPermission = async id => {
  await destroy(`/permissions/${id}`)
}

export const getAllPermission = async ()=> {
   const { data } = await get('/permissions/all-permission')
   return data
 }

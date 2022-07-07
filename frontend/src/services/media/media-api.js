import { get } from 'boot/axios'

export const getMedia = async (params = { page: '', rowsPerPage: 0 }) => {
  params.perPage = params.rowsPerPage || 0
  const { data } = await get('/media', params)
  params.rowsNumber = data.total
  return data.data
}

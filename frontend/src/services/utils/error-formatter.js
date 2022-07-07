export const formatResponseError = (error) => {
  const errorData = error.response?.data || {}
  const propErrors = Object.keys(errorData)
  return errorData?.[propErrors?.[0]]
}

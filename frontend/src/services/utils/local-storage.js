export const userToken  = () => {
  let token = localStorage.getItem('userToken')
  return `Bearer ${token}`
}


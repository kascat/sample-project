export const formatter = (num) => {
  let formatedNumber = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(parseFloat(num))

  return formatedNumber
}

export const parseDate = datetime => {
  if (datetime.includes('/')) {
    const splitedDatetime = datetime.split(' ')

    const splitedDate = splitedDatetime[0].split('/')

    splitedDatetime[1] = splitedDatetime[1] ? ` ${splitedDatetime[1]}` : ''
    return Date.parse(`${splitedDate[2]}-${splitedDate[1]}-${splitedDate[0]}${splitedDatetime[1]}`)
  }

  return Date.parse(datetime)
}

export const formatDateBR = (datetime) => {
  if (!datetime) {
    return datetime
  }

  if (datetime.includes('/')) {
    return datetime
  }

  const splitedDatetime = datetime.split(' ')

  const date = splitedDatetime[0].split('-').reverse().join('/')

  const time = splitedDatetime[1] ? ` ${splitedDatetime[1]}` : ''
  return `${date}${time}`
}

export const validateTime = (val) => {
  val = val || ''

  if (val.length !== 5) {
    return false
  }

  const splittedTime = val.split(':')
  const hour = splittedTime[0]
  const minute = splittedTime[1]

  return (hour >= 0 && hour <= 23) && (minute >= 0 && minute <= 59)
}

export const calculateIntervalInMinutes = (initialHour, finalHour) => {
  initialHour = initialHour || '00:00'
  finalHour = finalHour || '00:00'

  const finalDate = initialHour <= finalHour ? '2000-01-01' : '2000-01-02'

  const timeInterval = (new Date(`${finalDate} ${finalHour}`)) - (new Date(`2000-01-01 ${initialHour}`))

  return timeInterval / 60000
}

export const formatDatetimeBR = (datetime) => {
  const splitedDate = String(datetime).split('T')[0]
  const finalDate = formatDateBR(splitedDate)
  return finalDate
}

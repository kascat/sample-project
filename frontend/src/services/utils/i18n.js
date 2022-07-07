import { useI18n } from 'vue-i18n'

export const t = (labelToTranslate) => {
  const { messages, locale } = useI18n()
  const localeMessages = messages.value?.[locale.value]
  const keys = (labelToTranslate || '').split('.')

  let currentKey = labelToTranslate
  const translatedLabel = keys.reduce((ac, key, i, arr) => {
    currentKey = currentKey.slice(key.length + 1)
    const keyMessage = ac?.[key]?.[currentKey]

    if (!keyMessage) {
      return ac?.[key]
    }

    arr.splice(i)

    return keyMessage
  }, localeMessages)

  return translatedLabel !== undefined ? translatedLabel : labelToTranslate;
}

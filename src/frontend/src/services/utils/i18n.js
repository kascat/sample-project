import { useI18n } from 'vue-i18n';

let appMessages;
let appLocale;

export const t = (labelToTranslate, data = {}) => {
  if (!appMessages || !appLocale) {
    const { messages, locale } = useI18n();
    appMessages = messages;
    appLocale = locale;
  }

  const localeMessages = appMessages.value?.[appLocale.value];
  const keys = (labelToTranslate || '').split('.');

  let currentKey = labelToTranslate;
  let translatedLabel = keys.reduce((ac, key, i, arr) => {
    currentKey = currentKey.slice(key.length + 1);
    const keyMessage = ac?.[key]?.[currentKey];

    if (!keyMessage) {
      return ac?.[key];
    }

    arr.splice(i);

    return keyMessage;
  }, localeMessages);

  if (undefined === translatedLabel) {
    return labelToTranslate;
  }

  for (const key in data) {
    translatedLabel = translatedLabel.replace(`{${key}}`, data[key]);
  }

  return translatedLabel;
};

export const tc = (labelToTranslate, count = 1, data = {}) => {
  if (+count === 1) {
    return t(`${labelToTranslate}.singular`, data);
  }

  data.count = count;

  return t(`${labelToTranslate}.plural`, data);
};

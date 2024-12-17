import { boot } from 'quasar/wrappers';
import axios from 'axios';
import { resetUserInLocalStorage } from './user';

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: process.env.API_URL });

api.interceptors.request.use(
  function (config) {
    config.headers.Accept = 'application/json';
    config.headers['Content-Type'] = 'application/json';

    const token = localStorage.getItem('userToken');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }

    return config;
  },
  error => {
    return Promise.reject(error);
  },
);

api.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    const isUserLogged = localStorage.getItem('isUserLogged');
    const userToken = localStorage.getItem('userToken');

    if (
      error.response?.status === 401 &&
      (isUserLogged || userToken)
    ) {
      resetUserInLocalStorage();
      window.location.reload();
    }
    return Promise.reject(error);
  },
);

export default boot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios;
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api;
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
});

const get = async (path, params) => {
  return api.get(path, { params });
};

const show = async (path, params) => {
  return api.get(path, { params });
};

const post = async (path, params, config) => {
  return api.post(path, params, config);
};

const put = async (path, params, config) => {
  return api.put(path, params, config);
};

const destroy = async path => {
  return api.delete(path);
};

const download = async (path, params = {}, method = 'GET') => {
  const api = axios.create({ baseURL: process.env.API_URL });
  const userToken = localStorage.getItem('userToken');

  return api({
    url: path,
    method,
    responseType: 'blob',
    headers: { Authorization: `Bearer ${userToken}` },
    params,
  }).then(response => {
    const link = document.createElement('a');

    link.setAttribute(
      'href',
      window.URL.createObjectURL(new Blob([ response.data ])),
    );

    link.setAttribute(
      'download',
      getFileNameFromContentDisposition(response.headers?.['content-disposition']),
    );

    document.body.appendChild(link);

    link.click();
  }).catch(async error => {
    const { response } = error;
    const isJsonBlob = (data) => data instanceof Blob && data.type === 'application/json';
    const responseData = isJsonBlob(response?.data) ? await (response?.data)?.text() : response?.data || {};

    error.response.data = (typeof responseData === 'string') ? JSON.parse(responseData) : responseData;

    throw error;
  });
};

export {
  api,
  get,
  show,
  post,
  put,
  destroy,
  download,
};

function getFileNameFromContentDisposition(contentDisposition) {
  const currentTime = Date.now();
  const defaultName = `download-${currentTime}`;

  if (!contentDisposition) {
    return defaultName;
  }

  // Primeiro, tenta extrair o valor de filename* (que pode estar codificado)
  const filenameStarMatch = contentDisposition.match(/filename\*\s*=\s*utf-8''(.+)/i);

  if (filenameStarMatch) {
    return decodeURIComponent(filenameStarMatch[1]);
  }

  // Caso contrário, tenta extrair o valor de filename
  const filenameMatch = contentDisposition.match(/filename\s*=\s*"?(.*?)"?($|;)/i);

  if (filenameMatch) {
    return filenameMatch[1];
  }

  return defaultName;
}

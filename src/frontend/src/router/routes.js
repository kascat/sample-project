import {
  checkIfLoggedUserHasAllAbilities,
  checkIfLoggedUserHasAnyAbilities,
} from 'boot/user';
import { ABILITIES } from 'src/constants/abilities';

const redirectToHomeIfLogged = (to, from, next) => {
  if (localStorage.getItem('isUserLogged')) {
    next('/');
  } else {
    next();
  }
};

const handleAuthAndRedirection = (to, from, next) => {
  if (!localStorage.getItem('isUserLogged')) {
    const path = to.fullPath;
    const pathEncoded = encodeURIComponent(path);

    next({ name: 'login', query: { 'redirect_uri': pathEncoded } });

    return;
  }

  if (to.redirectedFrom) {
    next();

    return;
  }

  const redirectPathEncoded = from.query.redirect_uri;
  const redirectPath = decodeURIComponent(redirectPathEncoded || '');

  next(redirectPath || null);
};

const checkLoggedUserAbilities = (to, from, next) => {
  if (!to.meta.allAbilities && !to.meta.anyAbilities) {
    next();

    return;
  }

  if (
    checkIfLoggedUserHasAllAbilities(to.meta.allAbilities) ||
    checkIfLoggedUserHasAnyAbilities(to.meta.anyAbilities)
  ) {
    next();

    return;
  }

  next({ name: 'home' });
};

/**
 * Descrição das habilidades para acesso às rotas.
 * O mapeamento da rota deve possuir uma das definições abaixo no "meta":
 * - anyAbilities: Usuário deve possuir ao menos uma das habilidades informadas.
 *   Se o array for vazio ou conter valor nulo a rota será habilitada.
 * - allAbilities: Usuário deve possuir todas as habilidades informadas.
 *   Se o array for vazio ou conter somente um valor nulo a rota será habilitada.
 **/

const permissions = [
  {
    path: '/permissions',
    name: 'permissions',
    component: () => import('pages/Permissions/PermissionsListPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
  {
    path: '/permissions/create',
    name: 'permissions_create',
    component: () => import('pages/Permissions/PermissionsFormPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
  {
    path: '/permissions/update/:id',
    name: 'permissions_update',
    component: () => import('pages/Permissions/PermissionsFormPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
];

const users = [
  {
    path: '/users',
    name: 'users',
    component: () => import('pages/Users/UsersListPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
  {
    path: '/users/create',
    name: 'users_create',
    component: () => import('pages/Users/UsersFormPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
  {
    path: '/users/update/:id',
    name: 'users_update',
    component: () => import('pages/Users/UsersFormPage'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
];

// const account = [
//   {
//     path: '/account',
//     name: 'account',
//     component: () => import('pages/Account/ManageAccountPage'),
//     beforeEnter: checkLoggedUserAbilities,
//     meta: { allAbilities: [ ABILITIES.MANAGE_ACCOUNT ] },
//   },
// ];

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout'),
    beforeEnter: handleAuthAndRedirection,
    children: [
      {
        path: '',
        name: 'home',
        redirect: '/dashboard',
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/Dashboard/DashboardPage'),
      },
      ...permissions,
      ...users,
      // ...account,
    ],
  },
  {
    path: '/',
    beforeEnter: redirectToHomeIfLogged,
    component: () => import('layouts/PublicLayout'),
    children: [
      {
        path: 'register',
        name: 'register',
        component: () => import('pages/Auth/RegisterPage'),
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    beforeEnter: redirectToHomeIfLogged,
    component: () => import('pages/Auth/LoginPage'),
  },
  {
    path: '/password-setting/:token',
    name: 'password_setting',
    beforeEnter: redirectToHomeIfLogged,
    component: () => import('pages/Auth/PasswordSettingPage'),
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Errors/Error404Page'),
  },
];

export default routes;

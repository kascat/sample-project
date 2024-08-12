import {
  checkIfLoggedUserHasAllAbilities,
  checkIfLoggedUserHasAnyAbilities,
} from 'boot/user';
import { ABILITIES } from 'src/constants/abilities';

const redirectToDashboardIfLogged = (to, from, next) => {
  if (localStorage.getItem('isUserLogged')) {
    next('/');
  } else {
    next();
  }
};

const redirectToLoginIfNotLogged = (to, from, next) => {
  if (localStorage.getItem('isUserLogged')) {
    next();
  } else {
    next('/login');
  }
};

const checkLoggedUserAbilities = (to, from, next) => {
  if (
    checkIfLoggedUserHasAllAbilities(to.meta.allAbilities) ||
    checkIfLoggedUserHasAnyAbilities(to.meta.anyAbilities)
  ) {
    next();
  } else {
    next('/');
  }
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
    component: () => import('pages/Permissions/PermissionsList'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
  {
    path: '/permissions/create',
    name: 'permissions_create',
    component: () => import('pages/Permissions/PermissionsForm'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
  {
    path: '/permissions/update/:id',
    name: 'permissions_update',
    component: () => import('pages/Permissions/PermissionsForm'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.PERMISSIONS ] },
  },
];

const users = [
  {
    path: '/users',
    name: 'users',
    component: () => import('pages/Users/UsersList'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
  {
    path: '/users/create',
    name: 'users_create',
    component: () => import('pages/Users/UsersForm'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
  {
    path: '/users/update/:id',
    name: 'users_update',
    component: () => import('pages/Users/UsersForm'),
    beforeEnter: checkLoggedUserAbilities,
    meta: { allAbilities: [ ABILITIES.USERS ] },
  },
];

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout'),
    beforeEnter: redirectToLoginIfNotLogged,
    children: [
      {
        path: '',
        name: 'home',
        redirect: '/dashboard',
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/Dashboard/Index'),
      },
      ...permissions,
      ...users,
    ],
  },
  {
    path: '/login',
    name: 'login',
    beforeEnter: redirectToDashboardIfLogged,
    component: () => import('pages/Auth/PageLogin'),
  },
  {
    path: '/reset-password/:token',
    name: 'reset_password',
    beforeEnter: redirectToDashboardIfLogged,
    component: () => import('pages/Auth/ResetPasswordPage'),
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Errors/Error404'),
  },
];

export default routes;

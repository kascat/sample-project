import { loggedUser } from '../boot/user'

const redirectToDashboardIfLogged = (to, from, next) => {
  if (localStorage.getItem('isUserLogged')) {
    next('/')
  } else {
    next()
  }
}

const redirectToLoginIfNotLogged = (to, from, next) => {
  if (localStorage.getItem('isUserLogged')) {
    next()
  } else {
    next('/login')
  }
}

const checkPermission = (to, from, next) => {
  if (loggedUser.permission?.abilities?.includes(to.meta.permission)) {
    next()
  } else {
    next('/')
  }
}

const permissions = [
  {
    path: '/permissions',
    name: 'permissions',
    component: () => import('pages/Permissions/PermissionsList'),
    beforeEnter: checkPermission,
    meta: { permission: 'permissions' }
  },
  {
    path: '/permissions/create',
    name: 'permissions_create',
    component: () => import('pages/Permissions/PermissionsForm'),
    beforeEnter: checkPermission,
    meta: { permission: 'permissions' }
  },
  {
    path: '/permissions/update/:id',
    name: 'permissions_update',
    component: () => import('pages/Permissions/PermissionsForm'),
    beforeEnter: checkPermission,
    meta: { permission: 'permissions' }
  }
]

const users = [
  {
    path: '/users',
    name: 'users',
    component: () => import('pages/Users/UsersList'),
    beforeEnter: checkPermission,
    meta: { permission: 'users' }
  },
  {
    path: '/users/create',
    name: 'users_create',
    component: () => import('pages/Users/UsersForm'),
    beforeEnter: checkPermission,
    meta: { permission: 'users' }
  },
  {
    path: '/users/update/:id',
    name: 'users_update',
    component: () => import('pages/Users/UsersForm'),
    beforeEnter: checkPermission,
    meta: { permission: 'users' }
  }
]

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout'),
    beforeEnter: redirectToLoginIfNotLogged,
    children: [
      {
        path: '',
        name: 'home',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/Index')
      },
      ...permissions,
      ...users,
    ]
  },
  {
    path: '/login',
    name: 'login',
    beforeEnter: redirectToDashboardIfLogged,
    component: () => import('pages/Login/PageLogin')
  },
  {
    path: '/reset-password/:token',
    name: 'reset_password',
    beforeEnter: redirectToDashboardIfLogged,
    component: () => import('pages/Login/ResetPasswordPage')
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404')
  },
]

export default routes

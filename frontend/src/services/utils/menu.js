import { loggedUser } from '../../boot/user'

const generalItems = [
  {
    label: 'Dashboard',
    icon: 'o_dashboard',
    to: {
      name: 'dashboard'
    },
    permission: "allowed"
  },
  {
    label: 'Acesso ao Sistema',
    icon: 'o_manage_accounts',
    children: [
      {
        label: 'Usuários',
        icon: 'o_account_circle',
        to: {
          name: 'users'
        },
        permission: 'users'
      },
      {
        label: 'Permissões',
        icon: 'fingerprint',
        to: {
          name: 'permissions'
        },
        permission: 'permissions'
      }
    ]
  },
]

export const generateMenu = () => {
  const filteredMenu = []
  const abilities = [
    "allowed",
    ...(loggedUser.permission?.abilities || [])
  ]
  for (const menuItem of generalItems) {
    if (abilities.includes(menuItem.permission)) {
      filteredMenu.push(menuItem)
      continue
    }

    if (menuItem.children) {
      menuItem.children = menuItem.children.filter(children => abilities.includes(children.permission))

      if (menuItem.children.length > 0) {
        filteredMenu.push(menuItem)
      }
    }
  }
  return filteredMenu
}

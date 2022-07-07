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
    label: 'Clientes',
    icon: 'people_outline',
    children: [
      {
        label: 'Listagem',
        icon: 'list_alt',
        to: {
          name: 'clients'
        },
        permission: 'clients'
      },
      {
        label: 'Contatos',
        icon: 'o_contact_phone',
        to: {
          name: 'contacts'
        },
        permission: 'contacts'
      },
      {
        label: 'Contratos',
        icon: 'o_fact_check',
        to: {
          name: 'contracts'
        },
        permission: 'contracts'
      }
    ]
  },
  {
    label: 'Certificados fito',
    icon: 'o_article',
    children: [
      {
        label: 'Agentes autorizados',
        icon: 'o_verified',
        to: {
          name: 'client_agents'
        },
        permission: 'clientAgents'
      },
      {
        label: 'Certificados',
        icon: 'o_card_membership',
        to: {
          name: 'certificates'
        },
        permission: 'certificates'
      }
    ]
  },
  {
    label: 'Filiais',
    icon: 'o_cabin',
    to: {
      name: 'companies'
    },
    permission: 'companies'
  },
  {
    label: 'Madeireiros',
    icon: 'o_factory',
    to: {
      name: 'loggers'
    },
    permission: 'loggers'
  },
  {
    label: 'Produtos',
    icon: 'o_fence',
    to: {
      name: 'products'
    },
    permission: 'products'
  },
  {
    label: 'Tipos de Tratamento',
    icon: 'o_science',
    to: {
      name: 'treatment_type'
    },
    permission: 'treatmentTypes'
  },
  {
    label: 'Unidades de Tratamento',
    icon: 'o_warehouse',
    to: {
      name: 'treatment_unities'
    },
    permission: 'treatmentUnities'
  },
  {
    label: 'Solicitação de Tratamento',
    icon: 'o_content_paste',
    to: {
      name: 'treatment_requests'
    },
    permission: 'treatmentRequests'
  },
  {
    label: 'Ordem de Serviço',
    icon: 'o_ballot',
    to: {
      name: 'service_orders'
    },
    permission: 'serviceOrders'
  },
  {
    label: 'Tipo de Pagamento',
    icon: 'o_local_atm',
    to: {
      name: 'billing_type'
    },
    permission: 'billingTypes'
  },
  {
    label: 'Templates',
    icon: 'o_wysiwyg',
    to: {
      name: 'templates'
    },
    permission: 'templates'
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

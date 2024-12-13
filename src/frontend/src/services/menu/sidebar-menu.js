import {
  checkIfLoggedUserHasAnyAbilities,
  checkIfLoggedUserHasAllAbilities,
} from 'boot/user';
import { ABILITIES } from 'src/constants/abilities';

/**
 * Descrição das habilidades para exibição do item no menu:
 * O item deve possuir uma das definições abaixo:
 * - anyAbilities: Usuário deve possuir ao menos uma das habilidades informadas.
 *   Se o array for vazio ou conter valor nulo o menu será habilitado.
 * - allAbilities: Usuário deve possuir todas as habilidades informadas.
 *   Se o array for vazio ou conter somente um valor nulo o menu será habilitado.
 *
 * OBS: Vide mapeamento de rotas para o menu refletir as mesmas regras de acesso às rotas (router/routes.js)
 */
const allMenuItems = [
  {
    label: 'menu.dashboard',
    icon: 'o_dashboard',
    to: {
      name: 'dashboard',
    },
    anyAbilities: [ null ],
  },
  {
    label: 'menu.system_access',
    icon: 'o_admin_panel_settings',
    children: [
      {
        label: 'menu.users',
        icon: 'o_manage_accounts',
        to: {
          name: 'users',
        },
        allAbilities: [ ABILITIES.USERS ],
      },
      {
        label: 'menu.permissions',
        icon: 'o_key',
        to: {
          name: 'permissions',
        },
        allAbilities: [ ABILITIES.PERMISSIONS ],
      },
    ],
  },
];

export const generateMenu = () => {
  const filteredMenu = [];

  for (const menuItem of allMenuItems) {
    if (
      checkIfLoggedUserHasAllAbilities(menuItem.allAbilities) ||
      checkIfLoggedUserHasAnyAbilities(menuItem.anyAbilities)
    ) {
      filteredMenu.push(menuItem);
      continue;
    }

    if (menuItem.children) {
      menuItem.children = menuItem.children.filter(children => (
        checkIfLoggedUserHasAllAbilities(children.allAbilities) ||
        checkIfLoggedUserHasAnyAbilities(children.anyAbilities)
      ));

      if (menuItem.children.length > 0) {
        filteredMenu.push(menuItem);
      }
    }
  }
  return filteredMenu;
};

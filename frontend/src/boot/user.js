import { getLoggedUser } from 'src/services/user/user-api';

let loggedUser = {};

const resetLoggedUser = () => {
  loggedUser = {};
};

const resetUserInLocalStorage = () => {
  localStorage.removeItem('isUserLogged');
  localStorage.removeItem('userToken');
};

const loadLoggedUser = async () => {
  resetLoggedUser();

  try {
    const userToken = localStorage.getItem('userToken');

    if (userToken) {
      loggedUser = await getLoggedUser();
    } else {
      resetUserInLocalStorage();
    }
  } catch (e) {
    resetUserInLocalStorage();
  }
};

const getLoggedUserAbilities = () => {
  return loggedUser?.permission?.abilities || [];
};

const checkIfLoggedUserHasAbility = (ability) => {
  if (!ability) {
    return true;
  }

  return (loggedUser?.permission?.abilities || []).includes(ability);
};

const checkIfLoggedUserHasAllAbilities = (abilities) => {
  if (!abilities) {
    return false;
  }

  if (0 === abilities.length) {
    return true;
  }

  return abilities.every((ability) => checkIfLoggedUserHasAbility(ability));
};

const checkIfLoggedUserHasAnyAbilities = (abilities) => {
  if (!abilities) {
    return false;
  }

  if (0 === abilities.length) {
    return true;
  }

  return abilities.some((ability) => checkIfLoggedUserHasAbility(ability));
};

export default async ({ app }) => {
  await loadLoggedUser();
}

export {
  loggedUser,
  loadLoggedUser,
  getLoggedUserAbilities,
  checkIfLoggedUserHasAbility,
  checkIfLoggedUserHasAllAbilities,
  checkIfLoggedUserHasAnyAbilities,
  resetLoggedUser,
  resetUserInLocalStorage,
};

import type { User } from '~/interfaces/user';

export const useUserStore = defineStore('user', () => {
  const authUser = ref<User>(<User>{});

  async function setUserToken(token: string, remember = false) {
    let maxAge;

    if (!remember) {
      maxAge = 12 * 60 * 60;
    }

    const tokenCookie = useCookie('token', { maxAge });
    tokenCookie.value = token;
  }

  async function setUserData(data: User) {
    authUser.value = data;
  }

  async function cleanData() {
    authUser.value = <User>{};
  }

  async function removeToken() {
    const tokenCookie = useCookie('token');
    tokenCookie.value = null;
  }

  return {
    authUser,
    setUserToken,
    setUserData,
    cleanData,
    removeToken,
  };
});

import type { User } from '~/interfaces/user';

export const useUserStore = defineStore('user', () => {
  const authUser = ref<User>(<User>{});

  async function setUserToken(token: string, remember = false) {
    const maxAge = remember
      ? 60 * 60 * 24 * 30  // 30 days
      : 60 * 60 * 12;      // 12 hours

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

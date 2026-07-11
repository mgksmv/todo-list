import { useUserAPI } from '~/api/user';
import { useUserStore } from '~/stores/user';

export default defineNuxtPlugin(async () => {
  const userStore = useUserStore();
  const userAPI = useUserAPI();

  if (useCookie('token').value) {
    const response = await userAPI.getCurrentUser();
    await userStore.setUserData(response.data);
  }
});

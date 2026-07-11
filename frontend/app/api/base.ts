import { useUserStore } from '~/stores/user';
import type { NitroFetchOptions } from 'nitropack';
import type { APIResponse } from '~/types';

export function useAPI(options: any = {}) {
  const config = useRuntimeConfig();
  const token = useCookie('token');
  const router = useRouter();

  const fetchInstance = $fetch.create<APIResponse>({
    baseURL: `${config.public.backendURL}/api`,
    onRequest({ options }) {
      options.headers.set('Authorization', `Bearer ${token.value}`);
    },
    async onResponse({ response }) {
      switch (response.status) {
        case 401: {
          const userStore = useUserStore();

          await userStore.removeToken();

          const currentRoute = router.currentRoute.value;

          if (currentRoute && currentRoute.name !== 'login') {
            const currentRouteFullPath = currentRoute.fullPath;

            let query;
            if (currentRouteFullPath !== '/') {
              query = { redirect: currentRouteFullPath };
            }

            await router.push({ name: 'login', query });
          }

          await userStore.cleanData();

          break;
        }
      }
    },
    ...options,
  });

  const apiFetch = async function (
    path: string,
    options: NitroFetchOptions<
      string,
      | 'options'
      | 'get'
      | 'head'
      | 'patch'
      | 'post'
      | 'put'
      | 'delete'
      | 'connect'
      | 'trace'
    > = {},
  ): Promise<APIResponse> {
    try {
      return await fetchInstance(path, options);
    } catch (error: any) {
      const host = location.hostname.split('.')[1];

      if (host === 'localhost') {
        console.error(error);
      }

      if (error?.response?._data) {
        return {
          ...error.response._data,
          status: error.response.status,
        };
      }

      return error;
    }
  };

  return { apiFetch };
}

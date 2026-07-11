import { useAPI } from './base';

export function useUserAPI() {
  const { apiFetch } = useAPI();

  async function getCurrentUser() {
    return await apiFetch('/v1/user', {
      method: 'get',
    });
  }

  async function updateCurrentUser(body: object) {
    return await apiFetch('/v1/user', {
      method: 'put',
      body,
    });
  }

  async function updateCurrentUserPassword(body: object) {
    return await apiFetch('/v1/user/password', {
      method: 'put',
      body,
    });
  }

  return {
    getCurrentUser,
    updateCurrentUser,
    updateCurrentUserPassword,
  };
}

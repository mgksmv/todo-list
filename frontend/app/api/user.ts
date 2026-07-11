import { useAPI } from './base';

export function useUserAPI() {
  const { apiFetch } = useAPI();

  async function getCurrentUser() {
    return await apiFetch('/v1/user', {
      method: 'get',
    });
  }

  async function updateCurrentUser(payload: object) {
    return await apiFetch('/v1/user', {
      method: 'put',
      body: payload,
    });
  }

  async function updateCurrentUserPassword(payload: object) {
    return await apiFetch('/v1/user/password', {
      method: 'put',
      body: payload,
    });
  }

  return {
    getCurrentUser,
    updateCurrentUser,
    updateCurrentUserPassword,
  };
}

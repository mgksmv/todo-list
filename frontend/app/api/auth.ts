import { useAPI } from './base';

export function useAuthAPI() {
  const { apiFetch } = useAPI();

  async function login(data: {
    email: string;
    password: string;
    remember: boolean;
  }) {
    return await apiFetch('/v1/auth/login', {
      method: 'post',
      body: data,
    });
  }

  async function logout() {
    return await apiFetch('/v1/auth/logout', {
      method: 'post',
    });
  }

  return {
    login,
    logout,
  };
}

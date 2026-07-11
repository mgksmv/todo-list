import { useAPI } from './base';

export function useTaskAPI() {
  const { apiFetch } = useAPI();

  async function getPaginated(query: object = {}) {
    return await apiFetch('/v1/tasks', {
      method: 'get',
      query,
    });
  }

  async function create(body: object) {
    return await apiFetch('/v1/tasks', {
      method: 'post',
      body,
    });
  }

  async function getOne(id: number) {
    return await apiFetch(`/v1/tasks/${id}`, {
      method: 'get',
    });
  }

  async function update(id: number, body: object) {
    return await apiFetch(`/v1/tasks/${id}`, {
      method: 'put',
      body,
    });
  }

  async function destroy(id: number) {
    return await apiFetch(`/v1/tasks/${id}`, {
      method: 'delete',
    });
  }

  return {
    getPaginated,
    create,
    getOne,
    update,
    destroy,
  };
}

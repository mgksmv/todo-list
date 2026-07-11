export default defineNuxtRouteMiddleware((to) => {
  if (to.meta.withoutAuth) {
    return;
  }

  const token = useCookie('token');

  if (!token.value) {
    if (to.name !== 'login') {
      const query = to.fullPath !== '/' ? { redirect: to.fullPath } : undefined;
      return navigateTo({ name: 'login', query });
    }
    return;
  }

  if (to.name === 'login') {
    return navigateTo({ name: 'index' });
  }
});

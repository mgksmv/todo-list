export const useMainStore = defineStore(
  'main',
  () => {
    const isSidebarOpen = ref(true);

    return {
      isSidebarOpen,
    };
  },
  {
    persist: {
      pick: ['isSidebarOpen'],
    },
  },
);

export default defineNuxtPlugin(async () => {
  const appearance = localStorage.getItem('appearance');

  if (appearance === 'system') {
    const prefersDark = window.matchMedia(
      '(prefers-color-scheme: dark)',
    ).matches;

    if (prefersDark) {
      document.documentElement.classList.add('dark');
    }
  } else if (appearance === 'dark') {
    document.documentElement.classList.add('dark');
  }
});

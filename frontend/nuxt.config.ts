import tailwindcss from '@tailwindcss/vite';

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  ssr: false,
  modules: ['@pinia/nuxt', 'pinia-plugin-persistedstate/nuxt', '@nuxt/eslint', 'dayjs-nuxt'],
  runtimeConfig: {
    public: {
      appName: process.env.APP_NAME,
      appDomain: process.env.APP_DOMAIN,
      appURL: process.env.APP_URL,
      backendDomain: process.env.BACKEND_DOMAIN,
      backendURL: process.env.BACKEND_URL,
    },
  },
  css: ['./app/assets/css/app.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  dayjs: {
    locales: ['ru'],
    plugins: ['utc', 'timezone', 'relativeTime', 'customParseFormat'],
    defaultLocale: 'ru',
    defaultTimezone: 'Europe/Moscow',
  },
})

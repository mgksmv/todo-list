<script setup lang="ts">
import '~/assets/css/app.css';
import '~/assets/scss/app.scss';
import LoaderSpinner from './components/app/LoaderSpinner.vue';
import Toaster from '@/components/ui/toast/Toaster.vue';
</script>

<template>
  <NuxtLoadingIndicator color="#3F3F46" />
  <Toaster />

  <Suspense>
    <template #default>
      <NuxtPage />
    </template>
    <template #fallback>
      <LoaderSpinner />
    </template>
  </Suspense>
</template>

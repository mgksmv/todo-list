<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '~/types';

useHead({
  title: 'Главная',
});

const router = useRouter();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Главная',
    href: router.resolve({ name: 'index' }).fullPath,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-between gap-4 items-center pt-4 ps-4 pe-4">
      <p>Добро пожаловать!</p>
    </div>
  </AppLayout>
</template>

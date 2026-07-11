<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '~/types';

const TITLE = 'Задачи';

useHead({
  title: TITLE,
});

const router = useRouter();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Главная',
    href: router.resolve({ name: 'index' }).fullPath,
  },
  {
    title: TITLE,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

  </AppLayout>
</template>

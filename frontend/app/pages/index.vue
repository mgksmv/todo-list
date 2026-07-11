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

  </AppLayout>
</template>

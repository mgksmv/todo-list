<script setup lang="ts">
import { computed } from 'vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const layout = computed(() => {
  return AppSidebarLayout;
});
</script>

<template>
  <component :is="layout" :breadcrumbs="breadcrumbs">
    <slot />
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Layout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const layout = computed(() => {
  return Layout;
});
</script>

<template>
  <component :is="layout" :breadcrumbs="breadcrumbs">
    <slot />
  </component>
</template>

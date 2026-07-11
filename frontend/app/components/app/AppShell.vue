<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { useMainStore } from '~/stores/main';

interface Props {
  variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const { isSidebarOpen } = storeToRefs(useMainStore());
</script>

<template>
  <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
    <slot />
  </div>
  <SidebarProvider v-else :default-open="isSidebarOpen">
    <slot />
  </SidebarProvider>
</template>

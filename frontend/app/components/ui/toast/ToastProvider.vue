<script setup lang="ts">
import { ToastProvider } from 'reka-ui';

const props = defineProps<{
  duration?: number
  label?: string
}>();
</script>

<template>
  <ToastProvider v-bind="props">
    <slot />
  </ToastProvider>
</template>

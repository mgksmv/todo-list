<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ToastRoot, type ToastRootEmits, type ToastRootProps, useForwardPropsEmits } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';
import { type ToastVariants, toastVariants } from '.';

const props = defineProps<ToastRootProps & { class?: HTMLAttributes['class'], variant?: ToastVariants['variant'], onOpenChange?: (open: boolean) => void }>();
const emits = defineEmits<ToastRootEmits>();

const delegatedProps = computed(() => {
  const { class: _, variant: __, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <ToastRoot
    v-bind="forwarded"
    :class="cn(toastVariants({ variant }), props.class)"
    @update:open="onOpenChange"
  >
    <slot />
  </ToastRoot>
</template>

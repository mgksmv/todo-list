<script setup lang="ts">
import { onMounted, ref } from 'vue';

defineProps<{
  to: any;
}>();

const isMounted = ref(false);

onMounted(() => {
  isMounted.value = true;
});
</script>

<template>
  <Teleport v-if="isMounted" :to="to">
    <slot />
  </Teleport>
</template>

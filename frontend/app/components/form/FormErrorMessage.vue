<script setup lang="ts">
defineProps<{
  messages?: string[];
}>();
</script>

<template>
  <template v-if="messages?.length">
    <div
      v-for="message in messages"
      :key="message"
      class="text-destructive text-sm"
    >
      {{ message }}
    </div>
  </template>
</template>

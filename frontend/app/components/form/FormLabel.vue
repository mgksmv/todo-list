<script setup lang="ts">
withDefaults(
    defineProps<{
        required?: boolean,
    }>(),
    {
        required: false,
    },
);
</script>

<template>
    <p class="block text-sm font-medium">
        <slot />
        <span v-if="required" class="text-red-400">*</span>
    </p>
</template>

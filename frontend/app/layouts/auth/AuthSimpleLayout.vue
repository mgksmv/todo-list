<script setup lang="ts">
import AppLogoIcon from '@/components/app/AppLogoIcon.vue';

defineProps<{
  title?: string;
  description?: string;
}>();
</script>

<template>
  <div
    class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
  >
    <div class="w-full max-w-sm">
      <div class="flex flex-col gap-8">
        <div class="flex flex-col items-center gap-4">
          <NuxtLink
            :to="{ name: 'index' }"
            class="flex flex-col items-center gap-2 font-medium"
          >
            <div
              class="mb-1 flex h-9 w-full items-center justify-center rounded-md"
            >
              <AppLogoIcon
                class="size-80 fill-current text-[var(--foreground)] dark:text-white"
              />
            </div>
            <span class="sr-only">{{ title }}</span>
          </NuxtLink>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>

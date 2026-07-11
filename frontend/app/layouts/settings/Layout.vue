<script setup lang="ts">
import Heading from '@/components/app/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import type { NavItem } from '@/types';

const route = useRoute();
const router = useRouter();

const sidebarNavItems: NavItem[] = [
  {
    title: 'Профиль',
    href: router.resolve({ name: 'settings-profile' }).fullPath,
    isActive:
      route.path === router.resolve({ name: 'settings-profile' }).fullPath,
  },
  {
    title: 'Пароль',
    href: router.resolve({ name: 'settings-password' }).fullPath,
    isActive:
      route.path === router.resolve({ name: 'settings-password' }).fullPath,
  },
  {
    title: 'Тема',
    href: router.resolve({ name: 'settings-appearance' }).fullPath,
    isActive:
      route.path === router.resolve({ name: 'settings-appearance' }).fullPath,
  },
];
</script>

<template>
  <div class="px-4 py-6">
    <Heading title="Настройки" description="Управление настройками аккаунта" />

    <div class="flex flex-col lg:flex-row lg:space-x-12">
      <aside class="w-full max-w-xl lg:w-48">
        <nav class="flex flex-col space-y-1 space-x-0">
          <Button
            v-for="item in sidebarNavItems"
            :key="item.href"
            variant="ghost"
            :class="[
              'w-full justify-start',
              {
                'bg-muted': item.isActive,
              },
            ]"
            as-child
          >
            <NuxtLink :to="item.href">
              {{ item.title }}
            </NuxtLink>
          </Button>
        </nav>
      </aside>

      <Separator class="my-6 lg:hidden" />

      <div class="flex-1 md:max-w-2xl">
        <section class="max-w-xl space-y-12">
          <slot />
        </section>
      </div>
    </div>
  </div>
</template>

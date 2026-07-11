<script setup lang="ts">
import AppearanceTabs from '@/components/app/AppearanceTabs.vue';
import HeadingSmall from '@/components/app/HeadingSmall.vue';
import type { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

useHead({
  title: 'Внешний вид',
});

const router = useRouter();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Настройки внешнего вида',
    href: router.resolve({ name: 'settings-appearance' }).fullPath,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          title="Настройки внешнего вида"
          description="Обновите настройки внешнего вида вашего аккаунта"
        />
        <AppearanceTabs />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

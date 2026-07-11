<script setup lang="ts">
import { ref } from 'vue';
import { watchDebounced } from '@vueuse/core';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { FilterX, Plus, Search } from 'lucide-vue-next';
import { NuxtLink } from '#components';

const props = withDefaults(
  defineProps<{
    title: string;
    hasAppliedFilters?: boolean;
  }>(),
  {
    hasAppliedFilters: false,
  },
);

const emit = defineEmits(['onAddButtonClick', 'resetFilters']);

const route = useRoute();
const router = useRouter();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Главная',
    href: router.resolve({ name: 'index' }).fullPath,
  },
  {
    title: props.title,
    href: router.resolve({ name: 'tasks' }).fullPath,
  },
];

const searchKeyword = ref(route.query.title ? String(route.query.title)  : '');

watchDebounced(
  searchKeyword,
  () => {
    handleSearch();
  },
  { debounce: 500 },
);

async function handleSearch() {
  await router.replace({
    query: {
      title: searchKeyword.value !== '' ? searchKeyword.value : undefined,
      page: undefined,
    },
  });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-between gap-4 items-center pt-4 ps-4 pe-4">
      <div class="flex gap-4">
        <Button
          variant="default"
          class="bg-green-600 hover:bg-green-700 text-white"
          @click="emit('onAddButtonClick')"
        >
          <Plus class="h-4 w-4" />
          Создать
        </Button>

        <div>
          <div class="flex -space-x-px">
            <Button
              :as="NuxtLink"
              :to="{ name: 'tasks' }"
              :variant="!route.path.endsWith('/kanban') ? 'default' : 'secondary'"
              class="rounded-r-none focus:z-10"
            >
              Таблица
            </Button>

            <Button
              :as="NuxtLink"
              :to="{ name: 'tasks-kanban' }"
              :variant="route.path.endsWith('/kanban') ? 'default' : 'secondary'"
              class="rounded-l-none focus:z-10"
            >
              Канбан
            </Button>
          </div>
        </div>
      </div>

      <div class="flex gap-4">
        <Button
          v-if="hasAppliedFilters"
          variant="destructive"
          class="flex items-center gap-2"
          @click="emit('resetFilters')"
        >
          <FilterX :size="18" />
          Сбросить фильтры
        </Button>

        <div class="relative w-[400px]">
          <Input
            id="search-input"
            v-model="searchKeyword"
            placeholder="Введите заголовок задачи"
            class="pr-10"
          />
          <Button
            variant="ghost"
            size="icon"
            class="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
            @click="handleSearch"
          >
            <Search class="h-4 w-4 text-muted-foreground" />
          </Button>
        </div>
      </div>
    </div>

    <slot />
  </AppLayout>
</template>

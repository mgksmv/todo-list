<script setup lang="ts">
import RenderlessPagination from 'laravel-vue-pagination/src/RenderlessPagination.vue';

interface PaginationData {
  [key: string]: any;
}

interface Props {
  data: PaginationData;
  limit?: number;
  keepLength?: boolean;
}

interface Emits {
  (e: 'pagination-change-page', page: number): void;
}

withDefaults(defineProps<Props>(), {
  limit: 0,
  keepLength: false,
});

const emit = defineEmits<Emits>();

function handlePaginationChangePage(page: number) {
  emit('pagination-change-page', page);
}
</script>

<template>
  <RenderlessPagination
    :data="data"
    :limit="limit"
    :keep-length="keepLength"
    @pagination-change-page="handlePaginationChangePage"
    v-slot="slotProps"
  >
    <nav
      v-bind="$attrs"
      aria-label="Pagination"
      v-if="slotProps.computed.total > slotProps.computed.perPage"
      class="flex items-center justify-center"
    >
      <div class="flex items-center gap-1">
        <button
          :disabled="!slotProps.computed.prevPageUrl"
          v-on="slotProps.prevButtonEvents"
          class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-neutral-400 dark:text-neutral-500 bg-transparent border border-neutral-300 dark:border-neutral-600 rounded-xl hover:text-neutral-600 dark:hover:text-neutral-300 hover:border-neutral-400 dark:hover:border-neutral-500 focus:z-10 focus:outline-none transition-all duration-200 disabled:opacity-30 disabled:cursor-not-allowed"
          type="button"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
          <span class="hidden sm:inline">
            <slot name="prev-nav">Пред.</slot>
          </span>
        </button>

        <div class="flex items-center gap-1 mx-2">
          <button
            v-for="(page, key) in slotProps.computed.pageRange"
            :key="key"
            :aria-current="
              page === slotProps.computed.currentPage ? 'page' : null
            "
            v-on="slotProps.pageButtonEvents(page)"
            class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium transition-all duration-200 focus:z-10 focus:outline-none rounded-xl border"
            :class="
              page === slotProps.computed.currentPage
                ? 'bg-white text-black border-neutral-400 dark:bg-neutral-800 dark:text-white dark:border-neutral-500'
                : 'text-neutral-400 dark:text-neutral-500 border-neutral-300 dark:border-neutral-600 hover:text-neutral-600 dark:hover:text-neutral-300 hover:border-neutral-400 dark:hover:border-neutral-500'
            "
            type="button"
          >
            {{ page }}
          </button>
        </div>

        <button
          :disabled="!slotProps.computed.nextPageUrl"
          v-on="slotProps.nextButtonEvents"
          class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-neutral-400 dark:text-neutral-500 bg-transparent border border-neutral-300 dark:border-neutral-600 rounded-xl hover:text-neutral-600 dark:hover:text-neutral-300 hover:border-neutral-400 dark:hover:border-neutral-500 focus:z-10 focus:outline-none transition-all duration-200 disabled:opacity-30 disabled:cursor-not-allowed"
          type="button"
        >
          <span class="hidden sm:inline">
            <slot name="next-nav">След.</slot>
          </span>
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"
            />
          </svg>
        </button>
      </div>
    </nav>
  </RenderlessPagination>
</template>

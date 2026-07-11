<script setup lang="ts">
import type { BreadcrumbItem, DataResource } from '~/types';
import type { Task } from '~/interfaces/task';
import Pagination from '@/components/app/Pagination.vue';
import { useTaskAPI } from '~/api/task';
import { taskStatusOptions, getTaskStatusLabel, getTaskStatusBadgeVariant, TaskStatus } from '~/enums/task-status';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '~/components/ui/table';
import { Input } from '~/components/ui/input';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '~/components/ui/select';
import { Badge } from '~/components/ui/badge';
import { ArrowDown, ArrowUp, ArrowUpDown, FilterX, Plus, Trash } from 'lucide-vue-next';
import { watchDebounced } from '@vueuse/core';
import TaskModal from '~/components/tasks/TaskModal.vue';
import { Button } from '~/components/ui/button';
import { useToast } from '~/components/ui/toast/use-toast';
import { useUserStore } from '~/stores/user';
import AppLayout from '~/layouts/AppLayout.vue';

const TITLE = 'Задачи';

useHead({
  title: TITLE,
});

const route = useRoute();
const router = useRouter();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Главная',
    href: router.resolve({ name: 'index' }).fullPath,
  },
  {
    title: TITLE,
    href: router.resolve({ name: 'tasks' }).fullPath,
  },
];

const dayjs = useDayjs();

const taskAPI = useTaskAPI();
const { toast } = useToast();
const { authUser } = storeToRefs(useUserStore());

const {
  data: tasks,
  pending: tasksPending,
  refresh: tasksRefresh,
} = await useAsyncData<DataResource<Task[]>>(
  async () => {
    const response = await taskAPI.getPaginated(route.query);

    return {
      data: response.data,
      meta: response.meta,
    };
  },
  {
    watch: [() => route.query],
  },
);

const createDefaultFilters = () => ({
  title: undefined,
  user_name: undefined,
  due_date: undefined,
  status: undefined,
});

const createFiltersFromQuery = () => {
  return {
    title: route.query.title ? String(route.query.title) : undefined,
    user_name: route.query.user_name ? String(route.query.user_name) : undefined,
    due_date: route.query.due_date ? String(route.query.due_date) : undefined,
    status: route.query.status ? String(route.query.status) : undefined,
  };
};

const filters = ref(createFiltersFromQuery());

const sort = reactive({
  field: route.query.sort_field ? String(route.query.sort_field) : null,
  order: route.query.sort_order && route.query.sort_order == 'asc' ? 1 : -1,
});

const currentTask = ref<Task | null>(null);
const isModalOpen = ref(false);

const hasAppliedFilters = computed(() => {
  return (
    Object.values(filters.value).some(
      (value) => value !== undefined && value !== '',
    ) || Object.values(sort).some((value) => value != undefined && value !== '' && value !== -1)
  );
});

const canManageTask = (task: Task) => {
  return authUser.value.is_admin || task.user_id === authUser.value.id;
};

watch(tasksPending, async (pending) => {
  if (!pending) {
    await nextTick();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

watchDebounced(
  () => [filters.value.title, filters.value.user_name],
  () => {
    handleTableFilter();
  },
  { debounce: 500 },
);

async function handlePageChange(page: number) {
  await router.replace({
    query: { ...route.query, page },
  });
}

async function handleTableFilter() {
  const raw = Object.fromEntries(
    Object.entries(filters.value)
      .map(([key, value]) => [key, value])
      .filter(([key, value]) => {
        if (value === null || value === undefined) return false;
        if (typeof value === 'string') {
          if (key === 'status' && value === 'all') return false;
          return value.trim() !== '';
        }
        return true;
      }),
  );

  const query: Record<string, any> = {
    page: undefined,
  };

  for (const [key, value] of Object.entries(raw)) {
    if (Array.isArray(value)) {
      query[`${key}[]`] = value;
    } else {
      query[key] = value;
    }
  }

  await router.replace({ query });
}

async function handleResetFiltersButtonClick() {
  filters.value = createDefaultFilters();
  sort.field = null;
  sort.order = -1;
  await router.replace({ query: {} });
}

async function handleTableSort(field: string) {
  const query: Record<string, any> = {
    ...route.query,
    page: undefined,
  };

  if (sort.field === field) {
    if (sort.order === 1) {
      sort.order = -1;
      query.sort_order = 'desc';
    } else {
      sort.field = null;
      sort.order = -1;
      query.sort_field = undefined;
      query.sort_order = undefined;
    }
  } else {
    sort.field = field;
    sort.order = 1;
    query.sort_field = field;
    query.sort_order = 'asc';
  }

  await router.replace({ query });
}

function handleCreateButtonClick() {
  currentTask.value = null;
  isModalOpen.value = true;
}

function handleEditButtonClick(task: Task) {
  currentTask.value = task;
  isModalOpen.value = true;
}

async function handleModalClose(refreshData?: boolean) {
  isModalOpen.value = false;
  if (refreshData) {
    await tasksRefresh();
  }
}

async function handleDeleteTask(task: Task) {
  if (!confirm('Вы уверены, что хотите удалить задачу?')) {
    return;
  }

  const response = await taskAPI.destroy(task.id);

  if (response.success) {
    if (isModalOpen.value) {
      isModalOpen.value = false;
    }

    await tasksRefresh();

    toast({
      title: 'Успех',
      description: 'Задача удалена',
    });
  } else {
    toast({
      title: 'Ошибка',
      description: 'Не удалось удалить задачу',
      variant: 'destructive',
    });
  }
}
</script>

<template>
  <AppLayout v-if="tasks" :breadcrumbs="breadcrumbs">
    <div class="flex justify-between gap-4 items-center pt-4 ps-4 pe-4">
      <div class="flex gap-4">
        <Button
          variant="default"
          class="bg-green-600 hover:bg-green-700 text-white"
          @click="handleCreateButtonClick"
        >
          <Plus class="h-4 w-4" />
          Создать
        </Button>
      </div>

      <div class="flex gap-4">
        <Button
          v-if="hasAppliedFilters"
          variant="destructive"
          class="flex items-center gap-2"
          @click="handleResetFiltersButtonClick"
        >
          <FilterX :size="18" />
          Сбросить фильтры
        </Button>
      </div>
    </div>

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="border rounded-xl relative">
        <Table>
          <TableHeader>
            <TableRow class="bg-muted/50 hover:bg-muted/50">
              <TableHead class="w-[80px] cursor-pointer select-none" @click="handleTableSort('id')">
                <div class="flex items-center gap-2">
                  ID
                  <ArrowUpDown v-if="sort.field !== 'id'" class="h-4 w-4" />
                  <ArrowUp v-else-if="sort.order === 1" class="h-4 w-4" />
                  <ArrowDown v-else class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="cursor-pointer select-none" @click="handleTableSort('title')">
                <div class="flex items-center gap-2">
                  Заголовок
                  <ArrowUpDown v-if="sort.field !== 'title'" class="h-4 w-4" />
                  <ArrowUp v-else-if="sort.order === 1" class="h-4 w-4" />
                  <ArrowDown v-else class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="select-none">
                <div class="flex items-center gap-2">
                  Пользователь
                </div>
              </TableHead>
              <TableHead class="cursor-pointer select-none" @click="handleTableSort('status')">
                <div class="flex items-center gap-2">
                  Статус
                  <ArrowUpDown v-if="sort.field !== 'status'" class="h-4 w-4" />
                  <ArrowUp v-else-if="sort.order === 1" class="h-4 w-4" />
                  <ArrowDown v-else class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="cursor-pointer select-none" @click="handleTableSort('due_date')">
                <div class="flex items-center gap-2">
                  Дедлайн
                  <ArrowUpDown v-if="sort.field !== 'due_date'" class="h-4 w-4" />
                  <ArrowUp v-else-if="sort.order === 1" class="h-4 w-4" />
                  <ArrowDown v-else class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="cursor-pointer select-none" @click="handleTableSort('created_at')">
                <div class="flex items-center gap-2">
                  Дата создания
                  <ArrowUpDown v-if="sort.field !== 'created_at'" class="h-4 w-4" />
                  <ArrowUp v-else-if="sort.order === 1" class="h-4 w-4" />
                  <ArrowDown v-else class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="w-[50px]"></TableHead>
            </TableRow>
            <TableRow class="bg-muted/50 hover:bg-muted/50">
              <TableCell></TableCell>
              <TableCell>
                <Input
                  v-model="filters.title"
                  placeholder="Фильтр по заголовку"
                  class="h-8"
                />
              </TableCell>
              <TableCell>
                <Input
                  v-model="filters.user_name"
                  placeholder="Фильтр по пользователю"
                  class="h-8"
                />
              </TableCell>
              <TableCell>
                <Select
                  v-model="filters.status"
                  @update:model-value="handleTableFilter"
                >
                  <SelectTrigger class="h-8 w-full">
                    <SelectValue placeholder="Все статусы" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem value="all">
                        Все статусы
                      </SelectItem>
                      <SelectItem
                        v-for="option in taskStatusOptions"
                        :key="option.value"
                        :value="String(option.value)"
                      >
                        {{ option.label }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </TableCell>
              <TableCell></TableCell>
              <TableCell></TableCell>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="tasks.data.length > 0">
              <TableRow
                v-for="task in tasks.data"
                :key="task.id"
                class="cursor-pointer"
                @click="handleEditButtonClick(task)"
              >
                <TableCell>{{ task.id }}</TableCell>
                <TableCell>{{ task.title }}</TableCell>
                <TableCell>{{ task.user?.name }}</TableCell>
                <TableCell>
                  <template v-if="task.status">
                    <Badge :variant="getTaskStatusBadgeVariant(task.status as TaskStatus)">
                      {{ getTaskStatusLabel(task.status as TaskStatus) }}
                    </Badge>
                  </template>
                </TableCell>
                <TableCell>{{ dayjs(task.due_date).format('DD.MM.YYYY') }}</TableCell>
                <TableCell>{{ dayjs(task.created_at).format('DD.MM.YYYY HH:mm') }}</TableCell>
                <TableCell>
                  <Button
                    v-if="canManageTask(task)"
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 text-destructive hover:text-destructive hover:bg-destructive/10"
                    @click.stop="handleDeleteTask(task)"
                  >
                    <Trash class="h-4 w-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell colspan="6" class="h-24 text-center text-muted-foreground">
                Пусто
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
        <div v-if="tasksPending" class="absolute inset-0 flex items-center justify-center bg-background/50">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
      </div>

      <div class="flex justify-center my-4">
        <Pagination
          :data="tasks"
          :limit="2"
          @pagination-change-page="handlePageChange"
        />
      </div>
    </div>
  </AppLayout>

  <TaskModal
    v-if="isModalOpen"
    :is-open="isModalOpen"
    :task="currentTask"
    @close-modal="handleModalClose"
    @on-delete="handleDeleteTask"
  />
</template>

<script setup lang="ts">
import TaskLayout from '~/components/tasks/TaskLayout.vue';
import type { DataResource } from '~/types';
import type { Task } from '~/interfaces/task';

const TITLE = 'Задачи';

useHead({
  title: TITLE,
});

const route = useRoute();
const router = useRouter();

const {
  data: tasks,
  pending: tasksPending,
  refresh: tasksRefresh,
} = await useAsyncData<DataResource<Task[]>>(
  async () => {
    const response = {
      data: [],
      meta: {},
    };

    return {
      data: response.data,
      meta: response.meta,
    };
  },
  {
    watch: [() => route.query],
  },
);
</script>

<template>
  <TaskLayout
    v-if="tasks"
    :title="TITLE"
    :has-applied-filters="false"
  >

  </TaskLayout>
</template>

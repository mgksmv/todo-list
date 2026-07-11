<script setup lang="ts">
import { useToast } from '~/components/ui/toast/use-toast';
import { useForm } from '~/composables/useForm';
import { useUserStore } from '~/stores/user';
import { useTaskAPI } from '~/api/task';
import type { Task } from '@/interfaces/task';
import { Input } from '~/components/ui/input';
import { Button } from '~/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '~/components/ui/dialog';
import { Textarea } from '~/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '~/components/ui/select';
import { Label } from '~/components/ui/label';
import FormErrorMessage from '../form/FormErrorMessage.vue';
import { TaskStatus, taskStatusOptions } from '~/enums/task-status';
import { Loader2, Check, Trash } from 'lucide-vue-next';

const props = defineProps<{
  isOpen: boolean;
  task: Task | null;
}>();

const emit = defineEmits<{
  (e: 'closeModal', refreshData?: boolean): void;
  (e: 'onDelete', task: Task): void;
}>();

const header = (props.task?.id ? 'Изменить' : 'Создать') + ' задачу';

const { form, data, errors, setErrors, processing } = useForm(
  props.task
    ? {
      title: props.task.title,
      description: props.task.description || '',
      due_date: props.task.due_date ?? undefined,
      status: props.task.status || TaskStatus.PENDING,
    }
    : {
      title: '',
      description: '',
      due_date: undefined,
      status: TaskStatus.PENDING,
    },
);

const taskAPI = useTaskAPI();
const { toast } = useToast();
const { authUser } = storeToRefs(useUserStore());

const canManageTask = computed(() => {
  return authUser.value.is_admin || (props.task && props.task.user_id === authUser.value.id);
});

async function handleFormSubmit() {
  const isUpdate = props.task?.id;

  const response = isUpdate
    ? await taskAPI.update(props.task.id, data())
    : await taskAPI.create(data());

  if (response.success) {
    emit('closeModal', true);
    toast({
      title: 'Успех',
      description: isUpdate ? 'Задача обновлена' : 'Задача создана',
    });
  } else {
    setErrors(response.errors ?? {});

    toast({
      title: 'Ошибка',
      description: isUpdate
        ? 'Не удалось обновить задачу'
        : 'Не удалось создать задачу',
      variant: 'destructive',
    });
  }
}

async function handleDeleteButtonClick() {
  if (props.task) {
    emit('onDelete', props.task);
  }
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="emit('closeModal')">
    <DialogContent class="sm:max-w-[525px]">
      <DialogHeader>
        <DialogTitle>{{ header }}</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleFormSubmit" class="space-y-4 py-4">
        <div class="space-y-2">
          <Label for="title" class="flex gap-1">
            Заголовок
            <span class="text-destructive">*</span>
          </Label>
          <Input
            id="title"
            v-model="form.title"
            placeholder="Введите заголовок"
            :aria-invalid="!!errors?.title"
            required
          />
          <FormErrorMessage :messages="errors?.title" />
        </div>

        <div class="space-y-2">
          <Label for="description">Описание</Label>
          <Textarea
            id="description"
            v-model="form.description"
            placeholder="Введите описание"
            :aria-invalid="!!errors?.description"
          />
          <FormErrorMessage :messages="errors?.description" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="due_date">Дедлайн</Label>
            <Input
              id="due_date"
              v-model="form.due_date"
              type="date"
              :aria-invalid="!!errors?.due_date"
            />
            <FormErrorMessage :messages="errors?.due_date" />
          </div>

          <div class="space-y-2">
            <Label for="status">Статус</Label>
            <Select v-model="form.status">
              <SelectTrigger id="status" class="w-full">
                <SelectValue placeholder="Выберите статус" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="option in taskStatusOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </SelectItem>
              </SelectContent>
            </Select>
            <FormErrorMessage :messages="errors?.status" />
          </div>
        </div>

        <DialogFooter class="flex justify-between gap-2 pt-4">
          <div v-if="!task?.id || canManageTask" class="flex gap-2">
            <Button type="submit" :disabled="processing">
              <Loader2 v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
              <Check v-else class="h-4 w-4" />
              Сохранить
            </Button>
          </div>
          <Button
            v-if="task?.id && canManageTask"
            type="button"
            variant="destructive"
            :disabled="processing"
            @click="handleDeleteButtonClick"
          >
            <Trash class="h-4 w-4" />
            Удалить
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

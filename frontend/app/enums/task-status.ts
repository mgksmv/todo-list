import { enumToOptions } from '~/enums/index';
import type { BadgeVariants } from '~/components/ui/badge';

export enum TaskStatus {
  PENDING = 'pending',
  IN_PROGRESS = 'in_progress',
  COMPLETED = 'completed',
}

export const TaskStatusLabels: Record<TaskStatus, string> = {
  [TaskStatus.PENDING]: 'В ожидании',
  [TaskStatus.IN_PROGRESS]: 'В процессе',
  [TaskStatus.COMPLETED]: 'Завершена',
};

export const TaskStatusBadgeVariants: Record<TaskStatus, BadgeVariants['variant']> = {
  [TaskStatus.PENDING]: 'warning',
  [TaskStatus.IN_PROGRESS]: 'info',
  [TaskStatus.COMPLETED]: 'success',
};

export function getTaskStatusLabel(status: TaskStatus): string {
  return TaskStatusLabels[status];
}

export function getTaskStatusBadgeVariant(status: TaskStatus): BadgeVariants['variant'] {
  return TaskStatusBadgeVariants[status];
}

export const taskStatusOptions = enumToOptions(
  TaskStatus,
  TaskStatusLabels,
);

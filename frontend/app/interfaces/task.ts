import type { User } from '~/interfaces/user';

export interface Task {
  id: number;
  user_id: number;
  title: string;
  description: string | null;
  due_date: string;
  status: string;
  created_at: string;
  updated_at: string;

  user: User;
}

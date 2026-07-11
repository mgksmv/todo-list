import type { LucideIcon } from 'lucide-vue-next';
import type { LengthAwarePaginator } from './pagination';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href?: string;
}

export interface NavItem {
  title: string;
  href?: string;
  icon?: LucideIcon;
  isActive?: boolean;
  isHeader?: boolean;
}

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface APIResponse {
  success: boolean;
  data: any | null;
  meta: LengthAwarePaginator;
  message: string | null;
  errors: any;
  status?: number;
}

export interface DataResource<T> {
  data: T;
  meta: LengthAwarePaginator;
}

export type BreadcrumbItemType = BreadcrumbItem;

import type { LucideIcon } from 'lucide-vue-next';
import type { PaginationMeta } from './pagination';
import type { User } from '~/interfaces/user';

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

export interface APIResponse {
  success: boolean;
  data: any | null;
  meta: PaginationMeta;
  message: string | null;
  errors: any;
  status?: number;
}

export interface DataResource<T> {
  data: T;
  meta: PaginationMeta;
}

export type BreadcrumbItemType = BreadcrumbItem;

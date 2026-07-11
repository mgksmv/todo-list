<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

defineProps<{
  items: NavItem[];
}>();

const { state } = useSidebar();
</script>

<template>
  <SidebarGroup class="px-2 pb-0 pt-6">
    <SidebarMenu>
      <template v-for="item in items" :key="item.title">
        <template v-if="item.isHeader && state === 'expanded'">
          <SidebarGroupLabel class="mt-4 first:mt-0">{{
            item.title
          }}</SidebarGroupLabel>
        </template>

        <SidebarMenuItem v-else-if="item.href">
          <SidebarMenuButton
            as-child
            :is-active="item.isActive"
            :tooltip="item.title"
          >
            <NuxtLink :to="item.href">
              <component :is="item.icon" />
              <span>{{ item.title }}</span>
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </template>
    </SidebarMenu>
  </SidebarGroup>
</template>

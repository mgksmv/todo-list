<script setup lang="ts">
import NavFooter from '@/components/app/NavFooter.vue';
import NavMain from '@/components/app/NavMain.vue';
import NavUser from '@/components/app/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { CheckSquareIcon } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const router = useRouter();
const route = useRoute();

let baseNavItems: NavItem[] = [
  {
    title: 'Задачи',
    href: router.resolve({ name: 'tasks' }).fullPath,
    icon: CheckSquareIcon,
  },
];

const mainNavItems = computed(() => {
  return baseNavItems.map((item) => ({
    ...item,
    isActive:
      item.isActive ??
      (route.path === item.href || route.path.startsWith(item.href + '/')),
  }));
});

const footerNavItems: NavItem[] = [];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <NuxtLink :to="{ name: 'index' }">
              <AppLogo />
            </NuxtLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

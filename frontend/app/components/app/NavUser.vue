<script setup lang="ts">
import UserInfo from '@/components/app/UserInfo.vue';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '@/components/ui/sidebar';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';
import { useUserStore } from '~/stores/user';

const { isMobile, state } = useSidebar();
const { authUser } = storeToRefs(useUserStore());
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton
            size="lg"
            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
          >
            <UserInfo :user="authUser" />
            <ChevronsUpDown class="ml-auto size-4" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
          :side="
            isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'
          "
          align="end"
          :side-offset="4"
        >
          <UserMenuContent :user="authUser" />
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>
</template>

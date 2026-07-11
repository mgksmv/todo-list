<script setup lang="ts">
import UserInfo from '@/components/app/UserInfo.vue';
import {
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { LogOut, Settings } from 'lucide-vue-next';
import { useAuthAPI } from '~/api/auth';
import { useUserStore } from '~/stores/user';

interface Props {
  user: User;
}

const router = useRouter();

const authAPI = useAuthAPI();
const userStore = useUserStore();

const isLoading = ref(false);

const handleLogout = async () => {
  if (confirm('Вы уверены, что хотите выйти из аккаунта?')) {
    isLoading.value = true;

    const response = await authAPI.logout();

    if (response.success) {
      await userStore.cleanData();
      await userStore.removeToken();
      await router.push({ name: 'login' });
    }

    isLoading.value = false;
  }
};

defineProps<Props>();
</script>

<template>
  <DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
      <UserInfo :user="user" :show-email="true" />
    </div>
  </DropdownMenuLabel>
  <DropdownMenuSeparator />
  <DropdownMenuGroup>
    <DropdownMenuItem :as-child="true">
      <NuxtLink class="block w-full" :to="{ name: 'settings-profile' }">
        <Settings class="mr-2 h-4 w-4" />
        Настройки
      </NuxtLink>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator />
  <DropdownMenuItem :as-child="true">
    <button class="block w-full" :disabled="isLoading" @click="handleLogout">
      <LogOut class="mr-2 h-4 w-4" />
      Выйти
    </button>
  </DropdownMenuItem>
</template>

<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import HeadingSmall from '@/components/app/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import FormLabel from '@/components/form/FormLabel.vue';
import { useUserStore } from '~/stores/user';
import { useForm } from '~/composables/useForm';
import { useUserAPI } from '~/api/user';
import FormErrorMessage from '~/components/form/FormErrorMessage.vue';
import { useToast } from '~/components/ui/toast/use-toast';

useHead({
  title: 'Профиль',
});

const router = useRouter();
const { toast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Настройки профиля',
    href: router.resolve({ name: 'settings-profile' }).fullPath,
  },
];

const userStore = useUserStore();
const { authUser } = storeToRefs(userStore);

const { form, data, errors, setErrors, processing } = useForm({
  name: authUser.value.name,
  email: authUser.value.email,
});

const userAPI = useUserAPI();

async function handleSubmit() {
  const response = await userAPI.updateCurrentUser(data());

  if (response.success) {
    await userStore.setUserData(response.data);
    toast({
      title: 'Успех',
      description: 'Профиль успешно обновлен',
    });
  } else {
    setErrors(response.errors);
    toast({
      title: 'Ошибка',
      description: response.message || 'Не удалось обновить профиль',
      variant: 'destructive',
    });
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Профиль" description="Изменение данных профиля" />

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div class="grid gap-2">
            <FormLabel required> Имя </FormLabel>
            <Input
              id="name"
              v-model="form.name"
              :aria-invalid="!!errors?.name"
              type="text"
              autocomplete="name"
              placeholder="Введите имя"
              required
            />
            <FormErrorMessage :messages="errors?.name" />
          </div>

          <div class="grid gap-2">
            <FormLabel required> Email </FormLabel>
            <Input
              id="email"
              v-model="form.email"
              :aria-invalid="!!errors?.email"
              type="email"
              autocomplete="email"
              placeholder="Введите email"
              required
            />
            <FormErrorMessage :messages="errors?.email" />
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="processing" type="submit"> Сохранить </Button>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

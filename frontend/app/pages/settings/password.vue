<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/app/HeadingSmall.vue';
import type { BreadcrumbItem } from '@/types';
import FormLabel from '@/components/form/FormLabel.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '~/composables/useForm';
import { useUserAPI } from '~/api/user';
import FormErrorMessage from '~/components/form/FormErrorMessage.vue';
import { useToast } from '~/components/ui/toast/use-toast';

useHead({
  title: 'Пароль',
});

const router = useRouter();
const { toast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Настройки пароля',
    href: router.resolve({ name: 'settings-password' }).fullPath,
  },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const { form, data, errors, setErrors, processing, reset } = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const userAPI = useUserAPI();

async function updatePassword() {
  const response = await userAPI.updateCurrentUserPassword(data());

  if (response.success) {
    reset();
    toast({
      title: 'Успех',
      description: 'Пароль успешно изменен',
    });
  } else {
    setErrors(response.errors);
    toast({
      title: 'Ошибка',
      description: response.message || 'Не удалось изменить пароль',
      variant: 'destructive',
    });

    if (response.errors?.password) {
      if (passwordInput.value instanceof HTMLInputElement) {
        passwordInput.value.focus();
      }
    }

    if (response.errors?.current_password) {
      if (currentPasswordInput.value instanceof HTMLInputElement) {
        currentPasswordInput.value.focus();
      }
    }
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          title="Изменить пароль"
          description="Убедитесь, что используете длинный и надёжный пароль"
        />

        <form class="space-y-6" @submit.prevent="updatePassword">
          <div class="grid gap-2">
            <FormLabel required> Текущий пароль </FormLabel>
            <Input
              id="current_password"
              ref="currentPasswordInput"
              v-model="form.current_password"
              type="password"
              autocomplete="current-password"
              placeholder="Введите текущий пароль"
              required
            />
            <FormErrorMessage :messages="errors?.current_password" />
          </div>

          <div class="grid gap-2">
            <FormLabel required> Новый пароль </FormLabel>
            <Input
              id="password"
              ref="passwordInput"
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              placeholder="Введите новый пароль"
              required
            />
            <FormErrorMessage :messages="errors?.password" />
          </div>

          <div class="grid gap-2">
            <FormLabel required> Подтвердите пароль </FormLabel>
            <Input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              placeholder="Введите новый пароль ещё раз"
              required
            />
            <FormErrorMessage :messages="errors?.password_confirmation" />
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="processing" type="submit">
              Сохранить
            </Button>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { LoaderCircle } from 'lucide-vue-next';
import { useUserStore } from '~/stores/user';
import { useAuthAPI } from '~/api/auth';
import { useForm } from '~/composables/useForm';
import AuthBase from '~/layouts/AuthLayout.vue';
import { Input } from '~/components/ui/input';
import { Checkbox } from '~/components/ui/checkbox';
import { Button } from '~/components/ui/button';
import FormErrorMessage from '~/components/form/FormErrorMessage.vue';

useHead({
  title: 'Войти',
});

definePageMeta({
  meta: {
    withoutAuth: true,
  },
});

const { form, errors, processing, data, setErrors } = useForm({
  email: '',
  password: '',
  remember: false,
});

const responseMessage = ref();

const router = useRouter();
const route = useRoute();

const userStore = useUserStore();
const authAPI = useAuthAPI();

async function handleLogin() {
  processing.value = true;

  const response = await authAPI.login(data());

  if (response.success) {
    await userStore.setUserToken(response.data.token, response.data.remember);
    await userStore.setUserData(response.data.user);

    if (route.query.redirect) {
      await router.replace(route.query.redirect as string);
    } else {
      await router.push({ name: 'index' });
    }
  } else {
    setErrors(response.errors ?? {});
    responseMessage.value = response.message ?? null;
    processing.value = false;
  }
}
</script>

<template>
  <AuthBase
    title="Войдите в свой аккаунт"
    description="Введите ваш логин и пароль ниже для входа"
  >
    <form class="flex flex-col gap-6" @submit.prevent="handleLogin">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <label for="email" class="text-sm font-medium">
            Email
          </label>
          <Input
            v-model="form.email"
            id="email"
            type="email"
            name="email"
            class="w-full"
            required
            autofocus
            :tabindex="1"
            autocomplete="email"
            placeholder="email@example.com"
          />
          <FormErrorMessage v-if="responseMessage && !Object.keys(errors)?.length" :messages="[responseMessage]" />
          <FormErrorMessage :messages="errors?.email" />
        </div>

        <div class="grid gap-2 w-full">
          <div class="flex items-center justify-between">
            <label for="password" class="text-sm font-medium">Пароль</label>
          </div>
          <Input
            v-model="form.password"
            id="password"
            type="password"
            name="password"
            class="w-full"
            required
            :tabindex="2"
            autocomplete="current-password"
            placeholder="Пароль"
          />
          <FormErrorMessage :messages="errors?.password" />
        </div>

        <div class="flex items-center space-x-2">
          <Checkbox
            :checked="form.remember"
            @update:checked="(val) => form.remember = val"
            id="remember"
            name="remember"
            :tabindex="3"
          />
          <label for="remember" class="text-sm">Запомнить меня</label>
        </div>

        <Button
          type="submit"
          class="mt-4 w-full"
          :tabindex="4"
          :disabled="processing"
        >
          <LoaderCircle v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
          Войти
        </Button>
      </div>
    </form>
  </AuthBase>
</template>

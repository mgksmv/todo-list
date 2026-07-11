import { reactive, ref, toRaw, computed } from 'vue';

export function useForm<T extends Record<string, any> = Record<string, any>>(
  initialData: T = {} as T,
) {
  const form = reactive({ ...initialData });
  const defaults = ref({ ...initialData });

  const errors = ref<Record<string, string[]>>({});
  const processing = ref(false);

  const hasErrors = computed(() => Object.keys(errors.value).length > 0);

  function setDefaults(data: Partial<T>) {
    defaults.value = { ...defaults.value, ...data };
    Object.assign(form, defaults.value);
  }

  function reset() {
    Object.assign(form, defaults.value);
    errors.value = {};
  }

  function setErrors(newErrors: Record<string, string[]>) {
    errors.value = newErrors || {};
  }

  function setError(field: keyof T | string, message: string) {
    errors.value[field as string] = [message];
  }

  function clearErrors() {
    errors.value = {};
  }

  function data() {
    return toRaw(form);
  }

  return {
    form,
    errors,
    processing,
    hasErrors,
    setError,
    setDefaults,
    reset,
    setErrors,
    clearErrors,
    data,
  };
}

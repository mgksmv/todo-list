<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
  <svg
    viewBox="0 0 920 220"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    :class="className"
    v-bind="$attrs"
  >

    <!-- Kanban Icon -->
    <g transform="translate(20,35)">

      <!-- Columns -->
      <rect x="0" y="0" width="90" height="140" rx="14" fill="#F3F4F6"/>
      <rect x="100" y="0" width="90" height="140" rx="14" fill="#F3F4F6"/>
      <rect x="200" y="0" width="90" height="140" rx="14" fill="#F3F4F6"/>

      <!-- Headers -->
      <rect x="0" y="0" width="90" height="24" rx="14" fill="#3B82F6"/>
      <rect x="100" y="0" width="90" height="24" rx="14" fill="#FBBF24"/>
      <rect x="200" y="0" width="90" height="24" rx="14" fill="#34D399"/>

      <!-- Cards -->
      <rect x="18" y="42" width="54" height="28" rx="8" fill="#DBEAFE"/>
      <rect x="18" y="82" width="54" height="28" rx="8" fill="#DBEAFE"/>

      <rect x="118" y="42" width="54" height="28" rx="8" fill="#FDE7B0"/>
      <rect x="118" y="82" width="54" height="28" rx="8" fill="#FDE7B0"/>

      <rect x="218" y="42" width="54" height="28" rx="8" fill="#CFF7E4"/>
      <rect x="218" y="82" width="54" height="28" rx="8" fill="#CFF7E4"/>

    </g>

    <!-- Text -->
    <text
x="360"
          y="135"
          font-family="Inter, Arial, Helvetica, sans-serif"
          font-size="72"
          font-weight="700"
          fill="#0F172A"
          letter-spacing="-1.5">
      To-Do List
    </text>

  </svg>
</template>

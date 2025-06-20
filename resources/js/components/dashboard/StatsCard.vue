<script setup lang="ts">
defineProps<{
  title: string;
  value: string | number;
  icon?: string;
  trend?: {
    value: number;
    isPositive: boolean;
  };
  color?: string;
}>();
</script>

<template>
  <div class="flex h-full flex-col rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</h3>
      <div v-if="icon" class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
        <span class="text-lg" v-html="icon"></span>
      </div>
    </div>
    <div class="mt-2 flex items-baseline">
      <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ value }}</div>
      <div v-if="trend" class="ml-2">
        <span
          class="text-xs font-medium"
          :class="trend.isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
        >
          {{ trend.isPositive ? '↑' : '↓' }} {{ Math.abs(trend.value) }}%
        </span>
      </div>
    </div>
  </div>
</template>

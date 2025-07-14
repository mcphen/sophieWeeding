<script setup lang="ts">
import { ref, watch, computed } from 'vue';

interface AlertContent {
  type: 'info' | 'success' | 'warning' | 'error';
  title: string;
  message: string;
}

const props = defineProps<{
  modelValue: AlertContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: AlertContent): void;
}>();

const alertType = ref(props.modelValue.type);
const title = ref(props.modelValue.title);
const message = ref(props.modelValue.message);

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  alertType.value = newValue.type;
  title.value = newValue.title;
  message.value = newValue.message;
}, { deep: true });

// Update the model
function updateModel(updates: Partial<AlertContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}

// Get alert styles based on type
const alertStyles = computed(() => {
  switch (alertType.value) {
    case 'info':
      return {
        bg: 'bg-blue-50',
        border: 'border-blue-500',
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-blue-500',
        title: 'text-blue-800',
        message: 'text-blue-700'
      };
    case 'success':
      return {
        bg: 'bg-green-50',
        border: 'border-green-500',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-green-500',
        title: 'text-green-800',
        message: 'text-green-700'
      };
    case 'warning':
      return {
        bg: 'bg-yellow-50',
        border: 'border-yellow-500',
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        iconColor: 'text-yellow-500',
        title: 'text-yellow-800',
        message: 'text-yellow-700'
      };
    case 'error':
      return {
        bg: 'bg-red-50',
        border: 'border-red-500',
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-red-500',
        title: 'text-red-800',
        message: 'text-red-700'
      };
    default:
      return {
        bg: 'bg-gray-50',
        border: 'border-gray-500',
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        iconColor: 'text-gray-500',
        title: 'text-gray-800',
        message: 'text-gray-700'
      };
  }
});
</script>

<template>
  <div class="block-alert space-y-4">
    <!-- Alert type selection -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Type d'alerte</label>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <label
          class="flex items-center justify-center p-3 border rounded-md cursor-pointer"
          :class="[
            alertType === 'info' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500 ring-opacity-50' : 'border-gray-300 hover:bg-gray-50'
          ]"
        >
          <input
            type="radio"
            value="info"
            v-model="alertType"
            @change="updateModel({ type: alertType })"
            class="sr-only"
          >
          <div class="flex flex-col items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-gray-900">Information</span>
          </div>
        </label>

        <label
          class="flex items-center justify-center p-3 border rounded-md cursor-pointer"
          :class="[
            alertType === 'success' ? 'bg-green-50 border-green-500 ring-2 ring-green-500 ring-opacity-50' : 'border-gray-300 hover:bg-gray-50'
          ]"
        >
          <input
            type="radio"
            value="success"
            v-model="alertType"
            @change="updateModel({ type: alertType })"
            class="sr-only"
          >
          <div class="flex flex-col items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-gray-900">Succès</span>
          </div>
        </label>

        <label
          class="flex items-center justify-center p-3 border rounded-md cursor-pointer"
          :class="[
            alertType === 'warning' ? 'bg-yellow-50 border-yellow-500 ring-2 ring-yellow-500 ring-opacity-50' : 'border-gray-300 hover:bg-gray-50'
          ]"
        >
          <input
            type="radio"
            value="warning"
            v-model="alertType"
            @change="updateModel({ type: alertType })"
            class="sr-only"
          >
          <div class="flex flex-col items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-medium text-gray-900">Avertissement</span>
          </div>
        </label>

        <label
          class="flex items-center justify-center p-3 border rounded-md cursor-pointer"
          :class="[
            alertType === 'error' ? 'bg-red-50 border-red-500 ring-2 ring-red-500 ring-opacity-50' : 'border-gray-300 hover:bg-gray-50'
          ]"
        >
          <input
            type="radio"
            value="error"
            v-model="alertType"
            @change="updateModel({ type: alertType })"
            class="sr-only"
          >
          <div class="flex flex-col items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-gray-900">Erreur</span>
          </div>
        </label>
      </div>
    </div>

    <!-- Alert title -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Titre de l'alerte</label>
      <input
        type="text"
        v-model="title"
        @input="updateModel({ title: title })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Titre de l'alerte (optionnel)"
      >
    </div>

    <!-- Alert message -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Message de l'alerte</label>
      <textarea
        v-model="message"
        @input="updateModel({ message: message })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Message de l'alerte"
        rows="3"
      ></textarea>
    </div>

    <!-- Preview -->
    <div v-if="message" class="mt-4">
      <h4 class="text-sm font-medium text-gray-700 mb-2">Prévisualisation</h4>
      <div
        class="p-4 rounded-md border-l-4"
        :class="[alertStyles.bg, alertStyles.border]"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5" :class="alertStyles.iconColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="alertStyles.icon" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 v-if="title" class="text-sm font-medium" :class="alertStyles.title">{{ title }}</h3>
            <div class="text-sm" :class="alertStyles.message" v-html="message.replace(/\n/g, '<br>')"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

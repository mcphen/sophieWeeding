<script setup lang="ts">
import { ref, watch } from 'vue';

interface FaqItem {
  question: string;
  answer: string;
}

const props = defineProps<{
  modelValue: FaqItem[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: FaqItem[]): void;
}>();

const items = ref<FaqItem[]>([...props.modelValue]);
const openItems = ref<Set<number>>(new Set());

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  items.value = [...newValue];
}, { deep: true });

// Update the model
function updateModel() {
  emit('update:modelValue', items.value);
}

// Add a new FAQ item
function addItem() {
  items.value.push({ question: '', answer: '' });
  updateModel();
  // Open the newly added item
  openItems.value.add(items.value.length - 1);
}

// Remove an FAQ item
function removeItem(index: number) {
  items.value.splice(index, 1);
  updateModel();
  // Remove from open items
  openItems.value.delete(index);
}

// Update a specific item
function updateItem(index: number, updates: Partial<FaqItem>) {
  items.value[index] = { ...items.value[index], ...updates };
  updateModel();
}

// Toggle item visibility
function toggleItem(index: number) {
  if (openItems.value.has(index)) {
    openItems.value.delete(index);
  } else {
    openItems.value.add(index);
  }
}

// Move an item up
function moveUp(index: number) {
  if (index > 0) {
    const temp = items.value[index];
    items.value[index] = items.value[index - 1];
    items.value[index - 1] = temp;
    updateModel();
  }
}

// Move an item down
function moveDown(index: number) {
  if (index < items.value.length - 1) {
    const temp = items.value[index];
    items.value[index] = items.value[index + 1];
    items.value[index + 1] = temp;
    updateModel();
  }
}
</script>

<template>
  <div class="block-faq space-y-4">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-medium text-gray-900">Questions fréquentes</h3>
      <button
        type="button"
        @click="addItem"
        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-1 text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Ajouter une question
      </button>
    </div>

    <div v-if="items.length === 0" class="text-center py-8 bg-gray-50 rounded-md border border-dashed border-gray-300">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-gray-500">Aucune question ajoutée. Cliquez sur "Ajouter une question" pour commencer.</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="(item, index) in items"
        :key="index"
        class="border border-gray-200 rounded-md overflow-hidden"
      >
        <div class="bg-gray-50 p-3 flex justify-between items-center">
          <button
            type="button"
            @click="toggleItem(index)"
            class="flex-1 text-left flex items-center gap-2 font-medium text-gray-700"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 transition-transform"
              :class="{ 'rotate-90': openItems.has(index) }"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span>{{ item.question || 'Nouvelle question' }}</span>
          </button>

          <div class="flex items-center gap-1">
            <button
              type="button"
              @click="moveUp(index)"
              :disabled="index === 0"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30 rounded-md hover:bg-gray-100"
              title="Déplacer vers le haut"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="moveDown(index)"
              :disabled="index === items.length - 1"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30 rounded-md hover:bg-gray-100"
              title="Déplacer vers le bas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeItem(index)"
              class="p-1 text-red-500 hover:text-red-700 rounded-md hover:bg-red-50"
              title="Supprimer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <div v-if="openItems.has(index)" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
            <input
              type="text"
              v-model="item.question"
              @input="updateItem(index, { question: item.question })"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Saisissez votre question ici..."
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Réponse</label>
            <textarea
              v-model="item.answer"
              @input="updateItem(index, { answer: item.answer })"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Saisissez votre réponse ici..."
              rows="4"
            ></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

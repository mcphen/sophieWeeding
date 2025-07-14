<script setup lang="ts">
import { ref, watch } from 'vue';

interface QuoteContent {
  text: string;
  author: string;
}

const props = defineProps<{
  modelValue: QuoteContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: QuoteContent): void;
}>();

const text = ref(props.modelValue.text);
const author = ref(props.modelValue.author);

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  text.value = newValue.text;
  author.value = newValue.author;
}, { deep: true });

// Update the model
function updateModel(updates: Partial<QuoteContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}
</script>

<template>
  <div class="block-quote space-y-4">
    <!-- Quote text -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Citation</label>
      <textarea
        v-model="text"
        @input="updateModel({ text: text })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Saisissez votre citation ici..."
        rows="4"
      ></textarea>
    </div>

    <!-- Author -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Auteur</label>
      <input
        type="text"
        v-model="author"
        @input="updateModel({ author: author })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Nom de l'auteur"
      >
    </div>

    <!-- Preview -->
    <div v-if="text" class="mt-4 p-6 bg-gray-50 border-l-4 border-gray-300 rounded-r-md">
      <blockquote class="text-lg italic text-gray-700">
        "{{ text }}"
      </blockquote>
      <div v-if="author" class="mt-2 text-right text-gray-600">
        — {{ author }}
      </div>
    </div>
  </div>
</template>

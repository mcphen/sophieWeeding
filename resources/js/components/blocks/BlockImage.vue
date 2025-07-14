<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { getImageUrl } from '@/utils/imageHelper';

interface ImageContent {
  url: string;
  caption: string;
}

const props = defineProps<{
  modelValue: ImageContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: ImageContent): void;
}>();

const imagePreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const caption = ref(props.modelValue.caption);

// Computed property to get the correct image URL based on environment
const displayUrl = computed(() => {
  // If we have a local preview (from file upload), use that
  if (imagePreview.value) {
    return imagePreview.value;
  }

  // Otherwise, use the URL from the model, processed through our helper
  return getImageUrl(props.modelValue.url);
});

// Update caption when prop changes
watch(() => props.modelValue.caption, (newValue) => {
  caption.value = newValue;
});

// Update the model
function updateModel(updates: Partial<ImageContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}

// Handle image upload
function handleImageUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0] || null;

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string;

      // In a real app, you would upload the file to the server here
      // and then update the model with the URL
      // For now, we'll just use the data URL as a placeholder
      updateModel({ url: e.target?.result as string });
    };
    reader.readAsDataURL(file);
  }
}

// Remove the image
function removeImage() {
  imagePreview.value = null;
  updateModel({ url: '' });
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

// Initialize image preview if URL exists and is a data URL
if (props.modelValue.url && props.modelValue.url.startsWith('data:')) {
  imagePreview.value = props.modelValue.url;
}
</script>

<template>
  <div class="block-image space-y-4">
    <!-- Image upload/preview -->
    <div class="flex flex-col items-center gap-4">
      <!-- Preview -->
      <div
        v-if="displayUrl"
        class="relative w-full max-w-2xl border rounded-lg overflow-hidden"
      >
        <img :src="displayUrl" alt="Image" class="w-full h-auto object-contain">
        <button
          @click.prevent="removeImage"
          type="button"
          class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!-- Upload -->
      <div v-if="!displayUrl" class="flex items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
        <div class="text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <p class="mt-2 text-sm text-gray-500">Ajouter une image</p>
        </div>
      </div>

      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        class="hidden"
        @change="handleImageUpload"
      >

      <button
        v-if="!displayUrl"
        type="button"
        @click="fileInput?.click()"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
        </svg>
        Sélectionner une image
      </button>
    </div>

    <!-- Caption -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Légende de l'image</label>
      <input
        type="text"
        v-model="caption"
        @input="updateModel({ caption: caption })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Ajouter une légende..."
      >
    </div>
  </div>
</template>

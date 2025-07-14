<script setup lang="ts">
import { ref, computed } from 'vue';
import { getImageUrl } from '@/utils/imageHelper';

interface GalleryImage {
  id: string;
  file: File | null;
  preview: string;
  caption: string;
}

const props = defineProps<{
  modelValue: GalleryImage[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: GalleryImage[]): void;
}>();

// Create a local copy of the images
const images = ref<GalleryImage[]>(props.modelValue || []);

// Update the parent when images change
const updateImages = () => {
  emit('update:modelValue', images.value);
};

// Add a new image
const addImage = () => {
  const fileInput = document.createElement('input');
  fileInput.type = 'file';
  fileInput.accept = 'image/*';
  fileInput.multiple = true;

  fileInput.onchange = (event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;

    if (files && files.length > 0) {
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();

        reader.onload = (e) => {
          const newImage: GalleryImage = {
            id: Date.now().toString() + i,
            file: file,
            preview: e.target?.result as string,
            caption: ''
          };

          images.value.push(newImage);
          updateImages();
        };

        reader.readAsDataURL(file);
      }
    }
  };

  fileInput.click();
};

// Remove an image
const removeImage = (id: string) => {
  const index = images.value.findIndex(image => image.id === id);
  if (index !== -1) {
    images.value.splice(index, 1);
    updateImages();
  }
};

// Move an image up
const moveUp = (id: string) => {
  const index = images.value.findIndex(image => image.id === id);
  if (index > 0) {
    const temp = images.value[index];
    images.value[index] = images.value[index - 1];
    images.value[index - 1] = temp;
    updateImages();
  }
};

// Move an image down
const moveDown = (id: string) => {
  const index = images.value.findIndex(image => image.id === id);
  if (index < images.value.length - 1) {
    const temp = images.value[index];
    images.value[index] = images.value[index + 1];
    images.value[index + 1] = temp;
    updateImages();
  }
};

// Update an image caption
const updateCaption = (id: string, caption: string) => {
  const image = images.value.find(image => image.id === id);
  if (image) {
    image.caption = caption;
    updateImages();
  }
};

// Check if there are any images
const hasImages = computed(() => images.value.length > 0);

// Get the correct image URL based on environment
const getCorrectImageUrl = (image: GalleryImage): string => {
  // If the preview is a data URL (from file upload), use it directly
  if (image.preview.startsWith('data:')) {
    return image.preview;
  }

  // Otherwise, process the URL through our helper
  return getImageUrl(image.preview) || '';
};
</script>

<template>
  <div class="block-gallery bg-white p-4 rounded-lg border border-gray-300">
    <div class="mb-4 flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">Galerie d'images</h3>
      <button
        type="button"
        @click="addImage"
        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-1"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Ajouter des images
      </button>
    </div>

    <div v-if="!hasImages" class="text-center py-8 text-gray-500">
      Aucune image dans la galerie. Cliquez sur "Ajouter des images" pour commencer.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="(image, index) in images"
        :key="image.id"
        class="bg-gray-50 p-3 rounded-md border border-gray-200"
      >
        <div class="flex justify-between items-start mb-2">
          <h4 class="font-medium text-gray-700">Image {{ index + 1 }}</h4>
          <div class="flex gap-1">
            <button
              type="button"
              @click="moveUp(image.id)"
              :disabled="index === 0"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30"
              title="Déplacer vers le haut"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="moveDown(image.id)"
              :disabled="index === images.length - 1"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30"
              title="Déplacer vers le bas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeImage(image.id)"
              class="p-1 text-red-500 hover:text-red-700"
              title="Supprimer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <div class="relative aspect-video mb-2 overflow-hidden rounded-md border border-gray-300">
          <img
            :src="getCorrectImageUrl(image)"
            :alt="`Image ${index + 1}`"
            class="w-full h-full object-cover"
          />
        </div>

        <div>
          <label :for="`image-caption-${image.id}`" class="block text-sm font-medium text-gray-700 mb-1">Légende</label>
          <input
            :id="`image-caption-${image.id}`"
            type="text"
            v-model="image.caption"
            @input="updateCaption(image.id, image.caption)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            placeholder="Légende de l'image"
          >
        </div>
      </div>
    </div>
  </div>
</template>

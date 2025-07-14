<script setup lang="ts">
import { ref, watch } from 'vue';

interface FileContent {
  url: string;
  name: string;
  size: string;
  type: string;
}

const props = defineProps<{
  modelValue: FileContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: FileContent): void;
}>();

const fileUrl = ref(props.modelValue.url);
const fileName = ref(props.modelValue.name);
const fileSize = ref(props.modelValue.size);
const fileType = ref(props.modelValue.type);
const fileInput = ref<HTMLInputElement | null>(null);

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  fileUrl.value = newValue.url;
  fileName.value = newValue.name;
  fileSize.value = newValue.size;
  fileType.value = newValue.type;
}, { deep: true });

// Update the model
function updateModel(updates: Partial<FileContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}

// Handle file upload
function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0] || null;

  if (file) {
    // Format file size
    const formattedSize = formatFileSize(file.size);

    // In a real app, you would upload the file to the server here
    // and then update the model with the URL
    // For now, we'll just use the file name and size
    const reader = new FileReader();
    reader.onload = (e) => {
      updateModel({
        url: e.target?.result as string, // This would be a server URL in production
        name: file.name,
        size: formattedSize,
        type: file.type
      });
    };
    reader.readAsDataURL(file);
  }
}

// Format file size
function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';

  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Get file icon based on type
function getFileIcon(): string {
  const type = fileType.value.split('/')[0];

  switch (type) {
    case 'image':
      return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
    case 'video':
      return 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
    case 'audio':
      return 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3';
    case 'application':
      if (fileType.value.includes('pdf')) {
        return 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
      }
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    default:
      return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
  }
}

// Remove the file
function removeFile() {
  updateModel({
    url: '',
    name: '',
    size: '',
    type: ''
  });

  if (fileInput.value) {
    fileInput.value.value = '';
  }
}
</script>

<template>
  <div class="block-file space-y-4">
    <!-- File upload/preview -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Fichier téléchargeable</label>

      <!-- File preview -->
      <div
        v-if="fileUrl && fileName"
        class="border border-gray-200 rounded-md p-4 bg-gray-50 flex items-center justify-between"
      >
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getFileIcon()" />
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ fileName }}</h4>
            <p class="text-xs text-gray-500">{{ fileSize }}</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a
            :href="fileUrl"
            download
            class="p-1.5 text-blue-600 hover:text-blue-800 rounded-md hover:bg-blue-50"
            title="Télécharger"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
          <button
            @click="removeFile"
            type="button"
            class="p-1.5 text-red-500 hover:text-red-700 rounded-md hover:bg-red-50"
            title="Supprimer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Upload button -->
      <div v-if="!fileUrl || !fileName" class="flex flex-col items-center gap-4">
        <div class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
          <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Cliquez pour ajouter un fichier</p>
            <p class="text-xs text-gray-400">PDF, DOC, XLS, ZIP, etc.</p>
          </div>
        </div>

        <input
          ref="fileInput"
          type="file"
          class="hidden"
          @change="handleFileUpload"
        >

        <button
          type="button"
          @click="fileInput?.click()"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          Sélectionner un fichier
        </button>
      </div>

      <!-- File name input -->
      <div v-if="fileUrl && fileName" class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du fichier (affiché)</label>
        <input
          type="text"
          v-model="fileName"
          @input="updateModel({ name: fileName })"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Nom du fichier"
        >
      </div>
    </div>
  </div>
</template>

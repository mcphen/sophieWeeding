<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();

const editorRef = ref<HTMLDivElement | null>(null);
let editor: Quill;

onMounted(() => {
  if (editorRef.value) {
    editor = new Quill(editorRef.value, {
      theme: 'snow',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          [{ 'color': [] }, { 'background': [] }],
          ['link', 'image'],
          ['clean']
        ]
      },
      placeholder: 'Écrivez votre texte ici...'
    });

    // Set initial content
    if (props.modelValue) {
      editor.root.innerHTML = props.modelValue;
    }

    // Update model when content changes
    editor.on('text-change', () => {
      emit('update:modelValue', editor.root.innerHTML);
    });
  }
});

// Update editor content when modelValue changes externally
watch(() => props.modelValue, (newValue) => {
  if (editor && newValue !== editor.root.innerHTML) {
    editor.root.innerHTML = newValue;
  }
});
</script>

<template>
  <div class="block-text">
    <div ref="editorRef" class="min-h-[200px] border border-gray-300 rounded-md"></div>
  </div>
</template>

<style scoped>
.block-text :deep(.ql-editor) {
  min-height: 150px;
  max-height: 400px;
  overflow-y: auto;
}

.block-text :deep(.ql-toolbar) {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
  background-color: #f9fafb;
  border-color: #e5e7eb;
}

.block-text :deep(.ql-container) {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
  border-color: #e5e7eb;
  background-color: white;
}

.block-text :deep(.ql-toolbar button:hover) {
  color: #2563eb;
}

.block-text :deep(.ql-toolbar .ql-active) {
  color: #2563eb;
}
</style>

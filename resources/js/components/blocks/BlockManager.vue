<script setup lang="ts">
import { ref, computed } from 'vue';
import BlockText from './BlockText.vue';
import BlockTimeline from './BlockTimeline.vue';
import BlockGallery from './BlockGallery.vue';
import BlockImage from './BlockImage.vue';
import BlockVideo from './BlockVideo.vue';
import BlockQuote from './BlockQuote.vue';
import BlockFaq from './BlockFaq.vue';
import BlockEmbed from './BlockEmbed.vue';
import BlockFile from './BlockFile.vue';
import BlockAlert from './BlockAlert.vue';

interface Block {
  id: string;
  type: 'text' | 'timeline' | 'gallery' | 'image' | 'video' | 'quote' | 'faq' | 'embed' | 'file' | 'alert';
  content: any;
  position: number;
}

const props = defineProps<{
  modelValue: Block[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: Block[]): void;
}>();

// Create a local copy of the blocks
const blocks = ref<Block[]>(props.modelValue || []);

// Update the parent when blocks change
const updateBlocks = () => {
  // Ensure positions are sequential
  blocks.value.forEach((block, index) => {
    block.position = index + 1;
  });

  emit('update:modelValue', blocks.value);
};

// Add a new block
const addBlock = (type: 'text' | 'timeline' | 'gallery' | 'image' | 'video' | 'quote' | 'faq' | 'embed' | 'file' | 'alert') => {
  let defaultContent;

  switch (type) {
    case 'text':
      defaultContent = '';
      break;
    case 'timeline':
      defaultContent = [];
      break;
    case 'gallery':
      defaultContent = [];
      break;
    case 'image':
      defaultContent = { url: '', caption: '' };
      break;
    case 'video':
      defaultContent = { url: '', type: 'youtube', caption: '' };
      break;
    case 'quote':
      defaultContent = { text: '', author: '' };
      break;
    case 'faq':
      defaultContent = [{ question: '', answer: '' }];
      break;
    case 'embed':
      defaultContent = { code: '', caption: '' };
      break;
    case 'file':
      defaultContent = { url: '', name: '', size: '', type: '' };
      break;
    case 'alert':
      defaultContent = { type: 'info', title: '', message: '' };
      break;
    default:
      defaultContent = '';
  }

  const newBlock: Block = {
    id: Date.now().toString(),
    type,
    content: defaultContent,
    position: blocks.value.length + 1
  };

  blocks.value.push(newBlock);
  updateBlocks();
};

// Remove a block
const removeBlock = (id: string) => {
  const index = blocks.value.findIndex(block => block.id === id);
  if (index !== -1) {
    blocks.value.splice(index, 1);
    updateBlocks();
  }
};

// Move a block up
const moveUp = (id: string) => {
  const index = blocks.value.findIndex(block => block.id === id);
  if (index > 0) {
    const temp = blocks.value[index];
    blocks.value[index] = blocks.value[index - 1];
    blocks.value[index - 1] = temp;
    updateBlocks();
  }
};

// Move a block down
const moveDown = (id: string) => {
  const index = blocks.value.findIndex(block => block.id === id);
  if (index < blocks.value.length - 1) {
    const temp = blocks.value[index];
    blocks.value[index] = blocks.value[index + 1];
    blocks.value[index + 1] = temp;
    updateBlocks();
  }
};

// Update block content
const updateBlockContent = (id: string, content: any) => {
  const block = blocks.value.find(block => block.id === id);
  if (block) {
    block.content = content;
    updateBlocks();
  }
};

// Check if there are any blocks
const hasBlocks = computed(() => blocks.value.length > 0);

// Get the component for a block type
const getComponentForType = (type: string) => {
  switch (type) {
    case 'text':
      return BlockText;
    case 'timeline':
      return BlockTimeline;
    case 'gallery':
      return BlockGallery;
    case 'image':
      return BlockImage;
    case 'video':
      return BlockVideo;
    case 'quote':
      return BlockQuote;
    case 'faq':
      return BlockFaq;
    case 'embed':
      return BlockEmbed;
    case 'file':
      return BlockFile;
    case 'alert':
      return BlockAlert;
    default:
      return null;
  }
};
</script>

<template>
  <div class="block-manager">
    <!-- Block type buttons -->
    <div class="mb-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
      <button
        type="button"
        @click="addBlock('text')"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Texte
      </button>

      <button
        type="button"
        @click="addBlock('image')"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Image
      </button>

      <button
        type="button"
        @click="addBlock('gallery')"
        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Galerie
      </button>

      <button
        type="button"
        @click="addBlock('timeline')"
        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Timeline
      </button>

      <button
        type="button"
        @click="addBlock('quote')"
        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Citation
      </button>

      <!-- Hidden block types (faq, embed, file, alert) -->
      <!--
      <button
        type="button"
        @click="addBlock('faq')"
        class="px-4 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        FAQ
      </button>

      <button
        type="button"
        @click="addBlock('embed')"
        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Embed
      </button>

      <button
        type="button"
        @click="addBlock('file')"
        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Fichier
      </button>

      <button
        type="button"
        @click="addBlock('alert')"
        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        Alerte
      </button>
      -->
    </div>

    <!-- Empty state -->
    <div v-if="!hasBlocks" class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun bloc de contenu</h3>
      <p class="text-gray-500 mb-4">Ajoutez des blocs de contenu à votre article en utilisant les boutons ci-dessus.</p>
    </div>

    <!-- Blocks list -->
    <div v-else class="space-y-6">
      <div
        v-for="(block, index) in blocks"
        :key="block.id"
        class="bg-white rounded-lg border border-gray-300 overflow-hidden"
      >
        <!-- Block header -->
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-300 flex justify-between items-center">
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-200 text-gray-800 text-sm font-medium">
              {{ index + 1 }}
            </span>
            <h3 class="font-medium text-gray-800">
              {{
                block.type === 'text' ? 'Texte' :
                block.type === 'timeline' ? 'Timeline' :
                block.type === 'gallery' ? 'Galerie' :
                block.type === 'image' ? 'Image' :
                block.type === 'video' ? 'Vidéo' :
                block.type === 'quote' ? 'Citation' :
                block.type === 'faq' ? 'FAQ' :
                block.type === 'embed' ? 'Embed' :
                block.type === 'file' ? 'Fichier' :
                block.type === 'alert' ? 'Alerte' :
                'Bloc'
              }}
            </h3>
          </div>

          <div class="flex items-center gap-1">
            <button
              type="button"
              @click="moveUp(block.id)"
              :disabled="index === 0"
              class="p-1.5 text-gray-500 hover:text-gray-700 disabled:opacity-30 rounded-md hover:bg-gray-100"
              title="Déplacer vers le haut"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="moveDown(block.id)"
              :disabled="index === blocks.length - 1"
              class="p-1.5 text-gray-500 hover:text-gray-700 disabled:opacity-30 rounded-md hover:bg-gray-100"
              title="Déplacer vers le bas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeBlock(block.id)"
              class="p-1.5 text-red-500 hover:text-red-700 rounded-md hover:bg-red-50"
              title="Supprimer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Block content -->
        <div class="p-4">
          <component
            :is="getComponentForType(block.type)"
            v-model="block.content"
            @update:modelValue="updateBlockContent(block.id, $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

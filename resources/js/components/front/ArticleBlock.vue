<script setup lang="ts">
import { getImageUrl } from '@/utils/imageHelper';

// Define the props for the component
defineProps<{
  block: {
    id: number;
    type: string;
    content: any;
    position: number;
  }
}>();

// Computed property to get the correct image URL based on environment
const getCorrectImageUrl = (path: string): string => {
  return getImageUrl(path) || '';
};
</script>

<template>
  <div class="article-block mb-8">
    <!-- Text Block -->
    <div v-if="block.type === 'text'" class="prose prose-lg max-w-none">
      <div v-html="typeof block.content === 'object' && block.content.html ? block.content.html : block.content"></div>
    </div>

    <!-- Image Block -->
    <div v-else-if="block.type === 'image'" class="my-8">
      <figure class="relative rounded-lg overflow-hidden shadow-lg">
        <img
          :src="getCorrectImageUrl(block.content.url)"
          :alt="block.content.caption || 'Image'"
          class="w-full h-auto"
        />
        <figcaption v-if="block.content.caption" class="text-sm text-gray-600 mt-2 italic text-center">
          {{ block.content.caption }}
        </figcaption>
      </figure>
    </div>

    <!-- Gallery Block -->
    <div v-else-if="block.type === 'gallery'" class="my-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <figure v-for="(image, index) in block.content" :key="index" class="relative rounded-lg overflow-hidden shadow-md">
          <img
            :src="getCorrectImageUrl(image.preview)"
            :alt="image.caption || `Gallery image ${index + 1}`"
            class="w-full h-64 object-cover"
          />
          <figcaption v-if="image.caption" class="text-sm text-gray-600 mt-1 italic text-center">
            {{ image.caption }}
          </figcaption>
        </figure>
      </div>
    </div>

    <!-- Timeline Block -->
    <div v-else-if="block.type === 'timeline'" class="my-8">
      <div class="relative border-l-2 border-primary pl-8 ml-4">
        <div v-for="(item, index) in block.content" :key="index" class="mb-8 relative">
          <!-- Timeline dot -->
          <div class="absolute -left-10 top-0 w-4 h-4 rounded-full bg-primary"></div>

          <!-- Timeline content -->
          <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ item.title }}</h3>
            <p class="text-gray-700">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quote Block -->
    <div v-else-if="block.type === 'quote'" class="my-8">
      <blockquote class="p-6 bg-gray-50 border-l-4 border-primary rounded-r-lg">
        <p class="text-xl italic text-gray-800 mb-2">{{ block.content.text }}</p>
        <footer v-if="block.content.author" class="text-right text-gray-600">
          — {{ block.content.author }}
        </footer>
      </blockquote>
    </div>

    <!-- Video Block -->
    <div v-else-if="block.type === 'video'" class="my-8">
      <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-lg">
        <!-- YouTube or Vimeo embed -->
        <iframe
          v-if="block.content.type === 'youtube' || block.content.type === 'vimeo'"
          :src="block.content.url"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
          class="w-full h-full"
        ></iframe>

        <!-- Local video -->
        <video v-else controls class="w-full h-full">
          <source :src="block.content.url" type="video/mp4">
          Votre navigateur ne supporte pas la lecture de vidéos.
        </video>
      </div>
      <p v-if="block.content.caption" class="text-sm text-gray-600 mt-2 italic text-center">
        {{ block.content.caption }}
      </p>
    </div>

    <!-- Default fallback for other block types -->
    <div v-else class="prose prose-lg max-w-none">
      <p class="text-gray-500 italic">Contenu non disponible</p>
    </div>
  </div>
</template>

<style scoped>
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-w-16 iframe,
.aspect-w-16 video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Ensure prose styling is consistent */
:deep(.prose) {
  color: #374151;
  max-width: 65ch;
  font-size: 1.125rem;
  line-height: 1.7777778;
}

:deep(.prose p) {
  margin-top: 1.3333333em;
  margin-bottom: 1.3333333em;
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4) {
  color: #111827;
  font-weight: 600;
  line-height: 1.25;
}

:deep(.prose a) {
  color: #2563eb;
  text-decoration: underline;
  font-weight: 500;
}

:deep(.prose strong) {
  color: #111827;
  font-weight: 600;
}

:deep(.prose ul),
:deep(.prose ol) {
  padding-left: 1.625em;
}

:deep(.prose img) {
  margin-top: 2em;
  margin-bottom: 2em;
  border-radius: 0.375rem;
}
</style>

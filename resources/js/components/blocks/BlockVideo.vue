<script setup lang="ts">
import { ref, watch } from 'vue';

interface VideoContent {
  url: string;
  type: 'youtube' | 'vimeo' | 'local';
  caption: string;
}

const props = defineProps<{
  modelValue: VideoContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: VideoContent): void;
}>();

const url = ref(props.modelValue.url);
const videoType = ref(props.modelValue.type);
const caption = ref(props.modelValue.caption);

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  url.value = newValue.url;
  videoType.value = newValue.type;
  caption.value = newValue.caption;
}, { deep: true });

// Update the model
function updateModel(updates: Partial<VideoContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}

// Get embedded video URL
function getEmbedUrl() {
  if (!url.value) return '';

  if (videoType.value === 'youtube') {
    // Extract YouTube video ID
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const match = url.value.match(youtubeRegex);
    if (match && match[1]) {
      return `https://www.youtube.com/embed/${match[1]}`;
    }
  } else if (videoType.value === 'vimeo') {
    // Extract Vimeo video ID
    const vimeoRegex = /(?:vimeo\.com\/|player\.vimeo\.com\/video\/)([0-9]+)/;
    const match = url.value.match(vimeoRegex);
    if (match && match[1]) {
      return `https://player.vimeo.com/video/${match[1]}`;
    }
  }

  return url.value;
}

// Check if URL is valid
function isValidUrl() {
  if (!url.value) return false;

  if (videoType.value === 'youtube') {
    return /(?:youtube\.com\/|youtu\.be\/)/.test(url.value);
  } else if (videoType.value === 'vimeo') {
    return /vimeo\.com\//.test(url.value);
  }

  return true;
}
</script>

<template>
  <div class="block-video space-y-4">
    <!-- Video type selection -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Type de vidéo</label>
      <div class="flex gap-4">
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="video-type"
            value="youtube"
            v-model="videoType"
            @change="updateModel({ type: videoType })"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
          >
          <span class="ml-2 text-gray-700">YouTube</span>
        </label>
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="video-type"
            value="vimeo"
            v-model="videoType"
            @change="updateModel({ type: videoType })"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
          >
          <span class="ml-2 text-gray-700">Vimeo</span>
        </label>
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="video-type"
            value="local"
            v-model="videoType"
            @change="updateModel({ type: videoType })"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
          >
          <span class="ml-2 text-gray-700">Vidéo locale</span>
        </label>
      </div>
    </div>

    <!-- Video URL -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ videoType === 'local' ? 'URL de la vidéo' : 'Lien de la vidéo ' + (videoType === 'youtube' ? 'YouTube' : 'Vimeo') }}
      </label>
      <input
        type="text"
        v-model="url"
        @input="updateModel({ url: url })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        :placeholder="videoType === 'youtube' ? 'https://www.youtube.com/watch?v=...' : videoType === 'vimeo' ? 'https://vimeo.com/...' : 'URL de la vidéo'"
      >
      <p v-if="url && !isValidUrl()" class="mt-1 text-sm text-red-600">
        Format d'URL invalide pour {{ videoType === 'youtube' ? 'YouTube' : videoType === 'vimeo' ? 'Vimeo' : 'une vidéo' }}
      </p>
    </div>

    <!-- Video preview -->
    <div v-if="url && isValidUrl() && (videoType === 'youtube' || videoType === 'vimeo')" class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
      <iframe
        :src="getEmbedUrl()"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
        class="w-full h-full"
      ></iframe>
    </div>

    <div v-else-if="url && isValidUrl() && videoType === 'local'" class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
      <video controls class="w-full h-full">
        <source :src="url" type="video/mp4">
        Votre navigateur ne supporte pas la lecture de vidéos.
      </video>
    </div>

    <!-- Caption -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Légende de la vidéo</label>
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
</style>

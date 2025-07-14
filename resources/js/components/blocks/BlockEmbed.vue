<script setup lang="ts">
import { ref, watch, computed } from 'vue';

interface EmbedContent {
  code: string;
  caption: string;
}

const props = defineProps<{
  modelValue: EmbedContent;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: EmbedContent): void;
}>();

const code = ref(props.modelValue.code);
const caption = ref(props.modelValue.caption);

// Update local refs when props change
watch(() => props.modelValue, (newValue) => {
  code.value = newValue.code;
  caption.value = newValue.caption;
}, { deep: true });

// Update the model
function updateModel(updates: Partial<EmbedContent>) {
  emit('update:modelValue', {
    ...props.modelValue,
    ...updates
  });
}

// Sanitize HTML to prevent XSS attacks
// This is a simple implementation - in a production app, you'd want to use a library like DOMPurify
const sanitizedCode = computed(() => {
  // Only allow iframe tags with specific attributes
  if (!code.value) return '';

  const div = document.createElement('div');
  div.innerHTML = code.value;

  const iframes = div.querySelectorAll('iframe');
  if (iframes.length === 0) return code.value;

  for (const iframe of iframes) {
    // Remove potentially dangerous attributes
    const allowedAttributes = ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'allow', 'title', 'style', 'class'];

    for (const attr of iframe.attributes) {
      if (!allowedAttributes.includes(attr.name)) {
        iframe.removeAttribute(attr.name);
      }
    }

    // Ensure src is from a trusted domain
    const src = iframe.getAttribute('src');
    if (src) {
      const trustedDomains = [
        'youtube.com', 'www.youtube.com', 'youtu.be',
        'vimeo.com', 'player.vimeo.com',
        'twitter.com', 'platform.twitter.com',
        'facebook.com', 'www.facebook.com',
        'instagram.com', 'www.instagram.com'
      ];

      const url = new URL(src, window.location.origin);
      const domain = url.hostname.replace(/^www\./, '');

      if (!trustedDomains.some(d => domain.endsWith(d))) {
        iframe.setAttribute('src', '');
      }
    }
  }

  return div.innerHTML;
});
</script>

<template>
  <div class="block-embed space-y-4">
    <!-- Embed code -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Code d'intégration</label>
      <textarea
        v-model="code"
        @input="updateModel({ code: code })"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
        placeholder="<iframe src='...' frameborder='0' allowfullscreen></iframe>"
        rows="5"
      ></textarea>
      <p class="mt-1 text-xs text-gray-500">
        Collez ici le code d'intégration (iframe) fourni par le service (Twitter, Instagram, etc.)
      </p>
    </div>

    <!-- Preview -->
    <div v-if="code" class="border border-gray-200 rounded-md overflow-hidden">
      <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
        <h3 class="text-sm font-medium text-gray-700">Prévisualisation</h3>
      </div>
      <div class="p-4 bg-white">
        <div v-html="sanitizedCode" class="w-full"></div>
      </div>
    </div>

    <!-- Caption -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Légende</label>
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

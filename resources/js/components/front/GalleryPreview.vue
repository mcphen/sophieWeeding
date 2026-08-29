<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

// Interfaces correspondant aux modèles PHP
interface Photo {
  id: number;
  album_id: number;
  image_path: string;
  caption: string | null;
  image_url: string;
  created_at: string;
  updated_at: string;
}

interface Album {
  id: number;
  title: string;
  event_date: string;
  theme: string;
  photos: Photo[];
  created_at: string;
  updated_at: string;
}

// Props
defineProps<{
  bgColor?: string;
}>();

// États
const albums = ref<Album[]>([]);
const isLoading = ref(true);

// Récupérer les 3 derniers albums
const fetchLatestAlbums = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get('/api/albums/latest');
    albums.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des albums:', error);
  } finally {
    isLoading.value = false;
  }
};

// Récupérer une photo d'un album
const getAlbumPhoto = (album: Album) => {
  if (album.photos && album.photos.length > 0) {
    return album.photos[0].image_url;
  }
  return '/images/placeholder.jpg';
};

// Formater la date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

onMounted(() => {
  fetchLatestAlbums();
});
</script>

<template>
  <section :class="['py-24', bgColor || 'bg-gray-50']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="font-display text-3xl sm:text-4xl font-semibold text-gray-900">Notre galerie</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Des moments capturés sur le terrain, aux côtés des communautés que nous accompagnons
        </p>
      </div>

      <!-- Loader -->
      <div v-if="isLoading" class="py-12 flex justify-center items-center">
        <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
      </div>

      <!-- Albums Grid - mosaic layout: the first album is featured larger -->
      <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4 auto-rows-[140px] sm:auto-rows-[160px] md:auto-rows-[180px]">
        <div
          v-for="(album, index) in albums"
          :key="album.id"
          v-reveal:up
          class="group relative overflow-hidden rounded-2xl shadow-md hover:shadow-xl transition-shadow"
          :class="index === 0 ? 'col-span-2 row-span-2' : 'col-span-1 row-span-1'"
        >
          <img
            :src="getAlbumPhoto(album)"
            :alt="album.title"
            class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-110"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
            <div class="p-4 text-white">
              <h3 class="font-semibold" :class="index === 0 ? 'text-xl' : 'text-sm'">{{ album.title }}</h3>
              <p class="text-xs" :class="index === 0 ? 'block' : 'hidden sm:block'">{{ formatDate(album.event_date) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-12">
        <Link
          :href="route('portfolio')"
          class="inline-block px-8 py-3 rounded-full border border-primary/30 text-primary hover:border-primary hover:bg-primary-bg-light font-medium transition-colors"
        >
          Voir toute la galerie
        </Link>
      </div>
    </div>
  </section>
</template>

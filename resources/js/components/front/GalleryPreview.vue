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
    //return `/storage/${album.photos[0].image_path}`;
    return `${album.photos[0].image_path}`;
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
  <section :class="['py-20', bgColor || 'bg-gray-50']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-serif font-bold text-gray-900">Notre Galerie</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Des moments magiques capturés lors des mariages que nous avons organisés
        </p>
      </div>

      <!-- Loader -->
      <div v-if="isLoading" class="py-12 flex justify-center items-center">
        <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-t-2 border-[#d1922f]"></div>
      </div>

      <!-- Albums Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="album in albums"
          :key="album.id"
          class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow"
        >
          <img
            :src="getAlbumPhoto(album)"
            :alt="album.title"
            class="w-full h-64 object-cover object-center"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
            <div class="p-4 text-white">
              <h3 class="text-lg font-semibold">{{ album.title }}</h3>
              <p class="text-sm">{{ formatDate(album.event_date) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-12">
        <Link
          :href="route('portfolio')"
          class="inline-block px-8 py-3 rounded-full border border-[#d1922f]/30 text-[#d1922f] hover:border-[#d1922f] hover:bg-[#d1922f]/5 font-medium transition-colors"
        >
          Voir toute la galerie
        </Link>
      </div>
    </div>
  </section>
</template>

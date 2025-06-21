<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, onMounted, computed } from 'vue';

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

interface PaginatedData {
  data: Album[];
  links: any[];
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

// Définition des props avec l'interface Album
const props = defineProps<{
  albums: PaginatedData;
  filters: {
    theme: string | null;
    search: string | null;
    year: string | null;
  };
}>();

// États
const selectedAlbum = ref<Album | null>(null);
const showLightbox = ref(false);
const currentPhotoIndex = ref(0);
const activeFilter = ref(props.filters.theme || 'all');
const searchQuery = ref(props.filters.search || '');
const selectedYear = ref(props.filters.year || 'all');
const isLoading = ref(false);

// Unique themes pour les filtres
const uniqueThemes = computed(() => {
  const themes = props.albums.data.map(album => album.theme);
  return [...new Set(themes)];
});

// Années disponibles pour le filtre par année
const availableYears = computed(() => {
  const years = props.albums.data.map(album => new Date(album.event_date).getFullYear().toString());
  return [...new Set(years)].sort((a, b) => parseInt(b) - parseInt(a)); // Tri décroissant
});

// Appliquer les filtres et naviguer
const applyFilters = () => {
  isLoading.value = true;

  const params: Record<string, string | null> = {
    page: '1', // Retour à la première page lors d'un changement de filtre
    theme: activeFilter.value === 'all' ? null : activeFilter.value,
    search: searchQuery.value || null,
    year: selectedYear.value === 'all' ? null : selectedYear.value
  };

  // Nettoyer les paramètres null
  Object.keys(params).forEach(key => params[key] === null && delete params[key]);

  // Rediriger avec les nouveaux filtres
  router.get(route('portfolio'), params, {
    preserveState: true,
    preserveScroll: false,
    onSuccess: () => {
      isLoading.value = false;
    }
  });
};

// Ouvrir la lightbox
const openLightbox = (album: Album, photoIndex: number) => {
  selectedAlbum.value = album;
  console.log(selectedAlbum.value);
  currentPhotoIndex.value = photoIndex;
  showLightbox.value = true;
  console.log(selectedAlbum.value.photos[currentPhotoIndex.value].image_path)
  document.body.classList.add('overflow-hidden');
};

// Fermer la lightbox
const closeLightbox = () => {
  showLightbox.value = false;
  document.body.classList.remove('overflow-hidden');
};

// Navigation dans la lightbox
const nextPhoto = () => {
  if (selectedAlbum.value && selectedAlbum.value.photos.length > 0) {
    currentPhotoIndex.value = (currentPhotoIndex.value + 1) % selectedAlbum.value.photos.length;
  }
};

const prevPhoto = () => {
  if (selectedAlbum.value && selectedAlbum.value.photos.length > 0) {
    currentPhotoIndex.value = (currentPhotoIndex.value - 1 + selectedAlbum.value.photos.length) % selectedAlbum.value.photos.length;
  }
};

// Variable pour contrôler l'état d'ouverture des filtres sur mobile
const isFiltersOpen = ref(false);

// Gestionnaire de touches clavier pour la navigation dans la lightbox
const handleKeyDown = (e: KeyboardEvent) => {
  if (!showLightbox.value) return;

  if (e.key === 'Escape') {
    closeLightbox();
  } else if (e.key === 'ArrowRight') {
    nextPhoto();
  } else if (e.key === 'ArrowLeft') {
    prevPhoto();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);

  return () => {
    window.removeEventListener('keydown', handleKeyDown);
  };
});

const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'Portfolio', href: '/portfolio', current: true },

];
</script>

<template>
  <LayoutFront>
    <Head>
      <title>Portfolio </title>
      <meta name="description" content="Découvrez notre portfolio de mariages exceptionnels" />
    </Head>
      <div class="relative bg-gray-900">
          <!-- Image d'arrière-plan avec overlay -->
          <div class="absolute inset-0 overflow-hidden">
              <img src="/images/breadcrumb-bg.jpg" alt="Bannière À Propos" class="w-full h-full object-cover object-center opacity-40">
              <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
          </div>

          <!-- Contenu du breadcrumb -->
          <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
              <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">Portfolio</h1>

              <!-- Breadcrumb navigation -->
              <nav class="flex" aria-label="Breadcrumb">
                  <ol class="flex items-center space-x-2">
                      <li v-for="(item, index) in breadcrumbItems" :key="item.name">
                          <div class="flex items-center">
                              <Link
                                  :href="item.href"
                                  :class="[
                                        item.current ? 'text-white font-medium' : 'text-white/80 hover:text-white',
                                        'text-sm md:text-base transition-colors'
                                    ]"
                              >
                                  {{ item.name }}
                              </Link>

                              <!-- Séparateur, sauf pour le dernier élément -->
                              <svg
                                  v-if="index !== breadcrumbItems.length - 1"
                                  class="h-5 w-5 text-white/70 mx-2"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor"
                              >
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                              </svg>
                          </div>
                      </li>
                  </ol>
              </nav>
          </div>
      </div>
    <!-- En-tête de la page -->


    <!-- Filtres avancés -->
    <section class="py-8 bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between">
          <!-- Filtres sur mobile: menu déroulant -->
          <div class="lg:hidden mb-4">
            <button @click="isFiltersOpen = !isFiltersOpen" class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 rounded-lg">
              <span class="font-medium">Filtres</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="isFiltersOpen ? 'transform rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>

          <!-- Filtres version desktop -->
          <div :class="['lg:flex lg:flex-wrap lg:items-center lg:space-x-4', isFiltersOpen ? 'block' : 'hidden lg:flex']">
            <!-- Recherche -->
            <div class="mb-4 lg:mb-0 flex-grow lg:max-w-xs">
              <label for="search" class="sr-only">Rechercher un album</label>
              <div class="relative">
                <input
                  id="search"
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher un album..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                <button
                  @click="applyFilters"
                  class="absolute right-1 top-1 p-1.5 text-gray-500 hover:text-primary"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Filtre par thème -->
            <div class="mb-4 lg:mb-0">
              <select
                v-model="activeFilter"
                @change="applyFilters"
                class="px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white"
              >
                <option value="all">Tous les thèmes</option>
                <option v-for="theme in uniqueThemes" :key="theme" :value="theme">
                  {{ theme }}
                </option>
              </select>
            </div>

            <!-- Filtre par année -->
            <div class="mb-4 lg:mb-0">
              <select
                v-model="selectedYear"
                @change="applyFilters"
                class="px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent appearance-none bg-white"
              >
                <option value="all">Toutes les années</option>
                <option v-for="year in availableYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Loader -->
    <div v-if="isLoading" class="py-20 flex justify-center items-center">
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
        <div class="mt-4 text-lg font-medium text-primary">Chargement de la galerie...</div>
      </div>
    </div>

    <!-- Galerie principale -->
    <section v-else class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div v-if="albums.data.length === 0" class="text-center py-16">
          <p class="text-xl text-gray-600">Aucun album ne correspond à vos critères.</p>
          <button
            @click="activeFilter = 'all'; searchQuery = ''; selectedYear = 'all'; applyFilters()"
            class="mt-4 px-6 py-2 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors"
          >
            Réinitialiser les filtres
          </button>
        </div>

        <!-- Liste des albums -->
        <div v-for="album in albums.data" :key="album.id" class="mb-16">
          <div class="mb-8">
            <h2 class="text-3xl font-serif font-semibold text-primary">{{ album.title }}</h2>
            <div class="flex flex-wrap items-center gap-4 mt-2 text-gray-600">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ new Date(album.event_date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
              </span>
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ album.theme }}
              </span>
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ album.photos.length }} photos
              </span>
            </div>
          </div>

          <!-- Grille de photos en masonry -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="(photo, index) in album.photos.slice(0, 8)"
              :key="photo.id"
              class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer"
              :class="{'sm:col-span-2': index % 5 === 0 || index % 5 === 3, 'row-span-2': index % 7 === 0}"
              @click="openLightbox(album, index)"
            >
              <div class="relative pb-[100%]">
                <img
                    :src="`${photo.image_path}`"
                  :alt="photo.caption || album.title"
                  class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                  <p v-if="photo.caption" class="text-white text-sm font-medium">{{ photo.caption }}</p>
                </div>
              </div>
            </div>

            <!-- Bouton "Voir plus" si l'album a plus de 8 photos -->
            <div
              v-if="album.photos.length > 8"
              class="flex items-center justify-center p-8 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer"
              @click="openLightbox(album, 0)"
            >
              <div class="text-center">
                <p class="text-lg font-medium text-primary mb-2">Voir toutes les photos</p>
                <p class="text-gray-600">+{{ album.photos.length - 8 }} photos</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
          <div v-if="albums.meta && albums.meta.last_page > 1" class="mt-12 flex justify-center">
              <nav class="flex items-center space-x-1">
                  <Link
                      v-for="link in albums.links"
                      :key="link.label"
                      :href="link.url"
                      :class="[
        'px-4 py-2 border rounded-md transition-colors',
        link.active
          ? 'bg-primary border-primary text-white'
          : link.url
            ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
            : 'border-gray-200 text-gray-400 cursor-not-allowed'
      ]"
                  >{{ link.label.replace(/&laquo;|&raquo;/g, '') }}</Link>
                  />
              </nav>
          </div>

      </div>
    </section>

    <!-- Lightbox -->
    <div v-if="showLightbox" class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center overflow-hidden">
      <div @click.self="closeLightbox" class="absolute inset-0 flex items-center justify-center">
        <div class="relative max-w-6xl w-full max-h-[90vh] flex flex-col">
          <!-- Barre d'information -->
          <div class="bg-white/10 backdrop-blur-md p-4 rounded-t-lg flex justify-between items-center">
            <div class="text-white overflow-hidden">
              <h3 class="text-xl font-medium truncate">{{ selectedAlbum?.title }}</h3>
              <p v-if="selectedAlbum?.photos[currentPhotoIndex]?.caption" class="text-gray-200 text-sm truncate">
                {{ selectedAlbum.photos[currentPhotoIndex].caption }}
              </p>
            </div>
            <button @click="closeLightbox" class="text-white hover:text-gray-300 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Image principale -->
          <div class="relative bg-black h-full flex items-center justify-center">
            <img
              v-if="selectedAlbum?.photos[currentPhotoIndex]"
              :src="`${selectedAlbum.photos[currentPhotoIndex].image_path}`"

              :alt="selectedAlbum.photos[currentPhotoIndex].caption || selectedAlbum.title"
              class="max-h-[70vh] max-w-full object-contain"
            />

            <!-- Compteur de photos -->
            <div class="absolute bottom-4 left-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
              {{ currentPhotoIndex + 1 }} / {{ selectedAlbum?.photos.length }}
            </div>

            <!-- Boutons de navigation -->
            <button
              @click.stop="prevPhoto"
              class="absolute left-4 p-2 rounded-full bg-black/30 hover:bg-black/50 text-white transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              @click.stop="nextPhoto"
              class="absolute right-4 p-2 rounded-full bg-black/30 hover:bg-black/50 text-white transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Miniatures -->
          <div class="bg-black/80 p-4 overflow-x-auto">
            <div class="flex space-x-2">
              <button
                v-for="(photo, index) in selectedAlbum?.photos"
                :key="photo.id"
                @click.stop="currentPhotoIndex = index"
                class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden transition-all duration-200"
                :class="{'ring-2 ring-primary': currentPhotoIndex === index, 'opacity-60': currentPhotoIndex !== index}"
              >
                <img
                    :src="`${photo.image_path}`"
                  :alt="photo.caption || selectedAlbum?.title"
                  class="w-full h-full object-cover"
                  loading="lazy"
                />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

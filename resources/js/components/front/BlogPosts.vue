<template>
  <section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="font-display text-3xl sm:text-4xl font-semibold text-gray-900">Actualités & témoignages</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Suivez nos actions récentes et l'impact de notre fondation sur le terrain
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="text-center py-8">
        <div class="text-red-500 mb-4">
          <svg class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-gray-700">{{ error }}</p>
        <button
          @click="fetchLatestActualites"
          class="mt-4 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
        >
          Réessayer
        </button>
      </div>

      <!-- Blog posts grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Blog Posts -->
        <div
          v-for="(post, index) in blogPosts"
          :key="post.id"
          v-reveal:up
          class="group bg-white border-2 border-[var(--card-color)] overflow-hidden transition-shadow hover:shadow-lg"
          :style="{ '--card-color': cardColor(index) }"
        >
          <!-- Image entourée d'un cadre de couleur -->
          <div class="p-4 sm:p-5 bg-[var(--card-color)]">
            <div class="overflow-hidden">
              <img :src="post.imageUrl" :alt="post.title" class="w-full h-44 object-cover transition-transform duration-500 ease-out group-hover:scale-110" />
            </div>
          </div>

          <!-- Contenu centré sur fond blanc -->
          <div class="p-6 text-center">
            <div class="flex items-center justify-center mb-2">
              <span class="text-xs text-gray-500">{{ formatDate(post.date) }}</span>
              <span class="mx-2 text-gray-300">•</span>
              <span class="text-xs font-medium text-[var(--card-color)]">{{ post.category }}</span>
            </div>
            <h3 class="font-display text-xl font-semibold mb-3 text-[var(--card-color)]">{{ post.title }}</h3>
            <p class="text-gray-600 mb-6">
               <span>
                   {{ stripAndTruncateHtml(post.content, 120) }}
               </span>
            </p>
            <Link
              :href="post.url"
              class="inline-block px-6 py-2.5 text-xs font-semibold uppercase tracking-wider border-2 border-[var(--card-color)] text-[var(--card-color)] hover:bg-[var(--card-color)] hover:text-white transition-colors"
            >
              Lire la suite
            </Link>
          </div>
        </div>
      </div>

      <div class="text-center mt-12">
        <Link
          :href="'/blog'"
          class="inline-block px-8 py-3 rounded-full border border-[#d1922f]/30 text-[#d1922f] hover:border-[#d1922f] hover:bg-[#d1922f]/5 font-medium transition-colors"
        >
          Toutes les actualités
        </Link>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

// Définir l'interface pour les actualités
interface Actualite {
  id: number;
  title: string;
  published_at: string;
  image_path?: string;
  image_url?: string;
  description?: string;
  slug?: string;
}

// Extraire le texte brut du HTML et le tronquer
function stripAndTruncateHtml(html: string | null, maxLength: number = 100): string {
    if (!html) return '';

    // Créer un élément temporaire pour extraire le texte
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;
    const text = tempDiv.textContent || tempDiv.innerText || "";

    // Tronquer si nécessaire
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }

    return text;
}

// État des articles de blog
const blogPosts = ref<Array<{
  id: number;
  title: string;
  category: string;
  date: Date;
  imageUrl: string;
  url: string;
  content: string;
}>>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Fonction pour charger les dernières actualités
const fetchLatestActualites = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get<Actualite[]>('/api/actualites/latest');
    blogPosts.value = response.data.map((actualite: Actualite) => ({
      id: actualite.id,
      title: actualite.title,
      category: 'Actualité', // Catégorie par défaut
      date: new Date(actualite.published_at),
      imageUrl: actualite.image_url || '/images/blog-default.jpg',
      url: `/blog/${actualite.slug || actualite.id}`,
      content: actualite.description || ''
    }));
  } catch (err) {
    console.error('Erreur lors du chargement des actualités:', err);
    error.value = 'Impossible de charger les actualités. Veuillez réessayer plus tard.';

    // Données de secours en cas d'erreur
    blogPosts.value = [
      {
        id: 1,
        title: "Erreur de chargement",
        category: "Info",
        date: new Date(),
        imageUrl: "/images/blog-default.jpg",
        url: "/blog/erreur-de-chargement",
        content: "<p>Les actualités ne peuvent pas être chargées pour le moment. Veuillez réessayer plus tard.</p>"
      }
    ];
  } finally {
    loading.value = false;
  }
};

// Charger les actualités au montage du composant
onMounted(fetchLatestActualites);

// Palette de la marque, alternée par carte
const cardColors = ['#d1922f', '#1E2F52', '#5B3A22'];
const cardColor = (index: number) => cardColors[index % cardColors.length];

// Fonction pour formater la date
const formatDate = (date: Date) => {
  const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
  return date.toLocaleDateString('fr-FR', options);
};

</script>

<style scoped>

</style>

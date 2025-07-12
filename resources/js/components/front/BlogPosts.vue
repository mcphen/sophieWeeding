<template>
  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-serif font-bold text-gray-900">Actualités & Conseils</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Découvrez nos derniers articles et conseils pour préparer votre mariage
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#d1922f]"></div>
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
          class="mt-4 px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#b17926] transition-colors"
        >
          Réessayer
        </button>
      </div>

      <!-- Blog posts grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Blog Posts -->
        <div
          v-for="post in blogPosts"
          :key="post.id"
          class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100"
        >
          <img :src="post.imageUrl" :alt="post.title" class="w-full h-48 object-cover" />
          <div class="p-6">
            <div class="flex items-center mb-2">
              <span class="text-xs text-gray-500">{{ formatDate(post.date) }}</span>
              <span class="mx-2 text-gray-300">•</span>
              <span class="text-xs text-[#d1922f]">{{ post.category }}</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ post.title }}</h3>
            <p class="text-gray-600 mb-4">
               <span>
                   {{ stripAndTruncateHtml(post.content, 120) }}
               </span>
            </p>
            <Link
              :href="post.url"
              class="text-[#d1922f] font-medium hover:text-[#b17926] inline-flex items-center"
            >
              Lire la suite
              <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
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
      imageUrl: actualite.image_path ? `${actualite.image_path}` : '/images/blog-default.jpg',
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

// Fonction pour formater la date
const formatDate = (date: Date) => {
  const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
  return date.toLocaleDateString('fr-FR', options);
};

</script>

<style scoped>

</style>

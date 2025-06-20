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
              <span v-html="truncateHtml(post.content)"></span>
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

// Définir l'interface pour les actualités
interface Actualite {
  id: number;
  title: string;
  published_at: string;
  image_path?: string;
  description?: string;
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
      imageUrl: actualite.image_path ? `/storage/${actualite.image_path}` : '/images/blog-default.jpg',
      url: `/${actualite.id}/blog`,
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
        url: "#",
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

// Fonction pour tronquer le HTML tout en conservant la structure
const truncateHtml = (html: string, maxLength = 120) => {
  // Retirer les balises HTML pour compter les caractères
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = html;
  const textContent = tempDiv.textContent || tempDiv.innerText || '';

  if (textContent.length <= maxLength) {
    return html;
  }

  // Chercher où couper tout en gardant les balises intactes
  let truncated = '';
  let charCount = 0;
  let inTag = false;

  for (let i = 0; i < html.length; i++) {
    const char = html[i];

    if (char === '<') {
      inTag = true;
      truncated += char;
    } else if (char === '>') {
      inTag = false;
      truncated += char;
    } else if (!inTag) {
      // On compte seulement les caractères hors des balises
      if (charCount < maxLength) {
        truncated += char;
        charCount++;
      } else if (charCount === maxLength) {
        truncated += '...';
        charCount++;
      }
    } else {
      // Caractère à l'intérieur d'une balise
      truncated += char;
    }
  }

  // Assurer que toutes les balises sont fermées correctement
  const openTags = [];
  const regex = /<([^\/\s>]+)([^>]*)>/g;
  const closeRegex = /<\/([^>]+)>/g;
  let match;

  while ((match = regex.exec(truncated)) !== null) {
    // Ignorer les balises auto-fermantes comme <img/>
    if (!/\/>$/.test(match[0])) {
      openTags.push(match[1]);
    }
  }

  while ((match = closeRegex.exec(truncated)) !== null) {
    // Retirer la dernière occurrence de cette balise
    const tagIndex = openTags.lastIndexOf(match[1]);
    if (tagIndex !== -1) {
      openTags.splice(tagIndex, 1);
    }
  }

  // Fermer les balises restantes dans l'ordre inverse
  while (openTags.length) {
    truncated += `</${openTags.pop()}>`;
  }

  return truncated;
};
</script>

<style scoped>

</style>

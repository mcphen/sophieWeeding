<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed, onMounted } from 'vue';
import ArticleBlock from '@/components/front/ArticleBlock.vue';

// Interface pour le modèle Actualite
interface Actualite {
  id: number;
  title: string;
  image_path: string;
  image_url: string;
  published_at: string;
  created_at: string;
  updated_at: string;
  blocks?: Array<{
    id: number;
    type: string;
    content: any;
    position: number;
  }>;
}

// Définition des props
const props = defineProps<{
  actualite: Actualite;
  relatedArticles: Actualite[];
}>();

// États
const shareMenuOpen = ref(false);
const linkCopied = ref(false);
const currentUrl = ref('');

// Format de date français
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

// Extraire un extrait de la description pour les articles connexes
const getExcerpt = (text: string | null | undefined, maxLength: number = 100) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substr(0, maxLength) + '...';
};

    // Fonction pour obtenir le texte d'un bloc
const getBlockText = (actualite: Actualite, maxLength = 150): string => {
  if (actualite.blocks && actualite.blocks.length > 0) {
    // Trouver le premier bloc de type texte
    const textBlock = actualite.blocks.find(block => block.type === 'text');
    if (textBlock && textBlock.content) {
      // Handle both formats: with html key and direct content
      const htmlContent = typeof textBlock.content === 'object' && textBlock.content.html
        ? textBlock.content.html
        : textBlock.content;

      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = htmlContent;
      const text = tempDiv.textContent || tempDiv.innerText || "";
      return getExcerpt(text, maxLength);
    }
  }
  return '';
};

// Méta données pour le partage social
const metaTitle = computed(() => `${props.actualite.title} | Amour Éternel`);
const metaDescription = computed(() => getBlockText(props.actualite, 150));

// Sorted blocks for display
const sortedBlocks = computed(() => {
  if (props.actualite.blocks && props.actualite.blocks.length > 0) {
    return [...props.actualite.blocks].sort((a, b) => a.position - b.position);
  }
  return [];
});

// Options de partage
const shareOptions = computed(() => {
  return [
    {
      name: 'Facebook',
      url: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl.value)}`,
      icon: `<path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />`
    },
    {
      name: 'Twitter',
      url: `https://twitter.com/intent/tweet?text=${encodeURIComponent(metaTitle.value)}&url=${encodeURIComponent(currentUrl.value)}`,
      icon: `<path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />`
    },
    {
      name: 'LinkedIn',
      url: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(currentUrl.value)}`,
      icon: `<path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z" />`
    },
    {
      name: 'WhatsApp',
      url: `https://api.whatsapp.com/send?text=${encodeURIComponent(`${metaTitle.value} - ${currentUrl.value}`)}`,
      icon: `<path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />`
    },
    {
      name: 'Email',
      url: `mailto:?subject=${encodeURIComponent(metaTitle.value)}&body=${encodeURIComponent(`Découvrez cet article intéressant: ${currentUrl.value}`)}`,
      icon: `<path d="M12 12.713l-11.985-9.713h23.97l-11.985 9.713zm0 2.574l-12-9.725v15.438h24v-15.438l-12 9.725z" />`
    }
  ];
});

// Fonction pour copier le lien de l'article
const copyArticleLink = () => {
  navigator.clipboard.writeText(currentUrl.value).then(() => {
    linkCopied.value = true;
    setTimeout(() => {
      linkCopied.value = false;
    }, 2000);
  });
};


const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'Blog', href: '/blog', current: false },
    { name: props.actualite.title, href: '', current: true }
];

// Obtenir l'URL courante
onMounted(() => {
  currentUrl.value = window.location.href;
});
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ metaTitle }}</title>
      <meta name="description" :content="metaDescription" />
      <meta property="og:title" :content="metaTitle" />
      <meta property="og:description" :content="metaDescription" />
      <meta property="og:image" :content="actualite.image_url" />
      <meta property="og:url" :content="currentUrl" />
      <meta property="og:type" content="article" />
      <meta name="twitter:card" content="summary_large_image" />
    </Head>
      <div class="relative bg-gray-900">
          <!-- Image d'arrière-plan avec overlay -->
          <div class="absolute inset-0 overflow-hidden">
              <img src="/images/breadcrumb-bg.jpg" alt="Bannière À Propos" class="w-full h-full object-cover object-center opacity-40">
              <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
          </div>

          <!-- Contenu du breadcrumb -->
          <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
              <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">{{actualite.title}}</h1>

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
    <!-- Fil d'Ariane -->


    <!-- Article principal -->
    <article class="py-12 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de l'article -->
        <header class="mb-8 text-center">
          <h1 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-4">
            {{ actualite.title }}
          </h1>
          <div class="flex justify-center items-center text-gray-500 mb-8">
            <time :datetime="actualite.published_at" class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ formatDate(actualite.published_at) }}
            </time>
          </div>

          <!-- Image principale -->
          <div class="relative mb-10 shadow-xl rounded-lg overflow-hidden">
            <img
                :src="`${actualite.image_path}`"

              :alt="actualite.title"
              class="w-full h-auto object-cover object-center rounded-lg"
            />
          </div>

          <!-- Boutons de partage -->
          <div class="flex justify-center space-x-4 mb-8">
            <!-- Bouton de partage -->
            <div class="relative">
              <button
                @click="shareMenuOpen = !shareMenuOpen"
                class="px-4 py-2 bg-primary text-white rounded-full flex items-center hover:bg-primary-dark transition-colors"
                aria-expanded="false"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                Partager
              </button>

              <!-- Menu déroulant de partage -->
              <div
                v-if="shareMenuOpen"
                class="absolute right-0 z-10 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                role="menu"
                aria-orientation="vertical"
                tabindex="-1"
              >
                <div class="p-2">
                  <!-- Options de partage -->
                  <a
                    v-for="option in shareOptions"
                    :key="option.name"
                    :href="option.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary rounded-md"
                    @click="shareMenuOpen = false"
                  >
                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" v-html="option.icon"></svg>
                    {{ option.name }}
                  </a>

                  <!-- Bouton pour copier le lien -->
                  <button
                    @click="copyArticleLink"
                    class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary rounded-md"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    Copier le lien
                  </button>
                </div>
              </div>
            </div>

            <!-- Notification de lien copié -->
            <div
              v-if="linkCopied"
              class="fixed bottom-6 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-green-500 text-white rounded-full shadow-md z-50 transition-opacity duration-300"
            >
              Lien copié avec succès !
            </div>
          </div>
        </header>

        <!-- Contenu de l'article -->
        <div v-if="sortedBlocks.length > 0" class="article-blocks">
          <!-- Render blocks in order of position -->
          <ArticleBlock
            v-for="block in sortedBlocks"
            :key="block.id"
            :block="block"
          />
        </div>
        <!-- Message if no blocks -->
        <div v-else class="prose prose-lg max-w-none">
          <p class="text-gray-500 italic">Aucun contenu disponible pour cet article.</p>
        </div>

        <!-- Pied de l'article (tags, auteur, etc.) -->
        <footer class="mt-12 pt-8 border-t border-gray-200">
          <div class="flex justify-between items-center">
            <!-- Retour à la liste des articles -->
            <Link
              :href="route('blog')"
              class="flex items-center text-primary hover:text-primary-dark transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Retour aux articles
            </Link>
          </div>
        </footer>
      </div>
    </article>

    <!-- Articles connexes -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-serif font-bold text-gray-900 mb-8 text-center">
          Articles connexes
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="article in relatedArticles"
            :key="article.id"
            class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300"
          >
            <!-- Image de l'article -->
            <div class="relative h-48 overflow-hidden">
              <img
                :src="article.image_url"
                :alt="article.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
              />
              <div class="absolute bottom-0 left-0 bg-primary text-white px-3 py-1 text-sm">
                {{ formatDate(article.published_at) }}
              </div>
            </div>

            <!-- Contenu de l'article -->
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-900 mb-2 group-hover:text-primary transition-colors">
                {{ article.title }}
              </h3>
              <p class="text-sm text-gray-600 mb-4">
                {{ getBlockText(article) }}
              </p>
              <Link
                :href="route('blog.show', article.id)"
                class="inline-flex items-center text-primary font-medium group-hover:text-primary-dark"
              >
                Lire la suite
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section commentaires ou inscription newsletter -->
    <section class="py-16 bg-primary-bg-light">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-serif font-bold text-primary mb-4">
          Vous avez aimé cet article ?
        </h2>
        <p class="text-lg text-gray-700 mb-8">
          Abonnez-vous à notre newsletter pour recevoir nos derniers articles et conseils sur l'organisation de mariages.
        </p>
        <div class="max-w-md mx-auto">
          <div class="flex">
            <input
              type="email"
              placeholder="Votre adresse email"
              class="flex-grow px-4 py-2 rounded-l-full border-y border-l border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
            <button class="px-6 py-2 bg-primary text-white rounded-r-full hover:bg-primary-dark transition-colors">
              S'abonner
            </button>
          </div>
          <p class="text-sm text-gray-500 mt-2">
            Nous respectons votre vie privée. Désabonnez-vous à tout moment.
          </p>
        </div>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
/* Animation pour le bouton Copy */
@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}

@keyframes fadeOut {
  from { opacity: 1; transform: translate(-50%, 0); }
  to { opacity: 0; transform: translate(-50%, 20px); }
}

.fixed {
  animation: fadeIn 0.3s forwards;
}

/* Animations pour les articles connexes */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

.group:hover .group-hover\:translate-x-1 {
  transform: translateX(0.25rem);
}

/* Style pour la prose (contenu de l'article) */
.prose {
  color: #374151;
  max-width: 65ch;
  font-size: 1.125rem;
  line-height: 1.7777778;
}

.prose p {
  margin-top: 1.3333333em;
  margin-bottom: 1.3333333em;
}

/* Styles pour les réseaux sociaux */
.transition-opacity {
  transition-property: opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.duration-300 {
  transition-duration: 300ms;
}
</style>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// Current URL for canonical and sharing
const currentUrl = ref('');
let jsonLdScript: HTMLScriptElement | null = null;

// Function to inject JSON-LD script into the document head
const injectJsonLdScript = () => {
    if (jsonLdScript) {
        document.head.removeChild(jsonLdScript);
    }

    jsonLdScript = document.createElement('script');
    jsonLdScript.setAttribute('type', 'application/ld+json');
    jsonLdScript.textContent = JSON.stringify(blogJsonLd.value);
    document.head.appendChild(jsonLdScript);
};

onMounted(() => {
    currentUrl.value = window.location.href;
    // Inject JSON-LD after component is mounted
    injectJsonLdScript();
});

onUnmounted(() => {
    // Clean up the script tag when component is unmounted
    if (jsonLdScript && document.head.contains(jsonLdScript)) {
        document.head.removeChild(jsonLdScript);
    }
});
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
// Computed properties for meta tags
const metaTitle = computed(() => "Blog | Sophie Weddings Dreams - Conseils et Actualités Mariage");
const metaDescription = computed(() => "Découvrez nos articles, conseils et actualités sur l'organisation de mariage à Dakar, Sénégal. Tendances, idées et inspiration pour votre mariage parfait.");

// JSON-LD structured data for blog listing
const blogJsonLd = computed(() => {
    return {
        '@context': 'https://schema.org',
        '@type': 'Blog',
        headline: 'Blog Sophie Weddings Dreams',
        description: metaDescription.value,
        url: currentUrl.value,
        publisher: {
            '@type': 'Organization',
            name: 'Sophie Weddings Dreams',
            logo: {
                '@type': 'ImageObject',
                url: `${window.location.origin}/images/logo.png`
            }
        },
        blogPost: actualites.value.data.map(post => {
            // Get excerpt from first text block if available
            let description = '';
            if (post.blocks && post.blocks.length > 0) {
                const textBlock = post.blocks.find(block => block.type === 'text');
                if (textBlock && textBlock.content && textBlock.content.html) {
                    description = getExcerpt(stripAndTruncateHtml(textBlock.content.html, 150));
                }
            }

            return {
                '@type': 'BlogPosting',
                headline: post.title,
                description: description,
                datePublished: post.published_at,
                dateModified: post.updated_at,
                image: post.image_path ? `${window.location.origin}/storage/${post.image_path}` : '',
                url: `${window.location.origin}/blog/${post.id}`,
                author: {
                    '@type': 'Organization',
                    name: 'Sophie Weddings Dreams'
                }
            };
        })
    };
});

// Interface pour le modèle Actualite
interface Actualite {
    id: number;
    title: string;
    slug: string;
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

// Interface pour les données paginées
interface PaginatedData {
    data: Actualite[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    from: number | null;
    last_page: number;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

// Filter types are now handled directly in the component

// No props needed as we're fetching data directly with axios

// États
const searchQuery = ref('');
const selectedDate = ref('all');
const sortBy = ref('newest');
const isLoading = ref(true);
const showFilters = ref(false);

// États pour les articles et la pagination
const actualites = ref<PaginatedData>({
    data: [],
    links: [],
    current_page: 1,
    from: null,
    last_page: 1,
    path: '',
    per_page: 10,
    to: null,
    total: 0
});
const allArticles = ref<Actualite[]>([]);
const currentPage = ref(1);
const hasMoreArticles = ref(false);
const loadingMore = ref(false);

// Fonction pour charger les articles depuis l'API
const fetchArticles = async (page = 1, resetList = true) => {
    isLoading.value = resetList;
    loadingMore.value = !resetList;

    try {
        const params: Record<string, string | null> = {
            page: page.toString(),
            search: searchQuery.value || null,
            date: selectedDate.value === 'all' ? null : selectedDate.value,
            sort: sortBy.value === 'newest' ? null : sortBy.value,
        };

        // Nettoyer les paramètres null
        Object.keys(params).forEach((key) => params[key] === null && delete params[key]);

        const response = await axios.get('/api/actualites/paginated', { params });
        actualites.value = response.data;

        if (resetList) {
            allArticles.value = [...response.data.data];
        } else {
            allArticles.value = [...allArticles.value, ...response.data.data];
        }

        currentPage.value = response.data.current_page;
        hasMoreArticles.value = response.data.current_page < response.data.last_page;
    } catch (error) {
        console.error('Error fetching articles:', error);
    } finally {
        isLoading.value = false;
        loadingMore.value = false;
    }
};

// Format de date français
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

// Extraire un extrait de la description
const getExcerpt = (text: string, maxLength: number = 150) => {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
};

// Date options pour le filtre
const dateOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    return [
        { value: 'all', label: 'Toutes les dates' },
        { value: 'this-month', label: 'Ce mois-ci' },
        { value: 'last-month', label: 'Le mois dernier' },
        { value: currentYear.toString(), label: 'Cette année' },
        { value: (currentYear - 1).toString(), label: 'Année dernière' },
    ];
});

// Trier les options
const sortOptions = [
    { value: 'newest', label: 'Plus récent' },
    { value: 'oldest', label: 'Plus ancien' },
    { value: 'title-asc', label: 'Titre (A-Z)' },
    { value: 'title-desc', label: 'Titre (Z-A)' },
];

// Appliquer les filtres
const applyFilters = () => {
    // Fetch articles with current filters, reset to page 1
    fetchArticles(1, true);
};

// Réinitialiser les filtres
const resetFilters = () => {
    searchQuery.value = '';
    selectedDate.value = 'all';
    sortBy.value = 'newest';
    applyFilters();
};

// Fonction pour obtenir le texte d'un bloc
const getBlockText = (actualite: Actualite, maxLength = 120): string => {
    if (actualite.blocks && actualite.blocks.length > 0) {
        // Trouver le premier bloc de type texte
        const textBlock = actualite.blocks.find(block => block.type === 'text');
        if (textBlock && textBlock.content && textBlock.content.html) {
            return stripAndTruncateHtml(textBlock.content.html, maxLength);
        }
    }
    return '';
};
const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'Blog', href: '/blog', current: true }
];
// Fonction pour charger plus d'articles
const loadMoreArticles = () => {
    if (!hasMoreArticles.value || loadingMore.value) return;

    const nextPage = currentPage.value + 1;
    fetchArticles(nextPage, false);
};

// Note: resetArticles function is no longer needed as this is handled in fetchArticles

// Surveiller les changements de filtre
watch([selectedDate, sortBy], () => {
    applyFilters();
});

// Charger les articles au montage du composant
onMounted(() => {
    currentUrl.value = window.location.href;
    // Inject JSON-LD after component is mounted
    injectJsonLdScript();
    // Fetch articles on component mount
    fetchArticles();
});
</script>

<template>
    <LayoutFront>
        <Head>
            <title>{{ metaTitle }}</title>
            <meta name="description" :content="metaDescription" />
            <link rel="canonical" :href="currentUrl" />

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="website" />
            <meta property="og:title" :content="metaTitle" />
            <meta property="og:description" :content="metaDescription" />
            <meta property="og:url" :content="currentUrl" />

            <!-- Twitter -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="metaTitle" />
            <meta name="twitter:description" :content="metaDescription" />

            <!-- Structured Data is now injected programmatically -->
        </Head>

        <div class="relative bg-gray-900">
            <!-- Image d'arrière-plan avec overlay -->
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Bannière À Propos" class="w-full h-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>

            <!-- Contenu du breadcrumb -->
            <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">Blog</h1>

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


        <!-- Barre de recherche et filtres -->
        <section class="border-b bg-white py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Version mobile: bouton pour afficher/masquer les filtres -->
                <div class="mb-4 lg:hidden">
                    <button @click="showFilters = !showFilters" class="flex w-full items-center justify-between rounded-lg bg-gray-100 px-4 py-3">
                        <span class="font-medium">Filtres et recherche</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            :class="showFilters ? 'rotate-180 transform' : ''"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Filtres et recherche -->
                <div :class="['lg:flex lg:flex-wrap lg:items-center lg:justify-between lg:space-x-4', showFilters ? 'block' : 'hidden lg:flex']">
                    <!-- Barre de recherche -->
                    <div class="mb-4 lg:mb-0 lg:max-w-md lg:flex-grow">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Rechercher un article..."
                                class="focus:ring-primary w-full rounded-full border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:outline-none"
                            />
                            <button @click="applyFilters" class="hover:text-primary absolute top-1 right-1 p-1.5 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Sélecteurs de filtre -->
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <!-- Filtre par date -->
                        <select
                            v-model="selectedDate"
                            class="focus:ring-primary rounded-full border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:outline-none"
                        >
                            <option v-for="option in dateOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>

                        <!-- Tri -->
                        <select
                            v-model="sortBy"
                            class="focus:ring-primary rounded-full border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:outline-none"
                        >
                            <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>

                        <!-- Bouton de réinitialisation -->
                        <button
                            @click="resetFilters"
                            class="hover:text-primary hover:border-primary rounded-full border border-gray-300 px-4 py-2 text-sm text-gray-600 transition-colors"
                        >
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Loader -->
        <div v-if="isLoading" class="flex items-center justify-center py-20">
            <div class="flex flex-col items-center">
                <div class="border-primary h-16 w-16 animate-spin rounded-full border-t-2 border-b-2"></div>
                <div class="text-primary mt-4 text-lg font-medium">Chargement des articles...</div>
            </div>
        </div>

        <!-- Liste des articles -->
        <section v-else class="bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Message si aucun résultat -->
                <div v-if="actualites.data.length === 0" class="py-16 text-center">
                    <p class="text-xl text-gray-600">Aucun article ne correspond à vos critères de recherche.</p>
                    <button @click="resetFilters" class="bg-primary hover:bg-primary-dark mt-4 rounded-full px-6 py-2 text-white transition-colors">
                        Voir tous les articles
                    </button>
                </div>

                <!-- Grille d'articles -->
                <div v-else class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="actualite in allArticles"
                        :key="actualite.id"
                        class="group flex flex-col overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl"
                    >
                        <!-- Image de l'article -->
                        <div class="relative h-56 overflow-hidden">
                            <img
                                :src="actualite.image_url"
                                :alt="actualite.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div class="bg-primary absolute bottom-0 left-0 px-4 py-1 text-white">
                                {{ formatDate(actualite.published_at) }}
                            </div>
                        </div>

                        <!-- Contenu de l'article -->
                        <div class="flex flex-grow flex-col p-5">
                            <h2 class="group-hover:text-primary mb-2 font-serif text-xl font-semibold text-gray-800 transition-colors">
                                {{ actualite.title }}
                            </h2>
                            <p class="mb-4 flex-grow text-gray-600">
                                 <span>
                                       {{ getBlockText(actualite, 120) }}
                                   </span>

                            </p>
                            <Link
                                :href="route('blog.show', actualite.slug)"
                                class="text-primary group-hover:text-primary-dark mt-auto inline-flex items-center self-start font-medium"
                            >
                                Lire l'article
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="ml-1 h-5 w-5 transition-transform group-hover:translate-x-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Bouton "Voir plus" -->
                <div v-if="hasMoreArticles || loadingMore" class="mt-12 flex justify-center">
                    <button
                        @click="loadMoreArticles"
                        class="bg-primary hover:bg-primary-dark rounded-full px-8 py-3 text-white transition-colors flex items-center space-x-2"
                        :disabled="loadingMore"
                    >
                        <span v-if="!loadingMore">Voir plus d'articles</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Chargement...
                        </span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="bg-primary-bg-light py-16">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h2 class="text-primary mb-4 font-serif text-3xl font-bold">Restez informé</h2>
                <p class="mx-auto mb-8 max-w-3xl text-lg text-gray-700">
                    Abonnez-vous à notre newsletter pour recevoir nos derniers articles, conseils et tendances sur l'organisation de mariages.
                </p>
                <div class="mx-auto max-w-md">
                    <div class="flex">
                        <input
                            type="email"
                            placeholder="Votre adresse email"
                            class="focus:ring-primary flex-grow rounded-l-full border-y border-l border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:outline-none"
                        />
                        <button class="bg-primary hover:bg-primary-dark rounded-r-full px-6 py-2 text-white transition-colors">S'abonner</button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Nous respectons votre vie privée. Désabonnez-vous à tout moment.</p>
                </div>
            </div>
        </section>
    </LayoutFront>
</template>

<style scoped>
/* Animations pour les cartes d'articles */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

/* Animation pour le loader */
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>

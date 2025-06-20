<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';

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

// Computed properties for meta tags
const metaTitle = computed(() => "Blog | Sophie Wedding Dreams - Conseils et Actualités Mariage");
const metaDescription = computed(() => "Découvrez nos articles, conseils et actualités sur l'organisation de mariage à Dakar, Sénégal. Tendances, idées et inspiration pour votre mariage parfait.");

// JSON-LD structured data for blog listing
const blogJsonLd = computed(() => {
    return {
        '@context': 'https://schema.org',
        '@type': 'Blog',
        headline: 'Blog Sophie Wedding Dreams',
        description: metaDescription.value,
        url: currentUrl.value,
        publisher: {
            '@type': 'Organization',
            name: 'Sophie Wedding Dreams',
            logo: {
                '@type': 'ImageObject',
                url: `${window.location.origin}/images/logo.png`
            }
        },
        blogPost: props.actualites.data.map(post => ({
            '@type': 'BlogPosting',
            headline: post.title,
            description: getExcerpt(post.description, 150),
            datePublished: post.published_at,
            dateModified: post.updated_at,
            image: post.image_path ? `${window.location.origin}/storage/${post.image_path}` : '',
            url: `${window.location.origin}/blog/${post.id}`,
            author: {
                '@type': 'Organization',
                name: 'Sophie Wedding Dreams'
            }
        }))
    };
});

// Interface pour le modèle Actualite
interface Actualite {
    id: number;
    title: string;
    description: string;
    image_path: string;
    image_url: string;
    published_at: string;
    created_at: string;
    updated_at: string;
}

// Interface pour les données paginées
interface PaginatedData {
    data: Actualite[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
}

// Interface pour les filtres
interface Filters {
    search: string | null;
    date: string | null;
    sort: string | null;
}

// Définition des props
const props = defineProps<{
    actualites: PaginatedData;
    filters: Filters;
    categories?: string[];
}>();

// États
const searchQuery = ref(props.filters.search || '');
const selectedDate = ref(props.filters.date || 'all');
const sortBy = ref(props.filters.sort || 'newest');
const isLoading = ref(false);
const showFilters = ref(false);

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
    isLoading.value = true;

    const params: Record<string, string | null> = {
        page: '1', // Retour à la première page pour les nouveaux filtres
        search: searchQuery.value || null,
        date: selectedDate.value === 'all' ? null : selectedDate.value,
        sort: sortBy.value === 'newest' ? null : sortBy.value,
    };

    // Nettoyer les paramètres null
    Object.keys(params).forEach((key) => params[key] === null && delete params[key]);

    // Redirection avec les nouveaux filtres
    router.get(route('blog'), params, {
        preserveState: true,
        preserveScroll: false,
        onSuccess: () => {
            isLoading.value = false;
        },
    });
};

// Réinitialiser les filtres
const resetFilters = () => {
    searchQuery.value = '';
    selectedDate.value = 'all';
    sortBy.value = 'newest';
    applyFilters();
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
const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'Blog', href: '/blog', current: true }
];
// Surveiller les changements de filtre
watch([selectedDate, sortBy], () => {
    applyFilters();
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
                        v-for="actualite in actualites.data"
                        :key="actualite.id"
                        class="group flex flex-col overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl"
                    >
                        <!-- Image de l'article -->
                        <div class="relative h-56 overflow-hidden">
                            <img
                                :src="`/storage/${actualite.image_path}`"
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
                                <span v-html="truncateHtml(actualite.description)"></span>

                            </p>
                            <Link
                                :href="route('blog.show', actualite.id)"
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

                <!-- Pagination -->
                <div v-if="actualites.meta && actualites.meta.last_page > 1" class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-1">
                        <Link
                            v-for="link in actualites.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-md border px-4 py-2 transition-colors',
                                link.active
                                    ? 'bg-primary border-primary text-white'
                                    : link.url
                                      ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                      : 'cursor-not-allowed border-gray-200 text-gray-400',
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </nav>
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

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';

// Define props
const props = defineProps({
    products: {
        type: Object,
        required: true
    },
    filters: Object,
    contactSettings: Object
});

// Initialize filter states
const search = ref(props.filters?.search || '');
const minPrice = ref(props.filters?.min_price || '');
const maxPrice = ref(props.filters?.max_price || '');
const sort = ref(props.filters?.sort || 'newest');
const currentUrl = ref('');
let jsonLdScript: HTMLScriptElement | null = null;

// Function to inject JSON-LD script into the document head
const injectJsonLdScript = () => {
    if (jsonLdScript) {
        document.head.removeChild(jsonLdScript);
    }

    jsonLdScript = document.createElement('script');
    jsonLdScript.setAttribute('type', 'application/ld+json');
    jsonLdScript.textContent = JSON.stringify(productsJsonLd.value);
    document.head.appendChild(jsonLdScript);
};

// Computed properties for meta tags
const metaTitle = computed(() => "Produits de Mariage | Sophie Wedding Dreams");
const metaDescription = computed(() => "Découvrez notre collection de produits de mariage de haute qualité. Accessoires, décorations et plus pour rendre votre mariage parfait à Dakar, Sénégal.");

// JSON-LD structured data for product listing
const productsJsonLd = computed(() => {
  if (!props.products?.data || props.products.data.length === 0) {
    return {
      '@context': 'https://schema.org',
      '@type': 'ItemList',
      itemListElement: []
    };
  }

  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    numberOfItems: props.products.data.length,
    itemListElement: props.products.data.map((product, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      item: {
        '@type': 'Product',
        name: product.title,
        description: product.description,
        image: product.image_path ? `${window.location.origin}/storage/${product.image_path}` : '',
        offers: {
          '@type': 'Offer',
          price: product.price,
          priceCurrency: 'XOF',
          availability: 'https://schema.org/InStock'
        }
      }
    }))
  };
});

// Format price with currency
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);
};

// Apply filters with debounce
const applyFilters = debounce(() => {
    router.get(route('products'), {
        search: search.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        sort: sort.value
    }, {
        preserveState: true,
        replace: true
    });
}, 500);

// Watch for filter changes
watch([search, minPrice, maxPrice, sort], () => {
    applyFilters();
});

// Computed property for empty state
const noResults = computed(() => {
    return props.products?.data?.length === 0;
});

// Get current URL on mount and inject JSON-LD
onMounted(() => {
    currentUrl.value = window.location.href;
    // Inject JSON-LD after component is mounted
    injectJsonLdScript();
});

// Clean up the script tag when component is unmounted
onUnmounted(() => {
    if (jsonLdScript && document.head.contains(jsonLdScript)) {
        document.head.removeChild(jsonLdScript);
    }
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

        <!-- En-tête de la page -->
        <div class="bg-primary-bg-light py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-primary mb-4">Nos Produits</h1>
                <p class="text-lg text-gray-700 max-w-3xl mx-auto">
                    Découvrez notre sélection de produits de qualité pour rendre votre mariage inoubliable.
                </p>
            </div>
        </div>

        <!-- Filtres et liste des produits -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filtres -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Recherche -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input
                                type="text"
                                id="search"
                                v-model="search"
                                placeholder="Rechercher un produit..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <!-- Prix minimum -->
                        <div>
                            <label for="min-price" class="block text-sm font-medium text-gray-700 mb-1">Prix minimum</label>
                            <input
                                type="number"
                                id="min-price"
                                v-model="minPrice"
                                placeholder="0"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <!-- Prix maximum -->
                        <div>
                            <label for="max-price" class="block text-sm font-medium text-gray-700 mb-1">Prix maximum</label>
                            <input
                                type="number"
                                id="max-price"
                                v-model="maxPrice"
                                placeholder="100000"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>

                        <!-- Tri -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                            <select
                                id="sort"
                                v-model="sort"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            >
                                <option value="newest">Plus récent</option>
                                <option value="oldest">Plus ancien</option>
                                <option value="price_asc">Prix croissant</option>
                                <option value="price_desc">Prix décroissant</option>
                                <option value="title_asc">Titre A-Z</option>
                                <option value="title_desc">Titre Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Message si aucun résultat -->
                <div v-if="noResults" class="text-center py-12">
                    <p class="text-xl text-gray-600 mb-4">Aucun produit ne correspond à votre recherche</p>
                    <button
                        @click="search = ''; minPrice = ''; maxPrice = ''; sort = 'newest';"
                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
                    >
                        Réinitialiser les filtres
                    </button>
                </div>

                <!-- Grille de produits -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="product in products?.data || []" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <!-- Image du produit -->
                        <div class="h-48 overflow-hidden">
                            <img
                                v-if="product.image_path"
                                :src="'/storage/' + product.image_path"
                                :alt="product.title"
                                class="w-full h-full object-cover object-center"
                            />
                            <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Pas d'image</span>
                            </div>
                        </div>

                        <!-- Contenu du produit -->
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ product.title }}</h3>
                            <p class="text-primary font-bold mb-2">{{ formatPrice(product.price) }}</p>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ product.description }}</p>
                            <Link
                                :href="route('appointment.create')"
                                class="block w-full text-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
                            >
                                Prendre rendez-vous
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="mt-8 flex justify-center">
                    <div class="flex space-x-1">
                        <template v-for="(link, i) in products.links" :key="i">
                            <div
                                v-if="link.url === null"
                                class="px-4 py-2 text-gray-500 rounded-md"
                                v-html="link.label"
                            ></div>
                            <Link
                                v-else
                                :href="link.url"
                                class="px-4 py-2 rounded-md"
                                :class="{
                  'bg-primary text-white': link.active,
                  'bg-white text-gray-700 hover:bg-gray-50': !link.active
                }"
                            >
                                <span v-if="link.label === '&laquo; Previous'">&laquo; Précédent</span>
                                <span v-else-if="link.label === 'Next &raquo;'">Suivant &raquo;</span>
                                <span v-else>{{ link.label }}</span>
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section CTA -->
        <div class="bg-primary-bg-light py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mb-6">Besoin d'aide pour choisir ?</h2>
                <p class="text-lg text-gray-700 max-w-3xl mx-auto mb-8">
                    Contactez-nous pour obtenir des conseils personnalisés sur nos produits et services.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link
                        :href="route('appointment.create')"
                        class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
                    >
                        Prendre rendez-vous
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="px-8 py-3 bg-white text-primary border border-primary rounded-full hover:bg-gray-50 transition-colors font-medium"
                    >
                        Nous contacter
                    </Link>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<style scoped>
/* Animation subtile au survol des cartes de produit */
.product-card:hover {
    transform: translateY(-5px);
}

/* Limiter le nombre de lignes pour la description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

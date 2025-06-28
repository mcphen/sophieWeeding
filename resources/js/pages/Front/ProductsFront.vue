<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

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

// WhatsApp sharing
const whatsappShareUrl = computed(() => {
    const message = `Découvrez les produits chez Sophie Wedding - ${currentUrl.value}`;
    // Default WhatsApp number if not provided in settings
    const whatsappNumber = props.contactSettings?.whatsapp_number || '+221785383069';
    // Format number: remove spaces, +, and ensure it starts with country code
    const formattedNumber = whatsappNumber.replace(/\s+|\+/g, '');
    return `https://wa.me/${formattedNumber}?text=${encodeURIComponent(message)}`;
});

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

// Truncate description to a specific length and preserve HTML
const truncateDescription = (description: string, length: number = 100): string => {
    if (!description) return '';

    // Create a temporary div to parse HTML
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = description;

    // Get text content
    const textContent = tempDiv.textContent || tempDiv.innerText || '';

    // Truncate text
    if (textContent.length <= length) return description;

    // Return truncated text with ellipsis
    return textContent.substring(0, length) + '...';
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

// Track WhatsApp click
const trackWhatsAppClick = (productId = null) => {
    const actionDetails = productId ? `product_id:${productId}` : 'product_list';

    axios.post('/api/track-action', {
        page_visited: window.location.pathname,
        action: 'whatsapp_click',
        action_details: actionDetails
    }).catch(error => {
        console.error('Error tracking WhatsApp click:', error);
    });
};

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
                        <Link :href="route('product.show', { slug: product.slug })" class="h-48 overflow-hidden block">
                            <img
                                v-if="product.image_path"
                                :src="product.image_path"
                                :alt="product.title"
                                class="w-full h-full object-cover object-center"
                            />
                            <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Pas d'image</span>
                            </div>
                        </Link>

                        <!-- Contenu du produit -->
                        <div class="p-4">
                            <Link :href="route('product.show', { slug: product.slug })">
                                <h3 class="text-lg font-medium text-gray-900 mb-2 hover:text-primary">{{ product.title }}</h3>
                            </Link>
                            <p class="text-primary font-bold mb-2">{{ formatPrice(product.price) }}</p>
                            <div class="text-gray-600 text-sm mb-4 line-clamp-3" v-html="truncateDescription(product.description, 100)"></div>
                            <div class="flex flex-col space-y-2">
                                <Link
                                    :href="route('product.show', { slug: product.slug })"
                                    class="block w-full text-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
                                >
                                    Voir détails
                                </Link>
                                <a
                                    :href="whatsappShareUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center"
                                    @click="trackWhatsAppClick(product.id)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                        <path d="M12 0C5.373 0 0 5.373 0 12c0 6.628 5.373 12 12 12 6.628 0 12-5.373 12-12 0-6.628-5.373-12-12-12zm.029 18.88a7.947 7.947 0 0 1-3.76-.954l-4.17 1.093 1.112-4.063A7.935 7.935 0 0 1 4.2 12c0-4.373 3.557-7.93 7.93-7.93S20.06 7.627 20.06 12c0 4.373-3.557 7.93-7.93 7.93h-.1z" fill-rule="nonzero"/>
                                    </svg>
                                    Contacter sur WhatsApp
                                </a>
                            </div>
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
                    <a
                        :href="whatsappShareUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-8 py-3 bg-green-600 text-white rounded-full hover:bg-green-700 transition-colors font-medium flex items-center justify-center"
                        @click="trackWhatsAppClick()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 6.628 5.373 12 12 12 6.628 0 12-5.373 12-12 0-6.628-5.373-12-12-12zm.029 18.88a7.947 7.947 0 0 1-3.76-.954l-4.17 1.093 1.112-4.063A7.935 7.935 0 0 1 4.2 12c0-4.373 3.557-7.93 7.93-7.93S20.06 7.627 20.06 12c0 4.373-3.557 7.93-7.93 7.93h-.1z" fill-rule="nonzero"/>
                        </svg>
                        Contacter sur WhatsApp
                    </a>
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

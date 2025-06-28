<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';

// For image gallery
const currentImageIndex = ref(0);
const showGalleryModal = ref(false);

// Define types
interface ProductImage {
    id: number;
    image_url: string;
    order: number;
}

interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    image_path: string | null;
    image_url: string | null;
    images?: ProductImage[];
}

// Define props
const props = defineProps<{
    product: Product;
    relatedProducts: Product[];
    contactSettings?: Record<string, any>;
}>();

// Current URL for canonical and sharing
const currentUrl = ref('');
let jsonLdScript: HTMLScriptElement | null = null;

// WhatsApp sharing
const whatsappShareUrl = computed(() => {
    const message = `Découvrez ce produit chez Sophie Wedding: ${props.product.title} - ${currentUrl.value}`;
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
    jsonLdScript.textContent = JSON.stringify(productJsonLd.value);
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

// Format price with currency
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);
};

// Computed properties for meta tags
const metaTitle = computed(() => `${props.product.title} | Sophie Wedding`);
const metaDescription = computed(() => props.product.description
    ? props.product.description.substring(0, 160)
    : `Découvrez ${props.product.title} - Produit de mariage de qualité par Sophie Wedding Dreams`);

// JSON-LD structured data for product
const productJsonLd = computed(() => {
    // Prepare images array for JSON-LD
    let images = [];

    // Add multiple images if available
    if (props.product.images && props.product.images.length > 0) {
        images = props.product.images.map(img => img.image_url);
    }
    // Fallback to single image
    else if (props.product.image_path) {
        images = [`${typeof window !== 'undefined' ? window.location.origin : ''}${props.product.image_path}`];
    }

    return {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: props.product.title,
        description: props.product.description,
        image: images,
        offers: {
            '@type': 'Offer',
            price: props.product.price,
            priceCurrency: 'XOF',
            availability: 'https://schema.org/InStock'
        },
        brand: {
            '@type': 'Brand',
            name: 'Sophie Wedding Dreams'
        }
    };
});
</script>

<template>
    <LayoutFront>
        <Head>
            <title>{{ metaTitle }}</title>
            <meta name="description" :content="metaDescription" />
            <link rel="canonical" :href="currentUrl" />

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="product" />
            <meta property="og:title" :content="metaTitle" />
            <meta property="og:description" :content="metaDescription" />
            <meta property="og:image" :content="(props.product.images && props.product.images.length > 0) ? props.product.images[0].image_url : (props.product.image_path ? `${typeof window !== 'undefined' ? window.location.origin : ''}${props.product.image_path}` : '')" />
            <meta property="og:url" :content="currentUrl" />

            <!-- Twitter -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="metaTitle" />
            <meta name="twitter:description" :content="metaDescription" />
            <meta name="twitter:image" :content="(props.product.images && props.product.images.length > 0) ? props.product.images[0].image_url : (props.product.image_path ? `${typeof window !== 'undefined' ? window.location.origin : ''}${props.product.image_path}` : '')" />

            <!-- Structured Data is now injected programmatically -->
        </Head>

        <!-- Breadcrumb -->
        <div class="bg-gray-100 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center text-sm text-gray-600">
                    <Link :href="route('home')" class="hover:text-primary">Accueil</Link>
                    <span class="mx-2">/</span>
                    <Link :href="route('products')" class="hover:text-primary">Produits</Link>
                    <span class="mx-2">/</span>
                    <span class="text-primary">{{ props.product.title }}</span>
                </div>
            </div>
        </div>

        <!-- Product Detail Section -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="md:flex">
                        <!-- Product Image Gallery -->
                        <div class="md:w-1/2">
                            <div class="h-96 md:h-full overflow-hidden relative">
                                <!-- Main Image -->
                                <img
                                    v-if="props.product.images && props.product.images.length > 0"
                                    :src="props.product.images[currentImageIndex].image_url"
                                    :alt="props.product.title"
                                    class="w-full h-full object-cover object-center cursor-pointer"
                                    @click="showGalleryModal = true"
                                />
                                <img
                                    v-else-if="props.product.image_path"
                                    :src=" props.product.image_path"
                                    :alt="props.product.title"
                                    class="w-full h-full object-cover object-center"
                                />
                                <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">Pas d'image</span>
                                </div>

                                <!-- Navigation Arrows (if multiple images) -->
                                <div v-if="props.product.images && props.product.images.length > 1" class="absolute inset-0 flex items-center justify-between p-4">
                                    <button
                                        @click.prevent="currentImageIndex = (currentImageIndex - 1 + props.product.images.length) % props.product.images.length"
                                        class="bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 focus:outline-none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        @click.prevent="currentImageIndex = (currentImageIndex + 1) % props.product.images.length"
                                        class="bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 focus:outline-none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Thumbnails -->
                            <div v-if="props.product.images && props.product.images.length > 1" class="flex mt-4 space-x-2 overflow-x-auto">
                                <div
                                    v-for="(image, index) in props.product.images"
                                    :key="index"
                                    @click="currentImageIndex = index"
                                    class="w-20 h-20 flex-shrink-0 cursor-pointer border-2 rounded overflow-hidden"
                                    :class="{'border-primary': currentImageIndex === index, 'border-transparent': currentImageIndex !== index}"
                                >
                                    <img :src="image.image_url" :alt="props.product.title + ' image ' + (index + 1)" class="w-full h-full object-cover" />
                                </div>
                            </div>
                        </div>

                        <!-- Full Screen Gallery Modal -->
                        <div v-if="showGalleryModal && props.product.images && props.product.images.length > 0" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center">
                            <div class="relative w-full max-w-4xl">
                                <!-- Close Button -->
                                <button @click="showGalleryModal = false" class="absolute top-4 right-4 text-white z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Main Image -->
                                <img :src="props.product.images[currentImageIndex].image_url" :alt="props.product.title" class="w-full h-auto max-h-[80vh] object-contain" />

                                <!-- Navigation Arrows -->
                                <div class="absolute inset-0 flex items-center justify-between p-4">
                                    <button
                                        @click.prevent="currentImageIndex = (currentImageIndex - 1 + props.product.images.length) % props.product.images.length"
                                        class="bg-white bg-opacity-25 hover:bg-opacity-50 rounded-full p-2 focus:outline-none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        @click.prevent="currentImageIndex = (currentImageIndex + 1) % props.product.images.length"
                                        class="bg-white bg-opacity-25 hover:bg-opacity-50 rounded-full p-2 focus:outline-none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="md:w-1/2 p-8">
                            <h1 class="text-3xl font-serif font-bold text-gray-900 mb-4">{{ props.product.title }}</h1>
                            <p class="text-2xl text-primary font-bold mb-6">{{ formatPrice(props.product.price) }}</p>

                            <div class="prose prose-lg max-w-none mb-8">
                                <p v-html="props.product.description"></p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <Link
                                    :href="route('appointment.create')"
                                    class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium text-center"
                                >
                                    Prendre rendez-vous
                                </Link>
                                <Link
                                    :href="route('contact')"
                                    class="px-8 py-3 bg-white text-primary border border-primary rounded-full hover:bg-gray-50 transition-colors font-medium text-center"
                                >
                                    Nous contacter
                                </Link>
                                <a
                                    :href="whatsappShareUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-8 py-3 bg-green-600 text-white rounded-full hover:bg-green-700 transition-colors font-medium text-center flex items-center justify-center"
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
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-serif font-bold text-primary mb-8 text-center">Produits similaires</h2>

                <div v-if="relatedProducts && relatedProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="product in relatedProducts" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <!-- Product Image -->
                        <div class="h-48 overflow-hidden">
                            <img
                                v-if="product.image_url"
                                :src="product.image_url"
                                :alt="product.title"
                                class="w-full h-full object-cover object-center"
                            />
                            <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Pas d'image</span>
                            </div>
                        </div>

                        <!-- Product Content -->
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ product.title }}</h3>
                            <p class="text-primary font-bold mb-2">{{ formatPrice(product.price) }}</p>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ product.description }}</p>
                            <Link
                                :href="route('product.show', { slug: product.slug })"
                                class="block w-full text-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
                            >
                                Voir détails
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8">
                    <p class="text-gray-600">Aucun produit similaire disponible.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
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
                    <a
                        :href="whatsappShareUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-8 py-3 bg-green-600 text-white rounded-full hover:bg-green-700 transition-colors font-medium flex items-center justify-center"
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

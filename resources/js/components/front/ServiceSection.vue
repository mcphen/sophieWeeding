<template>
    <section class="py-20 bg-white" :class="props.bgColor">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Nos Services</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Découvrez comment nous pouvons transformer votre vision en un événement mémorable
                </p>
            </div>
            <div v-if="isLoading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#d1922f]"></div>
            </div>
            <div v-else-if="error" class="text-center text-red-500 py-8">
                {{ error }}
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service cards dynamically generated -->
                <div
                    v-for="service in services"
                    :key="service.id"
                    class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100"
                >
                    <!-- Image en haut qui occupe toute la largeur -->
                    <img
                        v-if="service.image_url"
                        :src="service.image_url"
                        :alt="service.title"
                        class="w-full h-48 object-cover"
                    />
                    <div v-else class="w-full h-48 bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900">{{ service.title }}</h3>
                        <p class="mt-4 text-gray-600" v-html="truncateHtml(service.description)">
                        </p>
                        <div v-if="service.min_price" class="mt-4 text-[#d1922f] font-medium">
                            À partir de {{ service.min_price }} €
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <Link
                    :href="'/services'"
                    class="inline-block px-8 py-3 rounded-full border border-[#d1922f]/30 text-[#d1922f] hover:border-[#d1922f] hover:bg-[#d1922f]/5 font-medium transition-colors"
                >
                    Tous nos services
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

interface Props {
    bgColor?: string;
}

const props = defineProps<Props>();

// Interface for service data
interface Service {
    id: number;
    title: string;
    description: string;
    image_url: string | null;
    min_price: number | null;
}

// Reactive state
const services = ref<Service[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);

// Function to fetch services from API
const fetchServices = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // API call to get services
        const response = await axios.get('/api/services');

        if (response.status === 200 && response.data) {
            services.value = response.data;
        } else {
            error.value = 'Impossible de charger les données des services';
        }
    } catch (err) {
        console.error('Erreur lors du chargement des services:', err);
        error.value = 'Une erreur est survenue lors du chargement des données';
    } finally {
        isLoading.value = false;
    }
};

// Fonction pour tronquer le HTML tout en conservant la structure
const truncateHtml = (html, maxLength = 120) => {
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

onMounted(() => {
    // Load services when component is mounted
    fetchServices();
});
</script>

<style scoped>
/* Additional animations */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

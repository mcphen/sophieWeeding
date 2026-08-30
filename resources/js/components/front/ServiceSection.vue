<template>
    <section class="py-20 bg-white" :class="props.bgColor">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary-dark">Nos actions</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-semibold text-gray-900">Ce que nous faisons sur le terrain</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Des actions concrètes menées avec et pour les communautés que nous accompagnons en Afrique.
                </p>
            </div>
            <div v-if="isLoading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
            </div>
            <div v-else-if="error" class="text-center text-red-500 py-8">
                {{ error }}
            </div>
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Carte vedette : la première action -->
                <div
                    v-if="services[0]"
                    v-reveal:up
                    class="lg:row-span-2 group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow min-h-[360px] lg:min-h-[560px]"
                >
                    <img
                        v-if="services[0].image_url"
                        :src="services[0].image_url"
                        :alt="services[0].title"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                    />
                    <div v-else class="absolute inset-0 bg-primary-bg-light"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></div>

                    <div class="absolute top-6 left-6 flex items-center justify-center w-14 h-14 rounded-full bg-white/90 text-primary text-2xl shadow-sm">
                        <i :class="iconFor(services[0].title)"></i>
                    </div>
                    <div v-if="services[0].stat_value" class="absolute top-6 right-6 bg-white/90 rounded-xl px-4 py-2 text-right shadow-sm">
                        <p class="font-display text-2xl font-bold text-primary leading-none">{{ services[0].stat_value }}</p>
                        <p v-if="services[0].stat_label" class="mt-1 text-[11px] uppercase tracking-wide text-gray-600">{{ services[0].stat_label }}</p>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 text-white">
                        <h3 class="font-display text-2xl md:text-3xl font-semibold">{{ services[0].title }}</h3>
                        <p class="mt-3 text-white/85 max-w-md" v-html="truncateHtml(services[0].description, 160)"></p>
                        <Link
                            :href="'/services'"
                            class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-white hover:gap-3 transition-all"
                        >
                            En savoir plus
                            <i class="bi bi-arrow-right"></i>
                        </Link>
                    </div>
                </div>

                <!-- Deux actions secondaires, empilées -->
                <div class="grid grid-rows-2 gap-6">
                    <div
                        v-for="service in services.slice(1, 3)"
                        :key="service.id"
                        v-reveal:up
                        class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow min-h-[170px] lg:min-h-[267px]"
                    >
                        <img
                            v-if="service.image_url"
                            :src="service.image_url"
                            :alt="service.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 bg-primary-bg-light"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></div>

                        <div class="absolute top-4 left-4 flex items-center justify-center w-10 h-10 rounded-full bg-white/90 text-primary text-base shadow-sm">
                            <i :class="iconFor(service.title)"></i>
                        </div>
                        <div v-if="service.stat_value" class="absolute top-4 right-4 bg-white/90 rounded-lg px-3 py-1.5 text-right shadow-sm">
                            <p class="font-display text-lg font-bold text-primary leading-none">{{ service.stat_value }}</p>
                            <p v-if="service.stat_label" class="mt-0.5 text-[10px] uppercase tracking-wide text-gray-600">{{ service.stat_label }}</p>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                            <h3 class="text-lg font-semibold">{{ service.title }}</h3>
                            <p class="mt-1 text-sm text-white/85 line-clamp-2" v-html="truncateHtml(service.description, 80)"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <Link
                    :href="'/services'"
                    class="inline-block px-8 py-3 rounded-full border border-primary/30 text-primary hover:border-primary hover:bg-primary-bg-light font-medium transition-colors"
                >
                    Voir toutes nos actions
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
    image_path: string | null;
    min_price: number | null;
    stat_value?: string | null;
    stat_label?: string | null;
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

// Choisit une icône thématique en fonction du titre de l'action
const iconFor = (title: string) => {
    const t = title.toLowerCase();
    if (t.includes('éduc') || t.includes('educ') || t.includes('scolar')) return 'bi bi-mortarboard-fill';
    if (t.includes('santé') || t.includes('sante') || t.includes('matern')) return 'bi bi-heart-pulse-fill';
    if (t.includes('solidair') || t.includes('social')) return 'bi bi-people-fill';
    return 'bi bi-flower3';
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

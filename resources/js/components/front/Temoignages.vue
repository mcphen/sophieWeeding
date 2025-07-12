<template>
    <section class="py-20 " :class="props.bgColor">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 :class="props.classNamesTitle">Ce que disent nos clients</h2>
                <div class="w-24 h-1 bg-primary mx-auto mb-8" v-if="props.isHorizontalDiv"></div>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Des moments inoubliables créés pour des couples extraordinaires
                </p>
            </div>

            <!-- État de chargement -->
            <div v-if="isLoading" class="flex justify-center items-center py-16">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-primary"></div>
            </div>

            <!-- Message d'erreur -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-center mx-auto max-w-2xl">
                <p>{{ error }}</p>
                <button @click="fetchTestimonials" class="mt-2 px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 transition-colors">
                    Réessayer
                </button>
            </div>

            <!-- Aucun témoignage trouvé -->
            <div v-else-if="testimonials.length === 0" class="text-center py-8 text-gray-500">
                Aucun témoignage à afficher pour le moment.
            </div>

            <!-- Slider de témoignages -->
            <div v-else class="relative">
                <div class="overflow-hidden">
                    <div
                        class="flex transition-transform duration-300 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndex * 50}%)` }"
                    >
                        <div
                            v-for="testimonial in testimonials"
                            :key="testimonial.id"
                            class="w-full md:w-1/2 flex-shrink-0 px-4"
                        >
                            <div class="bg-gray-50 rounded-xl p-8 shadow-sm h-full hover:shadow-md transition-shadow duration-300">
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0">
                                        <!-- Affichage de l'image s'il y en a une, sinon afficher les initiales -->
                                        <div v-if="testimonial.image_url" class="h-12 w-12 rounded-full overflow-hidden">
                                            <img :src="testimonial.image_url" :alt="testimonial.author_name" class="h-full w-full object-cover">
                                        </div>
                                        <div v-else class="h-12 w-12 rounded-full bg-[#d1922f]/20 flex items-center justify-center">
                                            <span class="text-[#d1922f] font-semibold">{{ getInitials(testimonial.author_name) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ testimonial.author_name }}</h4>
                                        <p class="text-gray-500">{{ testimonial.author_title }}</p>
                                    </div>
                                </div>
                                <!-- 5 étoiles pour chaque témoignage -->
                                <div class="flex justify-center mb-4 star-rating">
                                    <svg v-for="star in 5" :key="star" class="h-5 w-5 text-[#d1922f] mx-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 italic" v-html="testimonial.content">

                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex justify-center mt-8 gap-3">
                    <button
                        @click="prevSlide"
                        class="p-2 rounded-full border border-[#d1922f]/30 text-[#d1922f] hover:bg-[#d1922f]/5 disabled:opacity-30 disabled:cursor-not-allowed"
                        :disabled="currentIndex === 0 || testimonials.length <= 2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        @click="nextSlide"
                        class="p-2 rounded-full border border-[#d1922f]/30 text-[#d1922f] hover:bg-[#d1922f]/5 disabled:opacity-30 disabled:cursor-not-allowed"
                        :disabled="currentIndex >= testimonials.length - 2 || testimonials.length <= 2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Pagination Dots -->
                <div v-if="totalSlides > 1" class="flex justify-center gap-2 mt-4">
                    <button
                        v-for="n in totalSlides"
                        :key="n"
                        @click="goToSlide(n-1)"
                        class="w-2 h-2 rounded-full transition-colors"
                        :class="isActiveDot(n-1) ? 'bg-[#d1922f]' : 'bg-gray-300 hover:bg-[#d1922f]/50'"
                    ></button>
                </div>
            </div>
        </div>
    </section>
</template>


<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface Props {
    bgColor?: string;
    classNamesTitle?: string;
    isHorizontalDiv?: boolean;
}

const props = defineProps<Props>();

// Interface pour les témoignages basée sur le modèle Testimonial.php
interface Testimonial {
    id: number;
    author_name: string;
    author_title: string;
    content: string;
    image_path?: string;
    image_url?: string;
    created_at?: string;
    updated_at?: string;
}

// États réactifs
const testimonials = ref<Testimonial[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);
const currentIndex = ref(0);

// Fonction pour récupérer les témoignages depuis l'API
const fetchTestimonials = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // Appel API pour récupérer les témoignages
        const response = await axios.get('/testimonials/listes');

        if (response.status === 200 && response.data) {
            testimonials.value = response.data;

            // Réinitialiser l'index courant si nécessaire
            if (currentIndex.value > 0 && currentIndex.value >= testimonials.value.length - 2) {
                currentIndex.value = Math.max(0, testimonials.value.length - 2);
            }
        } else {
            error.value = 'Impossible de charger les témoignages';
        }
    } catch (err) {
        console.error('Erreur lors du chargement des témoignages:', err);
        error.value = 'Une erreur est survenue lors du chargement des témoignages';
    } finally {
        isLoading.value = false;
    }
};

// Calcul du nombre total de slides
const totalSlides = computed(() => {
    return Math.ceil(testimonials.value.length / 2);
});

// Fonction pour obtenir les initiales d'un nom
const getInitials = (name: string) => {
    if (!name) return '';

    // Diviser le nom en parties (prénom et nom de famille) et prendre l'initiale de chaque partie
    return name
        .split(' ')
        .map(part => part.charAt(0).toUpperCase())
        .join('');
};

// Fonction pour vérifier si un point de pagination est actif
const isActiveDot = (dotIndex: number) => {
    return dotIndex === Math.floor(currentIndex.value / 2);
};

// Fonctions de navigation
const nextSlide = () => {
    if (currentIndex.value < testimonials.value.length - 2) {
        currentIndex.value += 2;
    }
};

const prevSlide = () => {
    if (currentIndex.value > 0) {
        currentIndex.value -= 2;
    }
};

const goToSlide = (index: number) => {
    currentIndex.value = index * 2;
};

// Charger les témoignages au montage du composant
onMounted(() => {
    fetchTestimonials();
});
</script>

<style scoped>
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

/* Animation pour les étoiles */
.bg-gray-50:hover .star-rating svg {
    transform: scale(1.1);
    filter: drop-shadow(0 0 2px rgba(209, 146, 47, 0.5));
}
</style>

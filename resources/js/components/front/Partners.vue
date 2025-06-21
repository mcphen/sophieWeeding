<template>
  <section class="py-16" :class="props.bgColor">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <h2 :class="props.classNames">Nos Partenaires</h2>
          <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Nous collaborons avec les meilleurs prestataires pour vous offrir un service d'excellence
        </p>
      </div>

      <div class="relative">
        <div class="overflow-hidden">
          <div
            class="flex transition-transform duration-300 ease-in-out"
            :style="{ transform: `translateX(-${currentIndex * slideWidth}%)` }"
          >
            <div
              v-for="(partner, index) in partners"
              :key="index"
              :class="[
                'flex-shrink-0 px-2',
                isMobile ? 'w-1/2' : isTablet ? 'w-1/3' : 'w-1/4'
              ]"
            >
              <div class="flex items-center justify-center p-6 bg-white rounded-lg shadow-sm h-32">

                  <Link :href="partner.website_url" :target="'_blank'" >
                      <img
                          :src="`${partner.logo_path}`"
                          :alt="partner.name"
                          class="max-h-16 max-w-full object-contain"
                      />

                  </Link>

              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Controls -->
        <button
          @click="prevSlide"
          class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 p-2 rounded-full bg-white shadow-md border border-[#d1922f]/30 text-[#d1922f] hover:bg-[#d1922f]/5 disabled:opacity-30 disabled:cursor-not-allowed z-10"
          :disabled="currentIndex === 0"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          @click="nextSlide"
          class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 p-2 rounded-full bg-white shadow-md border border-[#d1922f]/30 text-[#d1922f] hover:bg-[#d1922f]/5 disabled:opacity-30 disabled:cursor-not-allowed z-10"
          :disabled="isLastSlide"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Pagination Dots -->
        <div class="flex justify-center gap-2 mt-8">
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
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

interface Props{
    bgColor?: string;
    classNames?: string;
}
const props = defineProps<Props>();

// Interface pour les partenaires
interface Partner {
    id: number;
    name: string;
    logo_path: string;
    website_url: string;
}

// État réactif
const partners = ref<Partner[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);


// Fonction pour charger les partenaires depuis l'API
const fetchPartners = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // Appel API pour récupérer les partenaires
        const response = await axios.get('/partners/listes');

        if (response.status === 200 && response.data) {
            partners.value = response.data;
        } else {
            error.value = 'Impossible de charger les données des partenaires';
        }
    } catch (err) {
        console.error('Erreur lors du chargement des partenaires:', err);
        error.value = 'Une erreur est survenue lors du chargement des données';
    } finally {
        isLoading.value = false;
    }
};


const currentIndex = ref(0);
const windowWidth = ref(window.innerWidth);

// Calcule combien de logos afficher à la fois selon la taille de l'écran
const isMobile = computed(() => windowWidth.value < 640);
const isTablet = computed(() => windowWidth.value >= 640 && windowWidth.value < 1024);
const itemsPerView = computed(() => {
  if (isMobile.value) return 2;
  if (isTablet.value) return 3;
  return 4;
});

// Calcule la largeur en pourcentage pour le déplacement du slide
const slideWidth = computed(() => {
  if (isMobile.value) return 50;
  if (isTablet.value) return 33.33;
  return 25;
});

// Calcule le nombre total de slides possibles
const totalSlides = computed(() => {
  return Math.ceil((partners.value.length - itemsPerView.value) / itemsPerView.value) + 1;
});

// Vérifie si on est au dernier slide
const isLastSlide = computed(() => {
  return currentIndex.value >= partners.value.length - itemsPerView.value;
});

// Fonctions de navigation
const nextSlide = () => {
  if (!isLastSlide.value) {
    currentIndex.value = Math.min(currentIndex.value + itemsPerView.value, partners.value.length - itemsPerView.value);
  }
};

const prevSlide = () => {
  if (currentIndex.value > 0) {
    currentIndex.value = Math.max(currentIndex.value - itemsPerView.value, 0);
  }
};

const goToSlide = (slideIndex: number) => {
  currentIndex.value = slideIndex * itemsPerView.value;
};

const isActiveDot = (dotIndex: number) => {
  const slidePosition = currentIndex.value / itemsPerView.value;
  return Math.floor(slidePosition) === dotIndex;
};

// Gestion du redimensionnement de la fenêtre
const handleResize = () => {
  windowWidth.value = window.innerWidth;
  // S'assurer que l'index actuel est toujours valide après redimensionnement
  if (isLastSlide.value && currentIndex.value > 0) {
    currentIndex.value = Math.max(0, partners.value.length - itemsPerView.value);
  }
};

onMounted(() => {
  window.addEventListener('resize', handleResize);

    // Charger les partenaires au montage du composant
    fetchPartners();

});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
/* Animations supplémentaires */
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


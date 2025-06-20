<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

// Définition des services avec état de chargement
interface Service {
  id: number;
  title: string;
  description: string;
  image_url: string;
  [key: string]: any;
}

const services = ref<Service[]>([]);
const loading = ref(true);
const currentUrl = ref('');
let jsonLdScript: HTMLScriptElement | null = null;

// Computed properties for meta tags
const metaTitle = computed(() => "Nos Services de Mariage | Sophie Wedding Dreams");
const metaDescription = computed(() => "Découvrez les services professionnels d'organisation et de planification de mariage proposés par Sophie Wedding Dreams à Dakar, Sénégal. Faites de votre mariage un événement inoubliable.");

// JSON-LD structured data for services
const servicesJsonLd = computed(() => {
  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    itemListElement: services.value.map((service, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      item: {
        '@type': 'Service',
        name: service.title,
        description: service.description,
        provider: {
          '@type': 'Organization',
          name: 'Sophie Wedding Dreams',
          image: '/images/logo.png',
          address: {
            '@type': 'PostalAddress',
            addressLocality: 'Dakar',
            addressRegion: 'Dakar',
            addressCountry: 'SN'
          }
        },
        image: service.image_url
      }
    }))
  };
});

// Function to inject JSON-LD script into the document head
const injectJsonLdScript = () => {
  if (jsonLdScript) {
    document.head.removeChild(jsonLdScript);
  }

  jsonLdScript = document.createElement('script');
  jsonLdScript.setAttribute('type', 'application/ld+json');
  jsonLdScript.textContent = JSON.stringify(servicesJsonLd.value);
  document.head.appendChild(jsonLdScript);
};

// Récupération des services depuis l'API
onMounted(async () => {
  currentUrl.value = window.location.href;

  try {
    const response = await axios.get('/api/services');
    services.value = response.data.map((service: Service) => ({
      ...service,
      alt: `${service.title} - Sophie Wedding`
    }));
    // Inject JSON-LD after services are loaded
    injectJsonLdScript();
  } catch (error) {
    console.error('Erreur lors du chargement des services:', error);
  } finally {
    loading.value = false;
  }
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
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-primary mb-4">Nos Services</h1>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
          Amour Éternel vous propose une gamme de services personnalisés pour rendre votre mariage aussi unique que votre histoire d'amour.
        </p>
      </div>
    </div>

    <!-- Liste des services -->
    <div class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- État de chargement -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>

        <!-- Message si aucun service n'est trouvé -->
        <div v-else-if="services.length === 0" class="text-center py-20">
          <p class="text-lg text-gray-600">Aucun service disponible pour le moment.</p>
        </div>

        <!-- Boucle sur chaque service -->
        <div v-else v-for="(service, index) in services" :key="service.id" class="mb-24 last:mb-0">
          <div class="flex flex-col md:flex-row" :class="{ 'md:flex-row-reverse': index % 2 !== 0 }">
            <!-- Image du service -->
            <div class="w-full md:w-1/2 mb-8 md:mb-0">
              <div class="h-full overflow-hidden rounded-lg shadow-lg">
                <img :src="service.image_url" :alt="service.alt" class="w-full h-full object-cover object-center" />
              </div>
            </div>

            <!-- Contenu du service -->
            <div class="w-full md:w-1/2 flex items-center" :class="[
              index % 2 === 0 ? 'md:pl-12' : 'md:pr-12'
            ]">
              <div>
                <h2 class="text-3xl font-serif font-bold text-primary mb-6">{{ service.title }}</h2>
                <p class="text-gray-700 text-lg mb-8 leading-relaxed" v-html="service.description"></p>
                <Link
                  :href="route('appointment.create')"
                  class="inline-block px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
                >
                  prendre rendez-vous
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section CTA -->
    <div class="bg-primary-bg-light py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mb-6">Prêt à créer votre mariage de rêve ?</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto mb-8">
          Contactez-nous dès aujourd'hui pour discuter de vos envies et découvrir comment nous pouvons les réaliser.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            :href="route('appointment.create')"
            class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
          >
            prendre rendez-vous
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
/* Ajout d'une transition douce pour les éléments interactifs */
a {
  transition: all 0.3s ease;
}

/* Animation subtile au survol des cartes de service */
.service-card:hover {
  transform: translateY(-5px);
}
</style>

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import Partners from '@/components/front/Partners.vue';
import Temoignages from '@/components/front/Temoignages.vue';
import CtaSection from '@/components/front/CtaSection.vue';

interface AboutData{
    content: string;
    image_url?: string;
}


// Définir les props
interface Props {
    about: AboutData;
}
// Récupérer les props - c'est ici qu'on accède aux données passées par le contrôleur
const props = defineProps<Props>();


// Interface pour les membres de l'équipe
interface TeamMember {
    id: number;
    firstname: string;
    lastname: string;
    position: string;
    bio: string;
    image_path: string;
}
// État pour stocker les membres de l'équipe
const teamMembers = ref<TeamMember[]>([]);
const isLoadingTeam = ref(true);
const teamError = ref<string | null>(null);
// Fonction pour charger les membres de l'équipe depuis l'API
const fetchTeamMembers = async () => {
    try {
        isLoadingTeam.value = true;
        teamError.value = null;

        // Appel à l'API pour récupérer les membres de l'équipe
        const response = await axios.get('/team-members/listes');

        // Vérification de la réponse
        if (response.status === 200 && response.data) {
            teamMembers.value = response.data;
        } else {
            teamError.value = 'Impossible de charger les données de l\'équipe';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des membres de l\'équipe:', error);
        teamError.value = 'Une erreur est survenue lors du chargement des données';
    } finally {
        isLoadingTeam.value = false;
    }
};

// Charger les données au montage du composant
onMounted(() => {
    fetchTeamMembers();
});




// Données pour le breadcrumb
const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'À Propos', href: '/about', current: true }
];

// Define CtaSettings interface
interface CtaSettings {
    fromColor: string;
    toColor: string;
    title: string;
    description: string;
    paragraphColor: string;
    linkRoute: string;
    buttonText: string;
    buttonTextColor: string;
}

// Get CTA settings from page props
const page = usePage();
const ctaSettings = computed(() => page.props.ctaSettings as CtaSettings || {
    fromColor: '#d1922f',
    toColor: '#bf8529',
    title: 'Prêts à planifier le mariage de vos rêves ?',
    description: 'Contactez-nous dès aujourd\'hui pour une consultation gratuite et commencez à transformer votre vision en réalité.',
    paragraphColor: '#faecd2',
    linkRoute: 'appointment.create',
    buttonText: 'Prendre rendez-vous',
    buttonTextColor: '#d1922f'
});

</script>

<template>
    <Head>
        <title>À Propos - Sophie Weddings Dreams</title>
        <meta name="description" content="Découvrez l'histoire d'Amour Éternel, notre équipe passionnée, nos partenaires de confiance et les témoignages de nos mariés" />
    </Head>

    <LayoutFront>
        <!-- Bannière du breadcrumb avec image de fond -->
        <div class="relative bg-gray-900">
            <!-- Image d'arrière-plan avec overlay -->
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Bannière À Propos" class="w-full h-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>

            <!-- Contenu du breadcrumb -->
            <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">À Propos</h1>

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

        <div class="py-12 bg-white">
            <!-- Section À Propos de Nous -->
            <section id="about-us" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-center text-primary mb-3">À Propos de Nous</h1>
                <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>

                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2">
                        <img
                            :src="props.about.image_url || '/images/about-us.jpg'"
                            alt="L'équipe Sophie Weddings Dreams"
                            class="rounded-lg shadow-lg w-full h-auto object-cover"
                        >
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-2xl font-serif font-semibold text-gray-800 mb-4">Notre Histoire</h2>
                        <div v-html="props.about.content"></div>
                    </div>
                </div>
            </section>

            <!-- Section Notre Équipe -->
            <section id="our-team" class="bg-primary-bg-light py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-serif font-bold text-center text-primary mb-3">Notre Équipe</h2>
                    <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>
                    <p class="text-center text-gray-600 max-w-3xl mx-auto mb-12">
                        Notre équipe de professionnels passionnés travaille avec dévouement pour transformer vos rêves en réalité. Chacun apporte son expertise unique pour créer des événements inoubliables.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="member in teamMembers" :key="member.firstname" class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:transform hover:scale-105">
                            <img :src="`${member.image_path}`" :alt="member.firstname" class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ member.firstname }} {{member.lastname}}</h3>
                                <p class="text-primary font-medium mb-3">{{ member.position }}</p>
                                <p class="text-gray-600" v-html="member.bio"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Nos Partenaires -->
            <Partners :class-names="'text-3xl font-serif font-bold text-center text-primary mb-3'" />



            <Temoignages
                :bg-color="'bg-primary-bg-light'"
                :class-names-title="'text-3xl font-serif font-bold text-center text-primary mb-3'"
                :is-horizontal-div="true"
            />

            <CtaSection
                :from-color="ctaSettings.fromColor"
                :to-color="ctaSettings.toColor"
                :title="ctaSettings.title"
                :description="ctaSettings.description"
                :paragraph-color="ctaSettings.paragraphColor"
                :link-route="ctaSettings.linkRoute"
                :button-text="ctaSettings.buttonText"
                :button-text-color="ctaSettings.buttonTextColor"
            />
        </div>
    </LayoutFront>

</template>

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import Partners from '@/components/front/Partners.vue';
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
    image_url: string;
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

// Partners section visibility
const partnersRef = ref(null);
const showPartnersSection = ref(false);

// Charger les données au montage du composant
onMounted(() => {
    fetchTeamMembers();

    // Check if partners exist when component is mounted
    setTimeout(() => {
        if (partnersRef.value && partnersRef.value.hasPartners) {
            showPartnersSection.value = partnersRef.value.hasPartners.value;
        }
    }, 0);
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
    toColor: '#8a5e12',
    title: 'Ensemble, changeons des vies à Dakar',
    description: 'Votre don, même modeste, nous permet d\'agir concrètement auprès des enfants et des familles qui en ont besoin. Rejoignez-nous.',
    paragraphColor: '#FBF1E9',
    linkRoute: 'donate',
    buttonText: 'Faire un don',
    buttonTextColor: '#1E2F52'
});

const values = [
    { title: 'Solidarité', description: 'Nous croyons en la force du collectif pour soutenir les plus vulnérables.' },
    { title: 'Impact', description: 'Chaque action est pensée pour produire un changement concret et durable.' },
    { title: 'Humanité', description: 'Nous agissons avec respect, écoute et dignité envers chaque personne accompagnée.' },
];

</script>

<template>
    <Head>
        <title>À propos - Amaël Fondation</title>
        <meta name="description" content="Découvrez l'histoire d'Amaël Fondation, notre mission, notre équipe engagée et nos partenaires à Dakar." />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="À propos - Amaël Fondation" />
        <meta property="og:description" content="Découvrez l'histoire d'Amaël Fondation, notre mission, notre équipe engagée et nos partenaires à Dakar." />
        <meta property="og:url" content="/about" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="À propos - Amaël Fondation" />
        <meta name="twitter:description" content="Découvrez l'histoire d'Amaël Fondation, notre mission, notre équipe engagée et nos partenaires à Dakar." />
    </Head>

    <LayoutFront>
        <!-- Bannière du breadcrumb avec image de fond -->
        <div class="relative bg-[#1A1512]">
            <!-- Image d'arrière-plan avec overlay -->
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Bannière À propos" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>

            <!-- Contenu du breadcrumb -->
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">À propos</h1>

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
            <!-- Section Notre Histoire -->
            <section id="about-us" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <h1 class="font-display text-3xl md:text-4xl font-semibold text-center text-gray-900 mb-3">Notre histoire</h1>
                <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>

                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2">
                        <img
                            :src="props.about.image_url || '/images/about-us.jpg'"
                            alt="L'équipe Amaël Fondation"
                            class="rounded-2xl shadow-lg w-full h-auto object-cover"
                        >
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Notre motivation</h2>
                        <div v-if="props.about?.content" v-html="props.about.content"></div>
                        <p v-else class="text-gray-600 leading-relaxed">
                            Amaël Fondation est née de la conviction qu'un geste de solidarité, aussi modeste soit-il,
                            peut changer une vie. À Dakar, nous accompagnons au quotidien les enfants, les mères et
                            les familles en situation de précarité, avec une mission claire&nbsp;: rendre l'espoir
                            accessible à tous, et une vision&nbsp;: bâtir une communauté solidaire où personne n'est
                            laissé pour compte.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section Nos Valeurs -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div v-for="value in values" :key="value.title" v-reveal:up class="rounded-2xl border border-gray-100 p-6 bg-primary-bg-light">
                        <h3 class="text-lg font-semibold text-primary mb-2">{{ value.title }}</h3>
                        <p class="text-gray-600 text-sm">{{ value.description }}</p>
                    </div>
                </div>
            </section>

            <!-- Section Notre Équipe -->
            <section id="our-team" class="bg-primary-bg-light py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-center text-gray-900 mb-3">Notre équipe</h2>
                    <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>
                    <p class="text-center text-gray-600 max-w-3xl mx-auto mb-12">
                        Une équipe engagée, sur le terrain, aux côtés des communautés que nous accompagnons chaque jour.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="member in teamMembers" :key="member.firstname" class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:transform hover:scale-105">
                            <img :src="member.image_url" :alt="member.firstname" class="w-full h-64 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ member.firstname }} {{member.lastname}}</h3>
                                <p class="text-primary font-medium mb-3">{{ member.position }}</p>
                                <p class="text-gray-600" v-html="member.bio"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Nos Partenaires - Only shown if partners exist -->
            <Partners
                v-show="showPartnersSection"
                ref="partnersRef"
                :class-names="'text-3xl font-bold text-center text-gray-900 mb-3'"
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

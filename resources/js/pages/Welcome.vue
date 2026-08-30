<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import Partners from '@/components/front/Partners.vue';
import BlogPosts from '@/components/front/BlogPosts.vue';
import CtaSection from '@/components/front/CtaSection.vue';
import GalleryPreview from '@/components/front/GalleryPreview.vue';
import { GraduationCap, HeartPulse, HandHeart, Users } from 'lucide-vue-next';

// Define props for banner photos
interface BannerPhoto {
    id: number;
    image_path: string;
    caption: string | null;
    image_url?: string;
}

interface UpcomingSession {
    id: number;
    start_date: string;
    start_time: string;
    location_label: string;
    formatted_price: string;
    available_spots: number | null;
    masterclass: { title: string; niveau: string; slug: string; image_url: string | null };
}

interface Props {
    bannerPhotos: BannerPhoto[];
    upcomingSessions: UpcomingSession[];
}

const props = defineProps<Props>();

// Local placeholder slides used until at least 3 real banner photos are uploaded from the admin
const fallbackBannerImages = [
    { id: -1, image_url: '/images/banner/banner-1.svg', caption: null },
    { id: -2, image_url: '/images/banner/banner-2.svg', caption: null },
    { id: -3, image_url: '/images/banner/banner-3.svg', caption: null },
];

// Process banner photos to ensure we have the image URL; fall back to local slides so the
// hero always rotates through at least 3 images even before real photos are uploaded.
const bannerPhotos = computed(() => {
    if (props.bannerPhotos.length >= 3) {
        return props.bannerPhotos.map(photo => {
            let imagePath = photo.image_path;
            if (imagePath && !imagePath.startsWith('/')) {
                imagePath = `/${imagePath}`;
            }
            return {
                ...photo,
                image_url: photo.image_url || (imagePath ? `/storage${imagePath}` : fallbackBannerImages[0].image_url)
            };
        });
    }
    return fallbackBannerImages;
});

// Slideshow functionality
const currentImageIndex = ref(0);
const slideInterval = ref<number | null>(null);

// Get current image for slideshow
const currentBannerImage = computed(() => {
    return bannerPhotos.value[currentImageIndex.value]?.image_url || fallbackBannerImages[0].image_url;
});

// Track image loading status
const imageLoaded = ref(false);
const handleImageLoad = () => {
    imageLoaded.value = true;
};

// Function to advance to the next image
const nextImage = () => {
    if (bannerPhotos.value.length <= 1) return;
    imageLoaded.value = false;
    currentImageIndex.value = (currentImageIndex.value + 1) % bannerPhotos.value.length;
};

// Handle image loading error
const handleImageError = () => {
    imageLoaded.value = true;
};

// Set up and clean up the slideshow interval
onMounted(() => {
    if (bannerPhotos.value.length > 1) {
        slideInterval.value = window.setInterval(nextImage, 5000); // Change image every 5 seconds
    }
});

onBeforeUnmount(() => {
    if (slideInterval.value !== null) {
        clearInterval(slideInterval.value);
    }
});

// Extraction jour / mois abrégé à partir d'une date au format d/m/Y, pour l'affichage type agenda
const monthAbbreviations = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];

const eventDay = (dateStr: string) => dateStr.split('/')[0];
const eventMonth = (dateStr: string) => {
    const monthIndex = parseInt(dateStr.split('/')[1], 10) - 1;
    return monthAbbreviations[monthIndex] ?? '';
};

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

// Get page props
const page = usePage();

// Get CTA settings from page props
const ctaSettings = computed(() => page.props.ctaSettings as CtaSettings || {
    fromColor: '#d1922f',
    toColor: '#8a5e12',
    title: 'Ensemble, changeons des vies en Afrique',
    description: 'Votre don, même modeste, nous permet d\'agir concrètement auprès des enfants et des familles qui en ont besoin. Rejoignez-nous.',
    paragraphColor: '#FBF1E9',
    linkRoute: 'donate',
    buttonText: 'Faire un don',
    buttonTextColor: '#1E2F52'
});

// Mission tiles: checkerboard mix of illustrated tiles and solid stat tiles (à la ChildFund),
// placeholder figures editable later from a dedicated admin section.
// Palette drawn from the logo (navy + gold), kept gold-heavy so it doesn't read as too blue.
const missionTiles = [
    { type: 'photo', gradient: 'linear-gradient(135deg, #8a5e12, #d1922f)', icon: GraduationCap, value: '500+', label: 'Enfants soutenus' },
    { type: 'solid', bg: '#d1922f', icon: HandHeart, value: '120+', label: 'Familles accompagnées' },
    { type: 'solid', bg: '#1E2F52', icon: Users, value: '30+', label: 'Bénévoles actifs' },
    { type: 'photo', gradient: 'linear-gradient(135deg, #1A1512, #5B3A22)', icon: HeartPulse, value: '5', label: "Années d'engagement" },
];

// Our three pillars, shown in an alternating image/text layout
const pillars = [
    {
        label: 'Éducation',
        title: 'Des fournitures scolaires pour ne laisser aucun enfant de côté',
        description: "Chaque rentrée, nous distribuons cahiers, manuels et kits scolaires aux enfants des familles les plus vulnérables, pour que l'école reste un droit accessible à tous.",
        icon: GraduationCap,
        gradient: 'linear-gradient(135deg, #8a5e12, #d1922f)',
    },
    {
        label: 'Santé maternelle',
        title: "Un accompagnement aux côtés des futures mères",
        description: "Suivi, kits de maternité et sensibilisation : nous soutenons les mères avant et après l'accouchement pour des débuts de vie plus sûrs.",
        icon: HeartPulse,
        gradient: 'linear-gradient(135deg, #10192E, #1E2F52)',
    },
    {
        label: 'Actions solidaires',
        title: 'Une présence de proximité auprès des familles',
        description: "Distributions alimentaires, aide d'urgence et écoute : notre équipe est présente sur le terrain, au plus près des besoins réels des communautés.",
        icon: HandHeart,
        gradient: 'linear-gradient(135deg, #1A1512, #5B3A22)',
    },
];

// Partners section visibility
const partnersRef = ref(null);
const showPartnersSection = ref(false);

// Check if partners exist when component is mounted
onMounted(() => {
    // We'll check the hasPartners property after the Partners component is mounted
    setTimeout(() => {
        if (partnersRef.value && partnersRef.value.hasPartners) {
            showPartnersSection.value = partnersRef.value.hasPartners.value;
        }
    }, 0);
});

</script>

<template>
    <Head title="Amaël Fondation — Donner espoir, agir pour demain" />


    <LayoutFront>
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-[#1A1512] min-h-[85vh] flex items-center">
            <div class="absolute inset-0">
                <Transition name="fade" mode="out-in">
                    <img
                        :key="currentImageIndex"
                        :src="currentBannerImage"
                        :alt="bannerPhotos[currentImageIndex]?.caption || 'Amaël Fondation'"
                        class="w-full h-full object-cover opacity-70"
                        @load="handleImageLoad"
                        @error="handleImageError"
                    />
                </Transition>
                <div class="absolute inset-0 bg-gradient-to-b from-[#1A1512]/70 via-[#1A1512]/60 to-[#1A1512]/85"></div>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-28 text-center">
                
                <h1 class="mt-6 font-display text-5xl sm:text-6xl lg:text-7xl font-semibold tracking-tight text-white">
                    Donner espoir,
                    <span class="block text-primary">agir pour demain</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-white/85 max-w-2xl mx-auto">
                    Amaël Fondation accompagne les enfants, les mères et les familles vulnérables en Afrique à travers
                    des actions concrètes de solidarité, d'éducation et de santé.
                </p>
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <Link
                        :href="route('donate')"
                        class="px-8 py-3.5 rounded-full bg-primary hover:bg-primary-dark text-white font-semibold shadow-lg shadow-primary/30 transition-colors"
                    >
                        Faire un don
                    </Link>
                    <Link
                        :href="route('volunteer')"
                        class="px-8 py-3.5 rounded-full border border-white/40 text-white hover:bg-white/10 font-medium transition-colors"
                    >
                        Devenir bénévole
                    </Link>
                </div>
            </div>

            <!-- Slideshow Navigation Indicators -->
            <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 z-20">
                <button
                    v-for="(photo, index) in bannerPhotos"
                    :key="index"
                    @click="currentImageIndex = index"
                    class="w-3 h-3 rounded-full transition-colors"
                    :class="index === currentImageIndex ? 'bg-white' : 'bg-white/50 hover:bg-white/70'"
                    :aria-label="`View image ${index + 1}`"
                ></button>
            </div>
        </section>

        <!-- Notre mission - split panel with a mission statement and a checkerboard of impact tiles -->
        <section class="bg-[#1A1512]">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left: mission statement on a solid dark panel -->
                <div class="flex items-center px-4 sm:px-6 lg:px-16 py-16 lg:py-24" v-reveal>
                    <div class="max-w-md">
                        <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary">Notre mission</span>
                        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold text-white leading-tight">
                            Les enfants et les familles d'abord
                        </h2>
                        <p class="mt-6 text-white/75 leading-relaxed">
                            Depuis sa création, Amaël Fondation porte des projets de solidarité, de fournitures scolaires,
                            d'aide à la maternité et de soutien communautaire en Afrique. Chaque don, chaque bénévole,
                            chaque partenaire nous permet d'aller plus loin.
                        </p>
                        <Link
                            :href="route('services')"
                            class="mt-8 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-white font-semibold hover:bg-primary-dark transition-colors"
                        >
                            Découvrir nos actions
                        </Link>
                    </div>
                </div>

                <!-- Right: checkerboard of impact tiles -->
                <div class="grid grid-cols-2 grid-rows-2">
                    <div
                        v-for="tile in missionTiles"
                        :key="tile.label"
                        v-reveal
                        class="relative flex flex-col items-center justify-center text-center gap-2 p-6 min-h-[180px] sm:min-h-[220px]"
                        :style="{ background: tile.type === 'photo' ? tile.gradient : tile.bg }"
                    >
                        <component :is="tile.icon" class="w-7 h-7 text-white/90" stroke-width="1.5" />
                        <p class="font-display text-3xl sm:text-4xl font-semibold text-white">{{ tile.value }}</p>
                        <p class="text-sm text-white/85 max-w-[14rem]">{{ tile.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nos piliers - alternating image/text layout -->
        <section class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-28">
                <div
                    v-for="(pillar, index) in pillars"
                    :key="pillar.title"
                    v-reveal:up
                    class="flex flex-col md:flex-row items-center gap-10"
                    :class="{ 'md:flex-row-reverse': index % 2 !== 0 }"
                >
                    <div class="w-full md:w-1/2">
                        <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg" :style="{ background: pillar.gradient }">
                            <div class="w-full h-full flex items-center justify-center">
                                <component :is="pillar.icon" class="w-20 h-20 text-white/90" stroke-width="1.25" />
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2">
                        <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary-dark">{{ pillar.label }}</span>
                        <h3 class="mt-2 font-display text-2xl sm:text-3xl font-semibold text-gray-900">{{ pillar.title }}</h3>
                        <p class="mt-4 text-gray-600 leading-relaxed">{{ pillar.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Prochains événements -->
        <section v-if="upcomingSessions.length > 0" class="py-24 bg-primary-bg-light">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary-dark">Agenda</span>
                    <h2 class="mt-2 font-display text-3xl sm:text-4xl font-semibold text-gray-900">Prochains événements</h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        Journées de sensibilisation, collectes solidaires et rencontres bénévoles à venir.
                    </p>
                </div>
                <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                    <Link
                        v-for="session in upcomingSessions"
                        :key="session.id"
                        v-reveal:up
                        :href="route('masterclass.show', session.masterclass.slug)"
                        class="group flex items-center gap-4 sm:gap-6 p-5 sm:p-6 hover:bg-primary-bg-light/50 transition-colors"
                    >
                        <!-- Feuillet de date, façon agenda -->
                        <div class="flex-shrink-0 w-16 sm:w-20 text-center">
                            <p class="font-display text-2xl sm:text-3xl font-bold text-primary leading-none">{{ eventDay(session.start_date) }}</p>
                            <p class="mt-1 text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-gray-500">{{ eventMonth(session.start_date) }}</p>
                        </div>
                        <div class="hidden sm:block w-px self-stretch bg-gray-100"></div>

                        <!-- Détails de l'événement -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-base sm:text-lg group-hover:text-primary transition-colors truncate">
                                {{ session.masterclass.title }}
                            </h3>
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-clock"></i>{{ session.start_time }}</span>
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-geo-alt"></i>{{ session.location_label }}</span>
                            </div>
                        </div>

                        <!-- Vignette -->
                        <div class="hidden md:block flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden">
                            <img
                                v-if="session.masterclass.image_url"
                                :src="session.masterclass.image_url"
                                :alt="session.masterclass.title"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full bg-primary-bg-light flex items-center justify-center text-primary">
                                <i class="bi bi-calendar-event text-lg"></i>
                            </div>
                        </div>

                        <i class="bi bi-chevron-right text-gray-300 group-hover:text-primary group-hover:translate-x-0.5 transition-all flex-shrink-0"></i>
                    </Link>
                </div>
                <div class="text-center mt-12">
                    <Link
                        :href="route('masterclasses')"
                        class="inline-block px-8 py-3 rounded-full border border-primary/30 text-primary hover:border-primary hover:bg-white font-medium transition-colors"
                    >
                        Voir tous les événements
                    </Link>
                </div>
            </div>
        </section>

        <!-- Portfolio Section -->
        <GalleryPreview bg-color="bg-gray-50" />

        <!-- Partners Section - Only shown if partners exist -->
        <Partners
            v-show="showPartnersSection"
            ref="partnersRef"
            :bg-color="'bg-gray-50'"
            :class-names="'text-3xl font-serif font-bold text-gray-900'"
        />

        <!-- Blog Preview -->

        <BlogPosts />
        <!-- CTA Section -->

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
    </LayoutFront>

</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.8s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

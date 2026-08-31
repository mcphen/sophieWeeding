<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted } from 'vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import axios from 'axios';
import { useToast } from 'vue-toastification';

// Define Service interface
interface Service {
    id: number;
    title: string;
    description?: string;
    image_path?: string;
    image_url?: string;
    min_price?: number;
}

const mobileMenuOpen = ref(false);
const dropdownStates = ref({
    about: false,
    services: false
});
const isLoading = ref(true);
const services = ref<Service[]>([]);
const showBackToTop = ref(false);
const newsletterEmail = ref('');
const isSubscribing = ref(false);
const showCookieConsent = ref(true);
const logoFailed = ref(false);
const isScrolled = ref(false);
const toast = useToast();

// Classes applied to the primary nav links: white/transparent while floating over the hero,
// solid once the header has become opaque on scroll.
const navLinkClass = computed(() =>
    isScrolled.value
        ? 'px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors'
        : 'px-3 py-2 text-white hover:text-white/70 font-medium transition-colors'
);

const joinButtonClass = computed(() =>
    isScrolled.value
        ? 'ml-2 px-4 py-2 text-primary border border-primary/40 rounded-full hover:bg-primary-bg-light font-medium transition-colors'
        : 'ml-2 px-4 py-2 text-white border border-white/50 rounded-full hover:bg-white/10 font-medium transition-colors'
);

const logoTextClass = computed(() =>
    isScrolled.value ? 'text-xl font-bold tracking-tight text-gray-900' : 'text-xl font-bold tracking-tight text-white'
);

const mobileButtonClass = computed(() =>
    isScrolled.value
        ? 'inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-primary hover:bg-primary-bg-light focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-light'
        : 'inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white/70 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/40'
);

// Function to fetch services from the backend
const fetchServices = async () => {
    try {
        const response = await axios.get('/api/services');
        services.value = response.data;
    } catch (error) {
        console.error('Error fetching services:', error);
    }
};

interface ContactSettings {
    contact_phone: string;
    contact_phone_fixed: string;
    contact_email: string;
    social_facebook: string;
    social_twitter: string;
    social_youtube: string;
    social_linkedin: string;
    social_tiktok: string;
    social_instagram: string;
    contact_address: string;
    opening_hours: string;
}

// Get contact settings from props
// Get contact settings from props
const page = usePage();
const contactSettings = computed(() => page.props.contactSettings as ContactSettings || {
    contact_phone: '(+221) 78 000 00 00',
    contact_phone_fixed: '(+221) 33 000 00 00',
    contact_email: 'contact@amaelfondation.org',
    social_facebook: '#',
    social_twitter: '#',
    social_youtube: '#',
    social_linkedin: '#',
    social_tiktok: '#',
    social_instagram: '#',
    contact_address: "Afrique de l'Ouest",
    opening_hours: 'Lundi - Vendredi : 9h - 18h'
});


const toggleDropdown = (menu: keyof typeof dropdownStates.value) => {
    dropdownStates.value[menu] = !dropdownStates.value[menu];
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// Function to handle scroll for back-to-top button and the floating header
const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500;
    isScrolled.value = window.scrollY > 40;
};

// Function to scroll back to top
const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

// Function to accept cookies
const acceptCookies = async () => {
    showCookieConsent.value = false;
    localStorage.setItem('cookiesAccepted', 'true');

    try {
        // Track the cookie acceptance
        const currentPage = window.location.pathname;
        await axios.post('/api/track-action', {
            page_visited: currentPage,
            action: 'cookie_consent',
            action_details: 'Cookie consent accepted'
        });

        // Store the consent in the database
        await axios.post('/api/cookie-consent');

        // No need to show a toast message for cookie acceptance
    } catch (error) {
        console.error('Error recording cookie consent:', error);
        // We don't show an error message to the user for this
    }
};

// Configuration du système de chargement
onMounted(() => {
    // Initialiser le loader comme visible
    isLoading.value = false;

    // Ajouter les écouteurs d'événements pour les transitions de page
    router.on('start', () => {
        isLoading.value = true;
    });

    router.on('finish', () => {
        // Utilisation d'un petit délai pour assurer que le DOM est mis à jour
        setTimeout(() => {
            isLoading.value = false;
        }, 200);
        handleScroll();
    });

    // Fetch services when component is mounted
    fetchServices();

    // Add scroll event listener for back-to-top button and the floating header
    window.addEventListener('scroll', handleScroll);
    handleScroll();

    // Check if cookies have been accepted
    if (localStorage.getItem('cookiesAccepted') === 'true') {
        showCookieConsent.value = false;
    }
});

// Remove event listeners when component is unmounted
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Function to track WhatsApp button clicks
const trackWhatsAppClick = () => {
    const currentPage = window.location.pathname;

    axios.post('/api/track-action', {
        page_visited: currentPage,
        action: 'whatsapp_click',
        action_details: 'WhatsApp button clicked'
    })
    .catch(error => {
        console.error('Error tracking WhatsApp click:', error);
    });
};

// Function to handle newsletter subscription
const subscribeToNewsletter = async () => {
    if (!newsletterEmail.value || !newsletterEmail.value.includes('@')) {
        toast.error('Veuillez entrer une adresse email valide');
        return;
    }

    isSubscribing.value = true;

    try {
        // Track the subscription attempt
        const currentPage = window.location.pathname;
        await axios.post('/api/track-action', {
            page_visited: currentPage,
            action: 'newsletter_subscription',
            action_details: 'Newsletter subscription attempt'
        });

        // Send the email to our backend
        const response = await axios.post('/api/newsletter/subscribe', {
            email: newsletterEmail.value,
            source: 'website_footer'
        });

        // Display appropriate message based on the response
        if (response.data.status === 'success') {
            toast.success(response.data.message);
            newsletterEmail.value = '';
        } else if (response.data.status === 'info') {
            toast.info(response.data.message);
        } else {
            toast.error(response.data.message);
        }
    } catch (error) {
        console.error('Error subscribing to newsletter:', error);
        const errorMessage = error.response?.data?.message || 'Une erreur est survenue. Veuillez réessayer plus tard.';
        toast.error(errorMessage);
    } finally {
        isSubscribing.value = false;
    }
};

</script>

<template>
    <Head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="Amaël Fondation - ONG engagée en Afrique pour l'éducation, la santé maternelle et la solidarité auprès des familles vulnérables." />
        <meta name="keywords" content="ONG, fondation, association, solidarité, Afrique, Afrique de l'Ouest, don, bénévolat, Amaël Fondation" />
        <meta name="author" content="Amaël Fondation" />
        <meta name="robots" content="index, follow" />


        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Amaël Fondation - Donner espoir, agir pour demain" />
        <meta property="og:description" content="Amaël Fondation - ONG engagée en Afrique pour l'éducation, la santé maternelle et la solidarité auprès des familles vulnérables." />
        <meta property="og:image" content="/images/logo.png" />
        <meta property="og:site_name" content="Amaël Fondation" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Amaël Fondation - Donner espoir, agir pour demain" />
        <meta name="twitter:description" content="Amaël Fondation - ONG engagée en Afrique pour l'éducation, la santé maternelle et la solidarité auprès des familles vulnérables." />
        <meta name="twitter:image" content="/images/logo.png" />

        <!-- Favicon -->
        <link rel="icon" href="/images/logo.png" type="image/png">
    </Head>

    <!-- Loader -->
    <div v-if="isLoading" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white bg-opacity-80 transition-opacity duration-500">
        <div class="flex flex-col items-center">
            <div class="h-24 w-24 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
            <div class="mt-4 text-xl font-medium text-primary">Chargement...</div>
        </div>
    </div>



    <div class="min-h-screen bg-white text-[#1b1b18]">
        <!-- Floating header: transparent over the hero, solid once scrolled -->
        <div
            class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
            :class="(isScrolled || mobileMenuOpen) ? 'bg-white/95 backdrop-blur-sm shadow-sm' : 'bg-transparent'"
        >
            <!-- Top Bar -->
            <div
                class="bg-primary text-white hidden md:block overflow-hidden transition-all duration-300"
                :class="isScrolled ? 'max-h-0 py-0 opacity-0' : 'max-h-12 py-2 opacity-100'"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <i class="bi bi-telephone text-current"></i>
                            <span class="text-sm">{{ contactSettings.contact_phone }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-phone-fill text-current"></i>
                            <span class="text-sm">{{ contactSettings.contact_phone_fixed }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-envelope mr-1 text-current"></i>
                            <span class="text-sm">{{ contactSettings.contact_email }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a :href="contactSettings.social_facebook" target="_blank" class="hover:text-white/70 transition-colors">
                            <i class="bi bi-facebook text-current text-lg"></i>
                        </a>
                        <a :href="contactSettings.social_instagram" target="_blank" class="hover:text-white/70 transition-colors">
                            <i class="bi bi-instagram text-current text-lg"></i>
                        </a>
                        <a :href="contactSettings.social_linkedin" target="_blank" class="hover:text-white/70 transition-colors">
                            <i class="bi bi-linkedin text-current text-lg"></i>
                        </a>
                        <a :href="contactSettings.social_youtube" target="_blank" class="hover:text-white/70 transition-colors">
                            <i class="bi bi-youtube text-current text-lg"></i>
                        </a>
                    </div>

                </div>
            </div>


            <!-- Navigation -->
            <header class="w-full">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between transition-all duration-300" :class="isScrolled ? 'h-16' : 'h-20'">
                        <div class="flex items-center">
                            <Link :href="route('home')" class="flex-shrink-0 flex items-center gap-2">
                                <img
                                    v-if="contactSettings.site_logo && !logoFailed"
                                    :src="contactSettings.site_logo"
                                    class="h-11 w-11 rounded-full object-cover"
                                    alt="Amaël Fondation"
                                    @error="logoFailed = true"
                                />
                                <span v-else class="flex h-11 w-11 items-center justify-center rounded-full bg-primary text-white font-serif text-lg font-bold">AF</span>
                                <span :class="logoTextClass">Amaël <span class="text-primary">Fondation</span></span>
                            </Link>
                        </div>

                        <!-- Desktop Menu -->
                        <nav class="hidden md:flex items-center space-x-1">
                            <Link :href="route('home')" :class="navLinkClass">
                                Accueil
                            </Link>
                            <Link :href="route('about')" :class="navLinkClass">
                                À propos
                            </Link>
                            <Link :href="route('services')" :class="navLinkClass">
                                Nos actions
                            </Link>
                            <Link :href="route('portfolio')" :class="navLinkClass">
                                Galerie
                            </Link>
                            <Link :href="route('masterclasses')" :class="navLinkClass">
                                Événements
                            </Link>
                            <Link :href="route('blog')" :class="navLinkClass">
                                Blog
                            </Link>
                            <Link :href="route('contact')" :class="navLinkClass">
                                Contact
                            </Link>
                            <Link :href="route('volunteer')" :class="joinButtonClass">
                                Nous rejoindre
                            </Link>
                            <Link
                                :href="route('donate')"
                                class="ml-2 px-6 py-2 bg-primary text-white rounded-full hover:bg-primary-dark font-semibold shadow-sm transition-colors"
                            >
                                Faire un don
                            </Link>
                        </nav>

                        <!-- Mobile menu button -->
                        <div class="flex md:hidden items-center">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                :class="mobileButtonClass"
                                :aria-expanded="mobileMenuOpen"
                                aria-controls="mobile-menu"
                                aria-label="Menu principal"
                            >
                            <span class="sr-only">Ouvrir le menu</span>
                            <svg
                                :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}"
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                            <svg
                                :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}"
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="md:hidden">
                <div class="pt-2 pb-4 space-y-1">
                    <Link
                        :href="route('home')"
                        class="block px-4 py-2 text-base font-medium text-primary border-l-4 border-primary bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Accueil
                    </Link>

                    <!-- Mobile À Propos dropdown -->
                    <Link
                        :href="route('about')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        À propos
                    </Link>
                    <Link
                        :href="route('services')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Nos actions
                    </Link>
                    <Link
                        :href="route('portfolio')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Galerie
                    </Link>
                    <Link
                        :href="route('masterclasses')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Événements
                    </Link>
                    <Link
                        :href="route('blog')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Blog
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Contact
                    </Link>
                    <Link
                        :href="route('volunteer')"
                        class="block mx-4 mt-3 px-4 py-2 text-center text-primary border border-primary/40 rounded-full hover:bg-primary-bg-light transition-colors"
                        @click="closeMobileMenu"
                    >
                        Nous rejoindre
                    </Link>
                    <Link
                        :href="route('donate')"
                        class="block mx-4 mt-3 px-4 py-2 bg-primary text-center text-white rounded-full hover:bg-primary-dark transition-colors font-semibold"
                        @click="closeMobileMenu"
                    >
                        Faire un don
                    </Link>
                </div>
            </div>
        </header>
        </div>

        <!-- Contenu principal (slot pour les pages) -->
        <main>
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-[#1A1512] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Newsletter Subscription -->
                    <div class="lg:col-span-4 mb-8 p-6 bg-white/5 rounded-2xl border border-white/10">
                        <h3 class="text-xl font-semibold mb-2 text-white">Restez informés de nos actions</h3>
                        <p class="text-gray-300 mb-5">
                            Recevez nos actualités et l'impact de vos dons directement par email.
                        </p>
                        <form @submit.prevent="subscribeToNewsletter" class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-envelope text-gray-500"></i>
                                </div>
                                <input
                                    type="email"
                                    v-model="newsletterEmail"
                                    placeholder="Votre adresse email"
                                    class="pl-10 pr-4 py-3 w-full rounded-md text-gray-800 border-2 border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 transition-all duration-200"
                                    aria-label="Adresse email pour la newsletter"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-md transition-colors font-medium shadow-md hover:shadow-lg"
                                :disabled="isSubscribing"
                            >
                                <span v-if="!isSubscribing">S'abonner</span>
                                <span v-else>
                                    <i class="bi bi-arrow-repeat animate-spin inline-block"></i> Envoi...
                                </span>
                            </button>
                        </form>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Amaël Fondation</h3>
                        <p class="text-gray-300 mb-4">
                            Donner espoir, agir pour demain. Une fondation au service des enfants et des familles en Afrique.
                        </p>
                        <div class="flex space-x-4">
                            <a :href="contactSettings.social_facebook" target="_blank" class="text-gray-300 hover:text-primary transition-colors">
                                <i class="bi bi-facebook text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_instagram" target="_blank" class="text-gray-300 hover:text-primary transition-colors">
                                <i class="bi bi-instagram text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_linkedin" target="_blank" class="text-gray-300 hover:text-primary transition-colors">
                                <i class="bi bi-linkedin text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_youtube" target="_blank" class="text-gray-300 hover:text-primary transition-colors">
                                <i class="bi bi-youtube text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link :href="route('home')" class="text-gray-300 hover:text-primary transition-colors">
                                    Accueil
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('about')" class="text-gray-300 hover:text-primary transition-colors">
                                    À propos
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('services')" class="text-gray-300 hover:text-primary transition-colors">
                                    Nos actions
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('portfolio')" class="text-gray-300 hover:text-primary transition-colors">
                                    Galerie
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('masterclasses')" class="text-gray-300 hover:text-primary transition-colors">
                                    Événements
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('blog')" class="text-gray-300 hover:text-primary transition-colors">
                                    Blog
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('contact')" class="text-gray-300 hover:text-primary transition-colors">
                                    Contact
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Nous soutenir</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link :href="route('donate')" class="text-gray-300 hover:text-primary transition-colors">
                                    Faire un don
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('volunteer')" class="text-gray-300 hover:text-primary transition-colors">
                                    Devenir bénévole
                                </Link>
                            </li>
                            <li v-for="service in services.slice(0, 3)" :key="service.id">
                                <Link :href="route('services') + '#service-' + service.id" class="text-gray-300 hover:text-primary transition-colors">
                                    {{ service.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact</h3>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-primary mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{contactSettings.contact_address}}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{contactSettings.contact_phone}}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{contactSettings.contact_email}}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-gray-400 text-center sm:text-left">
                        &copy; {{ new Date().getFullYear() }} Amaël Fondation. Tous droits réservés.
                    </p>
                    <div class="flex items-center gap-4 text-sm text-gray-400">
                        <Link :href="route('legal.notice')" class="hover:text-primary transition-colors">Mentions légales</Link>
                        <span class="text-gray-600">·</span>
                        <Link :href="route('legal.privacy')" class="hover:text-primary transition-colors">Politique de confidentialité</Link>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Cookie Consent Banner -->
        <div
            v-if="showCookieConsent"
            class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 shadow-lg z-40"
            role="alert"
            aria-live="polite"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-sm">
                        Nous utilisons des cookies pour améliorer votre expérience sur notre site. En continuant à naviguer, vous acceptez notre utilisation des cookies.
                        <Link :href="route('legal.privacy')" class="underline hover:text-primary">En savoir plus</Link>
                    </p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="acceptCookies"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md text-sm transition-colors"
                    >
                        Accepter
                    </button>
                </div>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button
            v-show="showBackToTop"
            @click="scrollToTop"
            class="fixed bottom-6 left-6 bg-primary text-white rounded-full p-3 shadow-lg hover:bg-primary-dark transition-all z-50 flex items-center justify-center"
            style="width: 50px; height: 50px;"
            aria-label="Retour en haut de page"
        >
            <i class="bi bi-arrow-up text-xl"></i>
        </button>

        <!-- WhatsApp Button -->
        <a
            :href="`https://wa.me/${(contactSettings.whatsapp_number || contactSettings.contact_phone || '221780000000').replace(/[^0-9]/g, '')}`"
            target="_blank"
            class="fixed bottom-6 right-6 bg-green-500 text-white rounded-full p-3 shadow-lg hover:bg-green-600 transition-all z-50 flex items-center justify-center"
            style="width: 60px; height: 60px;"
            @click="trackWhatsAppClick"
            aria-label="Contactez-nous sur WhatsApp"
        >
            <i class="bi bi-whatsapp text-2xl"></i>
        </a>
    </div>
</template>
<style scoped>
/* Animation pour le loader */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Style pour le placeholder du champ newsletter */
footer input[type="email"]::placeholder {
    color: white;
    opacity: 0.8;
}
</style>

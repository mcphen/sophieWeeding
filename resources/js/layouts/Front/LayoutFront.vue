<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import axios from 'axios';

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
    contact_phone: '(+221) 78 538 30 69',
    contact_phone_fixed: '(+221) 33 865 27 11',
    contact_email: 'sophieweddings5@gmail.com',
    social_facebook: 'https://www.facebook.com/Sophieweddingsdreams22/',
    social_twitter: '#',
    social_youtube: '#',
    social_linkedin: '#',
    social_tiktok: '#',
    social_instagram: 'https://www.instagram.com/sophie_weddings_dreams/',
    contact_address: 'Rue NG-70, 91 Ngor Almadies, Dakar 12000',
    opening_hours: 'Lundi - Vendredi: 8am - 6pm'
});


const toggleDropdown = (menu: keyof typeof dropdownStates.value) => {
    dropdownStates.value[menu] = !dropdownStates.value[menu];
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
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
    });

    // Fetch services when component is mounted
    fetchServices();
});

</script>

<template>
    <Head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="Sophie Weddings Dreams - Votre agence de mariage pour un événement inoubliable à Dakar, Sénégal. Organisation de mariages, décoration, traiteur et plus." />
        <meta name="keywords" content="mariage, wedding planner, organisation mariage, décoration mariage, Dakar, Sénégal, Sophie Wedding, événementiel" />
        <meta name="author" content="Sophie Weddings Dreams" />
        <meta name="robots" content="index, follow" />


        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Sophie Weddings Dreams - Votre agence de mariage à Dakar" />
        <meta property="og:description" content="Sophie Weddings Dreams - Votre agence de mariage pour un événement inoubliable à Dakar, Sénégal. Organisation de mariages, décoration, traiteur et plus." />
        <meta property="og:image" content="/images/logo.png" />
        <meta property="og:site_name" content="Sophie Weddings Dreams" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Sophie Weddings Dreams - Votre agence de mariage à Dakar" />
        <meta name="twitter:description" content="Sophie Weddings Dreams - Votre agence de mariage pour un événement inoubliable à Dakar, Sénégal. Organisation de mariages, décoration, traiteur et plus." />
        <meta name="twitter:image" content="/images/logo.png" />
    </Head>

    <!-- Loader -->
    <div v-if="isLoading" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white bg-opacity-80 transition-opacity duration-500">
        <div class="flex flex-col items-center">
            <div class="h-24 w-24 animate-spin rounded-full border-b-2 border-t-2 border-primary"></div>
            <div class="mt-4 text-xl font-medium text-primary">Chargement...</div>
        </div>
    </div>



    <div class="min-h-screen bg-white text-[#1b1b18]">
        <!-- Top Bar -->
        <div class="bg-primary text-white py-2 hidden md:block">
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
                    <a :href="contactSettings.social_facebook" target="_blank" class="hover:text-rose-200 transition-colors">
                        <i class="bi bi-facebook text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_instagram" target="_blank" class="hover:text-rose-200 transition-colors">
                        <i class="bi bi-instagram text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_linkedin" target="_blank" class="hover:text-rose-200 transition-colors">
                        <i class="bi bi-linkedin text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_youtube" target="_blank" class="hover:text-rose-200 transition-colors">
                        <i class="bi bi-youtube text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_tiktok" target="_blank" class="hover:text-rose-200 transition-colors">
                        <i class="bi bi-tiktok text-current text-lg"></i>
                    </a>
                </div>

            </div>
        </div>


        <!-- Navigation -->
        <header class="w-full bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <Link :href="'#'" class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-serif font-bold text-primary">
                                <img :src="'/images/logo.png'" style="width: 115px;">
                            </span>
                        </Link>
                    </div>

                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex items-center space-x-1">
                        <Link
                            :href="route('home')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Accueil
                        </Link>
                        <Link
                            :href="route('about')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            A propos
                        </Link>



                        <!-- Services Dropdown -->


                        <Link
                            :href="route('services')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Services
                        </Link>

                        <Link
                            :href="route('products')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Produits
                        </Link>

                        <Link
                            :href="route('portfolio')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Portfolio
                        </Link>
                        <Link
                            :href="route('blog')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Blog
                        </Link>
                        <Link
                            :href="route('contact')"
                            class="px-3 py-2 text-gray-800 hover:text-primary font-medium transition-colors"
                        >
                            Contact
                        </Link>
                        <Link
                            :href="route('appointment.create')"
                            class="ml-3 px-6 py-2 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors"
                        >
                            prendre rendez-vous
                        </Link>
                    </nav>

                    <!-- Mobile menu button -->
                    <div class="flex md:hidden items-center">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-primary hover:bg-primary-bg-light focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-light"
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
            <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="md:hidden">
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
                        class="block px-4 py-2 text-base font-medium text-primary border-l-4 border-primary bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        A Propos
                    </Link>

                    <!-- Mobile Services dropdown -->
                    <div>
                        <button
                            @click="toggleDropdown('services')"
                            class="w-full flex justify-between items-center px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        >
                            <span>Nos Services</span>
                            <svg
                                :class="{'transform rotate-180': dropdownStates.services}"
                                class="h-5 w-5 transition-transform duration-200"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div v-show="dropdownStates.services" class="pl-4 pr-2 py-2 space-y-1 bg-gray-50">
                            <Link
                                :href="route('services')"
                                class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-primary-bg-light rounded-md"
                                @click="closeMobileMenu"
                            >
                                Tous les services
                            </Link>
                        </div>
                    </div>

                    <Link
                        :href="route('products')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Produits
                    </Link>

                    <Link
                        :href="route('portfolio')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Portfolio
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
                        :href="route('appointment.create')"
                        class="block mx-4 mt-3 px-4 py-2 bg-primary text-center text-white rounded-full hover:bg-primary-dark transition-colors"
                        @click="closeMobileMenu"
                    >
                        prendre rendez-vous
                    </Link>
                </div>
            </div>
        </header>

        <!-- Contenu principal (slot pour les pages) -->
        <main>
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Sophie Weddings Dreams</h3>
                        <p class="text-gray-300 mb-4">
                            Wedding Planner & Wedding Designer Depuis 2016
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
                            <a :href="contactSettings.social_tiktok" target="_blank" class="text-gray-300 hover:text-primary transition-colors">
                                <i class="bi bi-tiktok text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liens Rapides</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link :href="route('home')" class="text-gray-300 hover:text-primary transition-colors">
                                    Accueil
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('about')" class="text-gray-300 hover:text-primary transition-colors">
                                    À Propos
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('services')" class="text-gray-300 hover:text-primary transition-colors">
                                    Nos Services
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('products')" class="text-gray-300 hover:text-primary transition-colors">
                                    Nos Produits
                                </Link>php
                            </li>
                            <li>
                                <Link :href="route('portfolio')" class="text-gray-300 hover:text-primary transition-colors">
                                    Portfolio
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
                        <h3 class="text-lg font-semibold mb-4">Nos Services</h3>
                        <ul class="space-y-2">
                            <li v-for="service in services" :key="service.id">
                                <Link :href="'#'" class="text-gray-300 hover:text-primary transition-colors">
                                    {{ service.title }}
                                </Link>
                            </li>
                            <!-- Fallback if no services are loaded -->
                            <li v-if="services.length === 0">
                                <span class="text-gray-400">Chargement des services...</span>
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

                <div class="mt-12 pt-8 border-t border-gray-700">
                    <p class="text-gray-400 text-center">
                        &copy; 2025 Sophie Weddings Dreams. Tous droits réservés.
                    </p>
                </div>
            </div>
        </footer>
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
</style>

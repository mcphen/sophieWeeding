<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount, reactive } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import Temoignages from '@/components/front/Temoignages.vue';
import Partners from '@/components/front/Partners.vue';
import BlogPosts from '@/components/front/BlogPosts.vue';
import CtaSection from '@/components/front/CtaSection.vue';
import GalleryPreview from '@/components/front/GalleryPreview.vue';
import ServiceSection from '@/components/front/ServiceSection.vue';
import MasterclassSection from '@/components/front/MasterclassSection.vue';
import axios from 'axios';

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

// Use default image if no banner photos are available
const defaultBannerImage = '/images/banner/banner.jpg';

// Process banner photos to ensure we have the image URL
const bannerPhotos = computed(() => {
    return props.bannerPhotos.map(photo => {
        // Ensure image_path is properly formatted
        let imagePath = photo.image_path;

        // If image_path already starts with a slash, don't add another one
        if (imagePath && !imagePath.startsWith('/')) {
            imagePath = `/${imagePath}`;
        }

        return {
            ...photo,
            // If image_url is provided, use it; otherwise construct from image_path
            image_url: photo.image_url || (imagePath ? `/storage${imagePath}` : defaultBannerImage)
        };
    });
});

// Slideshow functionality
const currentImageIndex = ref(0);
const slideInterval = ref<number | null>(null);

// Get current image for slideshow
const currentBannerImage = computed(() => {
    if (bannerPhotos.value.length === 0) return defaultBannerImage;
    const imageUrl = bannerPhotos.value[currentImageIndex.value].image_url;
    console.log('Loading image:', imageUrl); // Debug image path
    return imageUrl;
});

// Track image loading status
const imageLoaded = ref(false);
const handleImageLoad = () => {
    console.log('Image loaded successfully');
    imageLoaded.value = true;
};

// Function to advance to the next image
const nextImage = () => {
    if (bannerPhotos.value.length <= 1) return;
    // Reset image loaded state when changing images
    imageLoaded.value = false;
    currentImageIndex.value = (currentImageIndex.value + 1) % bannerPhotos.value.length;
};

// Handle image loading error
const handleImageError = () => {
    console.error('Failed to load image:', currentBannerImage.value);
    // If the image fails to load, we'll still show the content by setting imageLoaded to true
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

// Define ContactSettings interface
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

// Get contact settings from page props
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

// Get CTA settings from page props
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

// Contact form data
const contactForm = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Form validation state
const errors = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Form submission state
const isSubmitting = ref(false);
const isSuccess = ref(false);

// Handle form submission
const submitForm = async () => {
    // Reset errors and set submitting state
    isSubmitting.value = true;
    Object.keys(errors).forEach(key => {
        errors[key] = '';
    });

    try {
        // Send form data to server
        await axios.post('/contact', contactForm);

        // Handle success
        isSuccess.value = true;

        // Reset form ONLY after successful submission
        Object.keys(contactForm).forEach(key => {
            contactForm[key] = '';
        });

        // Reset success message after 5 seconds
        setTimeout(() => {
            isSuccess.value = false;
        }, 5000);
    } catch (error) {
        // Handle validation errors
        if (error.response && error.response.status === 422) {
            const validationErrors = error.response.data.errors;

            // Set error messages
            Object.keys(validationErrors).forEach(key => {
                errors[key] = validationErrors[key][0];
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};



</script>

<template>
    <Head title="Sophie Weddings Dreams"  />


    <LayoutFront>
        <!-- Hero Section - Slideshow with Text Overlay -->
        <div class="md:hidden">
            <!-- Mobile version with text outside image -->
            <section class="relative h-[70vh] overflow-hidden">
                <!-- Slideshow Background -->
                <div class="w-full h-full">
                    <!-- Slideshow Image -->
                    <img
                        :src="currentBannerImage"
                        :alt="bannerPhotos[currentImageIndex]?.caption || 'Mariage élégant'"
                        class="w-full h-full object-cover transition-opacity duration-1000"
                        @load="handleImageLoad"
                        @error="handleImageError"
                    />

                    <!-- Overlay for better text readability - reduced opacity -->
                    <div class="absolute inset-0" :class="{'bg-opacity-30': imageLoaded, 'bg-opacity-0': !imageLoaded}"></div>

                    <!-- Slideshow Navigation Indicators -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-20">
                        <button
                            v-for="(photo, index) in bannerPhotos"
                            :key="index"
                            @click="currentImageIndex = index"
                            class="w-3 h-3 rounded-full transition-colors"
                            :class="index === currentImageIndex ? 'bg-white' : 'bg-white/50 hover:bg-white/70'"
                            :aria-label="`View image ${index + 1}`"
                        ></button>
                    </div>
                </div>
            </section>

            <!-- Mobile text content outside the image -->
            <div class="w-full p-5 bg-gray-900/90 backdrop-blur-sm">
                <div class="max-w-3xl">
                    <h1 class="text-2xl font-serif font-bold tracking-tight text-white">
                        <span class="block">la passion de l'élégance</span>
                        <span class="block text-[#d1922f]">& l'exigence du raffinement</span>
                    </h1>
                    <p class="mt-6 text-lg text-white/90 max-w-3xl">
                        Wedding Planner & Wedding Designer Depuis 2016
                    </p>
                    <div class="mt-10 flex flex-wrap gap-4">
                        <Link
                            :href="route('appointment.create')"
                            class="px-8 py-3 rounded-full bg-primary hover:bg-primary-dark text-white font-medium transition-colors"
                        >
                            prendre rendez-vous
                        </Link>
                        <Link
                            :href="'#'"
                            class="px-8 py-3 rounded-full border border-white text-white hover:bg-white/10 font-medium transition-colors"
                        >
                            Découvrir nos services
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop version with text overlay -->
        <section class="relative hidden md:block h-screen overflow-hidden">
            <!-- Slideshow Background -->
            <div class="absolute inset-0 w-full h-full">
                <!-- Slideshow Image -->
                <img
                    :src="currentBannerImage"
                    :alt="bannerPhotos[currentImageIndex]?.caption || 'Mariage élégant'"
                    class="w-full h-full object-cover transition-opacity duration-1000"
                    @load="handleImageLoad"
                    @error="handleImageError"
                />

                <!-- Overlay for better text readability - reduced opacity -->
                <div class="absolute inset-0" :class="{'bg-opacity-30': imageLoaded, 'bg-opacity-0': !imageLoaded}"></div>
            </div>

            <!-- Content Overlay -->
            <div class="relative z-10 h-full flex items-start pt-32">
                <div class="w-1/2 p-5 ml-12 px-8 bg-gray-900/70 backdrop-blur-sm">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl font-serif font-bold tracking-tight text-white">
                            <span class="block">la passion de l'élégance</span>
                            <span class="block text-[#d1922f]">& l'exigence du raffinement</span>
                        </h1>
                        <p class="mt-6 text-lg text-white/90 max-w-3xl">
                            Wedding Planner & Wedding Designer Depuis 2016
                        </p>
                        <div class="mt-10 flex flex-wrap gap-4">
                            <Link
                                :href="route('appointment.create')"
                                class="px-8 py-3 rounded-full bg-primary hover:bg-primary-dark text-white font-medium transition-colors"
                            >
                                prendre rendez-vous
                            </Link>
                            <Link
                                :href="'#'"
                                class="px-8 py-3 rounded-full border border-white text-white hover:bg-white/10 font-medium transition-colors"
                            >
                                Découvrir nos services
                            </Link>
                        </div>
                    </div>
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
        <!-- Services Preview -->
        <ServiceSection />

        <!-- Portfolio Section -->
        <GalleryPreview bg-color="bg-gray-50" />
        <!-- Testimonials -->
        <Temoignages
            :bg-color="'bg-white'"
            :class-names-title="'text-3xl font-serif font-bold text-gray-900'"
        />

        <!-- Partners Section - Only shown if partners exist -->
        <Partners
            v-show="showPartnersSection"
            ref="partnersRef"
            :bg-color="'bg-gray-50'"
            :class-names="'text-3xl font-serif font-bold text-gray-900'"
        />

        <!-- Masterclasses Section -->
        <MasterclassSection :sessions="upcomingSessions" />

        <!-- Blog Preview -->

        <BlogPosts />
        <!-- Contact Section -->
        <section class="py-20 bg-[#d1922f]/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h2 class="text-3xl font-serif font-bold text-gray-900">Contactez-nous</h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Vous avez des questions ou vous souhaitez planifier une consultation ? N'hésitez pas à nous contacter.
                        </p>

                        <div class="mt-8 space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_phone }}</p>
                                    <p>{{ contactSettings.contact_phone_fixed }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900">Heures d'ouverture</h3>
                            <div class="mt-3 space-y-2 text-gray-600">
                                <p>{{ contactSettings.opening_hours }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Envoyez-nous un message</h3>

                        <!-- Success message -->
                        <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            <p class="font-medium">Merci pour votre message !</p>
                            <p>Nous vous contacterons dans les plus brefs délais.</p>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        v-model="contactForm.first_name"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                        :class="{'border-red-500': errors.first_name}"
                                        required
                                    />
                                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        v-model="contactForm.last_name"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                        :class="{'border-red-500': errors.last_name}"
                                        required
                                    />
                                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="contactForm.email"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                        :class="{'border-red-500': errors.email}"
                                        required
                                    />
                                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        v-model="contactForm.phone"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                        :class="{'border-red-500': errors.phone}"
                                    />
                                    <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Sujet *</label>
                                <input
                                    type="text"
                                    id="subject"
                                    v-model="contactForm.subject"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                    :class="{'border-red-500': errors.subject}"
                                    required
                                />
                                <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                <textarea
                                    id="description"
                                    v-model="contactForm.description"
                                    rows="4"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                    :class="{'border-red-500': errors.description}"
                                    required
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                            </div>

                            <button
                                type="submit"
                                class="w-full px-6 py-3 bg-[#d1922f] text-white font-medium rounded-md hover:bg-[#b87e28] transition-colors"
                                :disabled="isSubmitting"
                            >
                                <span v-if="isSubmitting">Envoi en cours...</span>
                                <span v-else>Envoyer le message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
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

<template>
    <LayoutFront>
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <div>
                                <Link :href="route('home')" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">Accueil</span>
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                                <Link :href="route('trainings')" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">Formations</Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-sm font-medium text-gray-500">{{ trainingSession.title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <!-- Image -->
                            <div v-if="trainingSession.image_url" class="relative h-72 sm:h-96">
                                <img :src="trainingSession.image_url" :alt="trainingSession.title" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="h-72 sm:h-96 bg-[#d1922f]/20 flex items-center justify-center">
                                <svg class="h-24 w-24 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="p-6 sm:p-8">
                                <h1 class="text-3xl font-serif font-bold text-gray-900">
                                    {{ trainingSession.title }}
                                </h1>

                                <div class="mt-6 flex flex-wrap gap-4">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 text-[#d1922f] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ formatDate(trainingSession.start_date) }} - {{ formatTime(trainingSession.end_date) }}</span>
                                    </div>

                                    <div v-if="trainingSession.location" class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 text-[#d1922f] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ trainingSession.location }}</span>
                                    </div>

                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 text-[#d1922f] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ trainingSession.formatted_price }}</span>
                                    </div>

                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 text-[#d1922f] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>
                                            {{ trainingSession.max_participants > 0
                                                ? `${trainingSession.registration_count}/${trainingSession.max_participants} participants`
                                                : `${trainingSession.registration_count} participants` }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-8 prose prose-lg max-w-none">
                                    <h2 class="text-xl font-semibold text-gray-900">Description</h2>
                                    <p>{{ trainingSession.description }}</p>
                                </div>

                                <!-- Document -->
                                <div v-if="trainingSession.document_url" class="mt-8">
                                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Document</h2>
                                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                        <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <a :href="trainingSession.document_url" target="_blank" class="ml-3 text-blue-600 hover:text-blue-800 underline">
                                            Télécharger le programme de formation (PDF)
                                        </a>
                                    </div>
                                </div>

                                <div v-if="trainingSession.content" class="mt-8 prose prose-lg max-w-none">
                                    <h2 class="text-xl font-semibold text-gray-900">Contenu de la formation</h2>
                                    <div v-html="trainingSession.content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Sessions -->
                        <div v-if="relatedSessions.length > 0" class="mt-12">
                            <h2 class="text-2xl font-serif font-bold text-gray-900 mb-6">Autres formations à venir</h2>
                            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <div v-for="session in relatedSessions" :key="session.id" class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                                            {{ session.title }}
                                        </h3>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <svg class="h-4 w-4 text-[#d1922f] mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ formatDate(session.start_date) }}
                                        </div>
                                        <div class="mt-4">
                                            <Link
                                                :href="route('training.show', session.slug)"
                                                class="text-[#d1922f] hover:text-[#c08529] font-medium"
                                            >
                                                Voir les détails →
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">S'inscrire à cette formation</h2>

                            <div v-if="trainingSession.is_full" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            Cette formation est complète. Veuillez consulter nos autres formations disponibles.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="success" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700">
                                            Votre inscription a été enregistrée avec succès. Nous vous contacterons prochainement.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form v-else @submit.prevent="submitForm">
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="form.name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]"
                                            required
                                        />
                                        <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</div>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input
                                            type="email"
                                            id="email"
                                            v-model="form.email"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]"
                                            required
                                        />
                                        <div v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</div>
                                    </div>

                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            v-model="form.phone"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]"
                                        />
                                        <div v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</div>
                                    </div>

                                    <div>
                                        <label for="message" class="block text-sm font-medium text-gray-700">Message (optionnel)</label>
                                        <textarea
                                            id="message"
                                            v-model="form.message"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]"
                                        ></textarea>
                                        <div v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message }}</div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button
                                        type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d1922f]"
                                        :disabled="processing"
                                    >
                                        <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Je m'inscris
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

const props = defineProps({
    trainingSession: Object,
    relatedSessions: Array,
    contactSettings: Object,
    ctaSettings: Object,
});

const success = ref(false);
const processing = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
});

const errors = ref({
    name: '',
    email: '',
    phone: '',
    message: '',
});

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

// Format time for display
const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const submitForm = () => {
    processing.value = true;
    errors.value = {
        name: '',
        email: '',
        phone: '',
        message: '',
    };

    form.post(route('training.register', props.trainingSession.id), {
        onSuccess: () => {
            form.reset();
            success.value = true;
            processing.value = false;
        },
        onError: (formErrors) => {
            errors.value = formErrors;
            processing.value = false;
        },
    });
};
</script>

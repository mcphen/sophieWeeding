<template>
    <LayoutFront>
        <div class="bg-gray-50 py-16">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h1 class="mt-4 text-3xl font-serif font-bold text-gray-900">
                                Inscription confirmée
                            </h1>
                            <p class="mt-2 text-lg text-gray-600">
                                Merci pour votre inscription à notre formation.
                            </p>
                        </div>

                        <div class="mt-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Détails de votre inscription</h2>

                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Formation</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ trainingSession.title }}</p>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Date</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ formatDate(trainingSession.start_date) }}</p>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Lieu</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ trainingSession.location || 'À déterminer' }}</p>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Nom</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ registration.name }}</p>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ registration.email }}</p>
                                    </div>

                                    <div v-if="registration.phone">
                                        <h3 class="text-sm font-medium text-gray-500">Téléphone</h3>
                                        <p class="mt-1 text-base font-medium text-gray-900">{{ registration.phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Un email de confirmation a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-center">
                            <Link
                                :href="route('trainings')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d1922f]"
                            >
                                Voir toutes les formations
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

// Define props with TypeScript interface
interface Props {
    registration: {
        id: number;
        name: string;
        email: string;
        phone?: string;
        created_at: string;
    };
    trainingSession: {
        id: number;
        title: string;
        start_date: string;
        location?: string;
    };
    contactSettings: Record<string, any>;
    ctaSettings: Record<string, any>;
}

defineProps<Props>();

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

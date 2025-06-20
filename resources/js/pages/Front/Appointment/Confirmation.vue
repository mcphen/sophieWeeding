<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

// Types
interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    address: string | null;
    is_lead: boolean;
    full_name: string;
}

interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
    slots: number;
    description: string | null;
    is_active: boolean;
}

interface Service {
    id: number;
    title: string;
    description: string | null;
    image_path: string | null;
    image_url: string | null;
    min_price: number | null;
}

interface Appointment {
    id: number;
    client_id: number;
    schedule_id: number;
    subject: string;
    description: string | null;
    status: string;
    created_at: string;
    updated_at: string;
    client: Client;
    schedule: Schedule;
    services: Service[];
}

// Props
interface Props {
    appointment: Appointment;
    success?: string;
}

const props = defineProps<Props>();

// Access props to avoid linting error
const { appointment, success } = props;

// Format date
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Format time
const formatTime = (time: string) => {
    return time.substring(0, 5); // Format HH:MM
};
</script>

<template>
    <LayoutFront>
        <Head title="Confirmation de rendez-vous" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="success" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                            {{ success }}
                        </div>

                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Confirmation de rendez-vous</h1>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                            <div class="flex items-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <h2 class="text-lg font-medium text-gray-900">Votre rendez-vous a été confirmé</h2>
                            </div>
                            <p class="text-gray-600">
                                Merci {{ appointment.client.first_name }} pour votre réservation. Nous avons bien reçu votre demande de rendez-vous et nous vous attendons à la date et l'heure indiquées ci-dessous.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Appointment Details -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du rendez-vous</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Date</p>
                                        <p class="text-base text-gray-900 capitalize">{{ formatDate(appointment.schedule.date) }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Heure</p>
                                        <p class="text-base text-gray-900">{{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Objet</p>
                                        <p class="text-base text-gray-900">{{ appointment.subject }}</p>
                                    </div>
                                    <div v-if="appointment.description">
                                        <p class="text-sm font-medium text-gray-500">Description</p>
                                        <p class="text-base text-gray-900">{{ appointment.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Vos informations</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Nom complet</p>
                                        <p class="text-base text-gray-900">{{ appointment.client.full_name }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Email</p>
                                        <p class="text-base text-gray-900">{{ appointment.client.email }}</p>
                                    </div>
                                    <div v-if="appointment.client.phone" class="mb-4">
                                        <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                        <p class="text-base text-gray-900">{{ appointment.client.phone }}</p>
                                    </div>
                                    <div v-if="appointment.client.address">
                                        <p class="text-sm font-medium text-gray-500">Adresse</p>
                                        <p class="text-base text-gray-900">{{ appointment.client.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Services sélectionnés</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="service in appointment.services" :key="service.id" class="py-3 flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-base font-medium text-gray-900">{{ service.title }}</p>
                                            <p v-if="service.description" class="text-sm text-gray-500">{{ service.description }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="mt-8 flex justify-center">
                            <a
                                href="/"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

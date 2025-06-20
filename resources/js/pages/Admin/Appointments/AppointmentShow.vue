<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

// Type pour un rendez-vous
interface AppointmentService {
    id: number;
    title: string;
    description: string;
    price: number;
    pivot: {
        appointment_id: number;
        service_id: number;
    };
}

interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    address: string;
}

interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Appointment {
    id: number;
    client_id: number;
    schedule_id: number;
    subject: string;
    description: string | null;
    status: 'pending' | 'confirmed' | 'cancelled';
    confirmed_at: string | null;
    confirmed_by: number | null;
    created_at: string;
    updated_at: string;
    client: Client;
    schedule: Schedule;
    services: AppointmentService[];
    confirmedBy?: User;
}

// Props
interface Props {
    appointment: Appointment;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rendez-vous', href: route('admin.appointments.index') },
    { title: `Rendez-vous #${props.appointment.id}`, href: route('admin.appointments.show', props.appointment.id) }
];

// État pour les modals
const showConfirmModal = ref(false);
const showDeleteModal = ref(false);

// Fonctions
function confirmAppointment() {
    window.location.href = route('admin.appointments.confirm', props.appointment.id);
}

function deleteAppointment() {
    window.location.href = route('admin.appointments.destroy', props.appointment.id);
}

// Formatage de la date
function formatDate(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

// Formatage de l'heure
function formatTime(timeString: string): string {
    // Convertir le format HH:MM:SS en HH:MM
    return timeString.substring(0, 5);
}

// Formatage de la date et heure
function formatDateTime(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Obtenir la classe de couleur en fonction du statut
function getStatusClass(status: string): string {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirmed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Obtenir le texte du statut en français
function getStatusText(status: string): string {
    switch (status) {
        case 'pending':
            return 'En attente';
        case 'confirmed':
            return 'Confirmé';
        case 'cancelled':
            return 'Annulé';
        default:
            return status;
    }
}
</script>

<template>
    <Head :title="`Rendez-vous #${appointment.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-gray-800">Rendez-vous #{{ appointment.id }}</h2>
                        <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status)]">
                            {{ getStatusText(appointment.status) }}
                        </span>
                    </div>
                    <p class="text-gray-500 mt-1">
                        Créé le {{ formatDateTime(appointment.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.appointments.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour à la liste
                    </Link>
                    <button
                        v-if="appointment.status === 'pending'"
                        @click="showConfirmModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        Confirmer
                    </button>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Message flash -->
            <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ $page.props.flash?.success }}
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informations du client -->
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Informations du client</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Nom</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.client.first_name }} {{ appointment.client.last_name }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.client.email }}</p>
                        </div>
                        <div v-if="appointment.client.phone" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Téléphone</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.client.phone }}</p>
                        </div>
                        <div v-if="appointment.client.address" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Adresse</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.client.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations du rendez-vous -->
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Détails du rendez-vous</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Sujet</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.subject }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Date</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDate(appointment.schedule.date) }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Heure</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}</p>
                        </div>
                        <div v-if="appointment.description" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations de confirmation -->
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Statut et confirmation</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Statut actuel</h4>
                            <p class="mt-1">
                                <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status)]">
                                    {{ getStatusText(appointment.status) }}
                                </span>
                            </p>
                        </div>
                        <div v-if="appointment.status === 'confirmed'" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Date de confirmation</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(appointment.confirmed_at) }}</p>
                        </div>
                        <div v-if="appointment.status === 'confirmed' && appointment.confirmedBy" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Confirmé par</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ appointment.confirmedBy.name }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Dernière mise à jour</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(appointment.updated_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services demandés -->
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Services demandés</h3>
                </div>
                <div class="p-4">
                    <div v-if="appointment.services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="service in appointment.services" :key="service.id" class="border rounded-md p-4">
                            <h4 class="font-medium text-gray-900">{{ service.title }}</h4>
<!--                            <p class="text-sm text-gray-500 mt-1">{{ service.description }}</p>-->
<!--                            <p class="text-sm font-medium text-primary mt-2">{{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(service.price) }}</p>-->
                        </div>
                    </div>
                    <div v-else class="text-center py-6">
                        <p class="text-gray-500">Aucun service sélectionné pour ce rendez-vous.</p>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmation -->
            <div v-if="showConfirmModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer le rendez-vous</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir confirmer ce rendez-vous ? Un email de confirmation sera envoyé au client.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="showConfirmModal = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </button>
                        <button
                            @click="confirmAppointment"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de suppression -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer la suppression</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </button>
                        <button
                            @click="deleteAppointment"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

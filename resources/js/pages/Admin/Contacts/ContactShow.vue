<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

// Type pour un contact
interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    address: string;
}

interface Contact {
    id: number;
    client_id: number;
    subject: string;
    description: string;
    created_at: string;
    updated_at: string;
    client: Client;
}

// Props
interface Props {
    contact: Contact;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Contacts', href: route('admin.contacts.index') },
    { title: `Contact #${props.contact.id}`, href: route('admin.contacts.show', props.contact.id) }
];

// État pour les modals
const showDeleteModal = ref(false);

// Fonctions
function deleteContact() {
    window.location.href = route('admin.contacts.destroy', props.contact.id);
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
</script>

<template>
    <Head :title="`Contact #${contact.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-gray-800">Contact #{{ contact.id }}</h2>
                    </div>
                    <p class="text-gray-500 mt-1">
                        Reçu le {{ formatDateTime(contact.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.contacts.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour à la liste
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Message flash -->
            <div v-if="$page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ $page.props.flash.success }}
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations du client -->
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Informations du client</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Nom</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ contact.client.first_name }} {{ contact.client.last_name }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ contact.client.email }}</p>
                        </div>
                        <div v-if="contact.client.phone" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Téléphone</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ contact.client.phone }}</p>
                        </div>
                        <div v-if="contact.client.address" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Adresse</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ contact.client.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations du message -->
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Détails du message</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Sujet</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ contact.subject }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Date de réception</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(contact.created_at) }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Dernière mise à jour</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(contact.updated_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu du message -->
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Message</h3>
                </div>
                <div class="p-4">
                    <div class="prose prose-sm max-w-none">
                        <p>{{ contact.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Modal de suppression -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer la suppression</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer ce message de contact ? Cette action est irréversible.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </button>
                        <button
                            @click="deleteContact"
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

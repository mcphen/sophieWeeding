<template>
    <Head :title="'Formation: ' + trainingSession.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails de la formation</h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('admin.training-sessions.edit', trainingSession.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Modifier
                    </Link>
                    <Link
                        :href="route('admin.training-sessions.index')"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition"
                    >
                        Retour à la liste
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Training Session Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Left Column: Image and Basic Info -->
                            <div>
                                <div v-if="trainingSession.image_url" class="mb-4">
                                    <img :src="trainingSession.image_url" :alt="trainingSession.title" class="w-full h-auto rounded-lg" />
                                </div>
                                <div v-else class="mb-4 bg-gray-100 rounded-lg p-8 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <!-- Document -->
                                <div v-if="trainingSession.document_url" class="mb-4 bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-lg mb-2">Document</h3>
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <a :href="trainingSession.document_url" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                                            Télécharger le document PDF
                                        </a>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-lg mb-2">Informations</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Statut:</span>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="trainingSession.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            >
                                                {{ trainingSession.is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date de début:</span>
                                            <span>{{ trainingSession.start_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date de fin:</span>
                                            <span>{{ trainingSession.end_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Lieu:</span>
                                            <span>{{ trainingSession.location || 'Non défini' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Prix:</span>
                                            <span>{{ trainingSession.price }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Participants:</span>
                                            <span>
                                                {{ trainingSession.registration_count }}
                                                <span v-if="trainingSession.max_participants > 0">/ {{ trainingSession.max_participants }}</span>
                                                <span
                                                    v-if="trainingSession.is_full"
                                                    class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                                                >
                                                    Complet
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Content -->
                            <div class="md:col-span-2">
                                <h2 class="text-2xl font-bold mb-4">{{ trainingSession.title }}</h2>

                                <div class="mb-6">
                                    <h3 class="font-semibold text-lg mb-2">Description</h3>
                                    <p class="text-gray-700">{{ trainingSession.description || 'Aucune description' }}</p>
                                </div>

                                <div v-if="trainingSession.content">
                                    <h3 class="font-semibold text-lg mb-2">Contenu détaillé</h3>
                                    <div class="prose max-w-none" v-html="trainingSession.content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registrations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-semibold mb-4">Inscriptions ({{ registrations.length }})</h3>

                        <div v-if="registrations.length === 0" class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune inscription</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Aucune personne ne s'est encore inscrite à cette formation.
                            </p>
                        </div>

                        <div v-else>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nom
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Téléphone
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date d'inscription
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Statut
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="registration in registrations" :key="registration.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ registration.name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ registration.email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ registration.phone || 'Non renseigné' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ registration.created_at }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="registration.is_confirmed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                                >
                                                    {{ registration.is_confirmed ? 'Confirmé' : 'En attente' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button
                                                        v-if="!registration.is_confirmed"
                                                        @click="confirmRegistration(registration.id)"
                                                        class="text-green-600 hover:text-green-900"
                                                        :disabled="processing"
                                                    >
                                                        Confirmer
                                                    </button>
                                                    <button
                                                        @click="confirmDelete(registration.id)"
                                                        class="text-red-600 hover:text-red-900"
                                                        :disabled="processing"
                                                    >
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Êtes-vous sûr de vouloir supprimer cette inscription ?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Cette action est irréversible.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">Annuler</SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                        @click="deleteRegistration"
                    >
                        Supprimer
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';

interface Props {
    trainingSession: {
        id: number;
        title: string;
        description: string | null;
        content: string | null;
        image_url: string | null;
        document_url: string | null;
        start_date: string;
        end_date: string;
        location: string | null;
        max_participants: number;
        price: string;
        is_active: boolean;
        registration_count: number;
        available_spots: number;
        is_full: boolean;
    };
    registrations: Array<{
        id: number;
        name: string;
        email: string;
        phone: string | null;
        message: string | null;
        is_confirmed: boolean;
        confirmed_at: string | null;
        created_at: string;
    }>;
}

defineProps<Props>();

const showDeleteModal = ref(false);
const processing = ref(false);
const registrationIdToDelete = ref<number | null>(null);

const confirmDelete = (id: number) => {
    registrationIdToDelete.value = id;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    registrationIdToDelete.value = null;
};

const deleteRegistration = () => {
    if (!registrationIdToDelete.value) return;

    processing.value = true;

    router.delete(route('admin.training-registrations.destroy', registrationIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};

const confirmRegistration = (id: number) => {
    processing.value = true;

    router.post(route('admin.training-registrations.confirm', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};
</script>

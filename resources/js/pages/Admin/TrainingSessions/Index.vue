<template>
    <Head title="Gestion des formations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et boutons d'action -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des formations</h2>
                </div>
                <div>
                    <Link
                        :href="route('admin.training-sessions.create')"
                        class="px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#c08529] transition"
                    >
                        Ajouter une formation
                    </Link>
                </div>
            </div>

            <!-- Contenu -->
            <div v-if="trainingSessions.length === 0" class="text-center py-8">
                <h3 class="text-lg font-medium text-gray-900">Aucune formation trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Commencez par ajouter une nouvelle formation.
                </p>
                <div class="mt-6">
                    <Link
                        :href="route('admin.training-sessions.create')"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529]"
                    >
                        Ajouter une formation
                    </Link>
                </div>
            </div>

            <div v-else>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lieu
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participants
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
                            <tr v-for="session in trainingSessions" :key="session.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ session.title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ session.start_date }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ session.location || 'Non défini' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ session.price }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ session.registration_count }}
                                        <span v-if="session.max_participants > 0">/ {{ session.max_participants }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="session.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ session.is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <span
                                        v-if="session.is_full"
                                        class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                                    >
                                        Complet
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link
                                            :href="route('admin.training-sessions.show', session.id)"
                                            class="text-[#d1922f] hover:text-[#c08529]"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('admin.training-sessions.edit', session.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="confirmDelete(session)"
                                            class="text-red-600 hover:text-red-900"
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

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Êtes-vous sûr de vouloir supprimer cette formation ?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Cette action est irréversible. Toutes les inscriptions associées seront également supprimées.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">Annuler</SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                        @click="deleteTrainingSession"
                    >
                        Supprimer
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Formations', href: route('admin.training-sessions.index') }
];

defineProps<{
    trainingSessions: Array<{
        id: number;
        title: string;
        start_date: string;
        end_date: string;
        location: string | null;
        price: string;
        is_active: boolean;
        registration_count: number;
        max_participants: number;
        is_full: boolean;
    }>;
}>();

const showDeleteModal = ref(false);
const processing = ref(false);
const trainingSessionToDelete = ref(null);

const confirmDelete = (session) => {
    trainingSessionToDelete.value = session;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    trainingSessionToDelete.value = null;
};

const deleteTrainingSession = () => {
    if (!trainingSessionToDelete.value) return;

    processing.value = true;

    router.delete(route('admin.training-sessions.destroy', trainingSessionToDelete.value.id), {
        onSuccess: () => {
            closeModal();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};
</script>

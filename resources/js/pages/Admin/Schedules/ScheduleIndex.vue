<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Type pour un créneau de disponibilité
interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
    slots: number;
    description: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Schedule[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    schedules: Pagination;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux créneaux
const schedulesList = computed(() => props.schedules.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Créneaux de disponibilité', href: route('admin.schedules.index') }
];

// État de confirmation de suppression
const scheduleToDelete = ref<Schedule | null>(null);
const showDeleteModal = ref(false);

// État pour les modals d'ajout, de modification et de duplication
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDuplicateModal = ref(false);
const currentSchedule = ref<Schedule | null>(null);

// Formulaire pour ajouter un créneau
const addForm = useForm({
    date: '',
    start_time: '',
    end_time: '',
    slots: 1,
    description: '',
    is_active: true
});

// Formulaire pour modifier un créneau
const editForm = useForm({
    date: '',
    start_time: '',
    end_time: '',
    slots: 1,
    description: '',
    is_active: true,
    _method: 'PUT'
});

// Formulaire pour dupliquer un créneau
const duplicateForm = useForm({
    weeks: 1,
});

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('list');

// Fonctions
function confirmDelete(schedule: Schedule) {
    scheduleToDelete.value = schedule;
    showDeleteModal.value = true;
}

function cancelDelete() {
    scheduleToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteSchedule() {
    if (scheduleToDelete.value) {
        window.location.href = route('admin.schedules.destroy', scheduleToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Ouvrir la modal d'édition
function openEditModal(schedule: Schedule) {
    currentSchedule.value = schedule;
    editForm.date = schedule.date;
    editForm.start_time = schedule.start_time;
    editForm.end_time = schedule.end_time;
    editForm.slots = schedule.slots;
    editForm.description = schedule.description || '';
    editForm.is_active = schedule.is_active;
    showEditModal.value = true;
}

// Soumettre le formulaire d'ajout
function submitAddForm() {
    addForm.post(route('admin.schedules.store'), {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            showAddModal.value = false;
        },
    });
}

// Soumettre le formulaire de modification
function submitEditForm() {
    editForm.post(route('admin.schedules.update', currentSchedule.value?.id), {
        preserveScroll: true,
        onSuccess: () => {
            editForm.reset();
            showEditModal.value = false;
        },
    });
}

// Ouvrir la modal de duplication
function openDuplicateModal(schedule: Schedule) {
    currentSchedule.value = schedule;
    duplicateForm.weeks = 1;
    showDuplicateModal.value = true;
}

// Soumettre le formulaire de duplication
function submitDuplicateForm() {
    duplicateForm.post(route('admin.schedules.duplicate', currentSchedule.value?.id), {
        preserveScroll: true,
        onSuccess: () => {
            duplicateForm.reset();
            showDuplicateModal.value = false;
        },
    });
}

// Formatage de la date
function formatDate(dateString: string): string {
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
</script>

<template>
    <Head title="Créneaux de disponibilité" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Créneaux de disponibilité</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez vos créneaux de disponibilité pour les rendez-vous
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Sélecteur de vue -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Liste
                        </button>
                    </div>

                    <button
                        @click="showAddModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter un créneau
                    </button>
                </div>
            </div>

            <!-- Message de succès -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <!-- Affichage en grille (cards) -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="schedule in schedulesList" :key="schedule.id" class="bg-white border rounded-xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
                    <!-- En-tête avec date -->
                    <div class="bg-blue-50 p-4 border-b">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">{{ formatDate(schedule.date) }}</h3>
                            <span v-if="schedule.is_active" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Actif</span>
                            <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Inactif</span>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-4 flex-1">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 text-sm">Horaire:</span>
                                <span class="font-medium">{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 text-sm">Places disponibles:</span>
                                <span class="font-medium">{{ schedule.slots }}</span>
                            </div>
                            <div v-if="schedule.description" class="mt-3">
                                <p class="text-sm text-gray-600">{{ schedule.description }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-auto">
                            <button
                                @click="openEditModal(schedule)"
                                class="px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition flex-1 text-center text-sm"
                            >
                                Modifier
                            </button>
                            <button
                                @click="openDuplicateModal(schedule)"
                                class="px-3 py-2 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition flex-1 text-center text-sm"
                            >
                                Dupliquer
                            </button>
                            <button
                                @click="confirmDelete(schedule)"
                                class="px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition flex-1 text-center text-sm"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Message si aucun créneau trouvé -->
                <div v-if="schedulesList.length === 0" class="col-span-full bg-gray-50 rounded-lg p-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun créneau de disponibilité trouvé</h3>
                    <p class="text-gray-500 mb-4">Commencez par ajouter un nouveau créneau de disponibilité.</p>
                    <button
                        @click="showAddModal = true"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter un créneau
                    </button>
                </div>
            </div>

            <!-- Affichage en liste (table) -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Places</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="schedule in schedulesList" :key="schedule.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatDate(schedule.date) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ schedule.slots }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ schedule.description || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="schedule.is_active" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Actif
                                </span>
                                <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        @click="openEditModal(schedule)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Modifier
                                    </button>
                                    <button
                                        @click="openDuplicateModal(schedule)"
                                        class="px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                            <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                                        </svg>
                                        Dupliquer
                                    </button>
                                    <button
                                        @click="confirmDelete(schedule)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="schedulesList.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500">Aucun créneau de disponibilité trouvé. Commencez par en ajouter un !</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ props.schedules.from }}</span> à
                            <span class="font-medium">{{ props.schedules.to }}</span> sur
                            <span class="font-medium">{{ props.schedules.total }}</span> créneaux
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.schedules.links"
                                :key="i"
                                :href="link.url || '#'"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm',
                                    link.url === null
                                        ? 'text-gray-300 cursor-not-allowed'
                                        : 'text-gray-500 hover:bg-gray-50',
                                    link.active ? 'bg-blue-50 text-blue-600 font-medium border-blue-500 z-10' : 'border-gray-300',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === props.schedules.links.length - 1 ? 'rounded-r-md' : '',
                                    'border'
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </Link>
                        </nav>
                    </div>
                </div>

                <!-- Pagination mobile -->
                <div class="flex flex-1 justify-between items-center sm:hidden">
                    <Link
                        :href="props.schedules.links[0].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.schedules.links[0].url === null
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-white text-gray-700 hover:bg-gray-50',
                            'border border-gray-300'
                        ]"
                    >
                        Précédent
                    </Link>
                    <Link
                        :href="props.schedules.links[props.schedules.links.length - 1].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.schedules.links[props.schedules.links.length - 1].url === null
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-white text-gray-700 hover:bg-gray-50',
                            'border border-gray-300'
                        ]"
                    >
                        Suivant
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer ce créneau de disponibilité du {{ formatDate(scheduleToDelete?.date || '') }} ?
                <br>Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="cancelDelete"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex-1 max-w-xs"
                >
                    Annuler
                </button>
                <button
                    @click="deleteSchedule"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un créneau -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Ajouter un créneau de disponibilité</h3>
                <button @click="showAddModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitAddForm">
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                    <input type="date" id="date" v-model="addForm.date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="addForm.errors.date" class="text-red-500 text-xs mt-1">{{ addForm.errors.date }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Heure de début</label>
                        <input type="time" id="start_time" v-model="addForm.start_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <div v-if="addForm.errors.start_time" class="text-red-500 text-xs mt-1">{{ addForm.errors.start_time }}</div>
                    </div>
                    <div>
                        <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">Heure de fin</label>
                        <input type="time" id="end_time" v-model="addForm.end_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <div v-if="addForm.errors.end_time" class="text-red-500 text-xs mt-1">{{ addForm.errors.end_time }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="slots" class="block text-gray-700 text-sm font-bold mb-2">Nombre de places</label>
                    <input type="number" id="slots" v-model="addForm.slots" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="addForm.errors.slots" class="text-red-500 text-xs mt-1">{{ addForm.errors.slots }}</div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description (optionnel)</label>
                    <textarea id="description" v-model="addForm.description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    <div v-if="addForm.errors.description" class="text-red-500 text-xs mt-1">{{ addForm.errors.description }}</div>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="addForm.is_active" class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Actif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition" :disabled="addForm.processing">
                        {{ addForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pour modifier un créneau -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Modifier un créneau de disponibilité</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitEditForm">
                <div class="mb-4">
                    <label for="edit_date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                    <input type="date" id="edit_date" v-model="editForm.date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="editForm.errors.date" class="text-red-500 text-xs mt-1">{{ editForm.errors.date }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="edit_start_time" class="block text-gray-700 text-sm font-bold mb-2">Heure de début</label>
                        <input type="time" id="edit_start_time" v-model="editForm.start_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <div v-if="editForm.errors.start_time" class="text-red-500 text-xs mt-1">{{ editForm.errors.start_time }}</div>
                    </div>
                    <div>
                        <label for="edit_end_time" class="block text-gray-700 text-sm font-bold mb-2">Heure de fin</label>
                        <input type="time" id="edit_end_time" v-model="editForm.end_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <div v-if="editForm.errors.end_time" class="text-red-500 text-xs mt-1">{{ editForm.errors.end_time }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="edit_slots" class="block text-gray-700 text-sm font-bold mb-2">Nombre de places</label>
                    <input type="number" id="edit_slots" v-model="editForm.slots" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="editForm.errors.slots" class="text-red-500 text-xs mt-1">{{ editForm.errors.slots }}</div>
                </div>

                <div class="mb-4">
                    <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">Description (optionnel)</label>
                    <textarea id="edit_description" v-model="editForm.description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    <div v-if="editForm.errors.description" class="text-red-500 text-xs mt-1">{{ editForm.errors.description }}</div>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="editForm.is_active" class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Actif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition" :disabled="editForm.processing">
                        {{ editForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pour dupliquer un créneau -->
    <div v-if="showDuplicateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Dupliquer un créneau de disponibilité</h3>
                <button @click="showDuplicateModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <p class="text-gray-600">
                    Vous êtes sur le point de dupliquer le créneau du <strong>{{ formatDate(currentSchedule?.date || '') }}</strong>
                    de <strong>{{ formatTime(currentSchedule?.start_time || '') }}</strong> à <strong>{{ formatTime(currentSchedule?.end_time || '') }}</strong>.
                </p>
            </div>

            <form @submit.prevent="submitDuplicateForm">
                <div class="mb-6">
                    <label for="duplicate_weeks" class="block text-gray-700 text-sm font-bold mb-2">Nombre de semaines à dupliquer</label>
                    <input
                        type="number"
                        id="duplicate_weeks"
                        v-model="duplicateForm.weeks"
                        min="1"
                        max="52"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <div v-if="duplicateForm.errors.weeks" class="text-red-500 text-xs mt-1">{{ duplicateForm.errors.weeks }}</div>
                    <p class="text-sm text-gray-500 mt-2">
                        Ce créneau sera dupliqué pour les {{ duplicateForm.weeks }} prochaines semaines.
                    </p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showDuplicateModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition" :disabled="duplicateForm.processing">
                        {{ duplicateForm.processing ? 'Duplication...' : 'Dupliquer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

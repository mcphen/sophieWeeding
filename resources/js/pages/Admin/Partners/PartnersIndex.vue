```vue
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Type pour un partenaire
interface Partner {
    id: number;
    name: string;
    website_url: string | null;
    logo_path: string;
    created_at: string;
    updated_at: string;
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Partner[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    partners: Pagination;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux partenaires
const partnersList = computed(() => props.partners.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Partenaires', href: route('admin.partners.index') }
];

// État de confirmation de suppression
const partnerToDelete = ref<Partner | null>(null);
const showDeleteModal = ref(false);

// État pour les modals d'ajout et de modification
const showAddModal = ref(false);
const showEditModal = ref(false);
const currentPartner = ref<Partner | null>(null);

// Formulaire pour ajouter un partenaire
const addForm = useForm({
    name: '',
    website_url: '',
    logo: null as File | null
});

// Formulaire pour modifier un partenaire
const editForm = useForm({
    name: '',
    website_url: '',
    logo: null as File | null,
    _method: 'PUT'
});

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('grid');

// Fonctions
function confirmDelete(partner: Partner) {
    partnerToDelete.value = partner;
    showDeleteModal.value = true;
}

function cancelDelete() {
    partnerToDelete.value = null;
    showDeleteModal.value = false;
}

function deletePartner() {
    if (partnerToDelete.value) {
        window.location.href = route('admin.partners.destroy', partnerToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Ouvrir la modal d'édition
function openEditModal(partner: Partner) {
    currentPartner.value = partner;
    editForm.name = partner.name;
    editForm.website_url = partner.website_url || '';
    editForm.logo = null;
    showEditModal.value = true;
}

// Soumettre le formulaire d'ajout
function submitAddForm() {
    addForm.post(route('admin.partners.store'), {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            showAddModal.value = false;
        },
    });
}

// Soumettre le formulaire de modification
function submitEditForm() {
    editForm.post(route('admin.partners.update', currentPartner.value?.id), {
        preserveScroll: true,
        onSuccess: () => {
            editForm.reset();
            showEditModal.value = false;
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
</script>

<template>
    <Head title="Partenaires" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Liste des partenaires</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez vos partenaires et leurs logos
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
                        Ajouter un partenaire
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
                <div v-for="partner in partnersList" :key="partner.id" class="bg-white border rounded-xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
                    <!-- Logo -->
                    <div class="relative h-48 bg-gray-100 p-4 flex items-center justify-center">
                        <img
                            v-if="partner.logo_path"
                            :src="`/storage/${partner.logo_path}`"
                            :alt="partner.name"
                            class="max-w-full max-h-full object-contain"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ partner.name }}</h3>
                        <div v-if="partner.website_url" class="mb-4 flex-1">
                            <a :href="partner.website_url" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                </svg>
                                {{ partner.website_url }}
                            </a>
                        </div>
                        <div v-else class="mb-4 flex-1">
                            <span class="text-gray-400 italic text-sm">Aucun site web</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">
                            Ajouté le {{ formatDate(partner.created_at) }}
                        </p>
                        <div class="flex gap-2 mt-auto">
                            <button
                                @click="openEditModal(partner)"
                                class="px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition flex-1 text-center text-sm"
                            >
                                Modifier
                            </button>
                            <button
                                @click="confirmDelete(partner)"
                                class="px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition flex-1 text-center text-sm"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Message si aucun partenaire trouvé -->
                <div v-if="partnersList.length === 0" class="col-span-full bg-gray-50 rounded-lg p-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun partenaire trouvé</h3>
                    <p class="text-gray-500 mb-4">Commencez par ajouter un nouveau partenaire.</p>
                    <button
                        @click="showAddModal = true"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter un partenaire
                    </button>
                </div>
            </div>

            <!-- Affichage en liste (table) -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site web</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'ajout</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="partner in partnersList" :key="partner.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img
                                    v-if="partner.logo_path"
                                    :src="`/storage/${partner.logo_path}`"
                                    :alt="partner.name"
                                    class="w-20 h-16 object-contain"
                                >
                                <div v-else class="w-20 h-16 bg-gray-100 flex items-center justify-center rounded-md border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ partner.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a v-if="partner.website_url" :href="partner.website_url" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                    </svg>
                                    Visiter le site
                                </a>
                                <span v-else class="text-gray-400 italic text-sm">Non renseigné</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(partner.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        @click="openEditModal(partner)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Modifier
                                    </button>
                                    <button
                                        @click="confirmDelete(partner)"
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
                        <tr v-if="partnersList.length === 0">
                            <td colspan="5" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500">Aucun partenaire trouvé. Commencez par en ajouter un !</p>
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
                            Affichage de <span class="font-medium">{{ props.partners.from }}</span> à
                            <span class="font-medium">{{ props.partners.to }}</span> sur
                            <span class="font-medium">{{ props.partners.total }}</span> partenaires
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.partners.links"
                                :key="i"
                                :href="link.url || '#'"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm',
                                    link.url === null
                                        ? 'text-gray-300 cursor-not-allowed'
                                        : 'text-gray-500 hover:bg-gray-50',
                                    link.active ? 'bg-blue-50 text-blue-600 font-medium border-blue-500 z-10' : 'border-gray-300',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === props.partners.links.length - 1 ? 'rounded-r-md' : '',
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
                        :href="props.partners.links[0].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.partners.links[0].url === null
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-white text-gray-700 hover:bg-gray-50',
                            'border border-gray-300'
                        ]"
                    >
                        Précédent
                    </Link>
                    <Link
                        :href="props.partners.links[props.partners.links.length - 1].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.partners.links[props.partners.links.length - 1].url === null
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
                Êtes-vous sûr de vouloir supprimer le partenaire "<span class="font-medium">{{ partnerToDelete?.name }}</span>" ?
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
                    @click="deletePartner"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un partenaire -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Ajouter un partenaire</h3>
                <button @click="showAddModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitAddForm">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
                    <input type="text" id="name" v-model="addForm.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="addForm.errors.name" class="text-red-500 text-xs mt-1">{{ addForm.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label for="website_url" class="block text-gray-700 text-sm font-bold mb-2">Site web (optionnel)</label>
                    <input type="url" id="website_url" v-model="addForm.website_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div v-if="addForm.errors.website_url" class="text-red-500 text-xs mt-1">{{ addForm.errors.website_url }}</div>
                </div>

                <div class="mb-6">
                    <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                    <input type="file" id="logo" @input="addForm.logo = $event.target.files[0]" class="block w-full text-sm text-gray-700 border border-gray-300 rounded py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept="image/*" required>
                    <div v-if="addForm.errors.logo" class="text-red-500 text-xs mt-1">{{ addForm.errors.logo }}</div>
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF. Taille max: 2Mo</p>
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

    <!-- Modal pour modifier un partenaire -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Modifier un partenaire</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitEditForm">
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
                    <input type="text" id="edit_name" v-model="editForm.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <div v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label for="edit_website_url" class="block text-gray-700 text-sm font-bold mb-2">Site web (optionnel)</label>
                    <input type="url" id="edit_website_url" v-model="editForm.website_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div v-if="editForm.errors.website_url" class="text-red-500 text-xs mt-1">{{ editForm.errors.website_url }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Logo actuel</label>
                    <div v-if="currentPartner?.logo_path" class="mb-3 bg-gray-100 p-2 rounded flex justify-center">
                        <img :src="`/storage/${currentPartner.logo_path}`" :alt="currentPartner.name" class="h-20 object-contain">
                    </div>

                    <label for="edit_logo" class="block text-gray-700 text-sm font-bold mb-2">Nouveau logo (optionnel)</label>
                    <input type="file" id="edit_logo" @input="editForm.logo = $event.target.files[0]" class="block w-full text-sm text-gray-700 border border-gray-300 rounded py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept="image/*">
                    <div v-if="editForm.errors.logo" class="text-red-500 text-xs mt-1">{{ editForm.errors.logo }}</div>
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver le logo actuel. Formats acceptés: JPG, PNG, GIF. Taille max: 2Mo</p>
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
</template>

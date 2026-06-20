<template>
    <Head title="Listes de diffusion" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">

            <!-- En-tête -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Listes de diffusion</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Gérez vos listes d'emails réutilisables pour les annonces.</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] text-sm font-medium transition shadow-sm"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle liste
                </button>
            </div>

            <!-- Flash message -->
            <div v-if="$page.props.flash?.success"
                class="flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ $page.props.flash.success }}
            </div>

            <!-- Vide -->
            <div v-if="lists.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="mt-3 text-gray-500 text-sm">Aucune liste de diffusion. Créez votre première liste !</p>
                <button @click="openCreateModal" class="mt-3 text-sm text-[#d1922f] hover:underline font-medium">
                    Créer une liste →
                </button>
            </div>

            <!-- Listes -->
            <div v-else class="flex flex-col gap-4">
                <div v-for="list in lists" :key="list.id" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Header de la liste -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-[#d1922f]/10 flex items-center justify-center">
                                <svg class="h-4 w-4 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900">{{ list.name }}</h3>
                                <p v-if="list.description" class="text-xs text-gray-500 truncate">{{ list.description }}</p>
                            </div>
                            <span class="flex-shrink-0 ml-2 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#d1922f]/10 text-[#d1922f]">
                                {{ list.entries.length }} email{{ list.entries.length !== 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button
                                @click="toggleExpanded(list.id)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-600 text-xs font-medium hover:bg-gray-100 transition"
                            >
                                <svg class="h-3.5 w-3.5 transition-transform" :class="expandedLists.includes(list.id) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                {{ expandedLists.includes(list.id) ? 'Réduire' : 'Voir les emails' }}
                            </button>
                            <button
                                @click="openEditModal(list)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-medium hover:bg-indigo-100 transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Modifier
                            </button>
                            <button
                                @click="confirmDelete(list)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>

                    <!-- Section emails (expandable) -->
                    <div v-if="expandedLists.includes(list.id)" class="px-6 py-5 space-y-4">

                        <!-- Ajouter des emails -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Ajouter des emails à cette liste</p>
                            <textarea
                                v-model="addEmailsForms[list.id]"
                                rows="3"
                                placeholder="exemple@email.com, autre@email.com&#10;ou une adresse par ligne"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm font-mono"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1 mb-3">Virgule, point-virgule ou retour à la ligne comme séparateur.</p>
                            <button
                                @click="submitAddEmails(list)"
                                :disabled="!addEmailsForms[list.id]?.trim() || addingToList === list.id"
                                class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] disabled:opacity-50 text-sm font-medium transition"
                            >
                                <svg v-if="addingToList === list.id" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Ajouter
                            </button>
                        </div>

                        <!-- Liste des emails -->
                        <div v-if="list.entries.length > 0">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ list.entries.length }} email{{ list.entries.length !== 1 ? 's' : '' }} dans cette liste</p>
                            <div class="divide-y divide-gray-100 border border-gray-200 rounded-lg overflow-hidden">
                                <div
                                    v-for="entry in list.entries"
                                    :key="entry.id"
                                    class="flex items-center justify-between px-4 py-2.5 bg-white hover:bg-gray-50 transition"
                                >
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="h-3.5 w-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                        <span class="text-sm text-gray-800 font-mono truncate">{{ entry.email }}</span>
                                        <span v-if="entry.name" class="text-xs text-gray-400">({{ entry.name }})</span>
                                    </div>
                                    <button
                                        @click="removeEntry(list, entry)"
                                        class="flex-shrink-0 ml-3 text-gray-300 hover:text-red-500 transition"
                                        title="Retirer cet email"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-sm text-gray-400">
                            Cette liste est vide. Ajoutez des emails ci-dessus.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal : Créer / Modifier une liste -->
        <Modal :show="showListModal" @close="closeListModal" max-width="md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5 border-b pb-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ editingList ? 'Modifier la liste' : 'Nouvelle liste de diffusion' }}
                    </h2>
                    <button @click="closeListModal" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="submitList" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la liste *</label>
                        <input
                            v-model="listForm.name"
                            type="text"
                            required
                            placeholder="Ex: VIP clients, Partenaires, Équipe commerciale…"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm"
                        />
                        <p v-if="listForm.errors.name" class="text-red-600 text-xs mt-1">{{ listForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                            <span class="font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <input
                            v-model="listForm.description"
                            type="text"
                            placeholder="Ex: Clients ayant acheté une prestation premium"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm"
                        />
                    </div>
                    <div class="flex justify-end gap-3 pt-2 border-t">
                        <SecondaryButton type="button" @click="closeListModal">Annuler</SecondaryButton>
                        <button
                            type="submit"
                            :disabled="listForm.processing"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] disabled:opacity-50 font-medium text-sm transition"
                        >
                            <svg v-if="listForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ editingList ? 'Enregistrer' : 'Créer la liste' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal : Confirmer suppression -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Supprimer cette liste ?</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            La liste <strong>{{ listToDelete?.name }}</strong> et tous ses emails seront supprimés. Cette action est irréversible.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeDeleteModal">Annuler</SecondaryButton>
                    <DangerButton :disabled="deleteProcessing" @click="deleteList">
                        <svg v-if="deleteProcessing" class="animate-spin h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Supprimer
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import { type BreadcrumbItemType } from '@/types';

type Entry = { id: number; email: string; name: string | null };
type EmailList = {
    id: number;
    name: string;
    description: string | null;
    entries_count: number;
    entries: Entry[];
};

const props = defineProps<{ lists: EmailList[] }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Listes de diffusion', href: '#' },
];

// Expand / collapse
const expandedLists = ref<number[]>([]);
const toggleExpanded = (id: number) => {
    const idx = expandedLists.value.indexOf(id);
    if (idx === -1) expandedLists.value.push(id);
    else expandedLists.value.splice(idx, 1);
};

// Modal créer / modifier liste
const showListModal = ref(false);
const editingList = ref<EmailList | null>(null);
const listForm = useForm({ name: '', description: '' });

const openCreateModal = () => {
    editingList.value = null;
    listForm.reset();
    listForm.clearErrors();
    showListModal.value = true;
};

const openEditModal = (list: EmailList) => {
    editingList.value = list;
    listForm.name = list.name;
    listForm.description = list.description ?? '';
    listForm.clearErrors();
    showListModal.value = true;
};

const closeListModal = () => {
    showListModal.value = false;
    editingList.value = null;
    listForm.reset();
    listForm.clearErrors();
};

const submitList = () => {
    if (editingList.value) {
        listForm.put(route('admin.email-lists.update', editingList.value.id), {
            preserveScroll: true,
            onSuccess: () => closeListModal(),
        });
    } else {
        listForm.post(route('admin.email-lists.store'), {
            preserveScroll: true,
            onSuccess: () => closeListModal(),
        });
    }
};

// Ajouter des emails à une liste
const addEmailsForms = reactive<Record<number, string>>({});
const addingToList = ref<number | null>(null);

const submitAddEmails = (list: EmailList) => {
    const raw = addEmailsForms[list.id] ?? '';
    if (!raw.trim()) return;
    addingToList.value = list.id;
    router.post(route('admin.email-lists.entries.store', list.id), { emails_raw: raw }, {
        preserveScroll: true,
        onFinish: () => {
            addingToList.value = null;
            addEmailsForms[list.id] = '';
        },
    });
};

// Retirer un email
const removeEntry = (list: EmailList, entry: Entry) => {
    router.delete(route('admin.email-lists.entries.destroy', { emailList: list.id, entry: entry.id }), {
        preserveScroll: true,
    });
};

// Modal suppression liste
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);
const listToDelete = ref<EmailList | null>(null);

const confirmDelete = (list: EmailList) => {
    listToDelete.value = list;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    listToDelete.value = null;
};

const deleteList = () => {
    if (!listToDelete.value) return;
    deleteProcessing.value = true;
    router.delete(route('admin.email-lists.destroy', listToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => { deleteProcessing.value = false; closeDeleteModal(); },
    });
};
</script>

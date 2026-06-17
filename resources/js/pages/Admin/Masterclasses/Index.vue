<template>
    <Head title="Gestion des masterclasses" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">Masterclasses</h2>
                <div class="flex items-center gap-3">
                    <!-- Toggle vue -->
                    <div class="flex items-center rounded-lg border border-gray-200 overflow-hidden">
                        <button
                            @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-[#d1922f] text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
                            class="p-2 transition"
                            title="Vue grille"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button
                            @click="view = 'table'"
                            :class="view === 'table' ? 'bg-[#d1922f] text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
                            class="p-2 transition"
                            title="Vue tableau"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                            </svg>
                        </button>
                    </div>
                    <Link
                        :href="route('admin.masterclasses.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] transition text-sm font-medium shadow-sm"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouvelle masterclass
                    </Link>
                </div>
            </div>

            <!-- État vide -->
            <div v-if="masterclasses.length === 0" class="text-center py-16">
                <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-4 text-base font-semibold text-gray-800">Aucune masterclass</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première masterclass.</p>
                <div class="mt-6">
                    <Link :href="route('admin.masterclasses.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529] shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Créer une masterclass
                    </Link>
                </div>
            </div>

            <!-- Vue grille -->
            <div v-else-if="view === 'grid'" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="mc in masterclasses"
                    :key="mc.id"
                    class="border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow bg-white"
                >
                    <!-- Image -->
                    <div class="relative h-40 bg-[#d1922f]/10">
                        <img v-if="mc.image_url" :src="mc.image_url" :alt="mc.title" class="h-full w-full object-cover" />
                        <div v-else class="h-full w-full flex items-center justify-center">
                            <svg class="h-12 w-12 text-[#d1922f]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="absolute top-2 right-2 px-2.5 py-0.5 text-xs font-semibold rounded-full"
                            :class="mc.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                            {{ mc.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <!-- Body -->
                    <div class="p-4">
                        <span class="inline-block text-xs font-semibold text-[#d1922f] bg-[#d1922f]/10 px-2.5 py-0.5 rounded-full mb-2">{{ mc.niveau }}</span>
                        <h3 class="font-semibold text-gray-900 line-clamp-1 text-base">{{ mc.title }}</h3>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ mc.sessions_count }} session(s) ·
                            <span class="text-[#d1922f] font-medium">{{ mc.upcoming_sessions_count }} à venir</span>
                        </p>
                        <!-- Actions -->
                        <div class="mt-4 flex items-center gap-2">
                            <Link
                                :href="route('admin.masterclasses.show', mc.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#d1922f] text-white text-xs font-medium hover:bg-[#c08529] transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Gérer
                            </Link>
                            <Link
                                :href="route('admin.masterclasses.edit', mc.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-medium hover:bg-indigo-100 transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Modifier
                            </Link>
                            <button
                                @click="confirmDelete(mc)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue tableau -->
            <div v-else class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Masterclass</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sessions</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">À venir</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        <tr v-for="mc in masterclasses" :key="mc.id" class="hover:bg-gray-50 transition">
                            <!-- Titre + image -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-14 rounded-lg overflow-hidden bg-[#d1922f]/10 flex-shrink-0">
                                        <img v-if="mc.image_url" :src="mc.image_url" :alt="mc.title" class="h-full w-full object-cover" />
                                        <div v-else class="h-full w-full flex items-center justify-center">
                                            <svg class="h-5 w-5 text-[#d1922f]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ mc.title }}</span>
                                </div>
                            </td>
                            <!-- Niveau -->
                            <td class="px-4 py-3">
                                <span class="inline-block text-xs font-semibold text-[#d1922f] bg-[#d1922f]/10 px-2.5 py-0.5 rounded-full">{{ mc.niveau }}</span>
                            </td>
                            <!-- Statut -->
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                    :class="mc.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    {{ mc.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <!-- Sessions -->
                            <td class="px-4 py-3 text-center text-gray-600 font-medium">{{ mc.sessions_count }}</td>
                            <!-- À venir -->
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-[#d1922f]">{{ mc.upcoming_sessions_count }}</span>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('admin.masterclasses.show', mc.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#d1922f] text-white text-xs font-medium hover:bg-[#c08529] transition"
                                        title="Gérer"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Gérer
                                    </Link>
                                    <Link
                                        :href="route('admin.masterclasses.edit', mc.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-medium hover:bg-indigo-100 transition"
                                        title="Modifier"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Modifier
                                    </Link>
                                    <button
                                        @click="confirmDelete(mc)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition"
                                        title="Supprimer"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal suppression -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Supprimer cette masterclass ?</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Toutes ses sessions et inscriptions seront définitivement supprimées. Cette action est irréversible.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Annuler</SecondaryButton>
                    <DangerButton :disabled="processing" @click="deleteMasterclass">
                        <svg v-if="processing" class="animate-spin h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Supprimer définitivement
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
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import { type BreadcrumbItemType } from '@/types';

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
];

defineProps<{
    masterclasses: Array<{
        id: number;
        title: string;
        niveau: string;
        image_url: string | null;
        is_active: boolean;
        sessions_count: number;
        upcoming_sessions_count: number;
        slug: string;
    }>;
}>();

const view = ref<'grid' | 'table'>('grid');
const showDeleteModal = ref(false);
const processing = ref(false);
const toDelete = ref<{ id: number } | null>(null);

const confirmDelete = (mc: { id: number }) => {
    toDelete.value = mc;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    toDelete.value = null;
};

const deleteMasterclass = () => {
    if (!toDelete.value) return;
    processing.value = true;
    router.delete(route('admin.masterclasses.destroy', toDelete.value.id), {
        onFinish: () => { processing.value = false; closeModal(); },
    });
};
</script>

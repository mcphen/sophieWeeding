<template>
    <Head title="Inscriptions à la session" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Résumé session -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-wrap gap-4 items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">{{ masterclass.title }}</p>
                    <h2 class="text-xl font-bold text-gray-800 mt-0.5">Inscriptions – {{ session.start_date }}</h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-3 flex-wrap">
                        <span>{{ session.location_label }} · {{ session.registration_count }} inscrit(s)<span v-if="session.max_participants"> sur {{ session.max_participants }}</span></span>
                        <span v-if="waitlist_count > 0" class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ waitlist_count }} en attente
                        </span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <button
                        @click="showAddModal = true"
                        class="px-3 py-1.5 text-sm bg-[#d1922f] text-white rounded-md hover:bg-[#b87e28] flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Ajouter un inscrit
                    </button>
                    <a
                        :href="route('admin.masterclass-sessions.export', { masterclass: masterclass.id, session: session.id })"
                        class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Exporter Excel
                    </a>
                    <button
                        v-if="unconfirmedCount > 0"
                        @click="showConfirmAllModal = true"
                        class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Confirmer tous ({{ unconfirmedCount }})
                    </button>
                    <button
                        v-if="session.registration_count > 0"
                        @click="confirmReminder"
                        class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Envoyer rappel
                    </button>
                    <Link :href="route('admin.masterclasses.show', masterclass.id)" class="text-sm text-[#d1922f] hover:underline">
                        ← Retour
                    </Link>
                </div>
            </div>

            <!-- Liste des inscriptions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div v-if="registrations.length === 0" class="text-center py-8 text-gray-500">
                    Aucune inscription pour cette session.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="r in registrations" :key="r.id">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ r.name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ r.email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ r.phone || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ r.created_at }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium"
                                        :class="r.is_confirmed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-700'">
                                        {{ r.is_confirmed ? 'Confirmé' : 'En attente' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 items-center flex-wrap">
                                        <button v-if="!r.is_confirmed" @click="confirm(r.id)" class="text-green-600 hover:underline text-xs">Confirmer</button>
                                        <a
                                            v-if="r.is_confirmed"
                                            :href="route('admin.training-registrations.attestation', r.id)"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-[#aa6808] hover:underline text-xs"
                                            title="Télécharger l'attestation"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            Attestation
                                        </a>
                                        <button @click="confirmDelete(r)" class="text-red-600 hover:underline text-xs">Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Supprimer cette inscription ?</h2>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeModal">Annuler</SecondaryButton>
                    <DangerButton :disabled="processing" @click="deleteRegistration">Supprimer</DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Modal ajout manuel -->
        <Modal :show="showAddModal" @close="closeAddModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Ajouter un inscrit</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                        <input v-model="addForm.name" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]" />
                        <p v-if="addErrors.name" class="mt-1 text-xs text-red-600">{{ addErrors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input v-model="addForm.email" type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]" />
                        <p v-if="addErrors.email" class="mt-1 text-xs text-red-600">{{ addErrors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input v-model="addForm.phone" type="tel" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message / Note</label>
                        <textarea v-model="addForm.message" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]"></textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input v-model="addForm.send_confirmation" type="checkbox" class="rounded border-gray-300 text-[#d1922f]" />
                        Envoyer un email de confirmation au prospect
                    </label>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="closeAddModal">Annuler</SecondaryButton>
                    <button
                        :disabled="addProcessing"
                        @click="submitAdd"
                        class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50"
                    >
                        {{ addProcessing ? 'Ajout…' : 'Ajouter' }}
                    </button>
                </div>
            </div>
        </Modal>

        <Modal :show="showConfirmAllModal" @close="showConfirmAllModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Confirmer toutes les participations ?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Les <strong>{{ unconfirmedCount }}</strong> inscription(s) en attente seront confirmées et chaque participant recevra un email lui indiquant que son attestation est disponible.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showConfirmAllModal = false">Annuler</SecondaryButton>
                    <button
                        :disabled="confirmAllProcessing"
                        @click="confirmAll"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-50"
                    >
                        {{ confirmAllProcessing ? 'Confirmation…' : 'Confirmer et notifier' }}
                    </button>
                </div>
            </div>
        </Modal>

        <Modal :show="showReminderModal" @close="showReminderModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Envoyer un email de rappel ?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Un email de rappel sera envoyé à tous les <strong>{{ session.registration_count }}</strong> inscrit(s) de cette session.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showReminderModal = false">Annuler</SecondaryButton>
                    <button
                        :disabled="reminderProcessing"
                        @click="sendReminder"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ reminderProcessing ? 'Envoi…' : 'Envoyer' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import { type BreadcrumbItemType } from '@/types';

const props = defineProps<{
    masterclass: { id: number; title: string };
    session: { id: number; start_date: string; location_label: string; registration_count: number; max_participants: number | null };
    registrations: Array<{ id: number; name: string; email: string; phone: string | null; message: string | null; is_confirmed: boolean; confirmed_at: string | null; created_at: string }>;
    waitlist_count: number;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
    { title: props.masterclass.title, href: route('admin.masterclasses.show', props.masterclass.id) },
    { title: 'Inscriptions', href: '#' },
];

const unconfirmedCount = computed(() => props.registrations.filter(r => !r.is_confirmed).length);

const showDeleteModal = ref(false);
const processing = ref(false);
const toDelete = ref<{ id: number } | null>(null);

const showConfirmAllModal = ref(false);
const confirmAllProcessing = ref(false);

const showReminderModal = ref(false);
const reminderProcessing = ref(false);

const showAddModal = ref(false);
const addProcessing = ref(false);
const addForm = ref({ name: '', email: '', phone: '', message: '', send_confirmation: true });
const addErrors = ref<{ name?: string; email?: string }>({});

const closeAddModal = () => {
    showAddModal.value = false;
    addForm.value = { name: '', email: '', phone: '', message: '', send_confirmation: true };
    addErrors.value = {};
};

const submitAdd = () => {
    addErrors.value = {};
    if (!addForm.value.name.trim()) { addErrors.value.name = 'Le nom est requis.'; return; }
    if (!addForm.value.email.trim()) { addErrors.value.email = "L'email est requis."; return; }

    addProcessing.value = true;
    router.post(route('admin.training-registrations.admin-store', {
        masterclass: props.masterclass.id,
        session: props.session.id,
    }), addForm.value, {
        onSuccess: () => closeAddModal(),
        onError: (errors) => { addErrors.value = errors; },
        onFinish: () => { addProcessing.value = false; },
    });
};

const confirm = (id: number) => {
    router.post(route('admin.training-registrations.confirm', id));
};

const confirmAll = () => {
    confirmAllProcessing.value = true;
    router.post(route('admin.training-registrations.confirm-all', {
        masterclass: props.masterclass.id,
        session: props.session.id,
    }), {}, {
        onFinish: () => {
            confirmAllProcessing.value = false;
            showConfirmAllModal.value = false;
        },
    });
};

const confirmDelete = (r: { id: number }) => {
    toDelete.value = r;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showDeleteModal.value = false;
    toDelete.value = null;
};

const deleteRegistration = () => {
    if (!toDelete.value) return;
    processing.value = true;
    router.delete(route('admin.training-registrations.destroy', toDelete.value.id), {
        onFinish: () => { processing.value = false; closeModal(); },
    });
};

const confirmReminder = () => {
    showReminderModal.value = true;
};

const sendReminder = () => {
    reminderProcessing.value = true;
    router.post(route('admin.masterclass-sessions.reminder', {
        masterclass: props.masterclass.id,
        session: props.session.id,
    }), {}, {
        onFinish: () => {
            reminderProcessing.value = false;
            showReminderModal.value = false;
        },
    });
};
</script>

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
                    <button @click="showAddModal = true" class="px-3 py-1.5 text-sm bg-[#d1922f] text-white rounded-md hover:bg-[#b87e28] flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Ajouter un inscrit
                    </button>
                    <a :href="route('admin.masterclass-sessions.export', { masterclass: masterclass.id, session: session.id })" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Exporter Excel
                    </a>
                    <button v-if="unconfirmedCount > 0" @click="showConfirmAllModal = true" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Confirmer tous ({{ unconfirmedCount }})
                    </button>
                    <button v-if="session.registration_count > 0" @click="showReminderModal = true" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Envoyer rappel
                    </button>
                    <Link :href="route('admin.masterclasses.show', masterclass.id)" class="text-sm text-[#d1922f] hover:underline">← Retour</Link>
                </div>
            </div>

            <!-- Barre sélection multiple -->
            <div v-if="selectedIds.length > 0" class="bg-[#fff8ed] border border-[#d1922f]/30 rounded-xl px-5 py-3 flex items-center justify-between gap-4">
                <span class="text-sm font-medium text-[#aa6808]">
                    {{ selectedIds.length }} inscrit(s) sélectionné(s)
                </span>
                <div class="flex gap-2">
                    <button @click="selectedIds = []" class="text-xs text-gray-500 hover:underline">Désélectionner</button>
                    <button
                        @click="showBulkModal = true"
                        class="px-3 py-1.5 text-sm bg-[#d1922f] text-white rounded-md hover:bg-[#b87e28] flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Envoyer les attestations ({{ selectedIds.length }})
                    </button>
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
                                <th class="px-3 py-3 w-8">
                                    <input
                                        type="checkbox"
                                        :checked="allConfirmedSelected"
                                        :indeterminate="someSelected"
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]"
                                        :title="confirmedRegistrations.length === 0 ? 'Aucun inscrit confirmé' : 'Sélectionner tous les confirmés'"
                                        :disabled="confirmedRegistrations.length === 0"
                                    />
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="r in registrations" :key="r.id" :class="selectedIds.includes(r.id) ? 'bg-amber-50' : ''">
                                <td class="px-3 py-3">
                                    <input
                                        v-if="r.is_confirmed"
                                        type="checkbox"
                                        :value="r.id"
                                        v-model="selectedIds"
                                        class="rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]"
                                    />
                                </td>
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
                                        <button
                                            v-if="r.is_confirmed"
                                            @click="openAttestationModal(r)"
                                            class="inline-flex items-center gap-1 text-[#aa6808] hover:underline text-xs"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Attestation
                                        </button>
                                        <button @click="confirmDelete(r)" class="text-red-600 hover:underline text-xs">Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Attestation individuelle -->
        <Modal :show="showAttestationModal" @close="closeAttestationModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-1">Attestation</h2>
                <p class="text-sm text-gray-500 mb-6">{{ attestationTarget?.name }}</p>

                <div class="flex flex-col gap-3">
                    <!-- Aperçu -->
                    <a
                        v-if="attestationTarget"
                        :href="route('admin.training-registrations.attestation', attestationTarget.id) + '?inline=1'"
                        target="_blank"
                        class="flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-800">Aperçu</div>
                            <div class="text-xs text-gray-500">Ouvrir le PDF dans un nouvel onglet</div>
                        </div>
                    </a>

                    <!-- Télécharger -->
                    <a
                        v-if="attestationTarget"
                        :href="route('admin.training-registrations.attestation', attestationTarget.id)"
                        class="flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                    >
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-800">Télécharger</div>
                            <div class="text-xs text-gray-500">Enregistrer le fichier PDF</div>
                        </div>
                    </a>

                    <!-- Envoyer par email -->
                    <button
                        v-if="attestationTarget"
                        @click="sendAttestation"
                        :disabled="notifyProcessing"
                        class="flex items-center gap-3 px-4 py-3 border border-[#d1922f]/30 rounded-lg hover:bg-[#fff8ed] transition disabled:opacity-50 text-left w-full"
                    >
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-[#fff0d6] flex items-center justify-center">
                            <svg v-if="!notifyProcessing" class="w-4 h-4 text-[#aa6808]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <svg v-else class="w-4 h-4 text-[#aa6808] animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-800">{{ notifyProcessing ? 'Envoi en cours…' : 'Envoyer par email' }}</div>
                            <div class="text-xs text-gray-500">{{ attestationTarget?.email }}</div>
                        </div>
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeAttestationModal">Fermer</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal envoi groupé -->
        <Modal :show="showBulkModal" @close="showBulkModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Envoyer les attestations par email</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Un email avec le certificat PDF en pièce jointe sera envoyé aux
                    <strong>{{ selectedIds.length }}</strong> inscrit(s) sélectionné(s).
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showBulkModal = false">Annuler</SecondaryButton>
                    <button
                        :disabled="bulkProcessing"
                        @click="sendBulk"
                        class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50"
                    >
                        {{ bulkProcessing ? 'Envoi…' : 'Confirmer et envoyer' }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal suppression -->
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
                    <button :disabled="addProcessing" @click="submitAdd" class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50">
                        {{ addProcessing ? 'Ajout…' : 'Ajouter' }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal confirmer tous -->
        <Modal :show="showConfirmAllModal" @close="showConfirmAllModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Confirmer toutes les participations ?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Les <strong>{{ unconfirmedCount }}</strong> inscription(s) en attente seront confirmées et chaque participant recevra un email.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showConfirmAllModal = false">Annuler</SecondaryButton>
                    <button :disabled="confirmAllProcessing" @click="confirmAll" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-50">
                        {{ confirmAllProcessing ? 'Confirmation…' : 'Confirmer et notifier' }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal rappel -->
        <Modal :show="showReminderModal" @close="showReminderModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Envoyer un email de rappel ?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Un email de rappel sera envoyé à tous les <strong>{{ session.registration_count }}</strong> inscrit(s).
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showReminderModal = false">Annuler</SecondaryButton>
                    <button :disabled="reminderProcessing" @click="sendReminder" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 disabled:opacity-50">
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

type Registration = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    message: string | null;
    is_confirmed: boolean;
    confirmed_at: string | null;
    created_at: string;
};

const props = defineProps<{
    masterclass: { id: number; title: string };
    session: { id: number; start_date: string; location_label: string; registration_count: number; max_participants: number | null };
    registrations: Registration[];
    waitlist_count: number;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
    { title: props.masterclass.title, href: route('admin.masterclasses.show', props.masterclass.id) },
    { title: 'Inscriptions', href: '#' },
];

const unconfirmedCount = computed(() => props.registrations.filter(r => !r.is_confirmed).length);
const confirmedRegistrations = computed(() => props.registrations.filter(r => r.is_confirmed));

// ── Sélection multiple ──────────────────────────────────────────────
const selectedIds = ref<number[]>([]);

const allConfirmedSelected = computed(
    () => confirmedRegistrations.value.length > 0 && selectedIds.value.length === confirmedRegistrations.value.length
);
const someSelected = computed(
    () => selectedIds.value.length > 0 && selectedIds.value.length < confirmedRegistrations.value.length
);

const toggleSelectAll = () => {
    if (allConfirmedSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = confirmedRegistrations.value.map(r => r.id);
    }
};

// ── Modal attestation individuelle ──────────────────────────────────
const showAttestationModal = ref(false);
const attestationTarget = ref<Registration | null>(null);
const notifyProcessing = ref(false);

const openAttestationModal = (r: Registration) => {
    attestationTarget.value = r;
    showAttestationModal.value = true;
};

const closeAttestationModal = () => {
    showAttestationModal.value = false;
    attestationTarget.value = null;
    notifyProcessing.value = false;
};

const sendAttestation = () => {
    if (!attestationTarget.value) return;
    notifyProcessing.value = true;
    router.post(route('admin.training-registrations.notify-attestation', attestationTarget.value.id), {}, {
        onSuccess: () => closeAttestationModal(),
        onFinish: () => { notifyProcessing.value = false; },
    });
};

// ── Envoi groupé ────────────────────────────────────────────────────
const showBulkModal = ref(false);
const bulkProcessing = ref(false);

const sendBulk = () => {
    bulkProcessing.value = true;
    router.post(route('admin.training-registrations.bulk-notify-attestation'), { ids: selectedIds.value }, {
        onSuccess: () => { showBulkModal.value = false; selectedIds.value = []; },
        onFinish: () => { bulkProcessing.value = false; },
    });
};

// ── Suppression ─────────────────────────────────────────────────────
const showDeleteModal = ref(false);
const processing = ref(false);
const toDelete = ref<{ id: number } | null>(null);

const confirmDelete = (r: { id: number }) => { toDelete.value = r; showDeleteModal.value = true; };
const closeModal = () => { showDeleteModal.value = false; toDelete.value = null; };
const deleteRegistration = () => {
    if (!toDelete.value) return;
    processing.value = true;
    router.delete(route('admin.training-registrations.destroy', toDelete.value.id), {
        onFinish: () => { processing.value = false; closeModal(); },
    });
};

// ── Confirmation ────────────────────────────────────────────────────
const confirm = (id: number) => { router.post(route('admin.training-registrations.confirm', id)); };

const showConfirmAllModal = ref(false);
const confirmAllProcessing = ref(false);
const confirmAll = () => {
    confirmAllProcessing.value = true;
    router.post(route('admin.training-registrations.confirm-all', { masterclass: props.masterclass.id, session: props.session.id }), {}, {
        onFinish: () => { confirmAllProcessing.value = false; showConfirmAllModal.value = false; },
    });
};

// ── Rappel ──────────────────────────────────────────────────────────
const showReminderModal = ref(false);
const reminderProcessing = ref(false);
const sendReminder = () => {
    reminderProcessing.value = true;
    router.post(route('admin.masterclass-sessions.reminder', { masterclass: props.masterclass.id, session: props.session.id }), {}, {
        onFinish: () => { reminderProcessing.value = false; showReminderModal.value = false; },
    });
};

// ── Ajout manuel ────────────────────────────────────────────────────
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
    router.post(route('admin.training-registrations.admin-store', { masterclass: props.masterclass.id, session: props.session.id }), addForm.value, {
        onSuccess: () => closeAddModal(),
        onError: (errors) => { addErrors.value = errors; },
        onFinish: () => { addProcessing.value = false; },
    });
};
</script>

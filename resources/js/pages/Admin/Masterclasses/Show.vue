<template>
    <Head :title="masterclass.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">

            <!-- En-tête masterclass -->
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col sm:flex-row gap-6">
                <div v-if="masterclass.image_url" class="flex-shrink-0">
                    <img :src="masterclass.image_url" class="h-32 w-48 object-cover rounded-lg" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="inline-block text-xs font-semibold text-[#d1922f] bg-[#d1922f]/10 px-2 py-0.5 rounded mb-1">{{ masterclass.niveau }}</span>
                            <h2 class="text-2xl font-bold text-gray-800">{{ masterclass.title }}</h2>
                        </div>
                        <div class="flex gap-2 items-center flex-wrap">
                            <button
                                @click="showAnnounceModal = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm border border-[#d1922f] text-[#d1922f] rounded-lg hover:bg-[#d1922f]/10 transition"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Envoyer une annonce
                            </button>
                            <Link :href="route('admin.masterclasses.edit', masterclass.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm border border-indigo-300 text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Modifier
                            </Link>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                :class="masterclass.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                {{ masterclass.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <p v-if="masterclass.description" class="mt-2 text-gray-600 text-sm line-clamp-3">{{ masterclass.description }}</p>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <a v-if="masterclass.document_url" :href="masterclass.document_url" target="_blank"
                            class="text-sm text-blue-600 underline flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Programme PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Sessions ({{ sessions.length }})</h3>
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] text-sm font-medium transition shadow-sm"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter une session
                    </button>
                </div>

                <div v-if="sessions.length === 0" class="text-center py-10">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-3 text-sm text-gray-500">Aucune session pour cette masterclass.</p>
                    <button @click="openCreateModal" class="mt-3 text-sm text-[#d1922f] hover:underline font-medium">
                        Ajouter la première session →
                    </button>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Horaires</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Format</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prix</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Inscrits</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="s in sessions" :key="s.id" :class="s.is_past ? 'opacity-60' : ''" class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ extractDate(s.start_date) }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ extractTime(s.start_date) }}
                                    <span v-if="s.end_date" class="text-gray-400"> → {{ extractTime(s.end_date) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                        :class="{
                                            'bg-blue-100 text-blue-700': s.location_type === 'online',
                                            'bg-green-100 text-green-700': s.location_type === 'presentiel',
                                            'bg-purple-100 text-purple-700': s.location_type === 'both',
                                        }">
                                        {{ s.location_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ s.price }}</td>
                                <td class="px-4 py-3">
                                    <Link :href="route('admin.masterclass-sessions.registrations', { masterclass: masterclass.id, session: s.id })"
                                        class="inline-flex items-center gap-1 font-semibold text-[#d1922f] hover:underline">
                                        {{ s.registration_count }}<span v-if="s.max_participants" class="text-gray-400 font-normal"> / {{ s.max_participants }}</span>
                                    </Link>
                                    <span v-if="s.is_full" class="ml-1 text-xs text-red-600 font-semibold">Complet</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full font-semibold"
                                        :class="s.is_past ? 'bg-gray-100 text-gray-500' : (s.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')">
                                        {{ s.is_past ? 'Passée' : (s.is_active ? 'Active' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.masterclass-sessions.registrations', { masterclass: masterclass.id, session: s.id })"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#d1922f]/10 text-[#d1922f] text-xs font-medium hover:bg-[#d1922f]/20 transition"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Inscrits
                                        </Link>
                                        <button
                                            @click="openEditModal(s)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-medium hover:bg-indigo-100 transition"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Modifier
                                        </button>
                                        <button
                                            @click="confirmDeleteSession(s)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition"
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
        </div>

        <!-- Modal : Ajouter / Modifier une session -->
        <Modal :show="showSessionModal" @close="closeSessionModal" max-width="2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6 border-b pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ editingSession ? 'Modifier la session' : 'Nouvelle session' }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Masterclass : <span class="font-medium text-[#d1922f]">{{ masterclass.title }}</span>
                        </p>
                    </div>
                    <button @click="closeSessionModal" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitSession" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date et heure de début *</label>
                            <input v-model="sessionForm.start_date" type="datetime-local" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                            <p v-if="sessionForm.errors.start_date" class="text-red-600 text-xs mt-1">{{ sessionForm.errors.start_date }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                            <input v-model="sessionForm.end_date" type="datetime-local"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format *</label>
                        <div class="flex gap-5">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="radio" v-model="sessionForm.location_type" value="presentiel" class="text-[#d1922f]" /> Présentiel
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="radio" v-model="sessionForm.location_type" value="online" class="text-[#d1922f]" /> En ligne
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="radio" v-model="sessionForm.location_type" value="both" class="text-[#d1922f]" /> Les deux
                            </label>
                        </div>
                    </div>

                    <div v-if="sessionForm.location_type !== 'online'" class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input v-model="sessionForm.adresse" type="text" placeholder="Ex: 12 rue des Lilas, Paris 75001"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lien Google Maps</label>
                            <input v-model="sessionForm.google_maps_url" type="url" placeholder="https://maps.google.com/…"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                            <p v-if="sessionForm.errors.google_maps_url" class="text-red-600 text-xs mt-1">{{ sessionForm.errors.google_maps_url }}</p>
                        </div>
                    </div>

                    <div v-if="sessionForm.location_type !== 'presentiel'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lien de connexion (Zoom, Meet…)</label>
                        <input v-model="sessionForm.online_link" type="url" placeholder="https://zoom.us/…"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                        <p v-if="sessionForm.errors.online_link" class="text-red-600 text-xs mt-1">{{ sessionForm.errors.online_link }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix (€)</label>
                            <input v-model="sessionForm.price" type="number" min="0" step="0.01" placeholder="Laisser vide = gratuit"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places</label>
                            <input v-model="sessionForm.max_participants" type="number" min="1" placeholder="Laisser vide = illimité"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="session_is_active" type="checkbox" v-model="sessionForm.is_active"
                            class="rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]" />
                        <label for="session_is_active" class="text-sm text-gray-700">Session visible et ouverte aux inscriptions</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t">
                        <SecondaryButton type="button" @click="closeSessionModal">Annuler</SecondaryButton>
                        <button
                            type="submit"
                            :disabled="sessionForm.processing"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] disabled:opacity-50 font-medium text-sm transition"
                        >
                            <svg v-if="sessionForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ editingSession ? 'Enregistrer' : 'Créer la session' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal : Envoyer une annonce -->
        <Modal :show="showAnnounceModal" @close="closeAnnounceModal" max-width="lg">
            <div class="p-6">
                <div class="flex items-start gap-3 mb-5">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-[#d1922f]/10 flex items-center justify-center">
                        <svg class="h-4.5 w-4.5 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Envoyer une annonce</h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Annonce pour : <span class="font-medium text-[#d1922f]">{{ masterclass.title }}</span>
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitAnnounce" class="space-y-4">
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <input
                            id="include_past"
                            type="checkbox"
                            v-model="announceForm.include_past_participants"
                            class="mt-0.5 rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]"
                        />
                        <label for="include_past" class="text-sm cursor-pointer">
                            <span class="font-medium text-gray-800">Inclure tous les anciens participants</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Toutes les personnes inscrites à une masterclass (toutes sessions confondues, sans doublons)</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Adresses email supplémentaires
                            <span class="font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <textarea
                            v-model="announceForm.manual_emails"
                            rows="4"
                            placeholder="exemple@email.com, autre@email.com&#10;ou une adresse par ligne"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm font-mono"
                        ></textarea>
                        <p class="text-xs text-gray-400 mt-1">Virgule, point-virgule ou retour à la ligne comme séparateur.</p>
                    </div>

                    <p v-if="!announceForm.include_past_participants && !announceForm.manual_emails.trim()"
                        class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        Cochez l'option ci-dessus ou saisissez des emails pour envoyer l'annonce.
                    </p>

                    <div class="flex justify-end gap-3 pt-2 border-t">
                        <SecondaryButton type="button" @click="closeAnnounceModal">Annuler</SecondaryButton>
                        <button
                            type="submit"
                            :disabled="announceForm.processing || (!announceForm.include_past_participants && !announceForm.manual_emails.trim())"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-[#d1922f] text-white rounded-lg hover:bg-[#c08529] disabled:opacity-50 font-medium text-sm transition"
                        >
                            <svg v-if="announceForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ announceForm.processing ? 'Envoi en cours…' : 'Envoyer l\'annonce' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal : Supprimer une session -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Supprimer cette session ?</h2>
                        <p class="mt-1 text-sm text-gray-500">Toutes les inscriptions à cette session seront également supprimées. Cette action est irréversible.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeDeleteModal">Annuler</SecondaryButton>
                    <DangerButton :disabled="deleteProcessing" @click="deleteSession">
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
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import { type BreadcrumbItemType } from '@/types';

type Session = {
    id: number;
    start_date: string;
    end_date: string | null;
    start_date_form: string;
    end_date_form: string | null;
    location_type: string;
    location_label: string;
    adresse: string | null;
    google_maps_url: string | null;
    online_link: string | null;
    price: string;
    price_raw: number | null;
    max_participants: number | null;
    registration_count: number;
    available_spots: number | null;
    is_active: boolean;
    is_full: boolean;
    is_past: boolean;
};

const props = defineProps<{
    masterclass: {
        id: number;
        title: string;
        niveau: string;
        description: string | null;
        programme: string | null;
        image_url: string | null;
        document_url: string | null;
        is_active: boolean;
        slug: string;
    };
    sessions: Session[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
    { title: props.masterclass.title, href: '#' },
];

// --- Helpers affichage dates (format "dd/mm/yyyy HH:mm") ---
const extractDate = (dt: string) => dt ? dt.split(' ')[0] : '';
const extractTime = (dt: string) => dt ? dt.split(' ')[1] : '';

// --- Modal session (création + édition) ---
const showSessionModal = ref(false);
const editingSession = ref<Session | null>(null);

const sessionForm = useForm({
    start_date: '',
    end_date: '',
    location_type: 'presentiel',
    adresse: '',
    google_maps_url: '',
    online_link: '',
    price: '',
    max_participants: '',
    is_active: true,
    _method: 'POST' as 'POST' | 'PUT',
});

const openCreateModal = () => {
    editingSession.value = null;
    sessionForm.reset();
    sessionForm.clearErrors();
    sessionForm._method = 'POST';
    showSessionModal.value = true;
};

const openEditModal = (s: Session) => {
    editingSession.value = s;
    sessionForm.start_date = s.start_date_form;
    sessionForm.end_date = s.end_date_form ?? '';
    sessionForm.location_type = s.location_type;
    sessionForm.adresse = s.adresse ?? '';
    sessionForm.google_maps_url = s.google_maps_url ?? '';
    sessionForm.online_link = s.online_link ?? '';
    sessionForm.price = s.price_raw !== null ? String(s.price_raw) : '';
    sessionForm.max_participants = s.max_participants !== null ? String(s.max_participants) : '';
    sessionForm.is_active = s.is_active;
    sessionForm._method = 'PUT';
    sessionForm.clearErrors();
    showSessionModal.value = true;
};

const closeSessionModal = () => {
    showSessionModal.value = false;
    editingSession.value = null;
    sessionForm.reset();
    sessionForm.clearErrors();
};

const submitSession = () => {
    if (editingSession.value) {
        sessionForm.post(route('admin.masterclass-sessions.update', { masterclass: props.masterclass.id, session: editingSession.value.id }), {
            preserveScroll: true,
            onSuccess: () => closeSessionModal(),
        });
    } else {
        sessionForm.post(route('admin.masterclass-sessions.store', props.masterclass.id), {
            preserveScroll: true,
            onSuccess: () => closeSessionModal(),
        });
    }
};

// --- Modal annonce email ---
const showAnnounceModal = ref(false);
const announceForm = useForm({
    include_past_participants: true,
    manual_emails: '',
});

const closeAnnounceModal = () => {
    showAnnounceModal.value = false;
    announceForm.reset();
    announceForm.clearErrors();
};

const submitAnnounce = () => {
    announceForm.post(route('admin.masterclasses.announce', props.masterclass.id), {
        preserveScroll: true,
        onSuccess: () => closeAnnounceModal(),
    });
};

// --- Modal suppression ---
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);
const sessionToDelete = ref<{ id: number } | null>(null);

const confirmDeleteSession = (s: { id: number }) => {
    sessionToDelete.value = s;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    sessionToDelete.value = null;
};

const deleteSession = () => {
    if (!sessionToDelete.value) return;
    deleteProcessing.value = true;
    router.delete(route('admin.masterclass-sessions.destroy', { masterclass: props.masterclass.id, session: sessionToDelete.value.id }), {
        preserveScroll: true,
        onFinish: () => { deleteProcessing.value = false; closeDeleteModal(); },
    });
};
</script>

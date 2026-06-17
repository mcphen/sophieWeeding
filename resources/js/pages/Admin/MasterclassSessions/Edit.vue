<template>
    <Head title="Modifier la session" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm max-w-2xl">
            <div class="border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Modifier la session</h2>
                <p class="text-sm text-gray-500 mt-1">Masterclass : <span class="font-medium text-[#d1922f]">{{ masterclass.title }}</span></p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date et heure de début *</label>
                        <input v-model="form.start_date" type="datetime-local" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                        <p v-if="form.errors.start_date" class="text-red-600 text-sm mt-1">{{ form.errors.start_date }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                        <input v-model="form.end_date" type="datetime-local" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.location_type" value="presentiel" class="text-[#d1922f]" /> Présentiel
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.location_type" value="online" class="text-[#d1922f]" /> En ligne
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="form.location_type" value="both" class="text-[#d1922f]" /> Les deux
                        </label>
                    </div>
                </div>

                <div v-if="form.location_type !== 'online'" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input v-model="form.adresse" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lien Google Maps</label>
                        <input v-model="form.google_maps_url" type="url" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                        <p v-if="form.errors.google_maps_url" class="text-red-600 text-sm mt-1">{{ form.errors.google_maps_url }}</p>
                    </div>
                </div>

                <div v-if="form.location_type !== 'presentiel'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lien de connexion (Zoom, Meet…)</label>
                    <input v-model="form.online_link" type="url" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    <p v-if="form.errors.online_link" class="text-red-600 text-sm mt-1">{{ form.errors.online_link }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lien replay (après la session)</label>
                    <input v-model="form.replay_url" type="url" placeholder="https://youtube.com/watch?v=..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    <p class="text-xs text-gray-400 mt-1">À renseigner après la session — visible dans l'espace inscrit des participants.</p>
                    <p v-if="form.errors.replay_url" class="text-red-600 text-sm mt-1">{{ form.errors.replay_url }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix (€)</label>
                        <input v-model="form.price" type="number" min="0" step="0.01" placeholder="Laisser vide = gratuit" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places</label>
                        <input v-model="form.max_participants" type="number" min="1" placeholder="Laisser vide = illimité" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input id="is_active" type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-[#d1922f]" />
                    <label for="is_active" class="text-sm font-medium text-gray-700">Session visible et ouverte aux inscriptions</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#c08529] disabled:opacity-50 font-medium">
                        Enregistrer
                    </button>
                    <Link :href="route('admin.masterclasses.show', masterclass.id)" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

const props = defineProps<{
    masterclass: { id: number; title: string; slug: string };
    session: {
        id: number;
        start_date: string;
        end_date: string | null;
        location_type: string;
        adresse: string | null;
        google_maps_url: string | null;
        online_link: string | null;
        replay_url: string | null;
        price: number | null;
        max_participants: number | null;
        is_active: boolean;
    };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
    { title: props.masterclass.title, href: route('admin.masterclasses.show', props.masterclass.id) },
    { title: 'Modifier session', href: '#' },
];

const form = useForm({
    start_date: props.session.start_date,
    end_date: props.session.end_date ?? '',
    location_type: props.session.location_type,
    adresse: props.session.adresse ?? '',
    google_maps_url: props.session.google_maps_url ?? '',
    online_link: props.session.online_link ?? '',
    replay_url: props.session.replay_url ?? '',
    price: props.session.price !== null ? String(props.session.price) : '',
    max_participants: props.session.max_participants !== null ? String(props.session.max_participants) : '',
    is_active: props.session.is_active,
    _method: 'PUT',
});

const submit = () => {
    form.post(route('admin.masterclass-sessions.update', { masterclass: props.masterclass.id, session: props.session.id }));
};
</script>

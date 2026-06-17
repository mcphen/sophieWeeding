<template>
    <Head title="Participants" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">

            <!-- En-tête -->
            <div class="bg-white rounded-xl shadow-sm p-5 flex flex-wrap gap-4 items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Participants uniques</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ total }} participant(s) distincts sur toutes les masterclasses</p>
                </div>
                <div class="flex items-center gap-3">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Rechercher par nom ou email…"
                        class="border border-gray-300 rounded-md px-3 py-1.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-[#d1922f]"
                        @input="search"
                    />
                </div>
            </div>

            <!-- Tableau -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div v-if="participants.length === 0" class="py-16 text-center text-gray-400 text-sm">
                    Aucun participant trouvé.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Participant</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Inscriptions</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Confirmées</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dernière inscription</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="p in participants" :key="p.email" class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ p.name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ p.email }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ p.phone || '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 font-semibold text-xs">
                                        {{ p.total_inscriptions }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full font-semibold text-xs"
                                        :class="p.confirmed_count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
                                    >
                                        {{ p.confirmed_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ p.last_inscription_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

interface Participant {
    email: string;
    name: string;
    phone: string | null;
    total_inscriptions: number;
    confirmed_count: number;
    last_inscription_at: string;
}

const props = defineProps<{
    participants: Participant[];
    total: number;
    search: string;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Participants', href: '#' },
];

const searchQuery = ref(props.search);

let debounceTimer: ReturnType<typeof setTimeout>;
const search = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('admin.participants.index'), { search: searchQuery.value }, { preserveState: true, replace: true });
    }, 300);
};
</script>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed } from 'vue';

interface Donation {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    amount: number;
    method: string;
    message: string | null;
    status: string;
    created_at: string;
}

interface Pagination {
    data: Donation[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    donations: Pagination;
}

const props = defineProps<Props>();
const donations = computed(() => props.donations.data);

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Dons', href: route('admin.donations.index') },
];

const methodLabels: Record<string, string> = {
    orange_money: 'Orange Money',
    wave: 'Wave',
    virement: 'Virement bancaire',
};

function formatDateTime(dateString: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));
}

function destroy(donation: Donation) {
    if (confirm('Supprimer cette intention de don ?')) {
        router.delete(route('admin.donations.destroy', donation.id));
    }
}
</script>

<template>
    <Head title="Gestion des dons" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">Intentions de dons</h2>
                <p class="text-gray-500 mt-1">Suivez et contactez les donateurs pour finaliser leur don.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moyen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="donation in donations" :key="donation.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ donation.name }}</div>
                                <div class="text-sm text-gray-500">{{ donation.phone }}</div>
                                <div v-if="donation.email" class="text-sm text-gray-500">{{ donation.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ donation.amount.toLocaleString('fr-FR') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ methodLabels[donation.method] || donation.method }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDateTime(donation.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="destroy(donation)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="donations.length === 0" class="text-center py-12 text-gray-500">
                Aucune intention de don pour le moment.
            </div>

            <div v-if="props.donations.links && props.donations.links.length > 3" class="mt-4 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link
                        v-for="(link, i) in props.donations.links"
                        :key="i"
                        :href="link.url"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                        :class="{ 'z-10 bg-primary-bg-light border-primary text-primary': link.active, 'bg-gray-100 text-gray-500 cursor-not-allowed': !link.url }"
                    >
                        <span v-html="link.label"></span>
                    </Link>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>

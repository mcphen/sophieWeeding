<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed } from 'vue';

interface Volunteer {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    type: string;
    motivation: string;
    created_at: string;
}

interface Pagination {
    data: Volunteer[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    volunteers: Pagination;
}

const props = defineProps<Props>();
const volunteers = computed(() => props.volunteers.data);

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Bénévolat & partenariats', href: route('admin.volunteers.index') },
];

const typeLabels: Record<string, string> = {
    benevolat: 'Bénévolat',
    partenariat: 'Partenariat',
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

function destroy(volunteer: Volunteer) {
    if (confirm('Supprimer cette candidature ?')) {
        router.delete(route('admin.volunteers.destroy', volunteer.id));
    }
}
</script>

<template>
    <Head title="Bénévolat & partenariats" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">Candidatures bénévolat & partenariat</h2>
                <p class="text-gray-500 mt-1">Les personnes et organisations qui souhaitent rejoindre la fondation.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="volunteer in volunteers" :key="volunteer.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ volunteer.name }}</div>
                                <div class="text-sm text-gray-500">{{ volunteer.phone }}</div>
                                <div v-if="volunteer.email" class="text-sm text-gray-500">{{ volunteer.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ typeLabels[volunteer.type] || volunteer.type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ volunteer.motivation }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDateTime(volunteer.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="destroy(volunteer)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="volunteers.length === 0" class="text-center py-12 text-gray-500">
                Aucune candidature pour le moment.
            </div>

            <div v-if="props.volunteers.links && props.volunteers.links.length > 3" class="mt-4 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <Link
                        v-for="(link, i) in props.volunteers.links"
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

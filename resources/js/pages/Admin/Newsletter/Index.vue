<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Download, Search, SlidersHorizontal } from 'lucide-vue-next';

// Type pour un abonné à la newsletter
interface Subscriber {
    id: number;
    email: string;
    is_active: boolean;
    source: string;
    subscribed_at: string;
    unsubscribed_at: string | null;
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Subscriber[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    subscribers: Pagination;
    filters: {
        search?: string;
        status?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux abonnés
const subscribersList = computed(() => props.subscribers.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Abonnés Newsletter', href: route('admin.newsletter.index') }
];

// Filtres
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const sortField = ref(props.filters.sort_field || 'subscribed_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');
const showFilters = ref(false);

// Fonctions
function applyFilters() {
    window.location.href = route('admin.newsletter.index', {
        search: search.value,
        status: status.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    });
}

function resetFilters() {
    search.value = '';
    status.value = '';
    sortField.value = 'subscribed_at';
    sortDirection.value = 'desc';
    applyFilters();
}

function toggleSort(field: string) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    applyFilters();
}

function exportToExcel() {
    window.location.href = route('admin.newsletter.export');
}


// Formatage de la date et heure
function formatDateTime(dateString: string): string {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Badge de statut
function getStatusBadgeClass(isActive: boolean): string {
    return isActive
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
}
</script>

<template>
    <Head title="Abonnés Newsletter" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et filtres -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Abonnés Newsletter</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Gérez les abonnés à votre newsletter
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button variant="outline" @click="showFilters = !showFilters">
                        <SlidersHorizontal class="h-4 w-4 mr-2" />
                        Filtres
                    </Button>
                    <Button @click="exportToExcel">
                        <Download class="h-4 w-4 mr-2" />
                        Exporter Excel
                    </Button>
                </div>
            </div>

            <!-- Filtres -->
            <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <div class="relative">
                        <Input
                            id="search"
                            v-model="search"
                            placeholder="Rechercher par email..."
                            class="pl-10"
                            @keyup.enter="applyFilters"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select
                        id="status"
                        v-model="status"
                        @change="applyFilters"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <Button variant="outline" @click="resetFilters" class="ml-auto">
                        Réinitialiser
                    </Button>
                </div>
            </div>

            <!-- Tableau des abonnés -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[50px]">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('email')">
                                Email
                                <span v-if="sortField === 'email'" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('is_active')">
                                Statut
                                <span v-if="sortField === 'is_active'" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('source')">
                                Source
                                <span v-if="sortField === 'source'" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('subscribed_at')">
                                Date d'inscription
                                <span v-if="sortField === 'subscribed_at'" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="toggleSort('unsubscribed_at')">
                                Date de désinscription
                                <span v-if="sortField === 'unsubscribed_at'" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="subscriber in subscribersList" :key="subscriber.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ subscriber.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ subscriber.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full',
                                    getStatusBadgeClass(subscriber.is_active)
                                ]">
                                    {{ subscriber.is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ subscriber.source || 'Non spécifié' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(subscriber.subscribed_at) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ subscriber.unsubscribed_at ? formatDateTime(subscriber.unsubscribed_at) : '-' }}</td>
                        </tr>
                        <tr v-if="subscribersList.length === 0">
                            <td colspan="6" class="text-center py-6 text-gray-500">
                                Aucun abonné trouvé
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="props.subscribers.links && props.subscribers.links.length > 3" class="flex justify-center mt-4">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in props.subscribers.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-4 py-2 border rounded text-sm"
                            :class="{
                                'bg-primary text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-50': !link.active
                            }"
                        >
                            <span v-if="link.label === '&laquo; Previous'">&laquo; Précédent</span>
                            <span v-else-if="link.label === 'Next &raquo;'">Suivant &raquo;</span>
                            <span v-else>{{ link.label }}</span>
                        </Link>
                        <span
                            v-else
                            class="px-4 py-2 border rounded text-sm opacity-50 cursor-not-allowed"
                        >
                            <span v-if="link.label === '&laquo; Previous'">&laquo; Précédent</span>
                            <span v-else-if="link.label === 'Next &raquo;'">Suivant &raquo;</span>
                            <span v-else>{{ link.label }}</span>
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

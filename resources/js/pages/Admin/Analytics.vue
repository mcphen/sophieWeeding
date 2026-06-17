<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import BarChart from '@/components/dashboard/BarChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';

const props = defineProps<{
    kpis: {
        totalInscriptions: number;
        confirmedInscriptions: number;
        confirmationRate: number;
        estimatedRevenue: number;
        totalWaitlist: number;
    };
    inscriptionsParPeriode: {
        labels: string[];
        datasets: {
            label: string;
            data: number[];
            borderColor?: string;
            backgroundColor?: string;
        }[];
    };
    masterclassesPopulaires: {
        labels: string[];
        data: number[];
    };
    revenusParMasterclass: {
        labels: string[];
        data: number[];
    };
    upcomingSessions: Array<{
        id: number;
        masterclass_id: number;
        masterclass_title: string;
        start_date: string;
        location: string;
        price: string;
        registrations: number;
        max_participants: number | null;
        available_spots: number | null;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Analytiques Masterclasses', href: route('admin.analytics') },
];

function formatRevenue(amount: number): string {
    if (amount === 0) return '0 F CFA';
    return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' F CFA';
}

function fillRate(session: typeof props.upcomingSessions[0]): number | null {
    if (!session.max_participants) return null;
    return Math.round((session.registrations / session.max_participants) * 100);
}
</script>

<template>
    <Head title="Analytiques Masterclasses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-gray-50 dark:bg-gray-900">

            <!-- KPI Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5 shadow-sm dark:border-indigo-800 dark:bg-indigo-900/20">
                    <p class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Total inscriptions</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-700 dark:text-indigo-300">{{ kpis.totalInscriptions }}</p>
                </div>
                <div class="rounded-xl border border-green-100 bg-green-50 p-5 shadow-sm dark:border-green-800 dark:bg-green-900/20">
                    <p class="text-xs font-medium uppercase tracking-wide text-green-500 dark:text-green-400">Confirmées</p>
                    <p class="mt-2 text-3xl font-bold text-green-700 dark:text-green-300">{{ kpis.confirmedInscriptions }}</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-5 shadow-sm dark:border-amber-800 dark:bg-amber-900/20">
                    <p class="text-xs font-medium uppercase tracking-wide text-amber-500 dark:text-amber-400">Taux de confirmation</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-300">{{ kpis.confirmationRate }}%</p>
                </div>
                <div class="rounded-xl border border-violet-100 bg-violet-50 p-5 shadow-sm dark:border-violet-800 dark:bg-violet-900/20">
                    <p class="text-xs font-medium uppercase tracking-wide text-violet-500 dark:text-violet-400">Revenus estimés</p>
                    <p class="mt-2 text-xl font-bold text-violet-700 dark:text-violet-300 leading-tight">{{ formatRevenue(kpis.estimatedRevenue) }}</p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-5 shadow-sm dark:border-rose-800 dark:bg-rose-900/20">
                    <p class="text-xs font-medium uppercase tracking-wide text-rose-500 dark:text-rose-400">Liste d'attente</p>
                    <p class="mt-2 text-3xl font-bold text-rose-700 dark:text-rose-300">{{ kpis.totalWaitlist }}</p>
                </div>
            </div>

            <!-- Inscriptions par période -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2 border-gray-100 dark:border-gray-700">
                    Inscriptions — 30 derniers jours
                </h2>
                <div class="h-64">
                    <LineChart
                        :chart-data="inscriptionsParPeriode"
                        chart-title="Inscriptions par jour"
                        chart-id="inscriptions-periode"
                    />
                </div>
            </div>

            <!-- Masterclasses populaires + Revenus -->
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2 border-gray-100 dark:border-gray-700">
                        Masterclasses les plus populaires
                    </h2>
                    <div class="h-64">
                        <BarChart
                            :chart-data="masterclassesPopulaires"
                            chart-title="Inscriptions totales"
                            chart-id="mc-populaires"
                        />
                    </div>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2 border-gray-100 dark:border-gray-700">
                        Revenus estimés par masterclass
                    </h2>
                    <div class="h-64">
                        <BarChart
                            :chart-data="revenusParMasterclass"
                            chart-title="Revenus (F CFA)"
                            chart-id="mc-revenus"
                        />
                    </div>
                </div>
            </div>

            <!-- Prochaines sessions -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4 border-b pb-2 border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Prochaines sessions</h2>
                    <Link :href="route('admin.masterclasses.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Gérer les masterclasses →
                    </Link>
                </div>

                <div v-if="upcomingSessions.length === 0" class="py-8 text-center text-gray-500">
                    Aucune session à venir
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <th class="pb-3 pr-4 font-medium">Masterclass</th>
                                <th class="pb-3 pr-4 font-medium">Date</th>
                                <th class="pb-3 pr-4 font-medium">Format</th>
                                <th class="pb-3 pr-4 font-medium">Prix</th>
                                <th class="pb-3 pr-4 font-medium">Inscrits</th>
                                <th class="pb-3 pr-4 font-medium">Remplissage</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="session in upcomingSessions" :key="session.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100 max-w-[200px] truncate">
                                    {{ session.masterclass_title }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ session.start_date }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-700': session.location === 'En ligne',
                                            'bg-green-100 text-green-700': session.location === 'Présentiel',
                                            'bg-purple-100 text-purple-700': session.location === 'Présentiel & En ligne',
                                        }">
                                        {{ session.location }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ session.price }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ session.registrations }}</span>
                                    <span v-if="session.max_participants" class="text-gray-400"> / {{ session.max_participants }}</span>
                                    <span v-else class="text-gray-400"> / ∞</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <div v-if="session.max_participants" class="flex items-center gap-2">
                                        <div class="h-2 w-20 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all"
                                                :class="{
                                                    'bg-green-500': (fillRate(session) ?? 0) < 70,
                                                    'bg-amber-500': (fillRate(session) ?? 0) >= 70 && (fillRate(session) ?? 0) < 90,
                                                    'bg-red-500': (fillRate(session) ?? 0) >= 90,
                                                }"
                                                :style="{ width: Math.min(fillRate(session) ?? 0, 100) + '%' }"
                                            />
                                        </div>
                                        <span class="text-xs text-gray-500">{{ fillRate(session) }}%</span>
                                    </div>
                                    <span v-else class="text-xs text-gray-400">Illimité</span>
                                </td>
                                <td class="py-3">
                                    <Link
                                        :href="route('admin.masterclass-sessions.registrations', { masterclass: session.masterclass_id, session: session.id })"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                    >
                                        Inscriptions
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

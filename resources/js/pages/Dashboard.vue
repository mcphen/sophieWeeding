<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import StatsCard from '../components/dashboard/StatsCard.vue';
import BarChart from '../components/dashboard/BarChart.vue';
import LineChart from '../components/dashboard/LineChart.vue';
import PieChart from '../components/dashboard/PieChart.vue';

// Define props for the data passed from the controller
const props = defineProps<{
    visitorStats: {
        totalVisitors: number;
        totalPageViews: number;
        uniqueVisitorsToday: number;
        pageViewsToday: number;
        averagePageViewsPerVisitor: number;
    };
    pageVisits: {
        labels: string[];
        data: number[];
    };
    geoData: {
        countries: {
            labels: string[];
            data: number[];
        };
        cities: {
            labels: string[];
            data: number[];
        };
    };
    actionData: {
        labels: string[];
        data: number[];
    };
    timeData: {
        daily: {
            labels: string[];
            uniqueVisitors: number[];
            pageViews: number[];
        };
        hourly: {
            labels: number[];
            data: number[];
        };
    };
    recentAppointments: Array<{
        id: number;
        subject: string;
        description: string | null;
        status: string;
        created_at: string;
        client: {
            first_name: string;
            last_name: string;
            email: string;
        };
        services: Array<{
            id: number;
            name: string;
        }>;
    }>;
    recentContacts: Array<{
        id: number;
        subject: string;
        description: string;
        created_at: string;
        client: {
            first_name: string;
            last_name: string;
            email: string;
        };
    }>;
    logoUrl?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Icons for stats cards
const visitorIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>';
const pageViewIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>';
const todayIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>';
const averageIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';

// Prepare data for the time series chart
const timeSeriesData = {
    labels: props.timeData.daily.labels,
    datasets: [
        {
            label: 'Unique Visitors',
            data: props.timeData.daily.uniqueVisitors,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
        },
        {
            label: 'Page Views',
            data: props.timeData.daily.pageViews,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
        },
    ],
};
</script>

<template>
    <Head title="Visitor Tracking Dashboard">
        <!-- Favicon -->
        <link rel="icon" :href="logoUrl" type="image/png">
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs" :logoUrl="logoUrl">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-gray-50 dark:bg-gray-900">
            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <StatsCard
                    title="Total Visitors"
                    :value="visitorStats.totalVisitors"
                    :icon="visitorIcon"
                    class="transform transition-transform hover:scale-105"
                />
                <StatsCard
                    title="Total Page Views"
                    :value="visitorStats.totalPageViews"
                    :icon="pageViewIcon"
                    class="transform transition-transform hover:scale-105"
                />
                <StatsCard
                    title="Visitors Today"
                    :value="visitorStats.uniqueVisitorsToday"
                    :icon="todayIcon"
                    class="transform transition-transform hover:scale-105"
                />
                <StatsCard
                    title="Avg. Views Per Visitor"
                    :value="visitorStats.averagePageViewsPerVisitor"
                    :icon="averageIcon"
                    class="transform transition-transform hover:scale-105"
                />
            </div>

            <!-- Recent Appointments and Contacts -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Recent Appointments -->
                <div class="rounded-xl border border-sidebar-border/70 bg-blue-50 p-5 shadow-md dark:border-sidebar-border dark:bg-blue-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-blue-800 dark:text-blue-300 border-b pb-2 border-blue-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Derniers rendez-vous
                    </h2>
                    <div class="space-y-3">
                        <div v-if="recentAppointments.length === 0" class="text-center py-4 text-gray-500">
                            Aucun rendez-vous récent
                        </div>
                        <div v-for="appointment in recentAppointments" :key="appointment.id"
                            class="flex flex-col rounded-lg border border-blue-200 bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md hover:border-blue-300">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ appointment.subject }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ appointment.client.first_name }} {{ appointment.client.last_name }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': appointment.status === 'pending',
                                        'bg-green-100 text-green-800': appointment.status === 'confirmed',
                                        'bg-red-100 text-red-800': appointment.status === 'cancelled'
                                    }">
                                    {{
                                        appointment.status === 'pending' ? 'En attente' :
                                        appointment.status === 'confirmed' ? 'Confirmé' : 'Annulé'
                                    }}
                                </span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span v-for="service in appointment.services" :key="service.id"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ service.name }}
                                </span>
                            </div>
                            <div class="mt-3 flex justify-between items-center pt-2 border-t border-blue-50">
                                <span class="text-xs text-gray-500">
                                    {{ new Date(appointment.created_at).toLocaleDateString('fr-FR') }}
                                </span>
                                <Link :href="route('admin.appointments.show', appointment.id)"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    Voir détails
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-right">
                        <Link :href="route('admin.appointments.index')"
                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                            Voir tous les rendez-vous
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Recent Contacts -->
                <div class="rounded-xl border border-sidebar-border/70 bg-purple-50 p-5 shadow-md dark:border-sidebar-border dark:bg-purple-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-purple-800 dark:text-purple-300 border-b pb-2 border-purple-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </span>
                        Derniers contacts
                    </h2>
                    <div class="space-y-3">
                        <div v-if="recentContacts.length === 0" class="text-center py-4 text-gray-500">
                            Aucun contact récent
                        </div>
                        <div v-for="contact in recentContacts" :key="contact.id"
                            class="flex flex-col rounded-lg border border-purple-200 bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md hover:border-purple-300">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ contact.subject }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ contact.client.first_name }} {{ contact.client.last_name }}
                                    </p>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600 line-clamp-2 bg-purple-50/50 p-2 rounded">{{ contact.description }}</p>
                            <div class="mt-3 flex justify-between items-center pt-2 border-t border-purple-50">
                                <span class="text-xs text-gray-500">
                                    {{ new Date(contact.created_at).toLocaleDateString('fr-FR') }}
                                </span>
                                <Link :href="route('admin.contacts.show', contact.id)"
                                    class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                                    Voir détails
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-right">
                        <Link :href="route('admin.contacts.index')"
                            class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                            Voir tous les contacts
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Time Series Chart -->
            <div class="h-80 rounded-xl border border-sidebar-border/70 bg-green-50 p-5 shadow-md dark:border-sidebar-border dark:bg-green-900/20">
                <h2 class="mb-4 text-xl font-semibold text-green-800 dark:text-green-300 border-b pb-2 border-green-200">
                    <span class="inline-block mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Activité des visiteurs (30 derniers jours)
                </h2>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <LineChart
                        :chart-data="timeSeriesData"
                        chart-title="Visitor Activity (Last 30 Days)"
                        chart-id="visitor-activity"
                    />
                </div>
            </div>

            <!-- Page Visits and Actions -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="h-80 rounded-xl border border-sidebar-border/70 bg-amber-50 p-5 shadow-md dark:border-sidebar-border dark:bg-amber-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-amber-800 dark:text-amber-300 border-b pb-2 border-amber-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Pages les plus visitées
                    </h2>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <BarChart
                            :chart-data="pageVisits"
                            chart-title="Top Pages Visited"
                            chart-id="page-visits"
                        />
                    </div>
                </div>
                <div class="h-80 rounded-xl border border-sidebar-border/70 bg-teal-50 p-5 shadow-md dark:border-sidebar-border dark:bg-teal-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-teal-800 dark:text-teal-300 border-b pb-2 border-teal-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Actions des utilisateurs
                    </h2>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <BarChart
                            :chart-data="actionData"
                            chart-title="User Actions"
                            chart-id="user-actions"
                        />
                    </div>
                </div>
            </div>

            <!-- Geographical Data -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="h-80 rounded-xl border border-sidebar-border/70 bg-indigo-50 p-5 shadow-md dark:border-sidebar-border dark:bg-indigo-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-indigo-800 dark:text-indigo-300 border-b pb-2 border-indigo-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Visiteurs par pays
                    </h2>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <PieChart
                            :chart-data="geoData.countries"
                            chart-title="Visitors by Country"
                            chart-id="country-chart"
                        />
                    </div>
                </div>
                <div class="h-80 rounded-xl border border-sidebar-border/70 bg-pink-50 p-5 shadow-md dark:border-sidebar-border dark:bg-pink-900/20">
                    <h2 class="mb-4 text-xl font-semibold text-pink-800 dark:text-pink-300 border-b pb-2 border-pink-200">
                        <span class="inline-block mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </span>
                        Visiteurs par ville
                    </h2>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <PieChart
                            :chart-data="geoData.cities"
                            chart-title="Visitors by City"
                            chart-id="city-chart"
                        />
                    </div>
                </div>
            </div>

            <!-- Hourly Distribution -->
            <div class="h-80 rounded-xl border border-sidebar-border/70 bg-orange-50 p-5 shadow-md dark:border-sidebar-border dark:bg-orange-900/20">
                <h2 class="mb-4 text-xl font-semibold text-orange-800 dark:text-orange-300 border-b pb-2 border-orange-200">
                    <span class="inline-block mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Distribution horaire des visiteurs
                </h2>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <BarChart
                        :chart-data="{ labels: props.timeData.hourly.labels.map(hour => `${hour}:00`), data: props.timeData.hourly.data }"
                        chart-title="Hourly Visitor Distribution"
                        chart-id="hourly-chart"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Enhanced transitions for smoother interactions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid {
        gap: 1rem;
    }

    h2 {
        font-size: 1.1rem;
    }
}

/* Dark mode enhancements */
@media (prefers-color-scheme: dark) {
    .bg-white {
        background-color: rgba(30, 41, 59, 0.8);
    }
}
</style>

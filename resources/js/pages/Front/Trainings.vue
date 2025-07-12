<template>
    <LayoutFront>
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-3xl font-serif font-bold text-gray-900 sm:text-4xl">
                        Nos formations et séminaires
                    </h1>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                        Découvrez nos sessions de formation et séminaires pour développer vos compétences
                    </p>
                </div>

                <!-- Filters -->
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center">
                    <div class="w-full sm:w-auto mb-4 sm:mb-0">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="filters.search"
                                placeholder="Rechercher une formation..."
                                class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                                @input="debouncedSearch"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-full sm:w-auto">
                        <select
                            v-model="filters.date"
                            class="w-full sm:w-auto pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                            @change="applyFilters"
                        >
                            <option value="upcoming">Prochaines formations</option>
                            <option value="past">Formations passées</option>
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>

                <!-- Training Sessions List -->
                <div v-if="trainingSessions.data.length > 0" class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="session in trainingSessions.data" :key="session.id" class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="relative pb-48">
                            <img v-if="session.image_url" :src="session.image_url" :alt="session.title" class="absolute h-full w-full object-cover" />
                            <div v-else class="absolute h-full w-full bg-[#d1922f]/20 flex items-center justify-center">
                                <svg class="h-16 w-16 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-[#d1922f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-2 text-sm text-gray-500">
                                    {{ formatDate(session.start_date) }}
                                </div>
                            </div>
                            <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                {{ session.title }}
                            </h3>
                            <p class="mt-3 text-base text-gray-500 line-clamp-3">
                                {{ session.description }}
                            </p>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-[#d1922f] font-semibold">
                                    {{ session.formatted_price }}
                                </div>
                                <div v-if="session.location" class="text-sm text-gray-500 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ session.location }}
                                </div>
                            </div>
                            <div class="mt-6">
                                <Link
                                    :href="route('training.show', session.slug)"
                                    class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529]"
                                >
                                    Voir les détails
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="mt-12 bg-white p-8 rounded-lg shadow text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune formation trouvée</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Aucune formation ne correspond à vos critères de recherche.
                    </p>
                    <div class="mt-6">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#d1922f] hover:bg-[#c08529] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d1922f]"
                            @click="resetFilters"
                        >
                            Réinitialiser les filtres
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="trainingSessions.data.length > 0" class="mt-12 flex justify-center">
                    <Pagination :links="trainingSessions.links" />
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import Pagination from '@/components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    trainingSessions: Object,
    filters: Object,
    contactSettings: Object,
    ctaSettings: Object,
});

const filters = ref({
    search: props.filters.search || '',
    date: props.filters.date || 'upcoming',
});

// Generate available years for filter (current year and 5 years back)
const availableYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 6 }, (_, i) => currentYear - i);
});

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Debounced search function
const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

// Apply filters and update URL
const applyFilters = () => {
    router.get(route('trainings'), filters.value, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

// Reset filters
const resetFilters = () => {
    filters.value = {
        search: '',
        date: 'upcoming',
    };
    applyFilters();
};

// Watch for filter changes
watch(() => filters.value.date, () => {
    applyFilters();
});
</script>

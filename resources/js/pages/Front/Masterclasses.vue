<template>
    <LayoutFront>
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-3xl font-serif font-bold text-gray-900 sm:text-4xl">Nos Masterclasses</h1>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                        Des programmes complets par niveau pour développer votre expertise
                    </p>
                </div>

                <!-- Recherche -->
                <div class="mt-8 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Rechercher une masterclass…"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"
                            @input="debouncedSearch"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Liste masterclasses -->
                <div v-if="masterclasses.data.length > 0" class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="mc in masterclasses.data"
                        :key="mc.id"
                        :href="route('masterclass.show', mc.slug)"
                        class="bg-white overflow-hidden shadow rounded-xl hover:shadow-md transition group"
                    >
                        <div class="relative pb-52">
                            <img v-if="mc.image_url" :src="mc.image_url" :alt="mc.title" class="absolute h-full w-full object-cover group-hover:scale-105 transition duration-300" />
                            <div v-else class="absolute h-full w-full bg-[#d1922f]/10 flex items-center justify-center">
                                <svg class="h-16 w-16 text-[#d1922f]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span class="absolute top-3 left-3 bg-[#d1922f] text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ mc.niveau }}
                            </span>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-[#d1922f] transition">{{ mc.title }}</h3>
                            <p v-if="mc.description" class="mt-2 text-sm text-gray-500 line-clamp-2">{{ mc.description }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    <span v-if="mc.upcoming_sessions_count > 0" class="text-[#d1922f] font-semibold">
                                        {{ mc.upcoming_sessions_count }} session(s) à venir
                                    </span>
                                    <span v-else class="text-gray-400">Aucune session prochaine</span>
                                </span>
                                <span class="text-[#d1922f] text-sm font-medium group-hover:underline">Voir →</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- État vide -->
                <div v-else class="mt-12 bg-white p-10 rounded-xl shadow text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">Aucune masterclass trouvée</h3>
                    <button @click="resetSearch" class="mt-4 text-sm text-[#d1922f] hover:underline">Réinitialiser la recherche</button>
                </div>

                <!-- Pagination -->
                <div v-if="masterclasses.data.length > 0 && masterclasses.links.length > 3" class="mt-10 flex justify-center">
                    <Pagination :links="masterclasses.links" />
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import Pagination from '@/components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps<{
    masterclasses: {
        data: Array<{
            id: number;
            title: string;
            niveau: string;
            description: string | null;
            image_url: string | null;
            upcoming_sessions_count: number;
            slug: string;
        }>;
        links: Array<any>;
    };
    filters: { search?: string };
    contactSettings: any;
    ctaSettings: any;
}>();

const search = ref(props.filters.search ?? '');

const debouncedSearch = debounce(() => {
    router.get(route('masterclasses'), { search: search.value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
}, 300);

const resetSearch = () => {
    search.value = '';
    router.get(route('masterclasses'), {}, { preserveState: true, replace: true });
};
</script>

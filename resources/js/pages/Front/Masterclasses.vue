<template>
    <LayoutFront>
        <Head>
            <title>Événements - Amaël Fondation</title>
            <meta name="description" content="Retrouvez les prochains événements d'Amaël Fondation à Dakar : journées de sensibilisation, collectes, distributions et rencontres bénévoles." />
            <meta property="og:type" content="website" />
            <meta property="og:title" content="Événements - Amaël Fondation" />
            <meta property="og:description" content="Retrouvez les prochains événements d'Amaël Fondation à Dakar : journées de sensibilisation, collectes, distributions et rencontres bénévoles." />
            <meta property="og:url" content="/evenements" />
        </Head>

        <div class="relative bg-[#1A1512]">
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Événements" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="font-display text-4xl md:text-5xl font-semibold text-white mb-4">Nos événements</h1>
                <p class="max-w-2xl mx-auto text-lg text-white/85">
                    Journées de sensibilisation, collectes solidaires, distributions et rencontres bénévoles : retrouvez ici nos prochains rendez-vous à Dakar.
                </p>
            </div>
        </div>

        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Recherche -->
                <div class="flex justify-center">
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Rechercher un événement…"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            @input="debouncedSearch"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Liste événements -->
                <div v-if="masterclasses.data.length > 0" class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="mc in masterclasses.data"
                        :key="mc.id"
                        v-reveal:up
                        :href="route('masterclass.show', mc.slug)"
                        class="bg-white overflow-hidden shadow rounded-2xl hover:shadow-lg transition group"
                    >
                        <div class="relative pb-52 overflow-hidden">
                            <img v-if="mc.image_url" :src="mc.image_url" :alt="mc.title" class="absolute h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110" />
                            <div v-else class="absolute h-full w-full bg-primary-bg-light flex items-center justify-center">
                                <svg class="h-16 w-16 text-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span v-if="mc.niveau" class="absolute top-3 left-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ mc.niveau }}
                            </span>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-primary transition">{{ mc.title }}</h3>
                            <p v-if="mc.description" class="mt-2 text-sm text-gray-500 line-clamp-2">{{ mc.description }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    <span v-if="mc.upcoming_sessions_count > 0" class="text-primary font-semibold">
                                        {{ mc.upcoming_sessions_count }} date(s) à venir
                                    </span>
                                    <span v-else class="text-gray-400">Aucune date prochaine</span>
                                </span>
                                <span class="text-primary text-sm font-medium group-hover:underline">Voir →</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- État vide -->
                <div v-else class="mt-12 bg-white p-10 rounded-xl shadow text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">Aucun événement à venir pour le moment</h3>
                    <p class="mt-1 text-sm text-gray-500">Revenez bientôt, ou suivez-nous sur nos réseaux sociaux pour ne rien manquer.</p>
                    <button v-if="search" @click="resetSearch" class="mt-4 text-sm text-primary hover:underline">Réinitialiser la recherche</button>
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
import { Head, Link, router } from '@inertiajs/vue3';
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

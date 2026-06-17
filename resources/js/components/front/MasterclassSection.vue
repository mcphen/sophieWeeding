<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Session {
    id: number;
    start_date: string;
    start_time: string;
    location_label: string;
    formatted_price: string;
    available_spots: number | null;
    masterclass: {
        title: string;
        niveau: string;
        slug: string;
        image_url: string | null;
    };
}

defineProps<{ sessions: Session[] }>();
</script>

<template>
    <section v-if="sessions.length > 0" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-[#d1922f] mb-2">Formation</p>
                    <h2 class="text-3xl font-serif font-bold text-gray-900">Prochaines sessions</h2>
                    <p class="mt-2 text-gray-500">Rejoignez nos masterclasses et développez votre expertise.</p>
                </div>
                <Link
                    :href="route('masterclasses')"
                    class="shrink-0 text-sm font-medium text-[#d1922f] hover:underline flex items-center gap-1"
                >
                    Voir toutes les masterclasses
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

            <!-- Session cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                    v-for="s in sessions"
                    :key="s.id"
                    :href="route('masterclass.show', s.masterclass.slug)"
                    class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col"
                >
                    <!-- Image -->
                    <div class="relative h-40 bg-[#d1922f]/10 overflow-hidden">
                        <img
                            v-if="s.masterclass.image_url"
                            :src="s.masterclass.image_url"
                            :alt="s.masterclass.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="h-12 w-12 text-[#d1922f]/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <!-- Niveau badge -->
                        <span class="absolute top-3 left-3 px-2 py-0.5 text-xs font-semibold bg-[#d1922f] text-white rounded-full">
                            {{ s.masterclass.niveau }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-semibold text-gray-900 text-sm leading-snug group-hover:text-[#d1922f] transition-colors">
                            {{ s.masterclass.title }}
                        </h3>

                        <div class="mt-3 space-y-1.5 text-xs text-gray-500">
                            <!-- Date -->
                            <div class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-[#d1922f] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ s.start_date }} à {{ s.start_time }}</span>
                            </div>
                            <!-- Location -->
                            <div class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-[#d1922f] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ s.location_label }}</span>
                            </div>
                        </div>

                        <!-- Footer: price + spots -->
                        <div class="mt-auto pt-3 flex items-center justify-between border-t border-gray-100 mt-3">
                            <span class="text-sm font-bold text-[#d1922f]">{{ s.formatted_price }}</span>
                            <span v-if="s.available_spots !== null" class="text-xs text-gray-500">
                                {{ s.available_spots }} place{{ s.available_spots !== 1 ? 's' : '' }} restante{{ s.available_spots !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>

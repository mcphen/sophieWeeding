<template>
    <div class="flex items-center justify-center">
        <nav class="flex items-center space-x-1" aria-label="Pagination">
            <Link
                v-for="(link, i) in links"
                :key="i"
                :href="link.url || '#'"
                :class="[
                    'rounded-md border px-4 py-2 transition-colors',
                    link.active
                        ? 'bg-[#d1922f] border-[#d1922f] text-white'
                        : link.url
                          ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                          : 'cursor-not-allowed border-gray-200 text-gray-400',
                ]"
            >
                <!-- Previous page arrow -->
                <span v-if="i === 0 && link.label.includes('Previous')" aria-hidden="true">
                    &laquo;
                </span>
                <!-- Next page arrow -->
                <span v-else-if="i === links.length - 1 && link.label.includes('Next')" aria-hidden="true">
                    &raquo;
                </span>
                <!-- Page number or ellipsis -->
                <span v-else>{{ link.label.replace(/&laquo;|&raquo;/g, '') }}</span>
            </Link>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    links: PaginationLink[];
}>();
</script>

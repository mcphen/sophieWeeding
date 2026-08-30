<template>
    <section
        class="relative overflow-hidden py-24"
        :style="{ background: `linear-gradient(115deg, ${fromColor}, ${toColor})` }"
    >
        <!-- Subtle decorative shape, kept abstract rather than a rounded card -->
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -left-16 bottom-0 w-64 h-64 rounded-full bg-black/10 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[auto_1fr_auto] items-center gap-10">
            <div class="text-center lg:text-left">
                <p class="font-display text-6xl sm:text-7xl font-semibold text-white leading-none">{{ stat }}</p>
                <p class="mt-1 text-sm uppercase tracking-wide" :style="{ color: paragraphColor }">{{ statLabel }}</p>
            </div>

            <div v-reveal:up>
                <h2 class="font-display text-2xl sm:text-3xl font-semibold text-white max-w-2xl">
                    {{ title }}
                </h2>
                <p class="mt-4 max-w-2xl text-base sm:text-lg" :style="{ color: paragraphColor }">
                    {{ description }}
                </p>
            </div>

            <div class="flex-shrink-0">
                <Link
                    :href="safeHref"
                    class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-full bg-white hover:opacity-90 transition-opacity whitespace-nowrap"
                    :style="{ color: buttonTextColor }"
                >
                    {{ buttonText }}
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    fromColor?: string;
    toColor?: string;
    title?: string;
    description?: string;
    paragraphColor?: string;
    linkRoute?: string;
    buttonText?: string;
    buttonTextColor?: string;
    stat?: string;
    statLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    fromColor: '#d1922f',
    toColor: '#8a5e12',
    title: 'Ensemble, changeons des vies en Afrique',
    description: 'Votre don, même modeste, nous permet d\'agir concrètement auprès des enfants et des familles qui en ont besoin. Rejoignez-nous.',
    paragraphColor: '#FBF1E9',
    linkRoute: 'donate',
    buttonText: 'Faire un don',
    buttonTextColor: '#1E2F52',
    stat: '100%',
    statLabel: 'de votre don finance nos actions de terrain',
});

// linkRoute may be a named Ziggy route or a raw path; fall back gracefully if the route doesn't exist.
const safeHref = computed(() => {
    try {
        return route(props.linkRoute);
    } catch {
        return props.linkRoute?.startsWith('/') ? props.linkRoute : '/' + (props.linkRoute || '');
    }
});
</script>

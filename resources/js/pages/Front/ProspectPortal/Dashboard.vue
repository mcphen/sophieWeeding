<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed } from 'vue';

interface Session {
    id: number;
    start_date: string;
    start_time: string;
    location_label: string;
    formatted_price: string;
    is_past: boolean;
    replay_url: string | null;
}

interface Masterclass {
    title: string;
    niveau: string;
    slug: string;
}

interface Registration {
    id: number;
    name: string;
    email: string;
    is_confirmed: boolean;
    created_at: string;
    masterclass: Masterclass | null;
    session: Session | null;
}

const props = defineProps<{
    email: string;
    registrations: Registration[];
    has_signature: boolean;
}>();

const page = usePage();
const flash = computed(() => page.props.flash as Record<string, string> | null);

const logout = () => {
    router.post(route('prospect.portal.logout'));
};

const attestationUrl = (reg: Registration) => route('prospect.portal.attestation', reg.id);
</script>

<template>
    <Head title="Mon espace — Sophie Weddings Dream" />
    <LayoutFront>
        <section class="min-h-screen bg-gray-50 py-12 px-4">
            <div class="max-w-4xl mx-auto">

                <!-- En-tête du portail -->
                <div class="bg-gradient-to-r from-[#aa6808] to-[#d1922f] rounded-2xl p-6 md:p-8 text-white mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="text-white/70 text-sm font-medium uppercase tracking-wider mb-1">Mon espace inscrit</div>
                        <h1 class="text-2xl font-bold">Bienvenue !</h1>
                        <p class="text-white/80 text-sm mt-1">{{ email }}</p>
                    </div>
                    <button
                        @click="logout"
                        class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Se déconnecter
                    </button>
                </div>

                <!-- Flash messages -->
                <div v-if="flash?.success" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-700">
                    {{ flash.success }}
                </div>
                <div v-if="flash?.error" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                    {{ flash.error }}
                </div>

                <!-- Aucune inscription -->
                <div v-if="registrations.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucune inscription trouvée</h2>
                    <p class="text-gray-500 text-sm mb-6">Votre email n'est associé à aucune inscription pour le moment.</p>
                    <Link :href="route('masterclasses')" class="inline-block bg-[#aa6808] text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-[#8a5206] transition-colors">
                        Voir nos masterclasses
                    </Link>
                </div>

                <!-- Liste des inscriptions -->
                <div v-else class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        Mes inscriptions
                        <span class="text-sm font-normal text-gray-500 ml-2">({{ registrations.length }})</span>
                    </h2>

                    <div
                        v-for="reg in registrations"
                        :key="reg.id"
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                    >
                        <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <!-- Infos gauche -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <!-- Statut -->
                                    <span
                                        :class="reg.is_confirmed
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-amber-100 text-amber-700'"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                    >
                                        <span :class="reg.is_confirmed ? 'bg-green-500' : 'bg-amber-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                        {{ reg.is_confirmed ? 'Confirmée' : 'En attente' }}
                                    </span>
                                    <!-- Passé/À venir -->
                                    <span v-if="reg.session?.is_past" class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full font-medium">
                                        Session passée
                                    </span>
                                    <span v-else class="text-xs bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full font-medium">
                                        À venir
                                    </span>
                                </div>

                                <h3 class="font-semibold text-gray-900 text-base leading-tight">
                                    {{ reg.masterclass?.title ?? 'Masterclass' }}
                                </h3>
                                <p v-if="reg.masterclass?.niveau" class="text-xs text-[#aa6808] font-medium mt-0.5">
                                    Niveau : {{ reg.masterclass.niveau }}
                                </p>

                                <div v-if="reg.session" class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ reg.session.start_date }} à {{ reg.session.start_time }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        {{ reg.session.location_label }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ reg.session.formatted_price }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-400 mt-2">Inscrit le {{ reg.created_at }}</p>
                            </div>

                            <!-- Actions droite -->
                            <div class="flex flex-col gap-2 md:items-end">
                                <!-- Attestation -->
                                <a
                                    v-if="reg.is_confirmed && has_signature"
                                    :href="attestationUrl(reg)"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 bg-[#aa6808] hover:bg-[#8a5206] text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Attestation PDF
                                </a>
                                <span
                                    v-else-if="reg.is_confirmed && !has_signature"
                                    class="inline-flex items-start gap-1.5 text-xs text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg max-w-[220px]"
                                >
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Attestation en cours de signature — vous la recevrez par email
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-start gap-1.5 text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg max-w-[200px]"
                                    title="Votre attestation sera disponible après validation de votre participation par notre équipe."
                                >
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    En attente de validation
                                </span>

                                <!-- Replay -->
                                <a
                                    v-if="reg.session?.replay_url"
                                    :href="reg.session.replay_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Voir le replay
                                </a>

                                <!-- Lien masterclass -->
                                <Link
                                    v-if="reg.masterclass?.slug && !reg.session?.is_past"
                                    :href="route('masterclass.show', reg.masterclass.slug)"
                                    class="inline-flex items-center gap-1.5 text-xs text-[#aa6808] hover:underline"
                                >
                                    Voir la masterclass →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA bas de page -->
                <div class="mt-10 text-center">
                    <p class="text-sm text-gray-500 mb-3">Vous souhaitez vous inscrire à une nouvelle session ?</p>
                    <Link :href="route('masterclasses')" class="inline-block text-sm text-[#aa6808] font-medium hover:underline">
                        Voir toutes nos masterclasses →
                    </Link>
                </div>

            </div>
        </section>
    </LayoutFront>
</template>

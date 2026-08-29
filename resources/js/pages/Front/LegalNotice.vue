<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed } from 'vue';

interface ContactSettings {
    contact_email: string;
    contact_phone: string;
    contact_address: string;
}

const page = usePage();
const contactSettings = computed(() => page.props.contactSettings as ContactSettings);

const breadcrumbItems = [
    { name: 'Accueil', href: route('home'), current: false },
    { name: 'Mentions légales', href: route('legal.notice'), current: true },
];
</script>

<template>
    <Head>
        <title>Mentions légales - Amaël Fondation</title>
        <meta name="description" content="Mentions légales du site Amaël Fondation." />
        <meta property="og:title" content="Mentions légales - Amaël Fondation" />
        <meta property="og:description" content="Mentions légales du site Amaël Fondation." />
        <meta name="robots" content="noindex, follow" />
    </Head>

    <LayoutFront>
        <div class="relative bg-[#1A1512]">
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Mentions légales" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">Mentions légales</h1>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li v-for="(item, index) in breadcrumbItems" :key="item.name">
                            <div class="flex items-center">
                                <Link :href="item.href" :class="[item.current ? 'text-white font-medium' : 'text-white/80 hover:text-white', 'text-sm md:text-base transition-colors']">
                                    {{ item.name }}
                                </Link>
                                <svg v-if="index !== breadcrumbItems.length - 1" class="h-5 w-5 text-white/70 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="py-16 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 text-gray-700 leading-relaxed">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Éditeur du site</h2>
                    <p>
                        Le présent site est édité par <strong>Amaël Fondation</strong>, association/fondation à but non lucratif
                        basée à {{ contactSettings.contact_address }}.
                    </p>
                    <p class="text-sm text-gray-500 italic mt-2">
                        Numéro d'enregistrement, forme juridique et représentant légal : à compléter par la fondation.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Contact</h2>
                    <ul class="space-y-1 list-disc list-inside">
                        <li>Email : {{ contactSettings.contact_email }}</li>
                        <li>Téléphone : {{ contactSettings.contact_phone }}</li>
                        <li>Adresse : {{ contactSettings.contact_address }}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Hébergement</h2>
                    <p class="text-sm text-gray-500 italic">
                        Nom, adresse et contact de l'hébergeur du site : à compléter.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Propriété intellectuelle</h2>
                    <p>
                        L'ensemble des contenus présents sur ce site (textes, photographies, logo, mise en page) est la propriété
                        d'Amaël Fondation, sauf mention contraire. Toute reproduction sans autorisation préalable est interdite.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Responsabilité</h2>
                    <p>
                        Amaël Fondation s'efforce d'assurer l'exactitude des informations diffusées sur ce site mais ne saurait
                        être tenue responsable des erreurs, omissions ou de l'indisponibilité temporaire du site.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Liens vers d'autres sites</h2>
                    <p>
                        Ce site peut contenir des liens vers des sites tiers (réseaux sociaux, partenaires). Amaël Fondation
                        n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu.
                    </p>
                </section>

                <p class="pt-4 border-t border-gray-100">
                    Pour toute question relative à ces mentions légales, contactez-nous à
                    <a :href="`mailto:${contactSettings.contact_email}`" class="text-primary hover:underline">{{ contactSettings.contact_email }}</a>.
                    Voir aussi notre
                    <Link :href="route('legal.privacy')" class="text-primary hover:underline">politique de confidentialité</Link>.
                </p>
            </div>
        </div>
    </LayoutFront>
</template>

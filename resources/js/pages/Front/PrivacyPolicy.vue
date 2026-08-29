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
    { name: 'Politique de confidentialité', href: route('legal.privacy'), current: true },
];
</script>

<template>
    <Head>
        <title>Politique de confidentialité - Amaël Fondation</title>
        <meta name="description" content="Comment Amaël Fondation collecte, utilise et protège vos données personnelles." />
        <meta property="og:title" content="Politique de confidentialité - Amaël Fondation" />
        <meta property="og:description" content="Comment Amaël Fondation collecte, utilise et protège vos données personnelles." />
        <meta name="robots" content="noindex, follow" />
    </Head>

    <LayoutFront>
        <div class="relative bg-[#1A1512]">
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Politique de confidentialité" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">Politique de confidentialité</h1>
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
                <p>
                    Amaël Fondation attache une grande importance à la protection de vos données personnelles.
                    Cette page explique quelles informations nous collectons via ce site, pourquoi, et comment vous
                    pouvez exercer vos droits.
                </p>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Données que nous collectons</h2>
                    <ul class="space-y-1 list-disc list-inside">
                        <li>Formulaire de contact : nom, prénom, email, téléphone, message.</li>
                        <li>Formulaire "Faire un don" : nom, téléphone, email, montant, moyen de paiement, message.</li>
                        <li>Formulaire "Nous rejoindre" : nom, téléphone, email, motivation.</li>
                        <li>Newsletter : adresse email.</li>
                        <li>Navigation : pages consultées, à des fins statistiques internes.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Pourquoi nous les collectons</h2>
                    <p>
                        Ces informations nous permettent de répondre à vos demandes, d'assurer le suivi de vos dons ou de
                        votre candidature bénévole/partenariat, et de vous tenir informé de nos actions si vous y avez
                        consenti (newsletter). Nous ne vendons ni ne louons vos données à des tiers.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Conservation des données</h2>
                    <p>
                        Vos données sont conservées le temps nécessaire au traitement de votre demande, puis archivées ou
                        supprimées conformément à la réglementation applicable.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Vos droits</h2>
                    <p>
                        Vous disposez d'un droit d'accès, de rectification et de suppression de vos données personnelles.
                        Pour l'exercer, contactez-nous à
                        <a :href="`mailto:${contactSettings.contact_email}`" class="text-primary hover:underline">{{ contactSettings.contact_email }}</a>.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Cookies</h2>
                    <p>
                        Ce site utilise des cookies techniques nécessaires à son fonctionnement (préférences d'affichage,
                        session). Un bandeau vous permet d'accepter leur utilisation lors de votre première visite.
                    </p>
                </section>

                <p class="pt-4 border-t border-gray-100">
                    Voir aussi nos
                    <Link :href="route('legal.notice')" class="text-primary hover:underline">mentions légales</Link>.
                </p>
            </div>
        </div>
    </LayoutFront>
</template>

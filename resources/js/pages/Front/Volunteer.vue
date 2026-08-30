<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref } from 'vue';

const isSuccess = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    type: 'benevolat',
    motivation: '',
});

function submit() {
    form.post(route('volunteer.store'), {
        onSuccess: () => {
            isSuccess.value = true;
            form.reset();
            setTimeout(() => (isSuccess.value = false), 6000);
        },
    });
}

const breadcrumbItems = [
    { name: 'Accueil', href: route('home'), current: false },
    { name: 'Nous rejoindre', href: route('volunteer'), current: true },
];
</script>

<template>
    <Head>
        <title>Nous rejoindre - Amaël Fondation</title>
        <meta name="description" content="Devenez bénévole ou partenaire d'Amaël Fondation et engagez-vous à nos côtés en Afrique." />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Nous rejoindre - Amaël Fondation" />
        <meta property="og:description" content="Devenez bénévole ou partenaire d'Amaël Fondation et engagez-vous à nos côtés en Afrique." />
        <meta property="og:url" content="/nous-rejoindre" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Nous rejoindre - Amaël Fondation" />
        <meta name="twitter:description" content="Devenez bénévole ou partenaire d'Amaël Fondation et engagez-vous à nos côtés en Afrique." />
    </Head>

    <LayoutFront>
        <!-- Bannière -->
        <div class="relative bg-[#1A1512]">
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Nous rejoindre" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">Nous rejoindre</h1>
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
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary-dark">Engagez-vous</span>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-gray-900">Devenez bénévole ou partenaire</h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Que vous souhaitiez donner de votre temps sur le terrain ou nouer un partenariat institutionnel,
                        parlez-nous de votre projet et nous reviendrons vers vous rapidement.
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <p class="font-medium">Merci pour votre engagement !</p>
                        <p>Notre équipe reviendra vers vous rapidement.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Je souhaite *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="form.type = 'benevolat'"
                                    class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors"
                                    :class="form.type === 'benevolat' ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-700 hover:border-primary'"
                                >
                                    Devenir bénévole
                                </button>
                                <button
                                    type="button"
                                    @click="form.type = 'partenariat'"
                                    class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors"
                                    :class="form.type === 'partenariat' ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-700 hover:border-primary'"
                                >
                                    Proposer un partenariat
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                                <input id="name" v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                                <input id="phone" v-model="form.phone" type="tel" class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary" required />
                                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (optionnel)</label>
                            <input id="email" v-model="form.email" type="email" class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary" />
                        </div>

                        <div>
                            <label for="motivation" class="block text-sm font-medium text-gray-700 mb-1">Votre motivation *</label>
                            <textarea id="motivation" v-model="form.motivation" rows="5" class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary" required></textarea>
                            <p v-if="form.errors.motivation" class="mt-1 text-sm text-red-600">{{ form.errors.motivation }}</p>
                        </div>

                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-colors"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Envoi en cours...</span>
                            <span v-else>Envoyer ma candidature</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

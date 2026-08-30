<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed, ref } from 'vue';

interface ContactSettings {
    contact_phone: string;
    contact_email: string;
    whatsapp_number?: string;
}

const page = usePage();
const contactSettings = computed(() => page.props.contactSettings as ContactSettings);

const suggestedAmounts = [5000, 10000, 25000, 50000];

const methods = [
    { value: 'orange_money', label: 'Orange Money', description: 'Envoyez votre don au numéro ci-dessous puis validez le formulaire.' },
    { value: 'wave', label: 'Wave', description: 'Envoyez votre don via l\'application Wave puis validez le formulaire.' },
    { value: 'virement', label: 'Virement bancaire', description: 'Contactez-nous pour recevoir nos coordonnées bancaires (RIB).' },
];

const isSuccess = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    amount: 10000,
    method: 'orange_money',
    message: '',
});

const customAmount = ref<number | null>(null);

function selectAmount(amount: number) {
    form.amount = amount;
    customAmount.value = null;
}

function submit() {
    form.post(route('donate.store'), {
        onSuccess: () => {
            isSuccess.value = true;
            form.reset();
            setTimeout(() => (isSuccess.value = false), 6000);
        },
    });
}

const breadcrumbItems = [
    { name: 'Accueil', href: route('home'), current: false },
    { name: 'Faire un don', href: route('donate'), current: true },
];
</script>

<template>
    <Head>
        <title>Faire un don - Amaël Fondation</title>
        <meta name="description" content="Soutenez les actions d'Amaël Fondation en Afrique en faisant un don via Orange Money, Wave ou virement bancaire." />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Faire un don - Amaël Fondation" />
        <meta property="og:description" content="Soutenez les actions d'Amaël Fondation en Afrique en faisant un don via Orange Money, Wave ou virement bancaire." />
        <meta property="og:url" content="/faire-un-don" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Faire un don - Amaël Fondation" />
        <meta name="twitter:description" content="Soutenez les actions d'Amaël Fondation en Afrique en faisant un don via Orange Money, Wave ou virement bancaire." />
    </Head>

    <LayoutFront>
        <!-- Bannière -->
        <div class="relative bg-[#1A1512]">
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Faire un don" class="w-full h-full object-cover object-center opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>
            <div class="relative max-w-7xl mx-auto pt-32 pb-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">Faire un don</h1>
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
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="inline-block text-sm font-semibold tracking-wide uppercase text-primary-dark">Pourquoi donner ?</span>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-gray-900">Chaque don finance une action concrète</h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Vos dons financent l'achat de fournitures scolaires, le soutien à la maternité et nos actions
                        solidaires auprès des familles vulnérables en Afrique. 100% de votre générosité sert nos actions
                        de terrain.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
                    <!-- Moyens de paiement -->
                    <div class="lg:col-span-2 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Moyens de paiement</h3>
                        <div v-for="m in methods" :key="m.value" class="rounded-2xl border border-gray-100 bg-primary-bg-light p-5">
                            <p class="font-semibold text-gray-900">{{ m.label }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ m.description }}</p>
                            <p v-if="m.value !== 'virement'" class="mt-2 text-primary font-medium">{{ contactSettings.contact_phone }}</p>
                        </div>
                        <p class="text-sm text-gray-500">
                            Une question ? Contactez-nous sur
                            <a :href="`https://wa.me/${(contactSettings.whatsapp_number || contactSettings.contact_phone || '').replace(/[^0-9]/g, '')}`" target="_blank" class="text-primary underline">WhatsApp</a>
                            ou à <a :href="`mailto:${contactSettings.contact_email}`" class="text-primary underline">{{ contactSettings.contact_email }}</a>.
                        </p>
                    </div>

                    <!-- Formulaire -->
                    <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                        <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            <p class="font-medium">Merci pour votre générosité !</p>
                            <p>Notre équipe vous contactera pour finaliser votre don.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Montant du don (FCFA) *</label>
                                <div class="grid grid-cols-4 gap-2 mb-3">
                                    <button
                                        v-for="amount in suggestedAmounts"
                                        :key="amount"
                                        type="button"
                                        @click="selectAmount(amount)"
                                        class="px-2 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        :class="form.amount === amount && customAmount === null ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-700 hover:border-primary'"
                                    >
                                        {{ amount.toLocaleString('fr-FR') }}
                                    </button>
                                </div>
                                <input
                                    type="number"
                                    min="500"
                                    step="500"
                                    v-model.number="form.amount"
                                    @input="customAmount = form.amount"
                                    placeholder="Montant libre"
                                    class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                    required
                                />
                                <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Moyen de paiement *</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="m in methods"
                                        :key="m.value"
                                        type="button"
                                        @click="form.method = m.value"
                                        class="px-2 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        :class="form.method === m.value ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-700 hover:border-primary'"
                                    >
                                        {{ m.label }}
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
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                                <textarea id="message" v-model="form.message" rows="3" class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"></textarea>
                            </div>

                            <button
                                type="submit"
                                class="w-full px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-colors"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Envoi en cours...</span>
                                <span v-else>Je fais un don</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

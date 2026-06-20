<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed } from 'vue';

const form = useForm({ email: '' });
const page = usePage();

const flash = computed(() => page.props.flash as Record<string, string> | null);

const submit = () => {
    form.post(route('prospect.portal.send-link'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Mon espace inscrit — Sophie Weddings Dreams" />
    <LayoutFront>
        <section class="min-h-screen bg-gray-50 flex items-center justify-center py-20 px-4">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Header doré -->
                    <div class="bg-[#aa6808] px-8 py-8 text-white text-center">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold">Mon espace inscrit</h1>
                        <p class="text-white/80 text-sm mt-1">Accédez à vos inscriptions & attestations</p>
                    </div>

                    <!-- Corps -->
                    <div class="px-8 py-8">
                        <!-- Flash messages -->
                        <div v-if="flash?.success" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-700 flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ flash.success }}</span>
                        </div>

                        <div v-if="flash?.error" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                            {{ flash.error }}
                        </div>

                        <div v-if="flash?.info" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                            {{ flash.info }}
                        </div>

                        <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                            Entrez l'adresse email utilisée lors de votre inscription.
                            Vous recevrez un lien de connexion sécurisé valable <strong>1 heure</strong>.
                        </p>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adresse email
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    placeholder="votre@email.com"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#aa6808] focus:ring-2 focus:ring-[#aa6808]/20 outline-none transition text-sm"
                                />
                                <p v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-[#aa6808] hover:bg-[#8a5206] text-white font-semibold py-3 rounded-lg transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
                            >
                                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span>{{ form.processing ? 'Envoi en cours…' : 'Recevoir mon lien de connexion' }}</span>
                            </button>
                        </form>

                        <p class="text-center text-xs text-gray-400 mt-6">
                            Vous n'avez pas encore de compte ?
                            <a :href="route('masterclasses')" class="text-[#aa6808] hover:underline">Voir nos masterclasses</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </LayoutFront>
</template>

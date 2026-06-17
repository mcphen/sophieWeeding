<template>
    <LayoutFront>
        <div class="bg-gray-50 min-h-screen py-16">
            <div class="max-w-2xl mx-auto px-4">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="bg-[#d1922f] px-8 py-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-serif font-bold text-white">Inscription confirmée !</h1>
                        <p class="text-white/80 mt-2">Merci {{ registration.name }}, votre demande a bien été enregistrée.</p>
                    </div>

                    <!-- Récapitulatif -->
                    <div class="p-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-5">Récapitulatif</h2>

                        <dl class="space-y-4 divide-y divide-gray-100">
                            <div class="flex justify-between pt-4 first:pt-0">
                                <dt class="text-sm text-gray-500">Masterclass</dt>
                                <dd class="text-sm font-medium text-gray-900 text-right">{{ masterclass.title }}</dd>
                            </div>
                            <div class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Niveau</dt>
                                <dd>
                                    <span class="inline-block bg-[#d1922f]/10 text-[#d1922f] text-xs font-semibold px-2 py-0.5 rounded-full">{{ masterclass.niveau }}</span>
                                </dd>
                            </div>
                            <div class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Date</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ session.start_date }} à {{ session.start_time }}</dd>
                            </div>
                            <div class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Format</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ session.location_label }}</dd>
                            </div>
                            <div v-if="session.adresse" class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Lieu</dt>
                                <dd class="text-sm font-medium text-gray-900 text-right">{{ session.adresse }}</dd>
                            </div>
                            <div class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Email de contact</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ registration.email }}</dd>
                            </div>
                            <div class="flex justify-between pt-4">
                                <dt class="text-sm text-gray-500">Date d'inscription</dt>
                                <dd class="text-sm text-gray-600">{{ registration.created_at }}</dd>
                            </div>
                        </dl>

                        <div class="mt-8 bg-blue-50 rounded-lg p-4 text-sm text-blue-700">
                            <p class="font-medium">Un email de confirmation vous a été envoyé à {{ registration.email }}.</p>
                            <p class="mt-1 text-blue-600">Nous vous contacterons prochainement pour vous transmettre les informations de connexion ou d'accès.</p>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <Link :href="route('masterclasses')" class="flex-1 text-center px-4 py-2 border border-[#d1922f] text-[#d1922f] rounded-md hover:bg-[#d1922f]/5 text-sm font-medium">
                                Voir toutes les masterclasses
                            </Link>
                            <Link :href="route('home')" class="flex-1 text-center px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#c08529] text-sm font-medium">
                                Retour à l'accueil
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

defineProps<{
    registration: { id: number; name: string; email: string; created_at: string };
    masterclass: { title: string; niveau: string };
    session: { start_date: string; start_time: string; location_label: string; adresse: string | null };
}>();
</script>

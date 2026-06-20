<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

const props = defineProps<{
    valid: boolean;
    name?: string;
    training?: string;
    niveau?: string;
    date?: string;
    issued_at?: string;
}>();
</script>

<template>
    <Head title="Vérification de certificat — Sophie Weddings Dreams" />
    <LayoutFront>
        <div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
            <div class="max-w-lg w-full text-center">

                <!-- Invalide -->
                <template v-if="!valid">
                    <div class="text-6xl mb-6">✗</div>
                    <h1 class="text-2xl font-bold text-red-600 mb-3">Certificat invalide</h1>
                    <p class="text-gray-500">Ce certificat n'a pas pu être vérifié. Il est peut-être invalide ou a été altéré.</p>
                </template>

                <!-- Valide -->
                <template v-else>
                    <div class="text-6xl mb-6" style="color: #c9a84c;">✓</div>
                    <h1 class="text-2xl font-bold mb-2" style="color: #c9a84c;">Certificat authentique</h1>
                    <p class="text-gray-500 mb-8 text-sm">Ce certificat est authentique et a été délivré par Sophie Weddings Dreams.</p>

                    <div class="border border-gray-200 rounded-xl p-6 text-left space-y-4">
                        <div>
                            <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">Bénéficiaire</div>
                            <div class="text-xl font-semibold text-gray-800">{{ name }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">Formation</div>
                            <div class="font-medium text-gray-700">{{ training }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">Niveau</div>
                                <div class="font-medium text-gray-700">{{ niveau }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">Date de session</div>
                                <div class="font-medium text-gray-700">{{ date }}</div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100 text-xs text-gray-400">
                            Émis par <strong>Sophie Weddings Dreams / S.W.D. Academy</strong> · Dakar, Sénégal
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </LayoutFront>
</template>

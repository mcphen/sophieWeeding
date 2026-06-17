<template>
    <Head title="Paramètres Attestation" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 max-w-xl">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Signature de l'attestation</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Si une signature est configurée ici, elle sera automatiquement intégrée au PDF et
                    envoyée en pièce jointe lors de la confirmation d'une participation.
                    Sans signature, l'email notifie simplement le participant et il peut télécharger
                    le PDF (non signé) depuis son espace.
                </p>

                <!-- Aperçu signature actuelle -->
                <div v-if="signatureUrl" class="mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Signature actuelle</p>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 inline-block">
                        <img :src="signatureUrl" alt="Signature" class="max-h-24 object-contain" />
                    </div>
                    <div class="mt-3">
                        <button
                            type="button"
                            @click="removeSignature"
                            :disabled="processing"
                            class="text-sm text-red-600 hover:underline disabled:opacity-50"
                        >
                            Supprimer la signature
                        </button>
                    </div>
                </div>

                <div v-else class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                    Aucune signature configurée — mode manuel actif. L'admin doit télécharger, signer et envoyer manuellement.
                </div>

                <!-- Upload nouvelle signature -->
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ signatureUrl ? 'Remplacer la signature' : 'Importer une signature' }}
                        </label>
                        <p class="text-xs text-gray-400 mb-2">Image PNG ou JPG, fond transparent recommandé. Max 2 Mo.</p>
                        <input
                            type="file"
                            accept="image/png,image/jpeg"
                            @change="onFileChange"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-[#d1922f] file:text-white hover:file:bg-[#b87e28]"
                        />
                        <p v-if="errors.signature" class="mt-1 text-xs text-red-600">{{ errors.signature }}</p>
                    </div>

                    <!-- Aperçu avant upload -->
                    <div v-if="preview" class="mb-4 border border-gray-200 rounded-lg p-4 bg-gray-50 inline-block">
                        <p class="text-xs text-gray-400 mb-1">Aperçu</p>
                        <img :src="preview" alt="Aperçu" class="max-h-20 object-contain" />
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="submit"
                            :disabled="!file || processing"
                            class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50"
                        >
                            {{ processing ? 'Enregistrement…' : 'Enregistrer la signature' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

const props = defineProps<{
    signature_url: string | null;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Paramètres', href: route('admin.settings.index') },
    { title: 'Attestation', href: '#' },
];

const file = ref<File | null>(null);
const preview = ref<string | null>(null);
const processing = ref(false);
const errors = ref<{ signature?: string }>({});

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    file.value = input.files?.[0] ?? null;
    if (file.value) {
        preview.value = URL.createObjectURL(file.value);
    }
};

const submit = () => {
    if (!file.value) return;
    errors.value = {};
    processing.value = true;

    const formData = new FormData();
    formData.append('signature', file.value);

    router.post(route('admin.attestation-settings.update'), formData, {
        forceFormData: true,
        onError: (e) => { errors.value = e; },
        onFinish: () => { processing.value = false; file.value = null; preview.value = null; },
    });
};

const removeSignature = () => {
    processing.value = true;
    router.post(route('admin.attestation-settings.update'), { remove_signature: true }, {
        onFinish: () => { processing.value = false; },
    });
};
</script>

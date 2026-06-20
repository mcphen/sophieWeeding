<template>
    <Head title="Paramètres Attestation" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 max-w-xl">

            <!-- Signature -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Signature électronique</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Si une signature est configurée ici, elle sera automatiquement intégrée au certificat PDF.
                    Sans signature, le participant est notifié et peut télécharger le PDF depuis son espace.
                </p>

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
                    Aucune signature configurée — mode manuel actif.
                </div>

                <form @submit.prevent="submitSignature" enctype="multipart/form-data">
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

                    <div v-if="preview" class="mb-4 border border-gray-200 rounded-lg p-4 bg-gray-50 inline-block">
                        <p class="text-xs text-gray-400 mb-1">Aperçu</p>
                        <img :src="preview" alt="Aperçu" class="max-h-20 object-contain" />
                    </div>

                    <button
                        type="submit"
                        :disabled="!file || processing"
                        class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50"
                    >
                        {{ processing ? 'Enregistrement…' : 'Enregistrer la signature' }}
                    </button>
                </form>
            </div>

            <!-- Informations du certificat -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Informations du certificat</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Ces informations apparaissent en bas du certificat PDF sous la signature.
                </p>

                <form @submit.prevent="submitInfo" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du signataire</label>
                        <input
                            v-model="directorName"
                            type="text"
                            placeholder="Sophie Manca"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre / Fonction</label>
                        <input
                            v-model="directorTitle"
                            type="text"
                            placeholder="Directrice"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville (affiché sur le certificat)</label>
                        <input
                            v-model="city"
                            type="text"
                            placeholder="Dakar (Sénégal)"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d1922f]"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="infoProcessing"
                        class="px-4 py-2 bg-[#d1922f] text-white text-sm font-medium rounded-md hover:bg-[#b87e28] disabled:opacity-50"
                    >
                        {{ infoProcessing ? 'Enregistrement…' : 'Enregistrer' }}
                    </button>
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
    director_name: string;
    director_title: string;
    city: string;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Paramètres', href: '#' },
    { title: 'Attestation', href: '#' },
];

// Signature
const signatureUrl = ref(props.signature_url);
const file = ref<File | null>(null);
const preview = ref<string | null>(null);
const processing = ref(false);
const errors = ref<{ signature?: string }>({});

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    file.value = input.files?.[0] ?? null;
    if (file.value) preview.value = URL.createObjectURL(file.value);
};

const submitSignature = () => {
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

// Infos certificat
const directorName  = ref(props.director_name);
const directorTitle = ref(props.director_title);
const city          = ref(props.city);
const infoProcessing = ref(false);

const submitInfo = () => {
    infoProcessing.value = true;
    router.post(route('admin.attestation-settings.update'), {
        director_name:  directorName.value,
        director_title: directorTitle.value,
        city:           city.value,
    }, {
        onFinish: () => { infoProcessing.value = false; },
    });
};
</script>

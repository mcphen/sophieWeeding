<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Type declaration for the route function
declare function route(name: string, params?: Record<string, any>): string;

interface Setting {
    id: number;
    key: string;
    value: string;
    group: string;
    type: string;
    label: string;
    created_at?: string;
    updated_at?: string;
}

interface SettingsObject {
    [key: string]: string;
}

interface FormData {
    cta_from_color: string;
    cta_to_color: string;
    cta_title: string;
    cta_description: string;
    cta_paragraph_color: string;
    cta_link_route: string;
    cta_button_text: string;
    cta_button_text_color: string;
}

const props = defineProps<{
    settings: Setting[]
}>();

// Convert settings array to an object for easier form handling
const settingsObject: SettingsObject = {};
if (props.settings) {
    props.settings.forEach((setting: Setting) => {
        settingsObject[setting.key] = setting.value;
    });
}

// Create form with default values
const form = useForm<FormData>({
    // CTA settings
    cta_from_color: settingsObject['cta_from_color'] || '#d1922f',
    cta_to_color: settingsObject['cta_to_color'] || '#bf8529',
    cta_title: settingsObject['cta_title'] || 'Prêts à planifier le mariage de vos rêves ?',
    cta_description: settingsObject['cta_description'] || 'Contactez-nous dès aujourd\'hui pour une consultation gratuite et commencez à transformer votre vision en réalité.',
    cta_paragraph_color: settingsObject['cta_paragraph_color'] || '#faecd2',
    cta_link_route: settingsObject['cta_link_route'] || 'appointment.create',
    cta_button_text: settingsObject['cta_button_text'] || 'Prendre rendez-vous',
    cta_button_text_color: settingsObject['cta_button_text_color'] || '#d1922f',
});

const submit = () => {
    form.post(route('admin.cta-settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Paramètres CTA" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Paramètres de la section CTA</h1>

                        <form @submit.prevent="submit">
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Couleurs d'arrière-plan</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="cta_from_color" class="block text-sm font-medium text-gray-700">Couleur de début du dégradé</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="cta_from_color"
                                                v-model="form.cta_from_color"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.cta_from_color"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#d1922f"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur de début du dégradé d'arrière-plan
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="cta_to_color" class="block text-sm font-medium text-gray-700">Couleur de fin du dégradé</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="cta_to_color"
                                                v-model="form.cta_to_color"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.cta_to_color"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#bf8529"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur de fin du dégradé d'arrière-plan
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Texte</h2>

                                <div class="grid grid-cols-1 gap-6">
                                    <div class="mb-4">
                                        <label for="cta_title" class="block text-sm font-medium text-gray-700">Titre</label>
                                        <input
                                            id="cta_title"
                                            v-model="form.cta_title"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            placeholder="Prêts à planifier le mariage de vos rêves ?"
                                        >
                                        <p class="mt-1 text-sm text-gray-500">
                                            Titre principal de la section CTA
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="cta_description" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea
                                            id="cta_description"
                                            v-model="form.cta_description"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            placeholder="Contactez-nous dès aujourd'hui pour une consultation gratuite et commencez à transformer votre vision en réalité."
                                        ></textarea>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Texte descriptif sous le titre
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="cta_paragraph_color" class="block text-sm font-medium text-gray-700">Couleur du texte descriptif</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="cta_paragraph_color"
                                                v-model="form.cta_paragraph_color"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.cta_paragraph_color"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#faecd2"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur du texte descriptif (le titre est toujours blanc)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Bouton</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="cta_link_route" class="block text-sm font-medium text-gray-700">Route du lien</label>
                                        <input
                                            id="cta_link_route"
                                            v-model="form.cta_link_route"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            placeholder="appointment.create"
                                        >
                                        <p class="mt-1 text-sm text-gray-500">
                                            Nom de la route ou URL complète pour le bouton
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="cta_button_text" class="block text-sm font-medium text-gray-700">Texte du bouton</label>
                                        <input
                                            id="cta_button_text"
                                            v-model="form.cta_button_text"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            placeholder="Prendre rendez-vous"
                                        >
                                        <p class="mt-1 text-sm text-gray-500">
                                            Texte affiché sur le bouton
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="cta_button_text_color" class="block text-sm font-medium text-gray-700">Couleur du texte du bouton</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="cta_button_text_color"
                                                v-model="form.cta_button_text_color"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.cta_button_text_color"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#d1922f"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur du texte du bouton (le fond du bouton est toujours blanc)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Aperçu</h2>
                                <div class="rounded-2xl overflow-hidden shadow-xl">
                                    <div class="px-8 py-16 sm:px-16 sm:py-20 lg:flex lg:items-center" :style="{ background: `linear-gradient(to right, ${form.cta_from_color}, ${form.cta_to_color})` }">
                                        <div class="lg:w-0 lg:flex-1">
                                            <h2 class="text-3xl font-bold tracking-tight text-white">
                                                {{ form.cta_title }}
                                            </h2>
                                            <p class="mt-4 max-w-3xl text-lg" :style="{ color: form.cta_paragraph_color }">
                                                {{ form.cta_description }}
                                            </p>
                                        </div>
                                        <div class="mt-12 sm:w-full sm:max-w-md lg:mt-0 lg:ml-8 lg:flex-1">
                                            <button
                                                class="flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-full bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10"
                                                :style="{ color: form.cta_button_text_color }"
                                            >
                                                {{ form.cta_button_text }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Enregistrer les paramètres CTA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

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
    primary_color: string;
    primary_light: string;
    primary_dark: string;
    primary_bg_light: string;
    accent_light: string;
    text_dark: string;
    text_light: string;
    gray_light: string;
    gray_medium: string;
    gray_dark: string;
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
    // Color settings
    primary_color: settingsObject['primary_color'] || '#AA6808',
    primary_light: settingsObject['primary_light'] || '#d1922f',
    primary_dark: settingsObject['primary_dark'] || '#8a5406',
    primary_bg_light: settingsObject['primary_bg_light'] || '#fcf5ea',
    accent_light: settingsObject['accent_light'] || '#f3e3c8',
    text_dark: settingsObject['text_dark'] || '#1b1b18',
    text_light: settingsObject['text_light'] || '#ffffff',
    gray_light: settingsObject['gray_light'] || '#f8f8f8',
    gray_medium: settingsObject['gray_medium'] || '#e5e5e5',
    gray_dark: settingsObject['gray_dark'] || '#4b4b4b',
});

const submit = () => {
    form.post(route('admin.color-settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Paramètres de couleur" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Paramètres de couleur</h1>

                        <form @submit.prevent="submit">
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Couleurs primaires</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Couleur primaire</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="primary_color"
                                                v-model="form.primary_color"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.primary_color"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#AA6808"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur principale utilisée sur tout le site
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="primary_light" class="block text-sm font-medium text-gray-700">Couleur primaire claire</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="primary_light"
                                                v-model="form.primary_light"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.primary_light"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#d1922f"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Version plus claire de la couleur primaire
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="primary_dark" class="block text-sm font-medium text-gray-700">Couleur primaire foncée</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="primary_dark"
                                                v-model="form.primary_dark"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.primary_dark"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#8a5406"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Version plus foncée de la couleur primaire
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="primary_bg_light" class="block text-sm font-medium text-gray-700">Fond primaire clair</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="primary_bg_light"
                                                v-model="form.primary_bg_light"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.primary_bg_light"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#fcf5ea"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Version très claire de la couleur primaire pour les arrière-plans
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Couleurs d'accentuation</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="accent_light" class="block text-sm font-medium text-gray-700">Accentuation claire</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="accent_light"
                                                v-model="form.accent_light"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.accent_light"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#f3e3c8"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur d'accentuation claire pour les surlignages et les arrière-plans
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Couleurs de texte</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="text_dark" class="block text-sm font-medium text-gray-700">Texte foncé</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="text_dark"
                                                v-model="form.text_dark"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.text_dark"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#1b1b18"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur de texte foncée
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="text_light" class="block text-sm font-medium text-gray-700">Texte clair</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="text_light"
                                                v-model="form.text_light"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.text_light"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#ffffff"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur de texte claire
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Couleurs grises</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label for="gray_light" class="block text-sm font-medium text-gray-700">Gris clair</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="gray_light"
                                                v-model="form.gray_light"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.gray_light"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#f8f8f8"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Gris clair pour les arrière-plans
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="gray_medium" class="block text-sm font-medium text-gray-700">Gris moyen</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="gray_medium"
                                                v-model="form.gray_medium"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.gray_medium"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#e5e5e5"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Gris moyen pour les bordures et les séparateurs
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="gray_dark" class="block text-sm font-medium text-gray-700">Gris foncé</label>
                                        <div class="flex mt-1">
                                            <input
                                                id="gray_dark"
                                                v-model="form.gray_dark"
                                                type="color"
                                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            >
                                            <input
                                                v-model="form.gray_dark"
                                                type="text"
                                                class="ml-2 flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                                placeholder="#4b4b4b"
                                            >
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Gris foncé pour le texte et les icônes
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Aperçu</h2>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.primary_color, color: form.text_light }">
                                        Couleur primaire
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.primary_light, color: form.text_dark }">
                                        Couleur primaire claire
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.primary_dark, color: form.text_light }">
                                        Couleur primaire foncée
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.primary_bg_light, color: form.text_dark }">
                                        Fond primaire clair
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.accent_light, color: form.text_dark }">
                                        Accentuation claire
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.text_dark, color: form.text_light }">
                                        Texte foncé
                                    </div>
                                    <div class="p-4 rounded-md border" :style="{ backgroundColor: form.text_light, color: form.text_dark }">
                                        Texte clair
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.gray_light, color: form.text_dark }">
                                        Gris clair
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.gray_medium, color: form.text_dark }">
                                        Gris moyen
                                    </div>
                                    <div class="p-4 rounded-md" :style="{ backgroundColor: form.gray_dark, color: form.text_light }">
                                        Gris foncé
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Enregistrer les paramètres de couleur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

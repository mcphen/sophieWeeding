<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, onMounted } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Témoignages', href: route('admin.testimonials.index') },
    { title: 'Nouveau témoignage', href: route('admin.testimonials.create') }
];

// Formulaire Inertia
const form = useForm({
    author_name: '',
    author_title: '',
    content: '',
    image: null as File | null
});

// Prévisualisation de l'image
const imagePreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Quill editor
const editorRef = ref<HTMLDivElement | null>(null);
let editor: Quill;

onMounted(() => {
    if (editorRef.value) {
        editor = new Quill(editorRef.value, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Entrez le contenu du témoignage...'
        });
    }
});

// Fonctions
function handleImageUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] || null;

    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    } else {
        form.image = null;
        imagePreview.value = null;
    }
}

function removeImage() {
    form.image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function submit() {
    if (editor) {
        form.content = editor.root.innerHTML;
    }

    form.post(route('admin.testimonials.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirection gérée par le contrôleur
        }
    });
}
</script>

<template>
    <Head title="Ajouter un témoignage" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec style amélioré -->
            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Ajouter un témoignage</h2>
                <Link
                    :href="route('admin.testimonials.index')"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </Link>
            </div>

            <!-- Formulaire de création avec style amélioré -->
            <form @submit.prevent="submit" class="space-y-6 max-w-4xl mx-auto">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">Remplissez tous les champs nécessaires pour ajouter un nouveau témoignage client.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Colonne gauche -->
                    <div class="space-y-6">
                        <!-- Nom de l'auteur -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="author_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'auteur <span class="text-red-600">*</span></label>
                            <input
                                id="author_name"
                                v-model="form.author_name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Nom et prénom"
                                required
                            >
                            <p v-if="form.errors.author_name" class="mt-2 text-sm text-red-600">{{ form.errors.author_name }}</p>
                        </div>

                        <!-- Titre / Fonction de l'auteur -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="author_title" class="block text-sm font-medium text-gray-700 mb-2">Fonction / Titre</label>
                            <input
                                id="author_title"
                                v-model="form.author_title"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Ex: Directeur Général, Client fidèle, etc."
                            >
                            <p v-if="form.errors.author_title" class="mt-2 text-sm text-red-600">{{ form.errors.author_title }}</p>
                        </div>

                        <!-- Image -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Photo de l'auteur</label>

                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                <!-- Prévisualisation -->
                                <div
                                    v-if="imagePreview"
                                    class="relative w-40 h-40 border rounded-full overflow-hidden"
                                >
                                    <img :src="imagePreview" alt="Prévisualisation" class="w-full h-full object-cover">
                                    <button
                                        @click.prevent="removeImage"
                                        type="button"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Upload -->
                                <div v-if="!imagePreview" class="flex items-center justify-center w-40 h-40 border-2 border-dashed border-gray-300 rounded-full bg-gray-50">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Ajouter une photo</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <input
                                        id="image"
                                        ref="fileInput"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageUpload"
                                    >
                                    <button
                                        type="button"
                                        @click="fileInput?.click()"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition flex items-center justify-center gap-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                        {{ imagePreview ? 'Changer la photo' : 'Sélectionner une photo' }}
                                    </button>
                                    <p class="text-xs text-gray-500">Format recommandé : JPG, PNG (max 2Mo)</p>
                                </div>
                            </div>

                            <p v-if="form.errors.image" class="mt-2 text-sm text-red-600">{{ form.errors.image }}</p>
                        </div>
                    </div>

                    <!-- Colonne droite - Contenu du témoignage avec Quill -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Contenu du témoignage <span class="text-red-600">*</span></label>
                        <div class="quill-container">
                            <div ref="editorRef" class="min-h-[300px] border border-gray-300 rounded-md"></div>
                        </div>
                        <p v-if="form.errors.content" class="mt-2 text-sm text-red-600">{{ form.errors.content }}</p>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex gap-3 pt-6 border-t">
                    <button
                        type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2 shadow-sm"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ form.processing ? 'Enregistrement...' : 'Enregistrer le témoignage' }}
                    </button>

                    <Link
                        :href="route('admin.testimonials.index')"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style>
.quill-container .ql-editor {
    min-height: 200px;
    max-height: 500px;
    overflow-y: auto;
}

.quill-container .ql-toolbar {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    background-color: #f9fafb;
    border-color: #e5e7eb;
}

.quill-container .ql-container {
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-color: #e5e7eb;
    background-color: white;
}

.quill-container .ql-toolbar button:hover {
    color: #2563eb;
}

.quill-container .ql-toolbar .ql-active {
    color: #2563eb;
}
</style>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';
import { useEditor, EditorContent, BubbleMenu, FloatingMenu } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

// Définition des fils d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Actualités', href: route('admin.actualites.index') },
    { title: 'Nouvelle actualité', href: route('admin.actualites.create') }
];

// Types pour les blocs de contenu
// Removed unused interfaces

// Formulaire Inertia
const form = useForm({
    title: '',
    description: '', // Stockera le HTML final avec tous les blocs
    image: null as File | null,
    published_at: new Date().toISOString().split('T')[0] // Date du jour par défaut
});

// Prévisualisation de l'image
const imagePreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Éditeur TipTap
const showBubbleMenu = ref(false);
const showFloatingMenu = ref(false);

// Initialiser l'éditeur TipTap avec useEditor (recommandé par la documentation)
const editor = useEditor({
    extensions: [
        StarterKit,
        Image,
        Placeholder.configure({
            placeholder: 'Commencez à écrire votre article ici...',
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    content: '',
    onUpdate: ({ editor }) => {
        form.description = editor.getHTML();
    },
    onSelectionUpdate: ({ editor }) => {
        showBubbleMenu.value = editor.isActive('bold') || editor.isActive('italic') || editor.isActive('heading') || editor.isActive('bulletList');
        showFloatingMenu.value = editor.isFocused && editor.isEmpty;
    },
});

// Fonctions pour l'éditeur
const addImage = () => {
    const url = window.prompt('URL de l\'image');
    if (url && editor.value && editor.isReady.value) {
        editor.value.chain().focus().setImage({ src: url }).run();
    }
};

const addTimeline = () => {
    const timelineHtml = `
        <div class="timeline-block">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">Date</div>
                    <div class="timeline-connector"></div>
                    <div class="timeline-content">Contenu</div>
                </div>
            </div>
        </div>
    `;
    if (editor.value && editor.isReady.value) {
        editor.value.chain().focus().insertContent(timelineHtml).run();
    }
};

const addGallery = () => {
    const galleryHtml = `
        <div class="gallery-block">
            <div class="gallery">
                <div class="gallery-item">
                    <img src="https://via.placeholder.com/150" alt="Image de galerie">
                </div>
            </div>
        </div>
    `;
    if (editor.value && editor.isReady.value) {
        editor.value.chain().focus().insertContent(galleryHtml).run();
    }
};

// Fonctions pour l'image principale
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

// Soumettre le formulaire
function submit() {
    // Soumettre le formulaire
    form.post(route('admin.actualites.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirection gérée par le contrôleur
        },
        onError: (errors) => {
            // Scroll to the first error
            const firstError = Object.keys(errors)[0];
            if (firstError) {
                const element = document.getElementById(firstError);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.focus();
                }
            }
        }
    });
}
</script>

<template>
    <Head title="Ajouter une actualité" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec style amélioré -->
            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Ajouter une actualité</h2>
                <Link
                    :href="route('admin.actualites.index')"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </Link>
            </div>

            <!-- Formulaire de création avec style amélioré -->
            <div class="actualite-form-wrapper">
                <!-- FORM START - UNIQUE IDENTIFIER -->
                <form id="actualite-create-form" @submit.prevent="submit" class="space-y-6 max-w-4xl mx-auto">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">Créez votre article avec notre nouvel éditeur style WordPress. Ajoutez du texte, des images, des chronologies et des galeries n'importe où dans votre contenu.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Colonne gauche -->
                        <div class="space-y-6">
                            <!-- Titre de l'actualité -->
                            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                                <Label for="title" class="flex items-center gap-1">
                                    Titre de l'actualité
                                    <span class="text-red-600">*</span>
                                    <span class="ml-1 text-xs text-gray-500">(Ce titre apparaîtra en haut de votre article)</span>
                                </Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1.5 w-full"
                                    placeholder="Titre accrocheur de votre actualité"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Date de publication -->
                            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                                <Label for="published_at" class="flex items-center gap-1">
                                    Date de publication
                                    <span class="text-red-600">*</span>
                                    <span class="ml-1 text-xs text-gray-500">(Date à laquelle l'article sera visible)</span>
                                </Label>
                                <Input
                                    id="published_at"
                                    v-model="form.published_at"
                                    type="date"
                                    class="mt-1.5 w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.published_at" />
                            </div>

                            <!-- Image -->
                            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                                <Label for="image" class="flex items-center gap-1">
                                    Image de l'actualité
                                    <span class="ml-1 text-xs text-gray-500">(Image principale qui apparaîtra en haut de l'article)</span>
                                </Label>

                                <div class="flex flex-col sm:flex-row items-center gap-5 mt-3">
                                    <!-- Prévisualisation -->
                                    <div
                                        v-if="imagePreview"
                                        class="relative w-48 h-48 border rounded-lg overflow-hidden shadow-md"
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
                                    <div v-if="!imagePreview" class="flex items-center justify-center w-48 h-48 border-2 border-dashed border-blue-300 rounded-lg bg-blue-50">
                                        <div class="text-center p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm text-blue-600 font-medium">Ajouter une image</p>
                                            <p class="text-xs text-blue-500 mt-1">Cliquez pour sélectionner</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3">
                                        <input
                                            id="image"
                                            ref="fileInput"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleImageUpload"
                                        >
                                        <Button
                                            type="button"
                                            @click="fileInput?.click()"
                                            variant="outline"
                                            class="flex items-center justify-center gap-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ imagePreview ? 'Changer l\'image' : 'Sélectionner une image' }}
                                        </Button>
                                        <div class="text-sm text-gray-600 space-y-1">
                                            <p class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Format : JPG, PNG
                                            </p>
                                            <p class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Taille max : 2 Mo
                                            </p>
                                            <p class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Ratio idéal : 16:9
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <InputError class="mt-2" :message="form.errors.image" />
                            </div>
                        </div>

                        <!-- Colonne droite - Éditeur de contenu style WordPress -->
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                            <Label class="flex items-center gap-1 mb-4">
                                Contenu de l'actualité
                                <span class="ml-1 text-xs text-gray-500">(Créez votre article avec différents types de blocs)</span>
                            </Label>

                            <!-- Guide d'utilisation -->
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700 font-medium">Comment créer votre article :</p>
                                        <ul class="mt-1 text-sm text-blue-600 list-disc list-inside">
                                            <li>Cliquez n'importe où dans l'éditeur pour commencer à écrire</li>
                                            <li>Utilisez le menu flottant pour ajouter des blocs spéciaux</li>
                                            <li>Sélectionnez du texte pour le mettre en forme</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Éditeur TipTap -->
                            <div class="editor-wrapper min-h-[400px] border border-gray-200 rounded-md overflow-hidden">
                                <!-- Menu bulle pour la mise en forme du texte -->
                                <template v-if="editor && editor.isReady.value && showBubbleMenu">
                                    <BubbleMenu :editor="editor.value" class="bubble-menu bg-white shadow-lg rounded-md border border-gray-200 p-1 flex items-center gap-1">
                                        <button @click="editor.value.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.value.isActive('bold') }" class="p-1.5 rounded hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="editor.value.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.value.isActive('italic') }" class="p-1.5 rounded hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" />
                                            </svg>
                                        </button>
                                        <button @click="editor.value.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.value.isActive('heading', { level: 2 }) }" class="p-1.5 rounded hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="editor.value.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.value.isActive('bulletList') }" class="p-1.5 rounded hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </BubbleMenu>
                                </template>

                                <!-- Menu flottant pour ajouter des blocs -->
                                <template v-if="editor && editor.isReady.value && showFloatingMenu">
                                    <FloatingMenu :editor="editor.value" class="floating-menu bg-white shadow-lg rounded-md border border-gray-200 p-2 flex flex-col gap-2">
                                        <button @click="addImage" class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 text-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Ajouter une image</span>
                                        </button>
                                        <button @click="addTimeline" class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 text-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Ajouter une chronologie</span>
                                        </button>
                                        <button @click="addGallery" class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 text-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm9 4a1 1 0 10-2 0v6a1 1 0 102 0V7zm-3 2a1 1 0 10-2 0v4a1 1 0 102 0V9zm-3 3a1 1 0 10-2 0v1a1 1 0 102 0v-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Ajouter une galerie</span>
                                        </button>
                                    </FloatingMenu>
                                </template>

                                <!-- Contenu de l'éditeur -->
                                <EditorContent v-if="editor && editor.isReady.value" :editor="editor.value" class="prose max-w-none p-4 min-h-[400px]" />
                            </div>

                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t border-gray-200">
                        <!-- Résumé de l'article -->
                        <div class="flex-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Résumé de votre article</h3>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Titre:</span> {{ form.title || 'Non défini' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Date de publication:</span> {{ form.published_at || 'Non définie' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Image principale:</span> {{ form.image ? 'Définie' : 'Non définie' }}
                                </p>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                            <Button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Enregistrement...' : 'Enregistrer l\'actualité' }}</span>
                            </Button>
                            <Link
                                :href="route('admin.actualites.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-center"
                            >
                                Annuler
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Styles pour l'éditeur TipTap */
.editor-wrapper {
    position: relative;
}

.bubble-menu {
    z-index: 100;
}

.floating-menu {
    z-index: 100;
}

/* Styles pour les blocs spéciaux */
.timeline-block {
    margin: 2rem 0;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background-color: #f9fafb;
}

.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    margin-bottom: 1rem;
}

.timeline-date {
    flex: 0 0 30%;
    font-weight: 600;
    color: #4b5563;
}

.timeline-connector {
    position: relative;
    flex: 0 0 20px;
}

.timeline-connector::before {
    content: '';
    position: absolute;
    top: 0.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #3b82f6;
}

.timeline-connector::after {
    content: '';
    position: absolute;
    top: 1rem;
    bottom: -1.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    background-color: #e5e7eb;
}

.timeline-content {
    flex: 1;
    padding-left: 1rem;
}

.gallery-block {
    margin: 2rem 0;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background-color: #f9fafb;
}

.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.gallery-item {
    border-radius: 0.375rem;
    overflow: hidden;
}

.gallery-item img {
    width: 100%;
    height: auto;
    object-fit: cover;
}
</style>

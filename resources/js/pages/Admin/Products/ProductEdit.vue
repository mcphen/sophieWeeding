<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, onMounted } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// Type pour un produit
interface ProductImage {
    id: number;
    image_path: string;
    image_url: string;
    order: number;
}

interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    image_path: string | null;
    images: ProductImage[];
}

// Props
interface Props {
    product: Product;
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produits', href: route('admin.products.index') },
    { title: 'Modifier le produit', href: route('admin.products.edit', props.product.id) }
];

// Formulaire Inertia
const form = useForm({
    title: props.product.title,
    description: props.product.description || '',
    image: null as File | null,
    images: [] as File[],
    delete_images: [] as number[],
    price: props.product.price,
    _method: 'PUT',
});

// Prévisualisation des images
const imagePreview = ref<string | null>(props.product.image_path ? `/storage/${props.product.image_path}` : null);
const fileInput = ref<HTMLInputElement | null>(null);
const multipleFileInput = ref<HTMLInputElement | null>(null);
const existingImages = ref<ProductImage[]>(props.product.images || []);
const imagePreviews = ref<{file: File, preview: string}[]>([]);

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
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: 'Décrivez votre produit en détail...'
        });

        if (props.product.description) {
            editor.root.innerHTML = props.product.description;
        }
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
        imagePreview.value = props.product.image_path ? `/storage/${props.product.image_path}` : null;
    }
}

function handleMultipleImageUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const files = target.files;

    if (files && files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = (e) => {
                imagePreviews.value.push({
                    file: file,
                    preview: e.target?.result as string
                });
                form.images.push(file);
            };

            reader.readAsDataURL(file);
        }
    }
}

function removeImage() {
    form.image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function removeMultipleImage(index: number) {
    imagePreviews.value.splice(index, 1);
    form.images.splice(index, 1);
}

function removeExistingImage(imageId: number) {
    form.delete_images.push(imageId);
    existingImages.value = existingImages.value.filter(img => img.id !== imageId);
}

function submit() {
    if (editor) {
        form.description = editor.root.innerHTML;
    }

    form.post(route('admin.products.update', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirection gérée par le contrôleur
        }
    });
}
</script>

<template>
    <Head title="Modifier le produit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec style amélioré -->
            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Modifier le produit</h2>
                <Link
                    :href="route('admin.products.index')"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </Link>
            </div>

            <!-- Formulaire de modification avec style amélioré -->
            <form @submit.prevent="submit" class="space-y-6 max-w-4xl mx-auto">
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">Vous modifiez le produit <strong>"{{ props.product.title }}"</strong>. Tous les changements seront immédiatement visibles.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Colonne gauche -->
                    <div class="space-y-6">
                        <!-- Nom du produit -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Nom du produit <span class="text-red-600">*</span></label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Nom du produit"
                                required
                            >
                            <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <!-- Prix -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">FCFA</span>
                                </div>
                                <input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="1"
                                    min="0"
                                    class="w-full pl-14 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    placeholder="0"
                                    required
                                >
                            </div>
                            <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">{{ form.errors.price }}</p>
                        </div>

                        <!-- Image principale -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image principale du produit</label>

                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                <!-- Prévisualisation -->
                                <div
                                    v-if="imagePreview"
                                    class="relative w-40 h-40 border rounded-lg overflow-hidden"
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

                                <!-- Upload si pas d'image -->
                                <div v-if="!imagePreview" class="flex items-center justify-center w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Ajouter une image principale</p>
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
                                        {{ imagePreview ? 'Changer l\'image principale' : 'Sélectionner l\'image principale' }}
                                    </button>
                                    <p class="text-xs text-gray-500">Format recommandé : JPG, PNG (max 2Mo)</p>
                                </div>
                            </div>

                            <p v-if="form.errors.image" class="mt-2 text-sm text-red-600">{{ form.errors.image }}</p>
                        </div>

                        <!-- Images additionnelles existantes -->
                        <div v-if="existingImages.length > 0 || imagePreviews.length > 0" class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Images additionnelles existantes</label>

                            <!-- Images existantes -->
                            <div v-if="existingImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                                <div
                                    v-for="image in existingImages"
                                    :key="image.id"
                                    class="relative w-full aspect-square border rounded-lg overflow-hidden"
                                >
                                    <img :src="image.image_url" :alt="'Image ' + image.id" class="w-full h-full object-cover">
                                    <button
                                        @click.prevent="removeExistingImage(image.id)"
                                        type="button"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Nouvelles images à ajouter -->
                            <div v-if="imagePreviews.length > 0" class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Nouvelles images à ajouter</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <div
                                        v-for="(preview, index) in imagePreviews"
                                        :key="index"
                                        class="relative w-full aspect-square border rounded-lg overflow-hidden"
                                    >
                                        <img :src="preview.preview" :alt="`Nouvelle image ${index + 1}`" class="w-full h-full object-cover">
                                        <button
                                            @click.prevent="removeMultipleImage(index)"
                                            type="button"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ajouter de nouvelles images -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <label for="multiple-images" class="block text-sm font-medium text-gray-700 mb-2">Ajouter des images additionnelles</label>

                            <div class="flex flex-col gap-2">
                                <input
                                    id="multiple-images"
                                    ref="multipleFileInput"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="hidden"
                                    @change="handleMultipleImageUpload"
                                >
                                <button
                                    type="button"
                                    @click="multipleFileInput?.click()"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition flex items-center justify-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                    Ajouter des images
                                </button>
                                <p class="text-xs text-gray-500">Vous pouvez sélectionner plusieurs images (max 2Mo chacune)</p>
                            </div>

                            <p v-if="form.errors.images" class="mt-2 text-sm text-red-600">{{ form.errors.images }}</p>
                        </div>
                    </div>

                    <!-- Colonne droite - Description avec Quill -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description du produit</label>
                        <div class="quill-container">
                            <div ref="editorRef" class="min-h-[300px] border border-gray-300 rounded-md"></div>
                        </div>
                        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
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
                        {{ form.processing ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                    </button>

                    <Link
                        :href="route('admin.products.index')"
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

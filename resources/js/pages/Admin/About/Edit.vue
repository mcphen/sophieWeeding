<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'À propos', href: route('admin.about.edit') }
];

// Récupération des props
const page = usePage<{ about: { content: string, image_path?: string, image_url?: string }, flash: { success?: string } }>();
const flash = page.props.flash || {};
const about = page.props.about || {};

// Formulaire Inertia
const form = useForm<{ content: string, image: File | null }>({
    content: about.content || '',
    image: null
});

// Référence pour l'aperçu de l'image
const imagePreview = ref<string | null>(about.image_url || null);

// Référence pour l'éditeur Quill
const editorRef = ref<HTMLDivElement | null>(null);
let editor: Quill;

// Initialisation de Quill
onMounted(() => {
    if (editorRef.value) {
        editor = new Quill(editorRef.value, { theme: 'snow' });
        editor.root.innerHTML = form.content;
    }
});

// Fonction pour gérer le changement d'image
function handleImageChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        form.image = input.files[0];

        // Créer un aperçu de l'image
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Soumission du form
function submit() {
    form.content = editor.root.innerHTML;
    form.post(route('admin.about.update'), {
        preserveState: true,
        forceFormData: true
    });
}
</script>

<template>
    <Head title="À propos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-white">
            <h2 class="text-2xl font-semibold">Modifier « À propos »</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Image upload section -->
                <div>
                    <label for="image" class="block text-sm font-medium mb-1">Image</label>
                    <div class="flex flex-col space-y-2">
                        <!-- Image preview -->
                        <div v-if="imagePreview" class="mb-2">
                            <img :src="imagePreview" alt="Aperçu de l'image" class="max-w-xs max-h-48 object-contain border rounded">
                        </div>

                        <!-- File input -->
                        <input
                            type="file"
                            id="image"
                            @change="handleImageChange"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100"
                        />
                        <p class="text-xs text-gray-500">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                        <p v-if="form.errors.image" class="text-red-600 text-sm">{{ form.errors.image }}</p>
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium mb-1">Contenu</label>
                    <!-- Élément pour Quill -->
                    <div ref="editorRef" class="min-h-[200px] border rounded"></div>
                    <p v-if="form.errors.content" class="text-red-600 text-sm mt-1">{{ form.errors.content }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 text-white rounded flex items-center justify-center"
                >
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>{{ form.processing ? 'Enregistrement...' : 'Sauvegarder' }}</span>
                </button>

                <div v-if="flash?.success" class="mt-2 p-2 bg-green-100 text-green-800 rounded">
                    {{ flash.success }}
                </div>
            </form>
        </div>
    </AppLayout>
</template>

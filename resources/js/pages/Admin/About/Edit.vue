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
const page = usePage<{ about: { content: string }, flash: { success?: string } }>();
const flash = page.props.flash || {};


// Formulaire Inertia
const form = useForm<{ content: string }>({
    content: page.props.about?.content || ''
});

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

// Soumission du form
function submit() {
    form.content = editor.root.innerHTML;
    form.post(route('admin.about.update'), { preserveState: true });
}
</script>

<template>
    <Head title="À propos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-white">
            <h2 class="text-2xl font-semibold">Modifier « À propos »</h2>

            <form @submit.prevent="submit" class="space-y-4">
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

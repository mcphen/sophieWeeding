<template>
    <Head title="Nouvelle masterclass" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle masterclass</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                        <input v-model="form.title" type="text" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                        <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Niveau *</label>
                        <input v-model="form.niveau" type="text" required placeholder="ex: Niveau 1, Avancé…" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]" />
                        <p v-if="form.errors.niveau" class="text-red-600 text-sm mt-1">{{ form.errors.niveau }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <p class="text-xs text-gray-400 mb-1">Résumé affiché dans les cartes et aperçus — texte simple recommandé.</p>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programme détaillé</label>
                    <div class="quill-container border border-gray-300 rounded-md overflow-hidden">
                        <div ref="programmeEditorRef"></div>
                    </div>
                    <p v-if="form.errors.programme" class="text-red-600 text-sm mt-1">{{ form.errors.programme }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image de couverture</label>
                        <input type="file" accept="image/*" @change="e => form.image = (e.target as HTMLInputElement).files?.[0] ?? null" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-[#d1922f] file:text-white" />
                        <p v-if="form.errors.image" class="text-red-600 text-sm mt-1">{{ form.errors.image }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programme PDF</label>
                        <input type="file" accept=".pdf" @change="e => form.document = (e.target as HTMLInputElement).files?.[0] ?? null" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-gray-200 file:text-gray-700" />
                        <p v-if="form.errors.document" class="text-red-600 text-sm mt-1">{{ form.errors.document }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input id="is_active" type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]" />
                    <label for="is_active" class="text-sm font-medium text-gray-700">Masterclass active (visible sur le site)</label>
                </div>

                <!-- Section annonce par email -->
                <div class="border border-[#e8d5b0] rounded-lg bg-[#fdf8f0] p-4 space-y-4">
                    <div class="flex items-start gap-3">
                        <input id="send_announce" type="checkbox" v-model="form.send_announce" class="mt-0.5 rounded border-gray-300 text-[#d1922f] focus:ring-[#d1922f]" />
                        <div>
                            <label for="send_announce" class="text-sm font-semibold text-gray-800 cursor-pointer">
                                Envoyer une annonce par email
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Un email sera envoyé à tous les anciens participants (toutes masterclasses confondues) ainsi qu'aux adresses saisies manuellement.
                            </p>
                        </div>
                    </div>

                    <div v-if="form.send_announce">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Adresses email supplémentaires
                            <span class="font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <textarea
                            v-model="form.manual_emails"
                            rows="3"
                            placeholder="exemple@email.com, autre@email.com&#10;ou une adresse par ligne"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm font-mono"
                        ></textarea>
                        <p class="text-xs text-gray-400 mt-1">Séparez les adresses par une virgule, un point-virgule ou un retour à la ligne.</p>
                        <p v-if="form.errors.manual_emails" class="text-red-600 text-sm mt-1">{{ form.errors.manual_emails }}</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#c08529] disabled:opacity-50 font-medium">
                        {{ form.processing ? 'Création en cours…' : 'Créer la masterclass' }}
                    </button>
                    <Link :href="route('admin.masterclasses.index')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses', href: route('admin.masterclasses.index') },
    { title: 'Nouvelle', href: '#' },
];

const form = useForm({
    title: '',
    niveau: '',
    description: '',
    programme: '',
    image: null as File | null,
    document: null as File | null,
    is_active: true,
    send_announce: false,
    manual_emails: '',
});

const programmeEditorRef = ref<HTMLDivElement | null>(null);
let programmeEditor: Quill | null = null;

onMounted(() => {
    if (programmeEditorRef.value) {
        programmeEditor = new Quill(programmeEditorRef.value, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ header: [2, 3, false] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean'],
                ],
            },
            placeholder: 'Décrivez le contenu et le déroulé de la masterclass…',
        });
    }
});

const submit = () => {
    if (programmeEditor) {
        const html = programmeEditor.root.innerHTML;
        form.programme = html === '<p><br></p>' ? '' : html;
    }
    form.post(route('admin.masterclasses.store'), { forceFormData: true });
};
</script>

<style>
.quill-container .ql-toolbar {
    border: none;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}
.quill-container .ql-container {
    border: none;
    font-size: 0.9rem;
}
.quill-container .ql-editor {
    min-height: 200px;
    max-height: 500px;
    overflow-y: auto;
}
</style>

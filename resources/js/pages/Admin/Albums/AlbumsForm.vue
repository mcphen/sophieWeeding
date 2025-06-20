<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed, watch } from 'vue';

// Type pour un album
interface Photo {
    id: number;
    album_id: number;
    image_path: string;
    caption: string | null;
}

interface Album {
    id: number;
    title: string;
    event_date: string;
    theme: string | null;
    created_at?: string;
    updated_at?: string;
    photos?: Photo[];
}

// Props
interface Props {
    album: Album | null;
}

const props = defineProps<Props>();

// Fil d'Ariane
const isEditMode = computed(() => !!props.album?.id);
const pageTitle = computed(() => isEditMode.value ? 'Modifier un album' : 'Créer un album');

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Albums', href: route('admin.albums.index') },
    { title: pageTitle.value, href: isEditMode.value
        ? route('admin.albums.edit', props.album!.id)
        : route('admin.albums.create')
    }
];

// Formulaire
const form = useForm({
    title: props.album?.title || '',
    event_date: props.album?.event_date || new Date().toISOString().split('T')[0],
    theme: props.album?.theme || '',
    photos: [] as File[],
    captions: [] as (string | null)[],
    remove_photos: [] as number[]
});

// Prévisualisation des photos
const fileInputRef = ref<HTMLInputElement | null>(null);
const newPhotosPreviews = ref<{ file: File; preview: string; caption: string }[]>([]);
const removingPhoto = ref<Photo | null>(null);
const showDeletePhotoModal = ref(false);

// Initialisation du formulaire avec les valeurs existantes
watch(() => props.album, (newAlbum) => {
    if (newAlbum) {
        form.title = newAlbum.title;
        form.event_date = newAlbum.event_date;
        form.theme = newAlbum.theme || '';
    }
}, { immediate: true });

// Fonctions pour la gestion des photos
function handlePhotoUpload(event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files?.length) return;

    const files = Array.from(input.files);
    form.photos = [...form.photos, ...files];

    // Créer des prévisualisations
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            newPhotosPreviews.value.push({
                file,
                preview: e.target?.result as string,
                caption: ''
            });
            // Ajouter une légende vide pour chaque nouvelle photo
            form.captions.push('');
        };
        reader.readAsDataURL(file);
    });

    // Réinitialiser l'input pour permettre de sélectionner à nouveau le même fichier
    if (fileInputRef.value) fileInputRef.value.value = '';
}

function removeNewPhoto(index: number) {
    // Supprimer du formulaire
    const fileToRemove = newPhotosPreviews.value[index].file;
    const fileIndex = form.photos.findIndex(f => f === fileToRemove);
    if (fileIndex !== -1) {
        form.photos.splice(fileIndex, 1);
        form.captions.splice(fileIndex, 1);
    }

    // Supprimer de la prévisualisation
    newPhotosPreviews.value.splice(index, 1);
}

function confirmRemoveExistingPhoto(photo: Photo) {
    removingPhoto.value = photo;
    showDeletePhotoModal.value = true;
}

function removeExistingPhoto() {
    if (removingPhoto.value) {
        form.remove_photos.push(removingPhoto.value.id);
        showDeletePhotoModal.value = false;
        removingPhoto.value = null;
    }
}

function cancelRemovePhoto() {
    removingPhoto.value = null;
    showDeletePhotoModal.value = false;
}

function updateNewPhotoCaption(index: number, caption: string) {
    if (form.captions[index] !== undefined) {
        form.captions[index] = caption;
        newPhotosPreviews.value[index].caption = caption;
    }
}

// Soumission du formulaire
function handleSubmit() {
    if (isEditMode.value) {
        form.post(route('admin.albums.update', props.album!.id), {
            forceFormData: true,
            method: 'put'
        });
    } else {
        form.post(route('admin.albums.store'), {
            forceFormData: true
        });
    }
}

// Formatage de la date

</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl bg-white p-6 shadow-sm">
            <!-- En-tête avec titre -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h2>
                    <p class="text-gray-500 mt-1">
                        {{ isEditMode ? 'Modifiez les détails et les photos de votre album' : 'Créez un nouvel album photo' }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.albums.index')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour à la liste
                    </Link>
                </div>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Informations de base -->
                <div class="bg-gray-50 p-6 rounded-lg space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de l'album</h3>

                    <!-- Titre -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre de l'album *</label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <!-- Date de l'événement -->
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">Date de l'événement *</label>
                        <input
                            id="event_date"
                            v-model="form.event_date"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required
                        />
                        <p v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">{{ form.errors.event_date }}</p>
                    </div>

                    <!-- Thème -->
                    <div>
                        <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">Thème de l'album (optionnel)</label>
                        <input
                            id="theme"
                            v-model="form.theme"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        />
                        <p v-if="form.errors.theme" class="mt-1 text-sm text-red-600">{{ form.errors.theme }}</p>
                    </div>
                </div>

                <!-- Section des photos existantes (si en mode édition) -->
                <div v-if="isEditMode && props.album?.photos?.length" class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Photos existantes</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div
                            v-for="photo in props.album?.photos"
                            :key="photo.id"
                            :class="[
                                'relative rounded-lg overflow-hidden border bg-white',
                                form.remove_photos.includes(photo.id) ? 'opacity-50' : ''
                            ]"
                        >
                            <img
                                :src="`/storage/${photo.image_path}`"
                                :alt="photo.caption || 'Photo d\'album'"
                                class="w-full h-48 object-cover"
                            />

                            <div class="p-3">
                                <p v-if="photo.caption" class="text-sm text-gray-700 mb-2">{{ photo.caption }}</p>

                                <div class="flex justify-end">
                                    <button
                                        v-if="!form.remove_photos.includes(photo.id)"
                                        type="button"
                                        @click="confirmRemoveExistingPhoto(photo)"
                                        class="text-red-600 hover:text-red-800 transition text-sm flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        @click="form.remove_photos = form.remove_photos.filter(id => id !== photo.id)"
                                        class="text-green-600 hover:text-green-800 transition text-sm flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Restaurer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section d'upload de nouvelles photos -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ajouter des photos</h3>

                    <div class="mb-4">
                        <label for="photos" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ isEditMode ? 'Ajouter de nouvelles photos' : 'Photos de l\'album *' }}
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-md">
                            <div class="space-y-1 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label
                                        for="file-upload"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500"
                                    >
                                        <span>Télécharger des photos</span>
                                        <input
                                            id="file-upload"
                                            ref="fileInputRef"
                                            name="photos[]"
                                            type="file"
                                            multiple
                                            accept="image/*"
                                            class="sr-only"
                                            @change="handlePhotoUpload"
                                        />
                                    </label>
                                    <p class="pl-1">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 4MB</p>
                            </div>
                        </div>
                        <p v-if="form.errors.photos && form.errors.photos[0]" class="mt-1 text-sm text-red-600">{{ form.errors.photos[0] }}</p>
                    </div>

                    <!-- Prévisualisation des nouvelles photos -->
                    <div v-if="newPhotosPreviews.length > 0" class="mt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3">Nouvelles photos sélectionnées ({{ newPhotosPreviews.length }})</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div
                                v-for="(item, index) in newPhotosPreviews"
                                :key="index"
                                class="relative rounded-lg overflow-hidden border bg-white"
                            >
                                <img
                                    :src="item.preview"
                                    :alt="`Preview ${index + 1}`"
                                    class="w-full h-48 object-cover"
                                />

                                <button
                                    type="button"
                                    @click="removeNewPhoto(index)"
                                    class="absolute top-2 right-2 w-7 h-7 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="p-3">
                                    <div>
                                        <label :for="`caption-${index}`" class="block text-xs font-medium text-gray-700 mb-1">Légende (optionnel)</label>
                                        <input
                                            :id="`caption-${index}`"
                                            v-model="item.caption"
                                            @input="updateNewPhotoCaption(index, item.caption)"
                                            type="text"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md"
                                            placeholder="Ajouter une légende..."
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons du formulaire -->
                <div class="flex justify-end gap-4">
                    <Link
                        :href="route('admin.albums.index')"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ isEditMode ? 'Mettre à jour' : 'Créer l\'album' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>

    <!-- Modal de confirmation de suppression de photo -->
    <div v-if="showDeletePhotoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer cette photo ?
                <br>Cette action sera appliquée lors de l'enregistrement du formulaire.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="cancelRemovePhoto"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex-1 max-w-xs"
                >
                    Annuler
                </button>
                <button
                    @click="removeExistingPhoto"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Marquer pour suppression
                </button>
            </div>
        </div>
    </div>
</template>

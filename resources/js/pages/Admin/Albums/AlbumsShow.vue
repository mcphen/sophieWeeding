<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

// Types pour les photos et albums
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
    created_at: string;
    updated_at: string;
    photos: Photo[];
}

// Props
interface Props {
    album: Album;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Albums', href: route('admin.albums.index') },
    { title: props.album.title, href: route('admin.albums.show', props.album.id) }
];

// Galerie lightbox
const showLightbox = ref(false);
const currentPhotoIndex = ref(0);

function openLightbox(index: number) {
    currentPhotoIndex.value = index;
    showLightbox.value = true;
}

function closeLightbox() {
    showLightbox.value = false;
}

function nextPhoto() {
    if (currentPhotoIndex.value < props.album.photos.length - 1) {
        currentPhotoIndex.value++;
    } else {
        currentPhotoIndex.value = 0; // Boucle vers la première photo
    }
}

function prevPhoto() {
    if (currentPhotoIndex.value > 0) {
        currentPhotoIndex.value--;
    } else {
        currentPhotoIndex.value = props.album.photos.length - 1; // Boucle vers la dernière photo
    }
}

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}
</script>

<template>
    <Head :title="'Album - ' + album.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl bg-white p-6 shadow-sm">
            <!-- En-tête avec titre et boutons d'action -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ album.title }}</h2>
                    <p class="text-gray-500 mt-1">
                        Album créé le {{ formatDate(album.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.albums.index')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </Link>
                    <Link
                        :href="route('admin.albums.edit', album.id)"
                        class="px-4 py-2 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier l'album
                    </Link>
                </div>
            </div>

            <!-- Message de succès -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <!-- Informations de l'album -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-500">Date de l'événement</span>
                        <span class="text-lg text-gray-800">{{ formatDate(album.event_date) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-500">Nombre de photos</span>
                        <span class="text-lg text-gray-800">{{ album.photos.length }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-500">Thème</span>
                        <span class="text-lg text-gray-800">{{ album.theme || 'Non spécifié' }}</span>
                    </div>
                </div>
            </div>

            <!-- Galerie de photos -->
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Photos de l'album</h3>

                <div v-if="album.photos.length === 0" class="bg-gray-50 rounded-lg p-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune photo dans cet album</h3>
                    <p class="text-gray-500 mb-4">Ajoutez des photos en modifiant l'album.</p>
                    <Link
                        :href="route('admin.albums.edit', album.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter des photos
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div
                        v-for="(photo, index) in album.photos"
                        :key="photo.id"
                        class="relative rounded-lg overflow-hidden border bg-white hover:shadow-md transition cursor-pointer"
                        @click="openLightbox(index)"
                    >
                        <img
                            :src="`${photo.image_path}`"
                            :alt="photo.caption || 'Photo d\'album'"
                            class="w-full h-48 object-cover"
                        />

                        <div v-if="photo.caption" class="p-3">
                            <p class="text-sm text-gray-700">{{ photo.caption }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Lightbox pour visualiser les photos -->
    <div
        v-if="showLightbox && album.photos.length > 0"
        class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center"
        @click.self="closeLightbox"
    >
        <div class="relative max-w-4xl w-full h-screen flex items-center justify-center">
            <!-- Image principale -->
            <div class="relative w-full flex items-center justify-center">
                <img
                    :src="`${album.photos[currentPhotoIndex].image_path}`"
                    :alt="album.photos[currentPhotoIndex].caption || 'Photo d\'album'"
                    class="max-h-[80vh] max-w-full object-contain"
                />

                <!-- Légende -->
                <div
                    v-if="album.photos[currentPhotoIndex].caption"
                    class="absolute bottom-0 left-0 right-0 p-4 bg-black bg-opacity-50 text-white"
                >
                    <p>{{ album.photos[currentPhotoIndex].caption }}</p>
                </div>
            </div>

            <!-- Bouton fermer -->
            <button
                @click="closeLightbox"
                class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Bouton précédent -->
            <button
                v-if="album.photos.length > 1"
                @click.stop="prevPhoto"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Bouton suivant -->
            <button
                v-if="album.photos.length > 1"
                @click.stop="nextPhoto"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Indicateur de position -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-black bg-opacity-50 text-white rounded-full">
                {{ currentPhotoIndex + 1 }} / {{ album.photos.length }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Type pour une photo
interface Photo {
    id: number;
    album_id: number;
    image_path: string;
    caption: string | null;
    is_banner: boolean;
    banner_order: number | null;
    created_at: string;
    updated_at: string;
    album: {
        id: number;
        title: string;
    };
    image_url: string;
}

// Props
interface Props {
    photos: Photo[];
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux photos
const photosList = computed(() => props.photos);

// Photos sélectionnées pour la bannière
const selectedBannerPhotos = ref<Photo[]>([]);

// Initialiser les photos sélectionnées
const initSelectedPhotos = () => {
    selectedBannerPhotos.value = props.photos
        .filter(photo => photo.is_banner)
        .sort((a, b) => {
            if (a.banner_order === null) return 1;
            if (b.banner_order === null) return -1;
            return a.banner_order - b.banner_order;
        });
};

// Appeler l'initialisation au chargement
initSelectedPhotos();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Bannière', href: route('admin.banner.index') }
];

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('grid');

// Vérifier si une photo est sélectionnée pour la bannière
const isPhotoSelected = (photo: Photo) => {
    return selectedBannerPhotos.value.some(p => p.id === photo.id);
};

// Ajouter ou retirer une photo de la bannière
const toggleBannerPhoto = (photo: Photo) => {
    if (isPhotoSelected(photo)) {
        // Retirer la photo
        selectedBannerPhotos.value = selectedBannerPhotos.value.filter(p => p.id !== photo.id);
    } else {
        // Limiter à 4 photos maximum
        if (selectedBannerPhotos.value.length >= 4) {
            alert('Vous ne pouvez sélectionner que 4 photos maximum pour la bannière.');
            return;
        }

        // Ajouter la photo avec le prochain ordre disponible
        const nextOrder = selectedBannerPhotos.value.length;
        selectedBannerPhotos.value.push({
            ...photo,
            is_banner: true,
            banner_order: nextOrder
        });
    }
};

// Déplacer une photo vers le haut dans l'ordre
const movePhotoUp = (photo: Photo) => {
    const index = selectedBannerPhotos.value.findIndex(p => p.id === photo.id);
    if (index > 0) {
        // Échanger avec la photo précédente
        const temp = { ...selectedBannerPhotos.value[index - 1] };
        selectedBannerPhotos.value[index - 1] = { ...selectedBannerPhotos.value[index], banner_order: index - 1 };
        selectedBannerPhotos.value[index] = { ...temp, banner_order: index };
    }
};

// Déplacer une photo vers le bas dans l'ordre
const movePhotoDown = (photo: Photo) => {
    const index = selectedBannerPhotos.value.findIndex(p => p.id === photo.id);
    if (index < selectedBannerPhotos.value.length - 1) {
        // Échanger avec la photo suivante
        const temp = { ...selectedBannerPhotos.value[index + 1] };
        selectedBannerPhotos.value[index + 1] = { ...selectedBannerPhotos.value[index], banner_order: index + 1 };
        selectedBannerPhotos.value[index] = { ...temp, banner_order: index };
    }
};

// Formulaire pour enregistrer les modifications
const form = useForm({
    photos: [] as { id: number; is_banner: boolean; banner_order: number | null }[]
});

// Enregistrer les modifications
const saveBannerPhotos = () => {
    // Préparer les données pour l'envoi
    const photosToUpdate = props.photos.map(photo => {
        const selected = selectedBannerPhotos.value.find(p => p.id === photo.id);
        return {
            id: photo.id,
            is_banner: !!selected,
            banner_order: selected ? selected.banner_order : null
        };
    });

    form.photos = photosToUpdate;

    // Envoyer la requête
    form.post(route('admin.banner.update'), {
        onSuccess: () => {
            // Réinitialiser après succès
            initSelectedPhotos();
        }
    });
};
</script>

<template>
    <Head title="Gestion de la bannière" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'enregistrement -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion de la bannière</h2>
                    <p class="text-gray-500 mt-1">
                        Sélectionnez jusqu'à 4 photos pour la bannière de la page d'accueil
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Sélecteur de vue -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Liste
                        </button>
                    </div>

                    <button
                        @click="saveBannerPhotos"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                        :disabled="form.processing"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Enregistrer les modifications
                    </button>
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

            <!-- Photos sélectionnées pour la bannière -->
            <div v-if="selectedBannerPhotos.length > 0" class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Photos sélectionnées pour la bannière ({{ selectedBannerPhotos.length }}/4)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div v-for="(photo, index) in selectedBannerPhotos" :key="photo.id" class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="relative h-48">
                            <img :src="`${photo.image_path}`" :alt="photo.caption || 'Photo de bannière'" class="w-full h-full object-cover" />
                            <div class="absolute top-2 left-2 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-md">
                                {{ index + 1 }}
                            </div>
                        </div>
                        <div class="p-3 flex justify-between items-center">
                            <div class="text-sm text-gray-600 truncate">
                                {{ photo.caption || 'Sans légende' }}
                            </div>
                            <div class="flex gap-1">
                                <button
                                    @click="movePhotoUp(photo)"
                                    :disabled="index === 0"
                                    :class="[
                                        'p-1 rounded-md',
                                        index === 0 ? 'text-gray-300 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50'
                                    ]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button
                                    @click="movePhotoDown(photo)"
                                    :disabled="index === selectedBannerPhotos.length - 1"
                                    :class="[
                                        'p-1 rounded-md',
                                        index === selectedBannerPhotos.length - 1 ? 'text-gray-300 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-50'
                                    ]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button
                                    @click="toggleBannerPhoto(photo)"
                                    class="p-1 rounded-md text-red-600 hover:bg-red-50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toutes les photos disponibles -->
            <div>
                <h3 class="text-lg font-semibold mb-3">Toutes les photos disponibles</h3>

                <!-- Affichage en grille (cards) -->
                <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="photo in photosList" :key="photo.id"
                        class="bg-white border rounded-lg shadow-sm overflow-hidden hover:shadow-md transition"
                        :class="{ 'ring-2 ring-blue-500': isPhotoSelected(photo) }"
                    >
                        <div class="relative h-48">
                            <img :src="`${photo.image_path}`" :alt="photo.caption || 'Photo'" class="w-full h-full object-cover" />
                            <div v-if="isPhotoSelected(photo)" class="absolute top-2 right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                {{ selectedBannerPhotos.findIndex(p => p.id === photo.id) + 1 }}
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600 truncate">
                                    {{ photo.caption || 'Sans légende' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Album: {{ photo.album.title }}
                                </div>
                            </div>
                            <div class="mt-3">
                                <button
                                    @click="toggleBannerPhoto(photo)"
                                    :class="[
                                        'w-full py-2 rounded-md text-sm font-medium transition',
                                        isPhotoSelected(photo)
                                            ? 'bg-blue-50 text-blue-700 hover:bg-blue-100'
                                            : 'bg-gray-50 text-gray-700 hover:bg-gray-100'
                                    ]"
                                >
                                    {{ isPhotoSelected(photo) ? 'Retirer de la bannière' : 'Ajouter à la bannière' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Message si aucune photo trouvée -->
                    <div v-if="photosList.length === 0" class="col-span-full bg-gray-50 rounded-lg p-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune photo trouvée</h3>
                        <p class="text-gray-500 mb-4">Commencez par ajouter des photos dans vos albums.</p>
                    </div>
                </div>

                <!-- Affichage en liste (table) -->
                <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Légende</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Album</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="photo in photosList" :key="photo.id" class="hover:bg-gray-50 transition"
                                :class="{ 'bg-blue-50': isPhotoSelected(photo) }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-md overflow-hidden">
                                            <img :src="photo.image_url" :alt="photo.caption || 'Photo'" class="h-full w-full object-cover" />
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ photo.caption || 'Sans légende' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ photo.album.title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="isPhotoSelected(photo)" class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        Bannière #{{ selectedBannerPhotos.findIndex(p => p.id === photo.id) + 1 }}
                                    </span>
                                    <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Non sélectionnée
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <button
                                        @click="toggleBannerPhoto(photo)"
                                        :class="[
                                            'px-3 py-1.5 rounded-md transition inline-flex items-center',
                                            isPhotoSelected(photo)
                                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                                : 'bg-blue-50 text-blue-700 hover:bg-blue-100'
                                        ]"
                                    >
                                        <svg v-if="isPhotoSelected(photo)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ isPhotoSelected(photo) ? 'Retirer' : 'Ajouter' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="photosList.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500">Aucune photo trouvée. Commencez par ajouter des photos dans vos albums.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Type pour un produit
interface Product {
    id: number;
    name: string;
    description: string | null;
    price: number;
    stock: number;
    image_path: string | null;
    created_at: string;
    updated_at: string;
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Product[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    products: Pagination;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux produits
const productsList = computed(() => props.products.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produits', href: route('admin.products.index') }
];

// État de confirmation de suppression
const productToDelete = ref<Product | null>(null);
const showDeleteModal = ref(false);

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('grid');

// Fonctions
function confirmDelete(product: Product) {
    productToDelete.value = product;
    showDeleteModal.value = true;
}

function cancelDelete() {
    productToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteProduct() {
    if (productToDelete.value) {
        window.location.href = route('admin.products.destroy', productToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Formatage du prix
function formatPrice(price: number): string {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
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

// Extraire le texte brut du HTML et le tronquer
function stripAndTruncateHtml(html: string | null, maxLength: number = 100): string {
    if (!html) return '';

    // Créer un élément temporaire pour extraire le texte
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;
    const text = tempDiv.textContent || tempDiv.innerText || "";

    // Tronquer si nécessaire
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }

    return text;
}

// Badge de stock
function getStockBadgeClass(stock: number): string {
    if (stock <= 0) return 'bg-red-100 text-red-800';
    if (stock < 5) return 'bg-yellow-100 text-yellow-800';
    return 'bg-green-100 text-green-800';
}

function getStockStatus(stock: number): string {
    if (stock <= 0) return 'Épuisé';
    if (stock < 5) return 'Stock faible';
    return 'En stock';
}
</script>

<template>
    <Head title="Produits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Liste des produits</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez votre catalogue de produits
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

                    <Link
                        :href="route('admin.products.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter un produit
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

            <!-- Affichage en grille (cards) -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="product in productsList" :key="product.id" class="bg-white border rounded-xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
                    <!-- Image avec prix en surimpression -->
                    <div class="relative h-48 bg-gray-100">
                        <img
                            v-if="product.image_path"
                            :src="`${product.image_path}`"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="absolute bottom-2 right-2">
                            <span class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-full shadow-sm">
                                {{ formatPrice(product.price) }}
                            </span>
                        </div>
                        <div class="absolute top-2 left-2">
                            <span :class="[
                                'px-3 py-1 text-sm font-medium rounded-full shadow-sm',
                                getStockBadgeClass(product.stock)
                            ]">
                                {{ getStockStatus(product.stock) }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ product.name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 flex-1">
                            <span v-if="product.description">
                                {{ stripAndTruncateHtml(product.description, 120) }}
                            </span>
                            <span v-else class="text-gray-400 italic">Aucune description</span>
                        </p>
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs text-gray-500">
                                Créé le {{ formatDate(product.created_at) }}
                            </p>
                            <p class="text-sm font-medium">
                                Stock: <span :class="{ 'text-red-600': product.stock <= 0, 'text-yellow-600': product.stock > 0 && product.stock < 5, 'text-green-600': product.stock >= 5 }">{{ product.stock }}</span>
                            </p>
                        </div>
                        <div class="flex gap-2 mt-auto">
                            <Link
                                :href="route('admin.products.edit', product.id)"
                                class="px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition flex-1 text-center text-sm"
                            >
                                Modifier
                            </Link>
                            <button
                                @click="confirmDelete(product)"
                                class="px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition flex-1 text-center text-sm"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Message si aucun produit trouvé -->
                <div v-if="productsList.length === 0" class="col-span-full bg-gray-50 rounded-lg p-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-500 mb-4">Commencez par ajouter un nouveau produit à votre catalogue.</p>
                    <Link
                        :href="route('admin.products.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter un produit
                    </Link>
                </div>
            </div>

            <!-- Affichage en liste (table) -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in productsList" :key="product.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img
                                    v-if="product.image_path"
                                    :src="`${product.image_path}`"
                                    :alt="product.name"
                                    class="w-16 h-16 object-cover rounded-md border border-gray-200"
                                >
                                <div v-else class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-md border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div v-if="product.description" class="text-sm text-gray-600 max-w-xs">
                                    {{ stripAndTruncateHtml(product.description) }}
                                </div>
                                <span v-else class="text-sm text-gray-400 italic">Non renseigné</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'px-3 py-1 text-xs font-medium rounded-full',
                                    getStockBadgeClass(product.stock)
                                ]">
                                    {{ product.stock }} {{ product.stock <= 1 ? 'unité' : 'unités' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(product.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Modifier
                                    </Link>
                                    <button
                                        @click="confirmDelete(product)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="productsList.length === 0">
                            <td colspan="7" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="text-gray-500">Aucun produit trouvé. Commencez par en ajouter un !</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ props.products.from }}</span> à
                            <span class="font-medium">{{ props.products.to }}</span> sur
                            <span class="font-medium">{{ props.products.total }}</span> produits
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.products.links"
                                :key="i"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm',
                                    link.url === null
                                        ? 'text-gray-300 cursor-not-allowed'
                                        : 'text-gray-500 hover:bg-gray-50',
                                    link.active ? 'bg-blue-50 text-blue-600 font-medium border-blue-500 z-10' : 'border-gray-300',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === props.products.links.length - 1 ? 'rounded-r-md' : '',
                                    'border'
                                ]"
                            />
                        </nav>
                    </div>
                </div>

                <!-- Pagination mobile -->
                <div class="flex flex-1 justify-between items-center sm:hidden">
                    <Link
                        :href="props.products.links[0].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.products.links[0].url === null
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-white text-gray-700 hover:bg-gray-50',
                            'border border-gray-300'
                        ]"
                    >
                        Précédent
                    </Link>
                    <Link
                        :href="props.products.links[props.products.links.length - 1].url || '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
                            props.products.links[props.products.links.length - 1].url === null
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                : 'bg-white text-gray-700 hover:bg-gray-50',
                            'border border-gray-300'
                        ]"
                    >
                        Suivant
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer le produit "<span class="font-medium">{{ productToDelete?.name }}</span>" ?
                <br>Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="cancelDelete"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex-1 max-w-xs"
                >
                    Annuler
                </button>
                <button
                    @click="deleteProduct"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>

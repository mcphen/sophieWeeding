<template>
    <Head title="Ajouter une formation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et boutons d'action -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Ajouter une formation</h2>
                </div>
                <div>
                    <Link
                        :href="route('admin.training-sessions.index')"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition"
                    >
                        Retour à la liste
                    </Link>
                </div>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="col-span-2">
                        <Label for="title" value="Titre" />
                        <Input
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <Label for="description" value="Description" />
                        <textarea
                            id="description"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            v-model="form.description"
                            rows="3"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <!-- Content -->
                    <div class="col-span-2">
                        <Label for="content" value="Contenu détaillé" />
                        <textarea
                            id="content"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            v-model="form.content"
                            rows="6"
                        ></textarea>
                        <p class="mt-1 text-sm text-gray-500">Vous pouvez utiliser du HTML pour formater le contenu.</p>
                        <InputError class="mt-2" :message="form.errors.content" />
                    </div>

                    <!-- Image -->
                    <div class="col-span-2">
                        <Label for="image" value="Image" />
                        <input
                            id="image"
                            type="file"
                            class="mt-1 block w-full"
                            @input="form.image = $event.target.files[0]"
                        />
                        <p class="mt-1 text-sm text-gray-500">Format recommandé : JPG, PNG. Taille max : 2 Mo.</p>
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>

                    <!-- Document (PDF) -->
                    <div class="col-span-2">
                        <Label for="document" value="Document PDF (flyer, programme, etc.)" />
                        <input
                            id="document"
                            type="file"
                            accept=".pdf"
                            class="mt-1 block w-full"
                            @input="form.document = $event.target.files[0]"
                        />
                        <p class="mt-1 text-sm text-gray-500">Format accepté : PDF uniquement. Taille max : 10 Mo.</p>
                        <InputError class="mt-2" :message="form.errors.document" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <Label for="start_date" value="Date de début" />
                        <Input
                            id="start_date"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            v-model="form.start_date"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.start_date" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <Label for="end_date" value="Date de fin" />
                        <Input
                            id="end_date"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            v-model="form.end_date"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.end_date" />
                    </div>

                    <!-- Location -->
                    <div>
                        <Label for="location" value="Lieu" />
                        <Input
                            id="location"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.location"
                        />
                        <InputError class="mt-2" :message="form.errors.location" />
                    </div>

                    <!-- Price -->
                    <div>
                        <Label for="price" value="Prix (€)" />
                        <Input
                            id="price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full"
                            v-model="form.price"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>

                    <!-- Max Participants -->
                    <div>
                        <Label for="max_participants" value="Nombre maximum de participants" />
                        <Input
                            id="max_participants"
                            type="number"
                            min="0"
                            class="mt-1 block w-full"
                            v-model="form.max_participants"
                        />
                        <p class="mt-1 text-sm text-gray-500">Laissez 0 pour un nombre illimité.</p>
                        <InputError class="mt-2" :message="form.errors.max_participants" />
                    </div>

                    <!-- Is Active -->
                    <div>
                        <div class="flex items-center">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" value="Formation active" class="ml-2" />
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Les formations inactives ne sont pas visibles sur le site.</p>
                        <InputError class="mt-2" :message="form.errors.is_active" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8">
                    <Button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Créer la formation
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Formations', href: route('admin.training-sessions.index') },
    { title: 'Ajouter', href: route('admin.training-sessions.create') }
];

const form = useForm({
    title: '',
    description: '',
    content: '',
    image: null as File | null,
    document: null as File | null,
    start_date: '',
    end_date: '',
    location: '',
    max_participants: 0,
    price: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.training-sessions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

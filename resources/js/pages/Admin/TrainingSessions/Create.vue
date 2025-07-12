<template>
    <Head title="Ajouter une formation" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter une formation</h2>
                <Link
                    :href="route('admin.training-sessions.index')"
                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition"
                >
                    Retour à la liste
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div class="col-span-2">
                                    <InputLabel for="title" value="Titre" />
                                    <TextInput
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
                                    <InputLabel for="description" value="Description" />
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
                                    <InputLabel for="content" value="Contenu détaillé" />
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
                                    <InputLabel for="image" value="Image" />
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
                                    <InputLabel for="document" value="Document PDF (flyer, programme, etc.)" />
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
                                    <InputLabel for="start_date" value="Date de début" />
                                    <TextInput
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
                                    <InputLabel for="end_date" value="Date de fin" />
                                    <TextInput
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
                                    <InputLabel for="location" value="Lieu" />
                                    <TextInput
                                        id="location"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.location"
                                    />
                                    <InputError class="mt-2" :message="form.errors.location" />
                                </div>

                                <!-- Price -->
                                <div>
                                    <InputLabel for="price" value="Prix (€)" />
                                    <TextInput
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
                                    <InputLabel for="max_participants" value="Nombre maximum de participants" />
                                    <TextInput
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
                                        <InputLabel for="is_active" value="Formation active" class="ml-2" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Les formations inactives ne sont pas visibles sur le site.</p>
                                    <InputError class="mt-2" :message="form.errors.is_active" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-8">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Créer la formation
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import Checkbox from '@/components/Checkbox.vue';

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

<template>
    <Head title="Modifier une formation" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier une formation</h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('admin.training-sessions.show', trainingSession.id)"
                        class="px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#c08529] transition"
                    >
                        Voir les détails
                    </Link>
                    <Link
                        :href="route('admin.training-sessions.index')"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition"
                    >
                        Retour à la liste
                    </Link>
                </div>
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

                                <!-- Current Image -->
                                <div v-if="trainingSession.image_url" class="col-span-2">
                                    <InputLabel value="Image actuelle" />
                                    <div class="mt-2">
                                        <img :src="trainingSession.image_url" alt="Image de la formation" class="h-40 w-auto object-cover rounded-md" />
                                    </div>
                                </div>

                                <!-- Image -->
                                <div class="col-span-2">
                                    <InputLabel for="image" value="Nouvelle image" />
                                    <input
                                        id="image"
                                        type="file"
                                        class="mt-1 block w-full"
                                        @input="form.image = $event.target.files[0]"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Format recommandé : JPG, PNG. Taille max : 2 Mo. Laissez vide pour conserver l'image actuelle.</p>
                                    <InputError class="mt-2" :message="form.errors.image" />
                                </div>

                                <!-- Current Document -->
                                <div v-if="trainingSession.document_url" class="col-span-2">
                                    <InputLabel value="Document actuel" />
                                    <div class="mt-2 flex items-center">
                                        <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <a :href="trainingSession.document_url" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                                            Voir le document PDF
                                        </a>
                                    </div>
                                </div>

                                <!-- Document -->
                                <div class="col-span-2">
                                    <InputLabel for="document" value="Nouveau document PDF (flyer, programme, etc.)" />
                                    <input
                                        id="document"
                                        type="file"
                                        accept=".pdf"
                                        class="mt-1 block w-full"
                                        @input="form.document = $event.target.files[0]"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Format accepté : PDF uniquement. Taille max : 10 Mo. Laissez vide pour conserver le document actuel.</p>
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
                                    Mettre à jour la formation
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

interface Props {
    trainingSession: {
        id: number;
        title: string;
        description: string | null;
        content: string | null;
        image_url: string | null;
        document_url: string | null;
        start_date: string;
        end_date: string;
        location: string | null;
        max_participants: number;
        price: number;
        is_active: boolean;
        slug: string;
    };
}

const props = defineProps<Props>();

const form = useForm({
    title: props.trainingSession.title,
    description: props.trainingSession.description || '',
    content: props.trainingSession.content || '',
    image: null as File | null,
    document: null as File | null,
    start_date: props.trainingSession.start_date,
    end_date: props.trainingSession.end_date,
    location: props.trainingSession.location || '',
    max_participants: props.trainingSession.max_participants,
    price: props.trainingSession.price,
    is_active: props.trainingSession.is_active,
    _method: 'PUT',
});

const submit = () => {
    form.post(route('admin.training-sessions.update', props.trainingSession.id), {
        preserveScroll: true,
    });
};
</script>

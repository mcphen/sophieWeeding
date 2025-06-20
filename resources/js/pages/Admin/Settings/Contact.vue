<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    settings: Array
});

// Convert settings array to an object for easier form handling
const settingsObject = {};
if (props.settings) {
    props.settings.forEach(setting => {
        settingsObject[setting.key] = setting.value;
    });
}

// Create form with default values
const form = useForm({
    // Contact information
    'contact_phone': settingsObject['contact_phone'] || '',
    'contact_phone_fixed': settingsObject['contact_phone_fixed'] || '',
    'contact_email': settingsObject['contact_email'] || '',
    'contact_address': settingsObject['contact_address'] || '',

    // Social media
    'social_facebook': settingsObject['social_facebook'] || '',
    'social_twitter': settingsObject['social_twitter'] || '',
    'social_instagram': settingsObject['social_instagram'] || '',

    // Opening hours
    'opening_hours': settingsObject['opening_hours'] || '',
});

const submit = () => {
    form.post(route('admin.contact-settings.update'));
};
</script>

<template>
    <AppLayout>
        <Head title="Contact Settings" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Contact Settings</h1>

                        <form @submit.prevent="submit">
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Contact Information</h2>

                                <div class="mb-4">
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Mobile Phone</label>
                                    <input
                                        id="contact_phone"
                                        v-model="form.contact_phone"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="(+221) 78 538 30 69"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_phone_fixed" class="block text-sm font-medium text-gray-700">Fixed Phone</label>
                                    <input
                                        id="contact_phone_fixed"
                                        v-model="form.contact_phone_fixed"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="(+221) 33 865 27 11"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        id="contact_email"
                                        v-model="form.contact_email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="sophieweddings5@gmail.com"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea
                                        id="contact_address"
                                        v-model="form.contact_address"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="42 Rue des Amoureux, 75001 Paris, France"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Social Media</h2>

                                <div class="mb-4">
                                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                    <input
                                        id="social_facebook"
                                        v-model="form.social_facebook"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://facebook.com/sophieweddings"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                                    <input
                                        id="social_twitter"
                                        v-model="form.social_twitter"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://twitter.com/sophieweddings"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                    <input
                                        id="social_instagram"
                                        v-model="form.social_instagram"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://instagram.com/sophieweddings"
                                    >
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Opening Hours</h2>

                                <div class="mb-4">
                                    <label for="opening_hours" class="block text-sm font-medium text-gray-700">Opening Hours</label>
                                    <textarea
                                        id="opening_hours"
                                        v-model="form.opening_hours"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Monday - Friday: 9am - 6pm
Saturday: 10am - 4pm
Sunday: Closed"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

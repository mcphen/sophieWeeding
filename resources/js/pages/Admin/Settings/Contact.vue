<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Type declaration for the route function
declare function route(name: string, params?: Record<string, any>): string;

interface Setting {
    id: number;
    key: string;
    value: string;
    group: string;
    type: string;
    label: string;
    created_at?: string;
    updated_at?: string;
}

interface SettingsObject {
    [key: string]: string;
}

interface FormData {
    contact_phone: string;
    contact_phone_fixed: string;
    contact_email: string;
    contact_address: string;
    social_facebook: string;
    social_twitter: string;
    social_instagram: string;
    social_youtube: string;
    social_linkedin: string;
    social_tiktok: string;
    opening_hours: string;
    site_logo: File | null;
}

const props = defineProps<{
    settings: Setting[]
}>();

// Convert settings array to an object for easier form handling
const settingsObject: SettingsObject = {};
if (props.settings) {
    props.settings.forEach((setting: Setting) => {
        settingsObject[setting.key] = setting.value;
    });
}

// Create form with default values
const form = useForm<FormData>({
    // Contact information
    contact_phone: settingsObject['contact_phone'] || '',
    contact_phone_fixed: settingsObject['contact_phone_fixed'] || '',
    contact_email: settingsObject['contact_email'] || '',
    contact_address: settingsObject['contact_address'] || '',

    // Social media
    social_facebook: settingsObject['social_facebook'] || '',
    social_twitter: settingsObject['social_twitter'] || '',
    social_instagram: settingsObject['social_instagram'] || '',
    social_youtube: settingsObject['social_youtube'] || '',
    social_linkedin: settingsObject['social_linkedin'] || '',
    social_tiktok: settingsObject['social_tiktok'] || '',

    // Opening hours
    opening_hours: settingsObject['opening_hours'] || '',

    // Site logo
    site_logo: null,
});

// Current logo path
const currentLogo = settingsObject['site_logo'] || '/images/logo.png';

// Handle logo file selection
const handleLogoUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.site_logo = target.files[0];
    }
};

const submit = () => {
    // Use multipart/form-data for file uploads
    form.post(route('admin.contact-settings.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
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

                                <div class="mb-4">
                                    <label for="social_youtube" class="block text-sm font-medium text-gray-700">YouTube URL</label>
                                    <input
                                        id="social_youtube"
                                        v-model="form.social_youtube"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://youtube.com/c/sophieweddings"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                                    <input
                                        id="social_linkedin"
                                        v-model="form.social_linkedin"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://linkedin.com/company/sophieweddings"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_tiktok" class="block text-sm font-medium text-gray-700">TikTok URL</label>
                                    <input
                                        id="social_tiktok"
                                        v-model="form.social_tiktok"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://tiktok.com/@sophieweddings"
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

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Site Logo</h2>

                                <div class="mb-4">
                                    <label for="site_logo" class="block text-sm font-medium text-gray-700">Logo</label>

                                    <!-- Current logo preview -->
                                    <div class="mt-2 mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Current logo:</p>
                                        <img :src="currentLogo" alt="Current Logo" class="h-16 object-contain border rounded p-1">
                                    </div>

                                    <!-- Logo upload -->
                                    <input
                                        id="site_logo"
                                        type="file"
                                        @change="handleLogoUpload"
                                        class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary file:text-white
                                        hover:file:bg-primary-dark"
                                        accept="image/*"
                                    >
                                    <p class="mt-1 text-sm text-gray-500">
                                        Upload a new logo image (PNG, JPG, GIF up to 2MB)
                                    </p>
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

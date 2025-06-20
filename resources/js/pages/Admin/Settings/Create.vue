<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

// Create form with empty values
const form = useForm({
    key: '',
    value: '',
    group: 'general', // Default group
    type: 'text',     // Default type
    label: '',
});

// Available setting types
const settingTypes = [
    { value: 'text', label: 'Text' },
    { value: 'textarea', label: 'Text Area' },
    { value: 'number', label: 'Number' },
    { value: 'email', label: 'Email' },
    { value: 'url', label: 'URL' },
    { value: 'boolean', label: 'Boolean' },
    { value: 'date', label: 'Date' },
    { value: 'time', label: 'Time' },
    { value: 'datetime', label: 'Date & Time' },
    { value: 'color', label: 'Color' },
    { value: 'file', label: 'File' },
    { value: 'image', label: 'Image' },
];

// Common setting groups
const commonGroups = [
    { value: 'general', label: 'General' },
    { value: 'contact', label: 'Contact' },
    { value: 'social', label: 'Social Media' },
    { value: 'seo', label: 'SEO' },
    { value: 'appearance', label: 'Appearance' },
];

// Custom group input
const customGroup = ref('');
const useCustomGroup = ref(false);

// Submit form
const submit = () => {
    // If using custom group, set the form group value
    if (useCustomGroup.value && customGroup.value) {
        form.group = customGroup.value;
    }

    form.post(route('admin.settings.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Add New Setting" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Add New Setting</h1>

                        <form @submit.prevent="submit">
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Setting Information</h2>

                                <!-- Key Field -->
                                <div class="mb-4">
                                    <label for="key" class="block text-sm font-medium text-gray-700">Key <span class="text-red-500">*</span></label>
                                    <input
                                        id="key"
                                        v-model="form.key"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="setting_key_name"
                                        required
                                    >
                                    <p class="mt-1 text-sm text-gray-500">
                                        Unique identifier for this setting (e.g., site_title, contact_email)
                                    </p>
                                    <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">{{ form.errors.key }}</p>
                                </div>

                                <!-- Label Field -->
                                <div class="mb-4">
                                    <label for="label" class="block text-sm font-medium text-gray-700">Label <span class="text-red-500">*</span></label>
                                    <input
                                        id="label"
                                        v-model="form.label"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Setting Label"
                                        required
                                    >
                                    <p class="mt-1 text-sm text-gray-500">
                                        Human-readable name for this setting (e.g., Site Title, Contact Email)
                                    </p>
                                    <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</p>
                                </div>

                                <!-- Value Field -->
                                <div class="mb-4">
                                    <label for="value" class="block text-sm font-medium text-gray-700">Value</label>
                                    <textarea
                                        v-if="form.type === 'textarea'"
                                        id="value"
                                        v-model="form.value"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Setting value"
                                    ></textarea>
                                    <input
                                        v-else
                                        id="value"
                                        v-model="form.value"
                                        :type="form.type === 'boolean' ? 'checkbox' : form.type"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Setting value"
                                    >
                                    <p class="mt-1 text-sm text-gray-500">
                                        The value of this setting
                                    </p>
                                    <p v-if="form.errors.value" class="mt-1 text-sm text-red-600">{{ form.errors.value }}</p>
                                </div>

                                <!-- Type Field -->
                                <div class="mb-4">
                                    <label for="type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        required
                                    >
                                        <option v-for="type in settingTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">
                                        The data type of this setting
                                    </p>
                                    <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                                </div>

                                <!-- Group Field -->
                                <div class="mb-4">
                                    <label for="group" class="block text-sm font-medium text-gray-700">Group <span class="text-red-500">*</span></label>

                                    <div class="flex items-center mb-2">
                                        <input
                                            id="use-predefined-group"
                                            type="radio"
                                            :checked="!useCustomGroup"
                                            @change="useCustomGroup = false"
                                            class="focus:ring-primary h-4 w-4 text-primary border-gray-300"
                                        >
                                        <label for="use-predefined-group" class="ml-2 block text-sm text-gray-700">
                                            Use predefined group
                                        </label>
                                    </div>

                                    <select
                                        v-if="!useCustomGroup"
                                        id="group"
                                        v-model="form.group"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        required
                                    >
                                        <option v-for="group in commonGroups" :key="group.value" :value="group.value">
                                            {{ group.label }}
                                        </option>
                                    </select>

                                    <div class="flex items-center mt-3 mb-2">
                                        <input
                                            id="use-custom-group"
                                            type="radio"
                                            :checked="useCustomGroup"
                                            @change="useCustomGroup = true"
                                            class="focus:ring-primary h-4 w-4 text-primary border-gray-300"
                                        >
                                        <label for="use-custom-group" class="ml-2 block text-sm text-gray-700">
                                            Use custom group
                                        </label>
                                    </div>

                                    <input
                                        v-if="useCustomGroup"
                                        id="custom-group"
                                        v-model="customGroup"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Custom group name"
                                        required
                                    >

                                    <p class="mt-1 text-sm text-gray-500">
                                        Category to group related settings together
                                    </p>
                                    <p v-if="form.errors.group" class="mt-1 text-sm text-red-600">{{ form.errors.group }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <a
                                    :href="route('admin.settings.index')"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Save Setting
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

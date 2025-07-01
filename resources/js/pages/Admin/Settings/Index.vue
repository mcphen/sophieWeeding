<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    settings: Array
});
</script>

<template>
    <AppLayout>
        <Head title="Settings Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold">Settings Management</h1>
                            <div class="flex space-x-4">
                                <Link
                                    :href="route('admin.contact-settings')"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark"
                                >
                                    Contact Settings
                                </Link>
                                <Link
                                    :href="route('admin.color-settings')"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark"
                                >
                                    Color Settings
                                </Link>
                                <Link
                                    :href="route('admin.settings.create')"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark"
                                >
                                    Add New Setting
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="setting in settings" :key="setting.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ setting.key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="max-w-xs truncate">{{ setting.value }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ setting.group }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ setting.type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <Link
                                                    :href="route('admin.settings.edit', setting.id)"
                                                    class="text-primary hover:text-primary-dark"
                                                >
                                                    Edit
                                                </Link>
                                                <Link
                                                    :href="route('admin.settings.destroy', setting.id)"
                                                    method="delete"
                                                    as="button"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    v-if="setting.group !== 'contact'"
                                                >
                                                    Delete
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="settings.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No settings found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

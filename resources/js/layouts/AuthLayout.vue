<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

defineProps<{
    title?: string;
    description?: string;
    siteLogo?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description" :siteLogo="siteLogo">
        <slot />
    </AuthLayout>
</template>

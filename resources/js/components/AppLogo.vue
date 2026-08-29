<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    logoUrl?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-9 items-center justify-center overflow-hidden rounded-lg bg-sidebar-primary/15 text-sidebar-primary-foreground ring-1 ring-sidebar-primary/30">
        <AppLogoIcon class="h-full w-full fill-current object-cover text-sidebar-primary" :logoUrl="logoUrl" />
    </div>
    <div class="ml-2 grid flex-1 text-left">
        <span class="truncate font-display text-sm font-semibold leading-tight text-sidebar-foreground">Amaël Fondation</span>
        <span class="truncate text-xs text-sidebar-foreground/60">Espace Admin</span>
    </div>
</template>

<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    logoUrl?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md text-sidebar-primary-foreground" style="height: 100px;width: 100px">
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" :logoUrl="logoUrl" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">SWD</span>
    </div>
</template>

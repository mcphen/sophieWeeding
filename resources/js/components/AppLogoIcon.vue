<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
    logoUrl?: string;
}

defineProps<Props>();
</script>

<template>
    <img :src="logoUrl || '/images/logo.png'" :class="className" v-bind="$attrs" alt="Amaël Fondation" />
</template>

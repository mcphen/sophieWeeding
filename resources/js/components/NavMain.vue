<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { SidebarGroup, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import type { Component } from 'vue';

const props = defineProps<{
    items: NavItem[];
    label?: string;
    icon?: Component;
    open?: boolean;
}>();

const emit = defineEmits<{
    (e: 'toggle'): void;
}>();

const page = usePage<SharedData>();
const { state } = useSidebar();

function isActive(href: string): boolean {
    try {
        const path = new URL(href, window.location.origin).pathname;
        return page.url === path || page.url.startsWith(path + '/');
    } catch {
        return page.url === href;
    }
}
</script>

<template>
    <SidebarGroup class="px-2 py-1">
        <SidebarMenu v-if="!label">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isActive(item.href)"
                    :tooltip="item.title"
                    class="rounded-md border-l-2 border-transparent pl-2.5 text-sidebar-foreground/80 transition-colors data-[active=true]:border-sidebar-primary data-[active=true]:bg-sidebar-accent data-[active=true]:text-sidebar-accent-foreground [&>svg]:text-sidebar-foreground/50 data-[active=true]:[&>svg]:text-sidebar-primary"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>

        <Collapsible v-else :open="state === 'collapsed' ? true : !!open" @update:open="emit('toggle')">
            <CollapsibleTrigger as-child>
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-[0.65rem] font-semibold tracking-wider text-sidebar-foreground/45 uppercase outline-hidden transition-colors hover:text-sidebar-foreground/80 group-data-[collapsible=icon]:hidden"
                >
                    <span class="flex items-center gap-2">
                        <component :is="icon" v-if="icon" class="size-3.5 text-sidebar-foreground/45" />
                        <span>{{ label }}</span>
                    </span>
                    <ChevronRight class="size-3.5 shrink-0 transition-transform duration-200" :class="open ? 'rotate-90' : ''" />
                </button>
            </CollapsibleTrigger>
            <CollapsibleContent>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in items" :key="item.title">
                        <SidebarMenuButton
                            as-child
                            :is-active="isActive(item.href)"
                            :tooltip="item.title"
                            class="rounded-md border-l-2 border-transparent pl-2.5 text-sidebar-foreground/80 transition-colors data-[active=true]:border-sidebar-primary data-[active=true]:bg-sidebar-accent data-[active=true]:text-sidebar-accent-foreground [&>svg]:text-sidebar-foreground/50 data-[active=true]:[&>svg]:text-sidebar-primary"
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </CollapsibleContent>
        </Collapsible>
    </SidebarGroup>
</template>

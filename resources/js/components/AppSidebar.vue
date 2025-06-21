<script setup lang="ts">

import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,

    Briefcase,
    ShoppingCart,
    Users,

    MessageSquare,
    Newspaper,
    Image,
    Calendar,
    CalendarClock,
    Mail,
    FileText
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'À Propos',
        href: route('admin.about.edit'),
        icon: FileText,
    },
    {
        title: 'Services',
        href: route('admin.services.index'),
        icon: Briefcase,
    },
    {
        title: 'Produits',
        href: route('admin.products.index'),
        icon: ShoppingCart,
    },
    {
        title: 'Équipe',
        href: route('admin.team-members.index'),
        icon: Users,
    },
    {
        title: 'Témoignages',
        href: route('admin.testimonials.index'),
        icon: MessageSquare,
    },
    {
        title: 'Actualités',
        href: route('admin.actualites.index'),
        icon: Newspaper,
    },
    {
        title: 'Albums photos',
        href: route('admin.albums.index'),
        icon: Image,
    },
    {
        title: 'Créneaux',
        href: route('admin.schedules.index'),
        icon: Calendar,
    },
    {
        title: 'Rendez-vous',
        href: route('admin.appointments.index'),
        icon: CalendarClock,
    },
    {
        title: 'Contacts',
        href: route('admin.contacts.index'),
        icon: Mail,
    },
    {
        title: 'Utilisateurs',
        href: route('admin.users.index'),
        icon: Users,
    },

];



</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>

            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

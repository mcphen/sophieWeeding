<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Home,
    Briefcase,
    ShoppingCart,
    Users,
    UserCog,
    MessageSquare,
    Newspaper,
    Images,
    Calendar,
    CalendarClock,
    Mail,
    FileText,
    Settings,
    Bell,
    GraduationCap,
    Phone,
    Palette,
    Megaphone,
    UsersRound,
    BarChart3,
    PenLine,
    ListChecks,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

interface Props {
    logoUrl?: string;
}

defineProps<Props>();

const navItems: NavItem[] = [
    { title: 'Accueil', href: route('home'),      icon: Home },
    { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid },
];

const contenuItems: NavItem[] = [
    { title: 'À Propos',      href: route('admin.about.edit'),        icon: FileText },
    { title: 'Services',      href: route('admin.services.index'),    icon: Briefcase },
    { title: 'Produits',      href: route('admin.products.index'),    icon: ShoppingCart },
    { title: 'Équipe',        href: route('admin.team-members.index'), icon: Users },
    { title: 'Témoignages',   href: route('admin.testimonials.index'), icon: MessageSquare },
    { title: 'Actualités',    href: route('admin.actualites.index'),  icon: Newspaper },
    { title: 'Albums photos', href: route('admin.albums.index'),      icon: Images },
];

const formationItems: NavItem[] = [
    { title: 'Masterclasses',       href: route('admin.masterclasses.index'), icon: GraduationCap },
    { title: 'Participants',        href: route('admin.participants.index'),  icon: UsersRound },
    { title: 'Listes de diffusion', href: route('admin.email-lists.index'),  icon: ListChecks },
    { title: 'Analytiques',         href: route('admin.analytics'),           icon: BarChart3 },
];

const agendaItems: NavItem[] = [
    { title: 'Créneaux',    href: route('admin.schedules.index'),    icon: Calendar },
    { title: 'Rendez-vous', href: route('admin.appointments.index'), icon: CalendarClock },
];

const communicationItems: NavItem[] = [
    { title: 'Contacts',    href: route('admin.contacts.index'),   icon: Mail },
    { title: 'Newsletter',  href: route('admin.newsletter.index'), icon: Bell },
];

const parametresItems: NavItem[] = [
    { title: 'Contact',      href: route('admin.contact-settings'),     icon: Phone },
    { title: 'Couleurs',     href: route('admin.color-settings'),       icon: Palette },
    { title: 'CTA',          href: route('admin.cta-settings'),         icon: Megaphone },
    { title: 'Attestation',  href: route('admin.attestation-settings'), icon: PenLine },
    { title: 'Utilisateurs', href: route('admin.users.index'),          icon: UserCog },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo :logoUrl="logoUrl" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="navItems" />
            <NavMain label="Contenu"       :items="contenuItems" />
            <NavMain label="Formation"     :items="formationItems" />
            <NavMain label="Agenda"        :items="agendaItems" />
            <NavMain label="Communication" :items="communicationItems" />
            <NavMain label="Paramètres"    :items="parametresItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

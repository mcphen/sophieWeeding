<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    HeartHandshake,
    HandHeart,
    Folder,
    Radio,
    Eye,
    GalleryHorizontal,
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
    { title: 'Bannière accueil', href: route('admin.banner.index'),   icon: GalleryHorizontal },
    { title: 'À Propos',      href: route('admin.about.edit'),        icon: FileText },
    { title: 'Services',      href: route('admin.services.index'),    icon: Briefcase },
    { title: 'Produits',      href: route('admin.products.index'),    icon: ShoppingCart },
    { title: 'Équipe',        href: route('admin.team-members.index'), icon: Users },
    { title: 'Témoignages',   href: route('admin.testimonials.index'), icon: MessageSquare },
    { title: 'Actualités',    href: route('admin.actualites.index'),  icon: Newspaper },
    { title: 'Albums photos', href: route('admin.albums.index'),      icon: Images },
];

const formationItems: NavItem[] = [
    { title: 'Événements',          href: route('admin.masterclasses.index'), icon: GraduationCap },
    { title: 'Participants',        href: route('admin.participants.index'),  icon: UsersRound },
    { title: 'Listes de diffusion', href: route('admin.email-lists.index'),  icon: ListChecks },
    { title: 'Analytiques',         href: route('admin.analytics'),           icon: BarChart3 },
    { title: 'Visites du site',     href: route('admin.visitor-stats'),       icon: Eye },
];

const agendaItems: NavItem[] = [
    { title: 'Créneaux',    href: route('admin.schedules.index'),    icon: Calendar },
    { title: 'Rendez-vous', href: route('admin.appointments.index'), icon: CalendarClock },
];

const communicationItems: NavItem[] = [
    { title: 'Contacts',    href: route('admin.contacts.index'),   icon: Mail },
    { title: 'Dons',        href: route('admin.donations.index'),  icon: HandHeart },
    { title: 'Bénévolat',   href: route('admin.volunteers.index'), icon: HeartHandshake },
    { title: 'Newsletter',  href: route('admin.newsletter.index'), icon: Bell },
];

const parametresItems: NavItem[] = [
    { title: 'Contact',      href: route('admin.contact-settings'),     icon: Phone },
    { title: 'Couleurs',     href: route('admin.color-settings'),       icon: Palette },
    { title: 'CTA',          href: route('admin.cta-settings'),         icon: Megaphone },
    { title: 'Attestation',  href: route('admin.attestation-settings'), icon: PenLine },
    { title: 'Utilisateurs', href: route('admin.users.index'),          icon: UserCog },
];

const groups: Record<string, NavItem[]> = {
    Contenu: contenuItems,
    Formation: formationItems,
    Agenda: agendaItems,
    Communication: communicationItems,
    Paramètres: parametresItems,
};

function isItemActive(href: string, currentUrl: string): boolean {
    try {
        const path = new URL(href, window.location.origin).pathname;
        return currentUrl === path || currentUrl.startsWith(path + '/');
    } catch {
        return currentUrl === href;
    }
}

function findActiveGroup(currentUrl: string): string | null {
    for (const [name, items] of Object.entries(groups)) {
        if (items.some((item) => isItemActive(item.href, currentUrl))) {
            return name;
        }
    }
    return null;
}

// La section contenant la page active s'ouvre automatiquement ; les autres démarrent fermées.
const openGroup = ref<string | null>(findActiveGroup(usePage().url));

function toggleGroup(name: string) {
    openGroup.value = openGroup.value === name ? null : name;
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border">
        <SidebarHeader class="border-b border-sidebar-border/60 py-3">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="hover:bg-sidebar-accent">
                        <Link :href="route('dashboard')">
                            <AppLogo :logoUrl="logoUrl" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="gap-0">
            <NavMain :items="navItems" />
            <NavMain label="Contenu"       :icon="Folder"         :items="contenuItems"       :open="openGroup === 'Contenu'"       @toggle="toggleGroup('Contenu')" />
            <NavMain label="Formation"     :icon="GraduationCap"  :items="formationItems"     :open="openGroup === 'Formation'"     @toggle="toggleGroup('Formation')" />
            <NavMain label="Agenda"        :icon="Calendar"       :items="agendaItems"        :open="openGroup === 'Agenda'"        @toggle="toggleGroup('Agenda')" />
            <NavMain label="Communication" :icon="Radio"          :items="communicationItems" :open="openGroup === 'Communication'" @toggle="toggleGroup('Communication')" />
            <NavMain label="Paramètres"    :icon="Settings"       :items="parametresItems"    :open="openGroup === 'Paramètres'"    @toggle="toggleGroup('Paramètres')" />
        </SidebarContent>

        <SidebarFooter class="border-t border-sidebar-border/60">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import BarChart from '@/components/dashboard/BarChart.vue';
import { computed } from 'vue';
import {
    HandHeart,
    HeartHandshake,
    Mail,
    CalendarClock,
    GraduationCap,
    Bell,
    Newspaper,
    Images,
} from 'lucide-vue-next';

const props = defineProps<{
    kpis: {
        donationsTotal: number;
        donationsCount: number;
        donationsThisMonth: number;
        volunteersCount: number;
        volunteersThisMonth: number;
        contactsCount: number;
        contactsThisMonth: number;
        appointmentsUpcoming: number;
        appointmentsPending: number;
        registrationsTotal: number;
        registrationsConfirmed: number;
        newsletterSubscribers: number;
        emailListContacts: number;
    };
    donationsMonthly: {
        labels: string[];
        data: number[];
    };
    upcomingAppointments: Array<{
        id: number;
        subject: string;
        status: string;
        created_at: string;
        client: { first_name: string; last_name: string; email: string };
        schedule: { date: string; start_time: string } | null;
        services: Array<{ id: number; name: string }>;
    }>;
    recentContacts: Array<{
        id: number;
        subject: string;
        description: string;
        created_at: string;
        client: { first_name: string; last_name: string; email: string };
    }>;
    recentDonations: Array<{
        id: number;
        name: string;
        amount: number;
        method: string;
        created_at: string;
    }>;
    recentVolunteers: Array<{
        id: number;
        name: string;
        type: string;
        created_at: string;
    }>;
    content: {
        actualites: number;
        albums: number;
        masterclasses: number;
    };
    logoUrl?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const fcfa = (value: number) => new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';

const methodLabels: Record<string, string> = {
    orange_money: 'Orange Money',
    wave: 'Wave',
    virement: 'Virement',
};

const statusLabels: Record<string, string> = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    cancelled: 'Annulé',
};

const statusClasses: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-800',
    confirmed: 'bg-emerald-100 text-emerald-800',
    cancelled: 'bg-red-100 text-red-800',
};

function formatDate(value: string) {
    return new Date(value).toLocaleDateString('fr-FR');
}

const donationsChart = computed(() => props.donationsMonthly);
</script>

<template>
    <Head title="Dashboard">
        <link rel="icon" :href="logoUrl" type="image/png">
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs" :logoUrl="logoUrl">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="font-display text-2xl font-semibold text-foreground">Vue d'ensemble</h1>
                <p class="text-sm text-muted-foreground">L'activité de la fondation en un coup d'œil.</p>
            </div>

            <!-- KPI Cards -->
            <div class="grid auto-rows-min gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Dons collectés</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><HandHeart class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ fcfa(kpis.donationsTotal) }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.donationsCount }} dons · {{ fcfa(kpis.donationsThisMonth) }} ce mois-ci</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Bénévoles &amp; partenaires</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><HeartHandshake class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ kpis.volunteersCount }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.volunteersThisMonth }} nouvelles candidatures ce mois-ci</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Messages reçus</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><Mail class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ kpis.contactsCount }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.contactsThisMonth }} reçus ce mois-ci</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Rendez-vous à venir</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><CalendarClock class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ kpis.appointmentsUpcoming }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.appointmentsPending }} en attente de confirmation</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Inscriptions événements</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><GraduationCap class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ kpis.registrationsTotal }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.registrationsConfirmed }} confirmées</p>
                </div>

                <div class="flex h-full flex-col rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-muted-foreground">Audience newsletter</h3>
                        <div class="rounded-full bg-primary/10 p-2 text-primary"><Bell class="size-4" /></div>
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-foreground">{{ kpis.newsletterSubscribers }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">{{ kpis.emailListContacts }} contacts en listes de diffusion</p>
                </div>
            </div>

            <!-- Donations chart -->
            <div class="h-80 rounded-xl border border-border bg-card p-5 shadow-sm">
                <h2 class="mb-4 text-base font-semibold text-foreground">Dons des 6 derniers mois</h2>
                <div class="h-56">
                    <BarChart :chart-data="donationsChart" chart-title="Dons (FCFA)" chart-id="donations-monthly" />
                </div>
            </div>

            <!-- Recent activity -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Upcoming appointments -->
                <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 border-b border-border pb-3 text-base font-semibold text-foreground">
                        <CalendarClock class="size-4 text-primary" />
                        Prochains rendez-vous
                    </h2>
                    <div class="space-y-3">
                        <p v-if="upcomingAppointments.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            Aucun rendez-vous à venir.
                        </p>
                        <div
                            v-for="appointment in upcomingAppointments"
                            :key="appointment.id"
                            class="flex items-start justify-between gap-3 rounded-lg border border-border p-3"
                        >
                            <div>
                                <p class="text-sm font-medium text-foreground">{{ appointment.subject }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ appointment.client.first_name }} {{ appointment.client.last_name }}
                                    <template v-if="appointment.schedule"> · {{ formatDate(appointment.schedule.date) }}</template>
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClasses[appointment.status]">
                                {{ statusLabels[appointment.status] ?? appointment.status }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <Link :href="route('admin.appointments.index')" class="text-sm font-medium text-primary hover:underline">
                            Voir tous les rendez-vous →
                        </Link>
                    </div>
                </div>

                <!-- Recent contacts -->
                <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 border-b border-border pb-3 text-base font-semibold text-foreground">
                        <Mail class="size-4 text-primary" />
                        Derniers messages
                    </h2>
                    <div class="space-y-3">
                        <p v-if="recentContacts.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            Aucun message récent.
                        </p>
                        <div v-for="contact in recentContacts" :key="contact.id" class="rounded-lg border border-border p-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-medium text-foreground">{{ contact.subject }}</p>
                                <span class="shrink-0 text-xs text-muted-foreground">{{ formatDate(contact.created_at) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ contact.client.first_name }} {{ contact.client.last_name }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <Link :href="route('admin.contacts.index')" class="text-sm font-medium text-primary hover:underline">
                            Voir tous les messages →
                        </Link>
                    </div>
                </div>

                <!-- Recent donations -->
                <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 border-b border-border pb-3 text-base font-semibold text-foreground">
                        <HandHeart class="size-4 text-primary" />
                        Derniers dons
                    </h2>
                    <div class="space-y-3">
                        <p v-if="recentDonations.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            Aucun don enregistré.
                        </p>
                        <div v-for="donation in recentDonations" :key="donation.id" class="flex items-start justify-between gap-3 rounded-lg border border-border p-3">
                            <div>
                                <p class="text-sm font-medium text-foreground">{{ donation.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ methodLabels[donation.method] ?? donation.method }} · {{ formatDate(donation.created_at) }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-foreground">{{ fcfa(donation.amount) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <Link :href="route('admin.donations.index')" class="text-sm font-medium text-primary hover:underline">
                            Voir tous les dons →
                        </Link>
                    </div>
                </div>

                <!-- Recent volunteers -->
                <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 border-b border-border pb-3 text-base font-semibold text-foreground">
                        <HeartHandshake class="size-4 text-primary" />
                        Dernières candidatures
                    </h2>
                    <div class="space-y-3">
                        <p v-if="recentVolunteers.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            Aucune candidature récente.
                        </p>
                        <div v-for="volunteer in recentVolunteers" :key="volunteer.id" class="flex items-start justify-between gap-3 rounded-lg border border-border p-3">
                            <div>
                                <p class="text-sm font-medium text-foreground">{{ volunteer.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ formatDate(volunteer.created_at) }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">
                                {{ volunteer.type === 'benevolat' ? 'Bénévolat' : 'Partenariat' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <Link :href="route('admin.volunteers.index')" class="text-sm font-medium text-primary hover:underline">
                            Voir toutes les candidatures →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content overview -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="rounded-full bg-primary/10 p-2 text-primary"><Newspaper class="size-4" /></div>
                    <div>
                        <p class="text-lg font-semibold text-foreground">{{ content.actualites }}</p>
                        <p class="text-xs text-muted-foreground">Actualités publiées</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="rounded-full bg-primary/10 p-2 text-primary"><Images class="size-4" /></div>
                    <div>
                        <p class="text-lg font-semibold text-foreground">{{ content.albums }}</p>
                        <p class="text-xs text-muted-foreground">Albums photos</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 shadow-sm">
                    <div class="rounded-full bg-primary/10 p-2 text-primary"><GraduationCap class="size-4" /></div>
                    <div>
                        <p class="text-lg font-semibold text-foreground">{{ content.masterclasses }}</p>
                        <p class="text-xs text-muted-foreground">Événements actifs</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

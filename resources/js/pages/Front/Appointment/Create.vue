<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed, ref, reactive, watch } from 'vue';
import {
    CheckIcon,
    MagnifyingGlassIcon,
    CalendarIcon,
    UserIcon,
    ClockIcon,
    DocumentTextIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    InformationCircleIcon
} from '@heroicons/vue/24/outline';

// Types
interface Service {
    id: number;
    title: string;
    description: string | null;
    image_path: string | null;
    image_url: string | null;
    min_price: number | null;
}

interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
    slots: number;
    description: string | null;
    is_active: boolean;
}

// Props
interface Props {
    services: Service[];
    schedules: Schedule[];
}

const props = defineProps<Props>();

// Form
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    service_ids: [] as number[],
    schedule_id: null as number | null,
    subject: '',
    description: '',
});

// Multi-step form management
const steps = [
    { id: 'personal', name: 'Informations personnelles', icon: UserIcon, description: 'Vos coordonnées' },
    { id: 'services', name: 'Services', icon: DocumentTextIcon, description: 'Choisissez vos services' },
    { id: 'schedule', name: 'Horaire', icon: CalendarIcon, description: 'Sélectionnez une date et heure' },
    { id: 'details', name: 'Détails', icon: ClockIcon, description: 'Précisez votre demande' },
    { id: 'review', name: 'Confirmation', icon: CheckIcon, description: 'Vérifiez vos informations' }
];

const currentStep = ref(0);

const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
        window.scrollTo(0, 0);
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
        window.scrollTo(0, 0);
    }
};

const goToStep = (index: number) => {
    if (index >= 0 && index < steps.length) {
        currentStep.value = index;
        window.scrollTo(0, 0);
    }
};

const isStepComplete = (stepIndex: number) => {
    switch (stepIndex) {
        case 0: // Personal info
            return !!form.first_name && !!form.last_name && !!form.email;
        case 1: // Services
            return form.service_ids.length > 0;
        case 2: // Schedule
            return form.schedule_id !== null;
        case 3: // Details
            return !!form.subject;
        default:
            return false;
    }
};

// This computed property can be used to conditionally enable/disable the next button
// Currently using isStepComplete directly in the template

// Pagination and search functionality
const searchForm = reactive({
    date: '',
    start_time: '',
    end_time: ''
});

const displayLimit = ref(5);
const showingAllSchedules = ref(false);
const noSchedulesFound = ref(false);
const filteredSchedules = ref<Schedule[]>([]);
const isSearching = ref(false);

// Load more schedules
const loadMoreSchedules = () => {
    displayLimit.value += 5;
};

// Search for availability
const searchAvailability = () => {
    // Reset state
    noSchedulesFound.value = false;
    showingAllSchedules.value = false;
    isSearching.value = true;

    // Filter schedules based on search criteria
    filteredSchedules.value = props.schedules.filter(schedule => {
        let matches = true;

        if (searchForm.date) {
            // Convert both dates to YYYY-MM-DD format for comparison
            const searchDate = searchForm.date; // Already in YYYY-MM-DD format
            const scheduleDate = new Date(schedule.date).toISOString().split('T')[0];

            if (scheduleDate !== searchDate) {
                matches = false;
            }
        }

        if (searchForm.start_time && schedule.start_time < searchForm.start_time) {
            matches = false;
        }

        if (searchForm.end_time && schedule.end_time > searchForm.end_time) {
            matches = false;
        }

        return matches;
    });

    // Check if no schedules were found
    if (filteredSchedules.value.length === 0) {
        noSchedulesFound.value = true;
    } else {
        showingAllSchedules.value = true;
    }

    // Reset display limit
    displayLimit.value = 5;

    // Set timeout to hide searching indicator
    setTimeout(() => {
        isSearching.value = false;
    }, 500);
};

// Show all schedules
const showAllSchedules = () => {
    filteredSchedules.value = props.schedules;
    showingAllSchedules.value = true;
    noSchedulesFound.value = false;
};

// Initialize filtered schedules with all schedules
filteredSchedules.value = props.schedules;

// Group schedules by date for easier selection
const schedulesByDate = computed(() => {
    const grouped = {} as Record<string, Schedule[]>;

    // Use filtered schedules instead of all schedules
    const schedulesToDisplay = filteredSchedules.value.slice(0, displayLimit.value);

    schedulesToDisplay.forEach(schedule => {
        const date = new Date(schedule.date).toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        if (!grouped[date]) {
            grouped[date] = [];
        }

        grouped[date].push(schedule);
    });

    return grouped;
});

// Format time for display
const formatTime = (time: string) => {
    return time.substring(0, 5); // Format HH:MM
};

// Toggle service selection
const toggleService = (serviceId: number) => {
    const index = form.service_ids.indexOf(serviceId);
    if (index === -1) {
        form.service_ids.push(serviceId);
    } else {
        form.service_ids.splice(index, 1);
    }
};

// Check if a service is selected
const isServiceSelected = (serviceId: number) => {
    return form.service_ids.includes(serviceId);
};

// Get selected service names for summary
const selectedServiceNames = computed(() => {
    return props.services
        .filter(service => form.service_ids.includes(service.id))
        .map(service => service.title);
});

// Get selected schedule details for summary
const selectedSchedule = computed(() => {
    if (!form.schedule_id) return null;

    const schedule = props.schedules.find(s => s.id === form.schedule_id);
    if (!schedule) return null;

    const date = new Date(schedule.date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    return {
        date,
        time: `${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}`
    };
});

const breadcrumbItems = [
    { name: 'Accueil', href: '/', current: false },
    { name: 'Prendre rendez-vous', href: '', current: true }
];

// Form validation is handled by the isStepComplete function

// Submit the form
const submit = () => {
    form.post(route('appointment.store'));
};

// Auto-fill today's date in search form
const today = new Date().toISOString().split('T')[0];
searchForm.date = today;

// Watch for schedule selection to auto-advance
watch(() => form.schedule_id, (newVal) => {
    if (newVal !== null && currentStep.value === 2) {
        // Add a small delay to allow the user to see their selection
        setTimeout(() => {
            nextStep();
        }, 500);
    }
});
</script>

<template>
    <LayoutFront>
        <Head title="Prendre rendez-vous" />
        <div class="relative bg-gray-900">
            <!-- Image d'arrière-plan avec overlay -->
            <div class="absolute inset-0 overflow-hidden">
                <img src="/images/breadcrumb-bg.jpg" alt="Bannière Rendez-vous" class="w-full h-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
            </div>

            <!-- Contenu du breadcrumb -->
            <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">Prendre rendez-vous</h1>

                <!-- Breadcrumb navigation -->
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li v-for="(item, index) in breadcrumbItems" :key="item.name">
                            <div class="flex items-center">
                                <Link
                                    :href="item.href"
                                    :class="[
                                        item.current ? 'text-white font-medium' : 'text-white/80 hover:text-white',
                                        'text-sm md:text-base transition-colors'
                                    ]"
                                >
                                    {{ item.name }}
                                </Link>

                                <!-- Séparateur, sauf pour le dernier élément -->
                                <svg
                                    v-if="index !== breadcrumbItems.length - 1"
                                    class="h-5 w-5 text-white/70 mx-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Progress Steps -->
                <nav aria-label="Progress" class="mb-8">
                    <ol role="list" class="space-y-4 md:flex md:space-y-0 md:space-x-8">
                        <li v-for="(step, stepIdx) in steps" :key="step.id" class="md:flex-1">
                            <div
                                class="group flex flex-col border-l-4 border-indigo-600 py-2 pl-4 md:border-l-0 md:border-t-4 md:pb-0 md:pl-0 md:pt-4"
                                :class="[
                                    stepIdx < currentStep ? 'border-indigo-600' :
                                    stepIdx === currentStep ? 'border-indigo-500' :
                                    'border-gray-200'
                                ]"
                            >
                                <span
                                    class="text-sm font-medium flex items-center"
                                    :class="[
                                        stepIdx < currentStep ? 'text-indigo-600' :
                                        stepIdx === currentStep ? 'text-indigo-500' :
                                        'text-gray-500'
                                    ]"
                                >
                                    <component
                                        :is="step.icon"
                                        class="h-5 w-5 mr-2"
                                        :class="[
                                            stepIdx < currentStep ? 'text-indigo-600' :
                                            stepIdx === currentStep ? 'text-indigo-500' :
                                            'text-gray-400'
                                        ]"
                                    />
                                    {{ step.name }}
                                </span>
                                <span class="text-sm text-gray-500">{{ step.description }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Step 1: Personal Information -->
                            <div v-if="currentStep === 0" class="space-y-6 animate-fadeIn">
                                <div class="flex items-center space-x-2 mb-6">
                                    <UserIcon class="h-6 w-6 text-indigo-500" />
                                    <h2 class="text-xl font-medium text-gray-900">Vos informations personnelles</h2>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Veuillez renseigner vos coordonnées pour que nous puissions vous contacter concernant votre rendez-vous.
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            id="first_name"
                                            v-model="form.first_name"
                                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                            placeholder="Votre prénom"
                                        />
                                        <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            id="last_name"
                                            v-model="form.last_name"
                                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                            placeholder="Votre nom"
                                        />
                                        <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                                        <input
                                            type="email"
                                            id="email"
                                            v-model="form.email"
                                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                            placeholder="votre.email@exemple.com"
                                        />
                                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            v-model="form.phone"
                                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="06 12 34 56 78"
                                        />
                                        <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                                        <textarea
                                            id="address"
                                            v-model="form.address"
                                            rows="3"
                                            class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Votre adresse complète"
                                        ></textarea>
                                        <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-8">
                                    <button
                                        type="button"
                                        @click="nextStep"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :disabled="!isStepComplete(0)"
                                    >
                                        Continuer
                                        <ArrowRightIcon class="ml-2 h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Services Selection -->
                            <div v-if="currentStep === 1" class="space-y-6 animate-fadeIn">
                                <div class="flex items-center space-x-2 mb-6">
                                    <DocumentTextIcon class="h-6 w-6 text-indigo-500" />
                                    <h2 class="text-xl font-medium text-gray-900">Sélectionnez un ou plusieurs services</h2>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Choisissez les services qui vous intéressent. Vous pouvez en sélectionner plusieurs.
                                </p>

                                <div v-if="form.errors.service_ids" class="text-red-500 text-sm mb-4 p-3 bg-red-50 rounded-md">
                                    {{ form.errors.service_ids }}
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        v-for="service in services"
                                        :key="service.id"
                                        @click="toggleService(service.id)"
                                        class="border rounded-lg p-4 cursor-pointer transition-all hover:shadow-md"
                                        :class="isServiceSelected(service.id) ? 'border-indigo-500 bg-indigo-50 shadow' : 'border-gray-200 hover:border-indigo-300'"
                                    >
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-5 w-5 rounded-full border flex items-center justify-center"
                                                    :class="isServiceSelected(service.id) ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'"
                                                >
                                                    <CheckIcon v-if="isServiceSelected(service.id)" class="h-3 w-3 text-white" />
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-900">{{ service.title }}</h3>
                                                <p v-if="service.min_price" class="text-sm text-gray-500">À partir de {{ service.min_price }}€</p>
                                                <p v-if="service.description" class="text-sm text-gray-500 mt-1" v-html="service.description"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between mt-8">
                                    <button
                                        type="button"
                                        @click="prevStep"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <ArrowLeftIcon class="mr-2 h-5 w-5" />
                                        Retour
                                    </button>
                                    <button
                                        type="button"
                                        @click="nextStep"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :disabled="!isStepComplete(1)"
                                    >
                                        Continuer
                                        <ArrowRightIcon class="ml-2 h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Schedule Selection -->
                            <div v-if="currentStep === 2" class="space-y-6 animate-fadeIn">
                                <div class="flex items-center space-x-2 mb-6">
                                    <CalendarIcon class="h-6 w-6 text-indigo-500" />
                                    <h2 class="text-xl font-medium text-gray-900">Sélectionnez un créneau horaire</h2>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Choisissez la date et l'heure qui vous conviennent pour votre rendez-vous.
                                </p>

                                <div v-if="form.errors.schedule_id" class="text-red-500 text-sm mb-4 p-3 bg-red-50 rounded-md">
                                    {{ form.errors.schedule_id }}
                                </div>

                                <!-- Search form -->
                                <div class="bg-indigo-50 p-6 rounded-lg mb-6 shadow-sm">
                                    <h3 class="text-md font-medium text-indigo-700 mb-4 flex items-center">
                                        <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                                        Rechercher une disponibilité
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label for="search_date" class="block text-sm font-medium text-gray-700">Date</label>
                                            <input
                                                type="date"
                                                id="search_date"
                                                v-model="searchForm.date"
                                                class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div>
                                            <label for="search_start_time" class="block text-sm font-medium text-gray-700">Heure de début</label>
                                            <input
                                                type="time"
                                                id="search_start_time"
                                                v-model="searchForm.start_time"
                                                class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div>
                                            <label for="search_end_time" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                                            <input
                                                type="time"
                                                id="search_end_time"
                                                v-model="searchForm.end_time"
                                                class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                            type="button"
                                            @click="searchAvailability"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            <MagnifyingGlassIcon class="h-4 w-4 mr-1" />
                                            Rechercher
                                        </button>
                                    </div>
                                </div>

                                <!-- Loading indicator -->
                                <div v-if="isSearching" class="flex justify-center items-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                                    <span class="ml-2 text-gray-600">Recherche en cours...</span>
                                </div>

                                <!-- No schedules found message -->
                                <div v-if="noSchedulesFound" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                                    <p class="text-yellow-700">Aucun créneau disponible pour les critères sélectionnés.</p>
                                    <button
                                        type="button"
                                        @click="showAllSchedules"
                                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                                    >
                                        Voir toutes les disponibilités
                                    </button>
                                </div>

                                <!-- No schedules available message -->
                                <div v-if="Object.keys(schedulesByDate).length === 0 && !noSchedulesFound && !isSearching" class="text-gray-500 italic text-center py-4">
                                    Aucun créneau disponible pour le moment.
                                </div>

                                <!-- Schedule list -->
                                <div v-if="showingAllSchedules && !isSearching" class="animate-fadeIn">
                                    <div v-for="(schedules, date) in schedulesByDate" :key="date" class="mb-8">
                                        <h3 class="text-lg font-medium text-gray-900 mb-3 capitalize flex items-center">
                                            <CalendarIcon class="h-5 w-5 mr-2 text-indigo-500" />
                                            {{ date }}
                                        </h3>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                            <div
                                                v-for="schedule in schedules"
                                                :key="schedule.id"
                                                @click="form.schedule_id = schedule.id"
                                                class="border rounded-md p-3 text-center cursor-pointer transition-all hover:shadow-md"
                                                :class="form.schedule_id === schedule.id ? 'border-indigo-500 bg-indigo-50 shadow' : 'border-gray-200 hover:border-indigo-300'"
                                            >
                                                <span class="block text-sm font-medium">{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Load more button -->
                                <div v-if="showingAllSchedules && filteredSchedules.length > displayLimit && !isSearching" class="mt-6 flex justify-center">
                                    <button
                                        type="button"
                                        @click="loadMoreSchedules"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Voir plus de créneaux
                                    </button>
                                </div>

                                <div class="flex justify-between mt-8">
                                    <button
                                        type="button"
                                        @click="prevStep"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <ArrowLeftIcon class="mr-2 h-5 w-5" />
                                        Retour
                                    </button>
                                    <button
                                        type="button"
                                        @click="nextStep"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :disabled="!isStepComplete(2)"
                                    >
                                        Continuer
                                        <ArrowRightIcon class="ml-2 h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Step 4: Subject and Description -->
                            <div v-if="currentStep === 3" class="space-y-6 animate-fadeIn">
                                <div class="flex items-center space-x-2 mb-6">
                                    <ClockIcon class="h-6 w-6 text-indigo-500" />
                                    <h2 class="text-xl font-medium text-gray-900">Objet et description</h2>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Veuillez préciser l'objet de votre rendez-vous et ajouter des détails si nécessaire.
                                </p>

                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700">Objet du rendez-vous <span class="text-red-500">*</span></label>
                                    <input
                                        type="text"
                                        id="subject"
                                        v-model="form.subject"
                                        class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        placeholder="Ex: Consultation pour robe de mariée"
                                    />
                                    <div v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</div>
                                </div>
                                <div class="mt-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description (facultatif)</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Ajoutez des détails supplémentaires concernant votre rendez-vous..."
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                                </div>

                                <div class="flex justify-between mt-8">
                                    <button
                                        type="button"
                                        @click="prevStep"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <ArrowLeftIcon class="mr-2 h-5 w-5" />
                                        Retour
                                    </button>
                                    <button
                                        type="button"
                                        @click="nextStep"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :disabled="!isStepComplete(3)"
                                    >
                                        Vérifier et confirmer
                                        <ArrowRightIcon class="ml-2 h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Step 5: Review and Confirm -->
                            <div v-if="currentStep === 4" class="space-y-6 animate-fadeIn">
                                <div class="flex items-center space-x-2 mb-6">
                                    <CheckIcon class="h-6 w-6 text-indigo-500" />
                                    <h2 class="text-xl font-medium text-gray-900">Vérifiez et confirmez votre rendez-vous</h2>
                                </div>

                                <p class="text-gray-600 mb-6">
                                    Veuillez vérifier les informations ci-dessous avant de confirmer votre rendez-vous.
                                </p>

                                <div class="bg-gray-50 p-6 rounded-lg space-y-6">
                                    <!-- Personal Information Summary -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <UserIcon class="h-5 w-5 mr-2 text-indigo-500" />
                                            Informations personnelles
                                        </h3>
                                        <div class="bg-white p-4 rounded-md border border-gray-200">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Nom complet</p>
                                                    <p class="font-medium">{{ form.first_name }} {{ form.last_name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500">Email</p>
                                                    <p class="font-medium">{{ form.email }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500">Téléphone</p>
                                                    <p class="font-medium">{{ form.phone || 'Non renseigné' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500">Adresse</p>
                                                    <p class="font-medium">{{ form.address || 'Non renseignée' }}</p>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="goToStep(0)"
                                                class="mt-3 text-sm text-indigo-600 hover:text-indigo-800"
                                            >
                                                Modifier
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Services Summary -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <DocumentTextIcon class="h-5 w-5 mr-2 text-indigo-500" />
                                            Services sélectionnés
                                        </h3>
                                        <div class="bg-white p-4 rounded-md border border-gray-200">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li v-for="(service, index) in selectedServiceNames" :key="index" class="text-gray-800">
                                                    {{ service }}
                                                </li>
                                            </ul>
                                            <button
                                                type="button"
                                                @click="goToStep(1)"
                                                class="mt-3 text-sm text-indigo-600 hover:text-indigo-800"
                                            >
                                                Modifier
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Schedule Summary -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <CalendarIcon class="h-5 w-5 mr-2 text-indigo-500" />
                                            Créneau horaire
                                        </h3>
                                        <div class="bg-white p-4 rounded-md border border-gray-200">
                                            <div v-if="selectedSchedule">
                                                <p class="font-medium">{{ selectedSchedule.date }}</p>
                                                <p class="text-gray-600">{{ selectedSchedule.time }}</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="goToStep(2)"
                                                class="mt-3 text-sm text-indigo-600 hover:text-indigo-800"
                                            >
                                                Modifier
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Details Summary -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <ClockIcon class="h-5 w-5 mr-2 text-indigo-500" />
                                            Détails du rendez-vous
                                        </h3>
                                        <div class="bg-white p-4 rounded-md border border-gray-200">
                                            <div>
                                                <p class="text-sm text-gray-500">Objet</p>
                                                <p class="font-medium">{{ form.subject }}</p>
                                            </div>
                                            <div v-if="form.description" class="mt-3">
                                                <p class="text-sm text-gray-500">Description</p>
                                                <p class="text-gray-800">{{ form.description }}</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="goToStep(3)"
                                                class="mt-3 text-sm text-indigo-600 hover:text-indigo-800"
                                            >
                                                Modifier
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-indigo-50 p-4 rounded-md flex items-start mt-6">
                                    <InformationCircleIcon class="h-5 w-5 text-indigo-500 mt-0.5 flex-shrink-0" />
                                    <p class="ml-3 text-sm text-indigo-700">
                                        En confirmant ce rendez-vous, vous acceptez d'être contacté(e) par notre équipe pour toute information complémentaire.
                                    </p>
                                </div>

                                <div class="flex justify-between mt-8">
                                    <button
                                        type="button"
                                        @click="prevStep"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <ArrowLeftIcon class="mr-2 h-5 w-5" />
                                        Retour
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Traitement en cours...
                                        </span>
                                        <span v-else>Confirmer le rendez-vous</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

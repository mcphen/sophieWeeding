<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-serif font-bold text-gray-900">Disponibilités</h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          Consultez nos créneaux disponibles et réservez votre rendez-vous
        </p>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#d1922f]"></div>
      </div>

      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-8">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">
              {{ error }}
            </p>
          </div>
        </div>
      </div>

      <div v-else-if="schedules.length === 0" class="text-center py-12">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun créneau disponible</h3>
        <p class="text-gray-500">Aucun créneau de disponibilité n'est actuellement programmé. Veuillez nous contacter directement pour plus d'informations.</p>
      </div>

      <div v-else>
        <!-- Filtres par mois -->
        <div class="mb-8 flex flex-wrap justify-center gap-2">
          <button
            v-for="month in availableMonths"
            :key="month.value"
            @click="selectedMonth = month.value"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-colors',
              selectedMonth === month.value
                ? 'bg-[#d1922f] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ month.label }}
          </button>
        </div>

        <!-- Calendrier des disponibilités -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="schedule in filteredSchedules"
            :key="schedule.id"
            class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden"
          >
            <div class="bg-[#f9f5eb] p-4 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">{{ formatDate(schedule.date) }}</h3>
                <span class="px-2 py-1 bg-[#d1922f]/10 text-[#d1922f] text-xs rounded-full font-medium">
                  {{ schedule.slots }} place{{ schedule.slots > 1 ? 's' : '' }}
                </span>
              </div>
            </div>
            <div class="p-4">
              <div class="mb-4">
                <div class="flex justify-between mb-2">
                  <span class="text-gray-600">Horaire:</span>
                  <span class="font-medium">{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</span>
                </div>
                <div v-if="schedule.description" class="mt-3 text-sm text-gray-600">
                  {{ schedule.description }}
                </div>
              </div>
              <a
                href="#"
                @click.prevent="bookAppointment(schedule)"
                class="block w-full text-center px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#b17926] transition-colors"
              >
                Réserver ce créneau
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de réservation -->
    <div v-if="showBookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold text-gray-800">Réserver un rendez-vous</h3>
          <button @click="showBookingModal = false" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="mb-4 p-3 bg-[#f9f5eb] rounded-md">
          <p class="font-medium text-gray-800">{{ formatDate(selectedSchedule?.date || '') }}</p>
          <p class="text-gray-600">{{ formatTime(selectedSchedule?.start_time || '') }} - {{ formatTime(selectedSchedule?.end_time || '') }}</p>
        </div>

        <form @submit.prevent="submitBooking">
          <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom complet</label>
            <input type="text" id="name" v-model="bookingForm.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent" required>
          </div>

          <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" v-model="bookingForm.email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent" required>
          </div>

          <div class="mb-4">
            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Téléphone</label>
            <input type="tel" id="phone" v-model="bookingForm.phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent" required>
          </div>

          <div class="mb-6">
            <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message (optionnel)</label>
            <textarea id="message" v-model="bookingForm.message" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-[#d1922f] focus:border-transparent"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" @click="showBookingModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
              Annuler
            </button>
            <button type="submit" class="px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#b17926] transition" :disabled="bookingInProgress">
              {{ bookingInProgress ? 'Envoi en cours...' : 'Confirmer la réservation' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de confirmation -->
    <div v-if="showConfirmationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-500 mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2 text-center">Réservation confirmée</h3>
        <p class="mb-6 text-gray-600 text-center">
          Votre demande de rendez-vous a été envoyée avec succès. Nous vous contacterons prochainement pour confirmer votre réservation.
        </p>
        <div class="flex justify-center">
          <button
            @click="showConfirmationModal = false"
            class="px-4 py-2 bg-[#d1922f] text-white rounded-md hover:bg-[#b17926] transition"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Types
interface Schedule {
  id: number;
  date: string;
  start_time: string;
  end_time: string;
  slots: number;
  description: string | null;
  is_active: boolean;
}

// État
const schedules = ref<Schedule[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const selectedMonth = ref<string | null>(null);
const showBookingModal = ref(false);
const showConfirmationModal = ref(false);
const selectedSchedule = ref<Schedule | null>(null);
const bookingInProgress = ref(false);

// Formulaire de réservation
const bookingForm = ref({
  name: '',
  email: '',
  phone: '',
  message: ''
});

// Charger les disponibilités
onMounted(async () => {
  try {
    const response = await axios.get('/api/schedules/available');
    schedules.value = response.data;

    // Sélectionner le mois courant par défaut s'il y a des disponibilités
    if (schedules.value.length > 0) {
      const currentMonth = new Date().toISOString().substring(0, 7); // YYYY-MM
      const hasCurrentMonth = schedules.value.some(s => s.date.startsWith(currentMonth));
      selectedMonth.value = hasCurrentMonth ? currentMonth : availableMonths.value[0]?.value || null;
    }
  } catch (err) {
    error.value = "Impossible de charger les disponibilités. Veuillez réessayer plus tard.";
    console.error(err);
  } finally {
    loading.value = false;
  }
});

// Mois disponibles
const availableMonths = computed(() => {
  const months = new Set<string>();

  schedules.value.forEach(schedule => {
    const monthValue = schedule.date.substring(0, 7); // YYYY-MM
    months.add(monthValue);
  });

  return Array.from(months).map(month => {
    const date = new Date(month + '-01');
    const monthName = date.toLocaleDateString('fr-FR', { month: 'long' });
    const year = date.getFullYear();

    return {
      value: month,
      label: `${monthName.charAt(0).toUpperCase() + monthName.slice(1)} ${year}`
    };
  }).sort((a, b) => a.value.localeCompare(b.value));
});

// Créneaux filtrés par mois
const filteredSchedules = computed(() => {
  if (!selectedMonth.value) return schedules.value;

  return schedules.value.filter(schedule =>
    schedule.date.startsWith(selectedMonth.value)
  );
});

// Formatage de la date
function formatDate(dateString: string): string {
  if (!dateString) return '';

  const date = new Date(dateString);
  const options: Intl.DateTimeFormatOptions = {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  };

  const formatted = date.toLocaleDateString('fr-FR', options);
  return formatted.charAt(0).toUpperCase() + formatted.slice(1);
}

// Formatage de l'heure
function formatTime(timeString: string): string {
  if (!timeString) return '';

  // Convertir le format HH:MM:SS en HH:MM
  return timeString.substring(0, 5);
}

// Ouvrir la modal de réservation
function bookAppointment(schedule: Schedule) {
  selectedSchedule.value = schedule;
  showBookingModal.value = true;
}

// Soumettre la réservation
async function submitBooking() {
  if (!selectedSchedule.value) return;

  bookingInProgress.value = true;

  try {
    // Ici, vous pourriez implémenter l'envoi de la réservation à votre backend
    // Pour l'instant, nous simulons juste une réponse réussie
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Fermer la modal de réservation et afficher la confirmation
    showBookingModal.value = false;
    showConfirmationModal.value = true;

    // Réinitialiser le formulaire
    bookingForm.value = {
      name: '',
      email: '',
      phone: '',
      message: ''
    };
  } catch (err) {
    alert("Une erreur est survenue lors de la réservation. Veuillez réessayer.");
    console.error(err);
  } finally {
    bookingInProgress.value = false;
  }
}
</script>

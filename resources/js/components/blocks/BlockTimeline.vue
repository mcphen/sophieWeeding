<script setup lang="ts">
import { ref, computed } from 'vue';

interface TimelineEvent {
  id: string;
  year: string;
  text: string;
}

const props = defineProps<{
  modelValue: TimelineEvent[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: TimelineEvent[]): void;
}>();

// Create a local copy of the events
const events = ref<TimelineEvent[]>(props.modelValue || []);

// Update the parent when events change
const updateEvents = () => {
  emit('update:modelValue', events.value);
};

// Add a new event
const addEvent = () => {
  const newEvent: TimelineEvent = {
    id: Date.now().toString(),
    year: '',
    text: ''
  };
  events.value.push(newEvent);
  updateEvents();
};

// Remove an event
const removeEvent = (id: string) => {
  const index = events.value.findIndex(event => event.id === id);
  if (index !== -1) {
    events.value.splice(index, 1);
    updateEvents();
  }
};

// Move an event up
const moveUp = (id: string) => {
  const index = events.value.findIndex(event => event.id === id);
  if (index > 0) {
    const temp = events.value[index];
    events.value[index] = events.value[index - 1];
    events.value[index - 1] = temp;
    updateEvents();
  }
};

// Move an event down
const moveDown = (id: string) => {
  const index = events.value.findIndex(event => event.id === id);
  if (index < events.value.length - 1) {
    const temp = events.value[index];
    events.value[index] = events.value[index + 1];
    events.value[index + 1] = temp;
    updateEvents();
  }
};

// Update an event
const updateEvent = (id: string, field: 'year' | 'text', value: string) => {
  const event = events.value.find(event => event.id === id);
  if (event) {
    event[field] = value;
    updateEvents();
  }
};

// Check if there are any events
const hasEvents = computed(() => events.value.length > 0);
</script>

<template>
  <div class="block-timeline bg-white p-4 rounded-lg border border-gray-300">
    <div class="mb-4 flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
      <button
        type="button"
        @click="addEvent"
        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-1"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Ajouter un événement
      </button>
    </div>

    <div v-if="!hasEvents" class="text-center py-8 text-gray-500">
      Aucun événement dans la timeline. Cliquez sur "Ajouter un événement" pour commencer.
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="(event, index) in events"
        :key="event.id"
        class="bg-gray-50 p-4 rounded-md border border-gray-200"
      >
        <div class="flex justify-between items-start mb-3">
          <h4 class="font-medium text-gray-700">Événement {{ index + 1 }}</h4>
          <div class="flex gap-1">
            <button
              type="button"
              @click="moveUp(event.id)"
              :disabled="index === 0"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30"
              title="Déplacer vers le haut"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="moveDown(event.id)"
              :disabled="index === events.length - 1"
              class="p-1 text-gray-500 hover:text-gray-700 disabled:opacity-30"
              title="Déplacer vers le bas"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              type="button"
              @click="removeEvent(event.id)"
              class="p-1 text-red-500 hover:text-red-700"
              title="Supprimer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-1">
            <label :for="`event-year-${event.id}`" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
            <input
              :id="`event-year-${event.id}`"
              type="text"
              v-model="event.year"
              @input="updateEvent(event.id, 'year', event.year)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              placeholder="ex: 2020"
            >
          </div>
          <div class="md:col-span-3">
            <label :for="`event-text-${event.id}`" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              :id="`event-text-${event.id}`"
              v-model="event.text"
              @input="updateEvent(event.id, 'text', event.text)"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              placeholder="Description de l'événement"
            ></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

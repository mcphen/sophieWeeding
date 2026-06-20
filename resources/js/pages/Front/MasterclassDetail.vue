<template>
    <Head>
        <title>{{ og.title }} – Sophie Weddings Dreams</title>
        <meta head-key="description" name="description" :content="og.description" />

        <!-- Open Graph -->
        <meta head-key="og:type"        property="og:type"        content="website" />
        <meta head-key="og:url"         property="og:url"         :content="og.url" />
        <meta head-key="og:title"       property="og:title"       :content="og.title" />
        <meta head-key="og:description" property="og:description" :content="og.description" />
        <meta v-if="og.image" head-key="og:image" property="og:image" :content="og.image" />
        <meta head-key="og:site_name"   property="og:site_name"   content="Sophie Weddings Dreams" />
        <meta head-key="og:locale"      property="og:locale"      content="fr_FR" />

        <!-- Twitter Card -->
        <meta head-key="twitter:card"        name="twitter:card"        content="summary_large_image" />
        <meta head-key="twitter:title"       name="twitter:title"       :content="og.title" />
        <meta head-key="twitter:description" name="twitter:description" :content="og.description" />
        <meta v-if="og.image" head-key="twitter:image" name="twitter:image" :content="og.image" />
    </Head>
    <LayoutFront>
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Fil d'Ariane -->
                <nav class="flex mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li><Link :href="route('home')" class="text-gray-400 hover:text-gray-600">Accueil</Link></li>
                        <li><span class="text-gray-300 mx-1">›</span></li>
                        <li><Link :href="route('masterclasses')" class="text-gray-400 hover:text-gray-600">Masterclasses</Link></li>
                        <li><span class="text-gray-300 mx-1">›</span></li>
                        <li><span class="text-gray-600 font-medium">{{ masterclass.title }}</span></li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                    <!-- Contenu principal -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- En-tête masterclass -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div v-if="masterclass.image_url" class="h-72 sm:h-80">
                                <img :src="masterclass.image_url" :alt="masterclass.title" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="h-48 bg-[#d1922f]/10 flex items-center justify-center">
                                <svg class="h-20 w-20 text-[#d1922f]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="p-6 sm:p-8">
                                <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                                    <span class="inline-block bg-[#d1922f] text-white text-xs font-semibold px-3 py-1 rounded-full">{{ masterclass.niveau }}</span>
                                    <!-- Bouton partager -->
                                    <div class="relative">
                                        <button @click="toggleShare" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#d1922f] transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                            Partager
                                        </button>
                                        <div v-if="showShare" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-10 overflow-hidden">
                                            <button @click="copyLink" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                                {{ copied ? 'Lien copié !' : 'Copier le lien' }}
                                            </button>
                                            <a :href="whatsappUrl" target="_blank" @click="showShare = false" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="h-4 w-4 text-green-500" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                WhatsApp
                                            </a>
                                            <a :href="facebookUrl" target="_blank" @click="showShare = false" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="text-3xl font-serif font-bold text-gray-900">{{ masterclass.title }}</h1>
                                <p v-if="masterclass.description" class="mt-4 text-gray-600 leading-relaxed">{{ masterclass.description }}</p>

                                <div v-if="masterclass.programme" class="mt-6">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Programme</h2>
                                    <div class="prose prose-sm max-w-none text-gray-600" v-html="masterclass.programme"></div>
                                </div>

                                <div v-if="masterclass.document_url" class="mt-6 flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                    <svg class="h-8 w-8 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <a :href="masterclass.document_url" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">
                                        Télécharger le programme détaillé (PDF)
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Sessions disponibles -->
                        <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-5">Sessions disponibles</h2>

                            <div v-if="sessions.length === 0" class="text-center py-8 text-gray-500">
                                <p>Aucune session à venir pour le moment.</p>
                                <p class="text-sm mt-1">Revenez prochainement ou contactez-nous.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div
                                    v-for="s in sessions"
                                    :key="s.id"
                                    class="border rounded-lg p-4 cursor-pointer transition"
                                    :class="[
                                        s.is_full ? 'opacity-60 cursor-not-allowed border-gray-200' : 'hover:border-[#d1922f] hover:bg-[#d1922f]/5',
                                        selectedSession?.id === s.id ? 'border-[#d1922f] bg-[#d1922f]/5 ring-1 ring-[#d1922f]' : 'border-gray-200',
                                    ]"
                                    @click="!s.is_full && selectSession(s)"
                                >
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-semibold text-gray-900">{{ s.start_date }}</span>
                                                <span class="text-gray-500 text-sm">{{ s.start_time }}<span v-if="s.end_time"> – {{ s.end_time }}</span></span>
                                                <span class="px-2 py-0.5 text-xs rounded-full font-medium"
                                                    :class="{
                                                        'bg-blue-100 text-blue-700': s.location_type === 'online',
                                                        'bg-green-100 text-green-700': s.location_type === 'presentiel',
                                                        'bg-purple-100 text-purple-700': s.location_type === 'both',
                                                    }">
                                                    {{ s.location_label }}
                                                </span>
                                            </div>
                                            <div v-if="s.adresse" class="mt-1 text-sm text-gray-500 flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                {{ s.adresse }}
                                                <a v-if="s.google_maps_url" :href="s.google_maps_url" target="_blank" @click.stop class="text-[#d1922f] hover:underline ml-1">Voir sur Maps</a>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-[#d1922f]">{{ s.formatted_price }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <span v-if="s.is_full" class="text-red-600 font-medium">Complet</span>
                                                <span v-else-if="s.max_participants !== null">{{ s.available_spots }} place(s) restante(s)</span>
                                                <span v-else>Places disponibles</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="selectedSession?.id === s.id" class="mt-2 flex items-center gap-1 text-xs text-[#d1922f] font-medium">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Session sélectionnée
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Autres masterclasses -->
                        <div v-if="otherMasterclasses.length > 0">
                            <h2 class="text-xl font-serif font-bold text-gray-900 mb-4">Autres masterclasses</h2>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <Link v-for="mc in otherMasterclasses" :key="mc.id" :href="route('masterclass.show', mc.slug)" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                                    <div class="h-28 bg-[#d1922f]/10 relative">
                                        <img v-if="mc.image_url" :src="mc.image_url" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="p-3">
                                        <span class="text-xs text-[#d1922f] font-medium">{{ mc.niveau }}</span>
                                        <p class="font-medium text-gray-900 text-sm line-clamp-1 mt-0.5">{{ mc.title }}</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar formulaire inscription -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">S'inscrire</h2>

                            <div v-if="sessions.length === 0" class="text-sm text-gray-500 text-center py-4">
                                Aucune session disponible pour le moment.
                            </div>

                            <div v-else-if="!selectedSession" class="text-sm text-[#d1922f] text-center py-4 bg-[#d1922f]/5 rounded-lg">
                                ← Sélectionnez une session pour vous inscrire
                            </div>

                            <!-- Session complète → formulaire liste d'attente -->
                            <div v-else-if="selectedSession.is_full">
                                <!-- Succès liste d'attente -->
                                <div v-if="waitlistSuccess" class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-amber-800">Vous êtes sur liste d'attente !</p>
                                    <p class="text-xs text-amber-600 mt-1">Nous vous contacterons dès qu'une place se libère.</p>
                                </div>

                                <template v-else>
                                    <!-- Bannière "complet" -->
                                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-4">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs text-red-700 font-medium">Cette session est complète.</p>
                                    </div>

                                    <!-- CTA liste d'attente -->
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                                        <p class="text-sm font-semibold text-amber-800 mb-1">Rejoindre la liste d'attente</p>
                                        <p class="text-xs text-amber-700 leading-relaxed">
                                            Inscrivez-vous sur liste d'attente — vous serez notifié(e) par email dès qu'une place se libère ou qu'une nouvelle session est ajoutée.
                                        </p>
                                    </div>

                                    <!-- Alerte doublon -->
                                    <div v-if="waitlistInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                        <p class="text-xs text-blue-700">{{ waitlistInfo }}</p>
                                    </div>

                                    <form @submit.prevent="submitWaitlist" class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nom complet *</label>
                                            <input v-model="waitlistForm.name" type="text" required
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                                            <p v-if="waitlistErrors.name" class="text-red-600 text-xs mt-1">{{ waitlistErrors.name }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Email *</label>
                                            <input v-model="waitlistForm.email" type="email" required
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                                            <p v-if="waitlistErrors.email" class="text-red-600 text-xs mt-1">{{ waitlistErrors.email }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Téléphone</label>
                                            <input v-model="waitlistForm.phone" type="tel"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                                        </div>
                                        <button
                                            type="submit"
                                            :disabled="waitlistProcessing"
                                            class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-md text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 disabled:opacity-50 transition-colors"
                                        >
                                            <svg v-if="waitlistProcessing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            M'inscrire sur liste d'attente
                                        </button>
                                    </form>
                                </template>
                            </div>

                            <div v-else-if="success" class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                                <p class="text-sm text-green-700 font-medium">Inscription enregistrée !</p>
                                <p class="text-sm text-green-600 mt-1">Vous recevrez un email de confirmation.</p>
                            </div>

                            <form v-else @submit.prevent="submitForm" class="space-y-4">
                                <!-- Récap session sélectionnée -->
                                <div v-if="selectedSession" class="bg-[#d1922f]/5 rounded-lg p-3 text-sm">
                                    <p class="font-medium text-gray-800">{{ selectedSession.start_date }} à {{ selectedSession.start_time }}</p>
                                    <p class="text-gray-600">{{ selectedSession.location_label }}</p>
                                    <p class="text-[#d1922f] font-semibold mt-1">{{ selectedSession.formatted_price }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                                    <input v-model="form.name" type="text" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                                    <p v-if="errors.name" class="text-red-600 text-xs mt-1">{{ errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input v-model="form.email" type="email" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                                    <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                                    <input v-model="form.phone" type="tel" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm" />
                                    <p v-if="errors.phone" class="text-red-600 text-xs mt-1">{{ errors.phone }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                                    <textarea v-model="form.message" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#d1922f] focus:ring-[#d1922f] text-sm"></textarea>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#d1922f] hover:bg-[#c08529] disabled:opacity-50"
                                >
                                    <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Je m'inscris
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </LayoutFront>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

type Session = {
    id: number;
    start_date: string;
    start_time: string;
    end_time: string | null;
    start_date_iso: string;
    end_date_iso: string | null;
    location_type: string;
    location_label: string;
    adresse: string | null;
    google_maps_url: string | null;
    online_link: string | null;
    formatted_price: string;
    price_raw: number | null;
    max_participants: number | null;
    available_spots: number | null;
    is_full: boolean;
};

const props = defineProps<{
    masterclass: {
        id: number;
        title: string;
        niveau: string;
        description: string | null;
        programme: string | null;
        image_url: string | null;
        document_url: string | null;
        slug: string;
    };
    sessions: Session[];
    otherMasterclasses: Array<{ id: number; title: string; niveau: string; image_url: string | null; slug: string }>;
    contactSettings: any;
    ctaSettings: any;
    og: { title: string; description: string; image: string | null; url: string };
}>();

// ── Schema.org ───────────────────────────────────────────────────────────────

const attendanceModeMap: Record<string, string> = {
    presentiel: 'https://schema.org/OfflineEventAttendanceMode',
    online:     'https://schema.org/OnlineEventAttendanceMode',
    both:       'https://schema.org/MixedEventAttendanceMode',
};

const schemaEvents = computed(() =>
    props.sessions.map(s => ({
        '@context': 'https://schema.org',
        '@type': 'Event',
        name: props.masterclass.title,
        description: props.masterclass.description ?? undefined,
        startDate: s.start_date_iso,
        endDate: s.end_date_iso ?? undefined,
        eventStatus: 'https://schema.org/EventScheduled',
        eventAttendanceMode: attendanceModeMap[s.location_type] ?? attendanceModeMap.presentiel,
        image: props.og.image ?? undefined,
        url: props.og.url,
        location: s.location_type !== 'online'
            ? { '@type': 'Place', name: s.adresse ?? 'À préciser', address: s.adresse ?? undefined }
            : { '@type': 'VirtualLocation', url: s.online_link ?? props.og.url },
        organizer: {
            '@type': 'Organization',
            name: 'Sophie Weddings Dreams',
            url: window.location.origin,
        },
        offers: {
            '@type': 'Offer',
            price: s.price_raw ?? 0,
            priceCurrency: 'XOF',
            availability: s.is_full
                ? 'https://schema.org/SoldOut'
                : 'https://schema.org/InStock',
        },
    }))
);

let schemaScripts: HTMLScriptElement[] = [];

onMounted(() => {
    schemaScripts = schemaEvents.value.map(event => {
        const el = document.createElement('script');
        el.type = 'application/ld+json';
        el.text = JSON.stringify(event);
        document.head.appendChild(el);
        return el;
    });
});

onUnmounted(() => {
    schemaScripts.forEach(el => el.remove());
    schemaScripts = [];
});

// ── Partage ───────────────────────────────────────────────────────────────────

const showShare = ref(false);
const copied = ref(false);

const whatsappUrl = computed(() =>
    `https://wa.me/?text=${encodeURIComponent(props.og.title + '\n' + props.og.url)}`
);
const facebookUrl = computed(() =>
    `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.og.url)}`
);

const toggleShare = async () => {
    if (navigator.share) {
        try {
            await navigator.share({ title: props.og.title, text: props.og.description, url: props.og.url });
            return;
        } catch {}
    }
    showShare.value = !showShare.value;
};

const copyLink = async () => {
    await navigator.clipboard.writeText(props.og.url);
    copied.value = true;
    setTimeout(() => { copied.value = false; showShare.value = false; }, 2000);
};

const closeShareOnOutsideClick = (e: MouseEvent) => {
    if (!(e.target as Element).closest('.relative')) showShare.value = false;
};

onMounted(() => document.addEventListener('click', closeShareOnOutsideClick));
onUnmounted(() => document.removeEventListener('click', closeShareOnOutsideClick));

// ── Formulaire inscription ────────────────────────────────────────────────────

const selectedSession = ref<Session | null>(null);
const success = ref(false);
const processing = ref(false);
const errors = ref<Record<string, string>>({});

const form = useForm({ name: '', email: '', phone: '', message: '' });

const selectSession = (s: Session) => {
    selectedSession.value = s;
    success.value = false;
    waitlistSuccess.value = false;
    waitlistInfo.value = '';
    errors.value = {};
    waitlistErrors.value = {};
};

const submitForm = () => {
    if (!selectedSession.value) return;
    processing.value = true;
    errors.value = {};
    form.post(route('masterclass.register', { masterclass: props.masterclass.id, session: selectedSession.value.id }), {
        onSuccess: () => { success.value = true; processing.value = false; form.reset(); },
        onError: (formErrors) => { errors.value = formErrors; processing.value = false; },
    });
};

// ── Formulaire liste d'attente ────────────────────────────────────────────────

const page = usePage();
const flash = computed(() => (page.props.flash ?? {}) as Record<string, string>);

const waitlistSuccess = ref(false);
const waitlistInfo = ref('');
const waitlistProcessing = ref(false);
const waitlistErrors = ref<Record<string, string>>({});
const waitlistForm = useForm({ name: '', email: '', phone: '' });

// Réagit aux flash Inertia après la soumission
watch(flash, (f) => {
    if (f.waitlist_success) waitlistSuccess.value = true;
    if (f.waitlist_info)    waitlistInfo.value = f.waitlist_info;
});

const submitWaitlist = () => {
    if (!selectedSession.value) return;
    waitlistProcessing.value = true;
    waitlistErrors.value = {};
    waitlistForm.post(route('masterclass.waitlist', { masterclass: props.masterclass.id, session: selectedSession.value.id }), {
        onError: (errs) => { waitlistErrors.value = errs; },
        onFinish: () => { waitlistProcessing.value = false; waitlistForm.reset(); },
    });
};
</script>

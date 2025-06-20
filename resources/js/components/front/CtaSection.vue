<template>
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-[#d1922f] to-[#bf8529] rounded-2xl overflow-hidden shadow-xl">
                <div class="px-8 py-16 sm:px-16 sm:py-20 lg:flex lg:items-center">
                    <div class="lg:w-0 lg:flex-1">
                        <h2 class="text-3xl font-bold tracking-tight text-white">
                            Prêts à planifier le mariage de vos rêves ?
                        </h2>
                        <p class="mt-4 max-w-3xl text-lg text-[#faecd2]">
                            Contactez-nous dès aujourd'hui pour une consultation gratuite et commencez à transformer votre vision en réalité.
                        </p>
                    </div>
                    <div class="mt-12 sm:w-full sm:max-w-md lg:mt-0 lg:ml-8 lg:flex-1">
                        <Link
                            :href="route('appointment.create')"
                            class="flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-full text-[#d1922f] bg-white hover:bg-[#fdf7eb] md:py-4 md:text-lg md:px-10"
                        >
                            Prendre rendez-vous
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

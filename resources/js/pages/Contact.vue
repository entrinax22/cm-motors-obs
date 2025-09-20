<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Mail, MapPin, Phone } from 'lucide-vue-next';
</script>

<template>
    <Head title="Contact Us" />
    <MainLayout>
        <!-- Hero -->
        <section class="relative bg-gray-800 px-6 py-20 text-center text-white">
            <h1 class="mb-4 text-4xl font-extrabold text-yellow-400 md:text-5xl">Contact Us</h1>
            <p class="mx-auto max-w-2xl text-gray-300">We’d love to hear from you! Reach out for bookings, inquiries, or support.</p>
        </section>

        <!-- Contact Info -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg">
                <Phone class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Phone</h2>
                <p class="text-gray-300">+63 912 345 6789</p>
            </div>
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg">
                <Mail class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Email</h2>
                <p class="text-gray-300">info@cmmotorparts.com</p>
            </div>
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg">
                <MapPin class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Location</h2>
                <p class="text-gray-300">Surigao City, Philippines</p>
            </div>
        </section>

        <!-- Contact Form -->
        <section class="bg-gray-700 px-6 py-16">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-6 text-2xl font-bold text-white">Send us a Message</h2>
                <form class="space-y-6">
                    <div>
                        <input
                            type="text"
                            placeholder="Your Name"
                            class="w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-3 text-white placeholder-gray-400 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                        />
                    </div>
                    <div>
                        <input
                            type="email"
                            placeholder="Your Email"
                            class="w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-3 text-white placeholder-gray-400 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                        />
                    </div>
                    <div>
                        <textarea
                            rows="5"
                            placeholder="Your Message"
                            class="w-full rounded-lg border border-gray-600 bg-gray-900 px-4 py-3 text-white placeholder-gray-400 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        class="rounded-lg bg-yellow-500 px-6 py-3 font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
                    >
                        Send Message
                    </button>
                </form>
            </div>
        </section>
    </MainLayout>
</template>

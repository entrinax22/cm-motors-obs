<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Battery, Car, Settings } from 'lucide-vue-next';
</script>

<template>
    <Head title="Our Services" />
    <MainLayout>
        <!-- Hero -->
        <section class="relative bg-gray-800 px-6 py-20 text-center text-white">
            <h1 class="mb-4 text-4xl font-extrabold text-yellow-400 md:text-5xl">Our Services</h1>
            <p class="mx-auto max-w-2xl text-gray-300">Explore our wide range of automotive services designed to keep your vehicle in top shape.</p>
        </section>

        <!-- Services Grid -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Car class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">General Auto Repair</h2>
                <p class="text-gray-300">From engine tune-ups to brake services, we handle all kinds of repair and maintenance.</p>
            </div>
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Settings class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Diagnostics</h2>
                <p class="text-gray-300">We use modern tools to diagnose issues quickly and accurately, saving you time and money.</p>
            </div>
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Battery class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Battery & Electrical</h2>
                <p class="text-gray-300">Battery replacement, wiring, and complete electrical system checks for smooth performance.</p>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-16 text-center text-white">
            <h2 class="mb-6 text-3xl font-bold">Need a Service Today?</h2>
            <p class="mb-8 text-lg text-gray-300">Book your appointment now and let our experts take care of your car.</p>
            <Link
                href="/booking"
                class="rounded-lg bg-yellow-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
            >
                Book Now
            </Link>
        </section>
    </MainLayout>
</template>

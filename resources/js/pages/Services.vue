<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

import { Cog, Droplets, Gauge, ShieldCheck, Wrench, Zap } from 'lucide-vue-next';
</script>

<template>
    <Head title="Our Services" />

    <MainLayout>
        <!-- Hero Section -->
        <section class="relative bg-gray-800 px-6 py-20 text-center text-white">
            <h1 class="mb-4 text-4xl font-extrabold text-yellow-400 md:text-5xl">CM Motors Services</h1>
            <p class="mx-auto max-w-2xl text-gray-300">
                Explore our expert motorcycle repair and maintenance services designed to keep your bike running smoothly and safely.
            </p>
        </section>

        <!-- Services Grid -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <!-- Service 1 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Wrench class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">General Motorcycle Repair</h2>
                <p class="text-gray-300">From engine tune-ups to clutch, brake, and chain adjustments — we fix all kinds of motorcycle issues.</p>
            </div>

            <!-- Service 2 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Droplets class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Oil Change & Maintenance</h2>
                <p class="text-gray-300">Regular oil and filter changes to keep your motorcycle engine performing at its best.</p>
            </div>

            <!-- Service 3 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Zap class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Battery & Electrical</h2>
                <p class="text-gray-300">Battery replacement, wiring, lighting, and electrical system troubleshooting for all motorcycle types.</p>
            </div>

            <!-- Service 4 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Gauge class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Performance Tuning</h2>
                <p class="text-gray-300">Boost your bike’s power and efficiency with our carburetor and fuel tuning services.</p>
            </div>

            <!-- Service 5 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <Cog class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Custom Modifications</h2>
                <p class="text-gray-300">Upgrade your motorcycle with custom exhausts, accessories, and body modifications to match your style.</p>
            </div>

            <!-- Service 6 -->
            <div class="rounded-xl bg-gray-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl">
                <ShieldCheck class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-lg font-bold text-yellow-400">Safety Inspection</h2>
                <p class="text-gray-300">
                    Full safety checks — brakes, tires, fluids, and lights — ensuring your motorcycle is road-ready and safe to ride.
                </p>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-16 text-center text-white">
            <h2 class="mb-6 text-3xl font-bold">Need Motorcycle Service Today?</h2>
            <p class="mb-8 text-lg text-gray-300">Book your service now and let our experts take care of your motorcycle.</p>
            <Link
                href="/contact-us"
                class="rounded-lg bg-yellow-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
            >
                Contact Us
            </Link>
        </section>
    </MainLayout>
</template>

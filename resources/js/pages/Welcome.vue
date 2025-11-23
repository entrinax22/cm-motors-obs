<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, ThumbsUp, Wrench } from 'lucide-vue-next';
import { ref } from 'vue';
import BookingModal from '../components/BookingModal.vue';

const showBookingModal = ref(false);
</script>

<template>
    <Head title="Welcome" />
    <MainLayout>
        <!-- Hero Section -->
        <section
            class="relative flex min-h-[75vh] flex-col items-center justify-center bg-cover bg-center px-6 py-20 text-center"
            style="background-image: url('/images/bg_motor.png')"
        >
            <div class="absolute inset-0 bg-blue-900/70"></div>

            <div class="relative z-10 flex flex-col items-center">
                <img
                    src="/images/logo_cm_motors.png"
                    alt="CM Motorparts Logo"
                    class="mx-auto mb-6 h-28 w-28 rounded-full shadow-2xl ring-4 ring-cyan-400"
                />

                <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-white md:text-5xl lg:text-6xl">
                    Your Trusted <span class="text-cyan-400">Motorcycle Experts</span>
                </h1>

                <p class="mb-10 max-w-2xl text-lg text-gray-200 md:text-xl">
                    From tune-ups to custom builds — CM Motorparts keeps your ride running smooth, safe, and powerful.
                </p>

                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <button
                        @click="showBookingModal = true"
                        class="rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-3 text-lg font-semibold text-white shadow-lg transition-transform hover:scale-105 hover:from-blue-500 hover:to-cyan-400"
                    >
                        Book a Service
                    </button>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <div
                class="rounded-xl border border-blue-700 bg-blue-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <Wrench class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-xl font-bold text-cyan-400">Certified Mechanics</h2>
                <p class="text-gray-200">Our technicians specialize in all major motorcycle brands — from Honda to Yamaha, Kawasaki, and Suzuki.</p>
            </div>
            <div
                class="rounded-xl border border-blue-700 bg-blue-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <ShieldCheck class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-xl font-bold text-cyan-400">Genuine Motorcycle Parts</h2>
                <p class="text-gray-200">We stock only high-quality, authentic parts to guarantee safety and top performance for every ride.</p>
            </div>
            <div
                class="rounded-xl border border-blue-700 bg-blue-900 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <ThumbsUp class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-xl font-bold text-cyan-400">Trusted by Riders</h2>
                <p class="text-gray-200">Thousands of satisfied riders trust CM Motorparts for fast, reliable service and affordable prices.</p>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="bg-blue-950 px-6 py-16">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="mb-6 text-2xl font-bold text-white md:text-3xl">What Riders Say</h2>
                <p class="mb-10 text-gray-300">See what our loyal customers think about CM Motorparts’ service and expertise.</p>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-xl bg-blue-900 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">“Excellent service! My motorbike runs smoother than ever.”</p>
                        <span class="mt-3 block text-sm font-semibold text-cyan-400">- Mark R.</span>
                    </div>
                    <div class="rounded-xl bg-blue-900 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">“Affordable rates and quick turnaround. Highly recommended!”</p>
                        <span class="mt-3 block text-sm font-semibold text-cyan-400">- Ana P.</span>
                    </div>
                    <div class="rounded-xl bg-blue-900 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">“Friendly and professional staff. I always come back here.”</p>
                        <span class="mt-3 block text-sm font-semibold text-cyan-400">- Leo G.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-gradient-to-r from-blue-900 to-cyan-900 px-6 py-16 text-center text-white">
            <h2 class="mb-6 text-3xl font-bold">Need Maintenance or Spare Parts?</h2>
            <p class="mb-8 text-lg text-gray-200">Book a service now or explore our wide selection of motorcycle parts and accessories.</p>
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <button
                    @click="showBookingModal = true"
                    class="rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-3 text-lg font-semibold text-white shadow-lg transition-transform hover:scale-105 hover:from-blue-500 hover:to-cyan-400"
                >
                    Book Now
                </button>
                <Link
                    href="/services"
                    class="rounded-lg border border-cyan-400 px-8 py-3 text-lg font-semibold text-cyan-400 shadow-sm transition-transform hover:scale-105 hover:bg-cyan-400 hover:text-gray-900"
                >
                    Shop Services
                </Link>
            </div>
        </section>
    </MainLayout>

    <!-- Booking Modal Component -->
    <BookingModal v-model="showBookingModal" />
</template>

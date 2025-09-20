<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Smile, Wrench } from 'lucide-vue-next'; // icons
</script>

<template>
    <Head title="Welcome" />
    <MainLayout>
        <!-- Hero Section -->
        <section
            class="relative flex min-h-[75vh] flex-col items-center justify-center bg-cover bg-center px-6 py-20 text-center"
            style="background-image: url('/images/bg_motor.png')"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gray-900/70"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center">
                <img
                    src="/images/logo_cm_motors.png"
                    alt="CM Motorparts Logo"
                    class="ring-gold-400 mx-auto mb-6 h-28 w-28 rounded-full shadow-2xl ring-4"
                />

                <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-white md:text-5xl lg:text-6xl">
                    Welcome to <span class="text-yellow-400">CM Motorparts</span>
                </h1>

                <p class="mb-10 max-w-2xl text-lg text-gray-200 md:text-xl">
                    Your trusted partner for all automotive needs. <br />
                    Quality service, expert technicians, and customer satisfaction guaranteed.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link
                        href="/booking"
                        class="rounded-lg bg-yellow-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
                    >
                        Book an Appointment
                    </Link>
                    <Link
                        href="/services"
                        class="rounded-lg border border-yellow-400 px-8 py-3 text-lg font-semibold text-yellow-400 shadow-sm transition-transform hover:scale-105 hover:bg-yellow-400 hover:text-gray-900"
                    >
                        View Services
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <div
                class="rounded-xl border border-gray-700 bg-gray-800 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <Wrench class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-xl font-bold text-yellow-400">Expert Technicians</h2>
                <p class="text-gray-300">Our team is composed of highly trained professionals ready to handle any automotive issue.</p>
            </div>
            <div
                class="rounded-xl border border-gray-700 bg-gray-800 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <ShieldCheck class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-xl font-bold text-yellow-400">Quality Parts</h2>
                <p class="text-gray-300">We use only genuine and high-quality parts to ensure your vehicle's performance and safety.</p>
            </div>
            <div
                class="rounded-xl border border-gray-700 bg-gray-800 p-8 text-center shadow-lg transition-transform hover:-translate-y-1 hover:shadow-2xl"
            >
                <Smile class="mx-auto mb-4 h-10 w-10 text-yellow-400" />
                <h2 class="mb-2 text-xl font-bold text-yellow-400">Customer Satisfaction</h2>
                <p class="text-gray-300">We are committed to providing excellent service and ensuring our customers leave happy.</p>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="bg-gray-700 px-6 py-16">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="mb-6 text-2xl font-bold text-white md:text-3xl">What Our Customers Say</h2>
                <p class="mb-10 text-gray-300">Don’t just take our word for it. See what real clients have to say about CM Motorparts.</p>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-xl bg-gray-800 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">"Great service! My car feels brand new again."</p>
                        <span class="mt-3 block text-sm font-semibold text-yellow-400">- Juan D.</span>
                    </div>
                    <div class="rounded-xl bg-gray-800 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">"Friendly staff and quality work. Highly recommend!"</p>
                        <span class="mt-3 block text-sm font-semibold text-yellow-400">- Maria S.</span>
                    </div>
                    <div class="rounded-xl bg-gray-800 p-6 shadow-lg hover:shadow-2xl">
                        <p class="text-gray-200">"They fixed my issue fast and affordable. Thanks!"</p>
                        <span class="mt-3 block text-sm font-semibold text-yellow-400">- Carlo R.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-16 text-center text-white">
            <h2 class="mb-6 text-3xl font-bold">Ready to Get Started?</h2>
            <p class="mb-8 text-lg text-gray-300">Book your appointment today and experience hassle-free automotive service.</p>
            <Link
                href="/booking"
                class="rounded-lg bg-yellow-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
            >
                Book Now
            </Link>
        </section>
    </MainLayout>
</template>

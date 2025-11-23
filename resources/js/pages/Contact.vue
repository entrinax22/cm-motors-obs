<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Mail, MapPin, Phone } from 'lucide-vue-next';
import { ref } from 'vue';

// Form state
const form = ref({
    name: '',
    email: '',
    message: '',
});

const successMessage = ref('');
const errorMessage = ref('');
const loading = ref(false);

// Submit form
const sendMessage = async (e: Event) => {
    e.preventDefault();
    loading.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        const response = await axios.post('/contact-us/send', form.value);

        if (response.data.result) {
            successMessage.value = response.data.message;
            form.value = { name: '', email: '', message: '' }; // reset form
        } else {
            errorMessage.value = response.data.message || 'Failed to send message.';
        }
    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || 'Server error.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Contact Us" />
    <MainLayout>
        <!-- Hero -->
        <section class="relative bg-gradient-to-r from-blue-800 to-cyan-900 px-6 py-20 text-center text-white">
            <h1 class="mb-4 text-4xl font-extrabold text-cyan-200 md:text-5xl">Contact Us</h1>
            <p class="mx-auto max-w-2xl text-cyan-100">We’d love to hear from you! Reach out for bookings, inquiries, or support.</p>
        </section>

        <!-- Contact Info -->
        <section class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-3">
            <div class="rounded-xl bg-blue-900 p-8 text-center shadow-lg">
                <Phone class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-lg font-bold text-cyan-400">Phone</h2>
                <p class="text-cyan-200">+63 912 345 6789</p>
            </div>
            <div class="rounded-xl bg-blue-900 p-8 text-center shadow-lg">
                <Mail class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-lg font-bold text-cyan-400">Email</h2>
                <p class="text-cyan-200">info@cmmotorparts.com</p>
            </div>
            <div class="rounded-xl bg-blue-900 p-8 text-center shadow-lg">
                <MapPin class="mx-auto mb-4 h-10 w-10 text-cyan-400" />
                <h2 class="mb-2 text-lg font-bold text-cyan-400">Location</h2>
                <p class="text-cyan-200">Surigao City, Philippines</p>
            </div>
        </section>

        <!-- Contact Form -->
        <section class="bg-blue-800 px-6 py-16">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-6 text-2xl font-bold text-cyan-200">Send us a Message</h2>
                <form class="space-y-6" @submit="sendMessage">
                    <div>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Your Name"
                            class="w-full rounded-lg border border-cyan-600 bg-blue-900 px-4 py-3 text-white placeholder-cyan-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                        />
                    </div>
                    <div>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Your Email"
                            class="w-full rounded-lg border border-cyan-600 bg-blue-900 px-4 py-3 text-white placeholder-cyan-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                        />
                    </div>
                    <div>
                        <textarea
                            v-model="form.message"
                            rows="5"
                            placeholder="Your Message"
                            class="w-full rounded-lg border border-cyan-600 bg-blue-900 px-4 py-3 text-white placeholder-cyan-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                        ></textarea>
                    </div>

                    <!-- Success & Error messages -->
                    <p v-if="successMessage" class="text-green-400">{{ successMessage }}</p>
                    <p v-if="errorMessage" class="text-red-400">{{ errorMessage }}</p>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="rounded-lg bg-cyan-400 px-6 py-3 font-semibold text-blue-900 shadow-lg transition-transform hover:scale-105 hover:bg-cyan-300 disabled:opacity-50"
                    >
                        {{ loading ? 'Sending...' : 'Send Message' }}
                    </button>
                </form>
            </div>
        </section>
    </MainLayout>
</template>

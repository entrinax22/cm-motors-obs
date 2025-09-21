<template>
    <div class="flex min-h-screen flex-col bg-gray-50">
        <!-- Navigation -->
        <nav
            class="animate-fadeDown sticky top-0 z-50 border-b border-gray-200 bg-cover bg-center shadow-md"
            style="background-image: url('/images/tire_repair.jpg')"
        >
            <div class="bg-black/50">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Logo -->
                        <div class="animate-zoomIn flex items-center">
                            <Link href="/" class="flex items-center space-x-3">
                                <img src="/images/logo_cm_motors.png" alt="Logo" class="h-8 w-auto" />
                                <span
                                    class="bg-gradient-to-r from-white via-gray-200 to-gray-300 bg-clip-text text-xl font-bold text-transparent drop-shadow"
                                >
                                    CM Motorparts
                                </span>
                            </Link>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden items-center space-x-1 md:flex">
                            <Link href="/" :class="linkClass('/')">Home</Link>
                            <Link href="/services" :class="linkClass('/services')">Services</Link>
                            <Link href="/contact-us" :class="linkClass('/contact-us')">Contact</Link>

                            <!-- Account Dropdown -->
                            <div class="relative" ref="dropdownRef">
                                <button
                                    @click="toggleDropdown"
                                    class="flex items-center rounded-lg border border-yellow-600/40 bg-gray-800/50 px-4 py-2 text-sm font-medium text-white transition-all hover:scale-105 hover:border-yellow-500 hover:bg-yellow-600/20 hover:text-yellow-400"
                                >
                                    <img
                                        v-if="user.profile_photo_url"
                                        :src="user.profile_photo_url"
                                        alt="Profile"
                                        class="mr-2 h-6 w-6 rounded-full border border-yellow-500/40"
                                    />
                                    {{ user.name || 'Account' }}
                                    <svg
                                        class="ml-2 h-4 w-4 transition-transform"
                                        :class="{ 'rotate-180': accountOpen }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <transition name="fade">
                                    <div
                                        v-show="accountOpen"
                                        class="animate-fadeUp absolute right-0 z-50 mt-2 w-48 rounded-md border border-yellow-600/30 bg-gray-900/95 shadow-lg backdrop-blur-md"
                                    >   
                                        <Link
                                            v-if="$page.props.auth.user?.is_admin === 1"
                                            href="/admin/dashboard"
                                            class="block px-4 py-2 text-sm text-gray-200 transition-colors hover:bg-yellow-600/20 hover:text-yellow-400"
                                        >
                                            Dashboard
                                        </Link>
                                        <Link
                                            href="/my-bookings"
                                            class="block px-4 py-2 text-sm text-gray-200 transition-colors hover:bg-yellow-600/20 hover:text-yellow-400"
                                        >
                                            My Bookings
                                        </Link>
                                        <button
                                            @click="logout"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-200 transition-colors hover:bg-yellow-600/20 hover:text-yellow-400"
                                        >
                                            Logout
                                        </button>
                                    </div>
                                </transition>
                            </div>

                            <!-- Book Now Button -->
                            <button @click="showBookingModal = true" class="rounded-lg border border-yellow-600 bg-yellow-500 px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm hover:bg-yellow-400 hover:scale-105 transition-all duration-200">
                                Book Now
                            </button>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                class="transform rounded-lg p-2 text-white transition hover:scale-110 hover:bg-black/30"
                            >
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        v-if="!mobileMenuOpen"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile menu -->
                    <transition name="slide-fade">
                        <div v-show="mobileMenuOpen" class="animate-fadeDown border-t border-gray-200 bg-white/90 py-4 backdrop-blur-md md:hidden">
                            <div class="flex flex-col space-y-2">
                                <Link href="/" :class="mobileLinkClass('/')">Home</Link>
                                <Link href="/services" :class="mobileLinkClass('/services')">Services</Link>
                                <Link href="/contact-us" :class="mobileLinkClass('/contact-us')">Contact</Link>
                                <button @click="showBookingModal = true" class="py-3 pr-3 pl-4 text-sm font-semibold text-gray-800 bg-yellow-500 hover:bg-yellow-400 rounded">Book Now</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="w-full flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-16 bg-gray-800 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div class="col-span-2">
                        <div class="mb-4 flex items-center space-x-2">
                            <img src="/images/logo_cm_motors.png" alt="Logo" class="h-8 w-auto" />
                            <span class="text-xl font-bold">CM Motorparts</span>
                        </div>
                        <p class="mb-4 text-gray-300">
                            Your trusted partner for all automotive needs. Quality service, expert technicians, and customer satisfaction guaranteed.
                        </p>
                        <div class="flex space-x-4">
                            <!-- Social Icons -->
                            <a href="#" class="text-gray-300 hover:text-white">Twitter Icon</a>
                            <a href="#" class="text-gray-300 hover:text-white">Facebook Icon</a>
                            <a href="#" class="text-gray-300 hover:text-white">GitHub Icon</a>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><Link href="/about" class="text-gray-300 hover:text-white">About Us</Link></li>
                            <li><Link href="/services" class="text-gray-300 hover:text-white">Services</Link></li>
                            <li><Link href="/booking" class="text-gray-300 hover:text-white">Book Appointment</Link></li>
                            <li><Link href="/contact-us" class="text-gray-300 hover:text-white">Contact Us</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Contact Info</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-center space-x-2">+63 123 456 7890</li>
                            <li class="flex items-center space-x-2">info@cmmotorparts.com</li>
                            <li class="flex items-start space-x-2">123 Main Street, Butuan City, Caraga, Philippines</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-300">
                    <p>&copy; 2025 CM Motorparts. All rights reserved.</p>
                </div>
            </div>
        </footer>
        
        <!-- Booking Modal -->
        <div v-if="showBookingModal" class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
            <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-lg p-6 text-gray-100 border border-yellow-600/30">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 border-b border-yellow-600/40 pb-2">
                    <h2 class="text-xl font-bold text-yellow-400">Book a Service</h2>
                    <button @click="showBookingModal = false" class="text-gray-400 hover:text-yellow-400 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Form -->
                <form class="grid grid-cols-1 gap-4" @submit.prevent="submitBooking">
                    <!-- Service Select -->
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Service</label>
                        <multiselect
                            v-model="selectedService"
                            :options="services"
                            label="service_name"
                            track-by="service_id"
                            placeholder="Select a service"
                            :searchable="true"
                            :close-on-select="true"
                            :allow-empty="true"
                            class="rounded-md border border-yellow-500/50 bg-gray-800 text-gray-100 shadow-sm w-full"
                            :custom-label="serviceLabel"
                        />
                    </div>

                    <!-- Schedule -->
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Schedule</label>
                        <input type="datetime-local" v-model="bookingForm.scheduled_datetime"
                            class="w-full border border-yellow-500/50 rounded-md px-3 py-2 bg-gray-800 text-gray-100 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Notes</label>
                        <textarea v-model="bookingForm.notes"
                            class="w-full border border-yellow-500/50 rounded-md px-3 py-2 bg-gray-800 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                            placeholder="Any instructions (optional)"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showBookingModal = false"
                            class="px-4 py-2 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-500 text-gray-900 font-semibold rounded hover:bg-yellow-400 transition">
                            Book Now
                        </button>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mt-2 text-red-500 text-sm">{{ errorMessage }}</div>
                </form>
            </div>
        </div>

    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import Multiselect from "vue-multiselect";

const page = usePage();
const user = page.props.auth.user;

const mobileMenuOpen = ref(false);
const accountOpen = ref(false);
const dropdownRef = ref(null);
const showBookingModal = ref(false);

const toggleDropdown = () => (accountOpen.value = !accountOpen.value);

const form = useForm({});
const logout = () => form.post('/logout');

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        accountOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));

const isActive = (path) => path === '/' ? page.url === '/' : page.url.startsWith(path);
const linkClass = (path) => isActive(path)
    ? 'nav-link relative text-yellow-600 font-semibold after:content-[""] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-gradient-to-r after:from-blue-400 after:to-blue-600'
    : 'nav-link text-white hover:text-yellow-600';
const mobileLinkClass = (path) => isActive(path)
    ? 'relative text-yellow-600 font-semibold pl-4 py-3 border-l-4 border-blue-500 bg-blue-50'
    : 'text-gray-700 hover:text-yellow-600 hover:bg-gray-50 py-3 pl-4';

const services = ref([]);
const selectedService = ref(null);
const bookingForm = reactive({ scheduled_datetime: '', notes: '' });
const errorMessage = ref('');

const fetchServices = async () => {
    try {
        const response = await axios.get('/user/services/selectList');
        if (response.data.result) {
            services.value = response.data.data.map((s) => ({
                service_id: s.service_id,
                service_name: s.service_name,
                price: s.price,
            }));
        }
    } catch (err) { console.error(err); }
};

onMounted(() => fetchServices());

const serviceLabel = (service) => {
    if (!service) return '';
    return `${service.service_name} - ₱${service.price.toLocaleString()}`;
};

const submitBooking = async () => {
    if (!selectedService.value || !bookingForm.scheduled_datetime) {
        errorMessage.value = 'Please fill in all required fields';
        return;
    }

    try {
        const payload = {
            service_id: selectedService.value.service_id,
            scheduled_datetime: bookingForm.scheduled_datetime,
            notes: bookingForm.notes,
        };

        const response = await axios.post('/user/bookings/bookNow', payload);

        if (response.data.result === true) {
            // Reset form
            selectedService.value = null;
            bookingForm.scheduled_datetime = '';
            bookingForm.notes = '';
            errorMessage.value = '';

            showBookingModal.value = false;

            window.alert('Success');
        } else {
            errorMessage.value = response.data.message || 'Booking failed. Please try again.';
        }
    } catch (error) {
        console.error(error);
        errorMessage.value = 'An error occurred while submitting your booking.';
    }
};
</script>

<style>
.nav-link { position: relative; display: inline-block; padding: 0.5rem 1rem; transition: color 0.3s ease, transform 0.2s ease; }
.nav-link::after { content: ''; position: absolute; left: 0; bottom: 0; width: 0%; height: 2px; background: #9f6800; transition: width 0.3s ease; }
.nav-link:hover::after { width: 100%; }
.nav-link:hover { transform: scale(1.05); }

@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes zoomIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }

.animate-fadeUp { animation: fadeUp 0.6s ease forwards; }
.animate-fadeDown { animation: fadeDown 0.6s ease forwards; }
.animate-zoomIn { animation: zoomIn 0.8s ease forwards; }
</style>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
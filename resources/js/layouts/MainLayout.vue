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
                                            href="/user/myprofile"
                                            class="block px-4 py-2 text-sm text-gray-200 transition-colors hover:bg-yellow-600/20 hover:text-yellow-400"
                                        >
                                            My Profile
                                        </Link>
                                        <Link
                                            href="/mybookings"
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
                            <button
                                @click="showBookingModal = true"
                                class="rounded-lg border border-yellow-600 bg-yellow-500 px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm transition-all duration-200 hover:scale-105 hover:bg-yellow-400"
                            >
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
                                <button
                                    @click="showBookingModal = true"
                                    class="rounded bg-yellow-500 py-3 pr-3 pl-4 text-sm font-semibold text-gray-800 hover:bg-yellow-400"
                                >
                                    Book Now
                                </button>
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
                            Your trusted partner for all motorcycle care needs. Expert mechanics, quality service, and guaranteed customer
                            satisfaction.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><Link href="/services" class="text-gray-300 hover:text-white">Services</Link></li>
                            <li><Link href="/contact-us" class="text-gray-300 hover:text-white">Contact Us</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Contact Info</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-center space-x-2">+63 930 997 1898</li>
                            <li class="flex items-center space-x-2">info@cmmotorparts.com</li>
                            <li class="flex items-start space-x-2">San Benito, Surigao del Norte, Philippines</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-300">
                    <p>&copy; 2025 CM Motorparts. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Booking Modal Component -->
        <BookingModal v-model="showBookingModal" />
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import BookingModal from '../components/BookingModal.vue';

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

const isActive = (path) => (path === '/' ? page.url === '/' : page.url.startsWith(path));
const linkClass = (path) =>
    isActive(path)
        ? 'nav-link relative text-yellow-600 font-semibold after:content-[""] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-gradient-to-r after:from-blue-400 after:to-blue-600'
        : 'nav-link text-white hover:text-yellow-600';
const mobileLinkClass = (path) =>
    isActive(path)
        ? 'relative text-yellow-600 font-semibold pl-4 py-3 border-l-4 border-blue-500 bg-blue-50'
        : 'text-gray-700 hover:text-yellow-600 hover:bg-gray-50 py-3 pl-4';

// booking modal behavior moved into BookingModal.vue

// booking modal behavior moved into BookingModal.vue
</script>

<style>
.nav-link {
    position: relative;
    display: inline-block;
    padding: 0.5rem 1rem;
    transition:
        color 0.3s ease,
        transform 0.2s ease;
}
.nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0%;
    height: 2px;
    background: #9f6800;
    transition: width 0.3s ease;
}
.nav-link:hover::after {
    width: 100%;
}
.nav-link:hover {
    transform: scale(1.05);
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeUp {
    animation: fadeUp 0.6s ease forwards;
}
.animate-fadeDown {
    animation: fadeDown 0.6s ease forwards;
}
.animate-zoomIn {
    animation: zoomIn 0.8s ease forwards;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

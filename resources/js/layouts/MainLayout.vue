<template>
    <div class="flex min-h-screen flex-col bg-slate-50">
        <!-- Navigation -->
        <nav
            class="animate-fadeDown sticky top-0 z-50 border-b border-blue-700 bg-cover bg-center shadow-md"
            style="background-image: url('/images/tire_repair.jpg')"
        >
            <div class="bg-gradient-to-r from-blue-900/80 via-blue-800/80 to-cyan-900/80 backdrop-blur-sm">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Logo -->
                        <div class="animate-zoomIn flex items-center">
                            <Link href="/" class="flex items-center space-x-3">
                                <img src="/images/logo_cm_motors.png" alt="Logo" class="h-8 w-auto" />
                                <span
                                    class="bg-gradient-to-r from-white via-blue-200 to-cyan-300 bg-clip-text text-xl font-bold text-transparent drop-shadow"
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
                                    class="flex items-center rounded-lg border border-blue-500/40 bg-blue-900/40 px-4 py-2 text-sm font-medium text-white transition-all hover:scale-105 hover:border-cyan-400 hover:bg-blue-600/30 hover:text-cyan-300"
                                >
                                    <img
                                        v-if="user.profile_photo_url"
                                        :src="user.profile_photo_url"
                                        alt="Profile"
                                        class="mr-2 h-6 w-6 rounded-full border border-cyan-400/40"
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
                                        class="animate-fadeUp absolute right-0 z-50 mt-2 w-48 rounded-md border border-blue-500/40 bg-blue-950/95 shadow-lg backdrop-blur-md"
                                    >
                                        <Link
                                            v-if="$page.props.auth.user?.is_admin === 1"
                                            href="/admin/dashboard"
                                            class="block px-4 py-2 text-sm text-slate-200 transition-colors hover:bg-blue-600/20 hover:text-cyan-300"
                                        >
                                            Dashboard
                                        </Link>
                                        <Link
                                            href="/user/myprofile"
                                            class="block px-4 py-2 text-sm text-slate-200 transition-colors hover:bg-blue-600/20 hover:text-cyan-300"
                                        >
                                            My Profile
                                        </Link>
                                        <Link
                                            href="/mybookings"
                                            class="block px-4 py-2 text-sm text-slate-200 transition-colors hover:bg-blue-600/20 hover:text-cyan-300"
                                        >
                                            My Bookings
                                        </Link>
                                        <button
                                            @click="logout"
                                            class="w-full px-4 py-2 text-left text-sm text-slate-200 transition-colors hover:bg-blue-600/20 hover:text-cyan-300"
                                        >
                                            Logout
                                        </button>
                                    </div>
                                </transition>
                            </div>

                            <!-- Book Now Button -->
                            <button
                                @click="showBookingModal = true"
                                class="rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition-all hover:scale-105 hover:from-blue-500 hover:to-cyan-400"
                            >
                                Book Now
                            </button>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                class="transform rounded-lg p-2 text-white transition hover:scale-110 hover:bg-blue-800/50"
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
                        <div v-show="mobileMenuOpen" class="animate-fadeDown border-t border-blue-200 bg-white/90 py-4 backdrop-blur-md md:hidden">
                            <div class="flex flex-col space-y-2">
                                <Link href="/" :class="mobileLinkClass('/')">Home</Link>
                                <Link href="/services" :class="mobileLinkClass('/services')">Services</Link>
                                <Link href="/contact-us" :class="mobileLinkClass('/contact-us')">Contact</Link>

                                <button
                                    @click="showBookingModal = true"
                                    class="rounded bg-gradient-to-r from-blue-600 to-cyan-500 py-3 pr-3 pl-4 text-sm font-semibold text-white hover:from-blue-500 hover:to-cyan-400"
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
        <footer class="mt-16 bg-gradient-to-r from-blue-950 to-blue-900 text-slate-300">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div class="col-span-2">
                        <div class="mb-4 flex items-center space-x-2">
                            <img src="/images/logo_cm_motors.png" alt="Logo" class="h-8 w-auto" />
                            <span class="text-xl font-bold text-white">CM Motorparts</span>
                        </div>
                        <p class="mb-4 text-slate-300">
                            Your trusted partner for all motorcycle care needs. Expert mechanics, quality service, and guaranteed customer
                            satisfaction.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold text-white">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><Link href="/services" class="text-slate-300 hover:text-cyan-300">Services</Link></li>
                            <li><Link href="/contact-us" class="text-slate-300 hover:text-cyan-300">Contact Us</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold text-white">Contact Info</h4>
                        <ul class="space-y-2 text-slate-300">
                            <li class="flex items-center space-x-2">+63 930 997 1898</li>
                            <li class="flex items-center space-x-2">info@cmmotorparts.com</li>
                            <li class="flex items-start space-x-2">San Benito, Surigao del Norte, Philippines</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-blue-700 pt-8 text-center text-slate-400">
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
        ? 'nav-link relative text-cyan-300 font-semibold after:content-[""] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-gradient-to-r after:from-blue-400 after:to-cyan-400'
        : 'nav-link text-white hover:text-cyan-300';

const mobileLinkClass = (path) =>
    isActive(path)
        ? 'relative text-blue-600 font-semibold pl-4 py-3 border-l-4 border-blue-500 bg-blue-50'
        : 'text-slate-700 hover:text-cyan-500 hover:bg-slate-100 py-3 pl-4';
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
    background: #2563eb; /* blue-600 */
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

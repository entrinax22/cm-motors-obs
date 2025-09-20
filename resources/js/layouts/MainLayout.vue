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
                            <Link href="/" :class="linkClass('/')" class="nav-link"> Home </Link>
                            <Link href="/services" :class="linkClass('/services')" class="nav-link"> Services </Link>
                            <Link href="/contact-us" :class="linkClass('/contact-us')" class="nav-link"> Contact </Link>

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
                            <Link
                                href="/booking"
                                :class="
                                    isActive('/booking')
                                        ? 'border-yellow-600 bg-yellow-600 text-white'
                                        : 'border-yellow-600 bg-yellow-500 text-gray-800 hover:bg-yellow-400'
                                "
                                class="rounded-lg border px-4 py-2 text-sm font-semibold shadow-sm transition-all duration-200 hover:scale-105"
                            >
                                Book Now
                            </Link>
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
                                <Link href="/contact" :class="mobileLinkClass('/contact')">Contact</Link>
                                <Link
                                    href="/booking"
                                    :class="
                                        isActive('/booking')
                                            ? 'border-l-4 border-yellow-800 bg-blue-600 text-white'
                                            : 'border-l-4 border-yellow-800 bg-blue-600 text-white hover:bg-blue-700'
                                    "
                                    class="py-3 pr-3 pl-4 text-sm font-semibold transition-all duration-200 hover:scale-105"
                                >
                                    Book Now
                                </Link>
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
                            <a href="#" class="text-gray-300 hover:text-white">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                                    />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"
                                    />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.257.298.293.559.217.867-.053.174-.402 1.619-.402 1.619-.143.581-.598.704-1.378.434-1.918-.893-3.115-3.699-3.115-5.951C1.4 8.58 4.573 5.5 8.5 5.5c6.315 0 11.231 4.5 11.231 10.5 0 6.268-3.947 11.314-9.435 11.314-1.844 0-3.581-.96-4.175-2.105l-1.131 4.315c-.408 1.566-1.513 3.53-2.253 4.73C8.576 23.743 10.26 24 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><Link href="/about" class="text-gray-300 hover:text-white">About Us</Link></li>
                            <li><Link href="/services" class="text-gray-300 hover:text-white">Services</Link></li>
                            <li><Link href="/booking" class="text-gray-300 hover:text-white">Book Appointment</Link></li>
                            <li><Link href="/contact" class="text-gray-300 hover:text-white">Contact Us</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Contact Info</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-center space-x-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    ></path>
                                </svg>
                                <span>+63 123 456 7890</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <span>info@cmmotorparts.com</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <svg class="mt-0.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    ></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>123 Main Street, Butuan City, Caraga, Philippines</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-300">
                    <p>&copy; 2025 CM Motorparts. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const page = usePage();
const mobileMenuOpen = ref(false);
const accountOpen = ref(false);
const dropdownRef = ref(null);

const user = page.props.auth.user;
const form = useForm({});

const logout = () => {
    form.post('/logout');
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        accountOpen.value = false;
    }
};

const toggleDropdown = () => {
    accountOpen.value = !accountOpen.value;
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const isActive = (path) => {
    if (path === '/') {
        return page.url === '/';
    }
    return page.url.startsWith(path);
};

const linkClass = (path) =>
    isActive(path)
        ? 'nav-link relative text-yellow-600 font-semibold after:content-[""] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-gradient-to-r after:from-blue-400 after:to-blue-600'
        : 'nav-link text-white hover:text-yellow-600';

const mobileLinkClass = (path) =>
    isActive(path)
        ? 'relative text-yellow-600 font-semibold pl-4 py-3 border-l-4 border-blue-500 bg-blue-50'
        : 'text-gray-700 hover:text-yellow-600 hover:bg-gray-50 py-3 pl-4';
</script>

<style>
/* Hover underline animation */
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
    background: #9f6800; /* Tailwind blue-500 */
    transition: width 0.3s ease;
}
.nav-link:hover::after {
    width: 100%;
}
.nav-link:hover {
    transform: scale(1.05);
}

/* Animations */
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

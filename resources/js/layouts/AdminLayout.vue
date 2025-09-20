<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 transform bg-gray-900 transition-transform duration-500 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <!-- Logo -->
            <div class="animate-fadeInDown flex h-16 items-center justify-center border-b border-gray-700 bg-gray-800">
                <Link href="/admin" class="flex items-center space-x-2">
                    <img src="/images/logo_cm_motors.png" alt="Logo" class="h-8 w-auto transition-transform duration-300 hover:scale-110" />
                    <span class="text-xl font-bold text-white">CM Admin</span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="mt-5 h-full space-y-2 overflow-y-auto px-2 pb-4">
                <transition-group
                    enter-active-class="transition ease-out duration-500"
                    enter-from-class="opacity-0 -translate-x-3"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition ease-in duration-300"
                    leave-from-class="opacity-100 translate-x-0"
                    leave-to-class="opacity-0 -translate-x-3"
                >
                    <div v-for="group in navItems" :key="group.group">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ group.group }}</h3>
                        <div v-for="item in group.items" :key="item.href">
                            <Link
                                :href="item.href"
                                :class="isActive(item.href) ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200"
                            >
                                <component :is="item.icon" class="h-5 w-5 transition-transform group-hover:scale-110" />
                                <span>{{ item.label }}</span>
                            </Link>
                        </div>
                    </div>
                </transition-group>
            </nav>

            <!-- User Profile Section -->
            <div class="animate-fadeInUp absolute inset-x-0 bottom-0 border-t border-gray-700 bg-gray-800 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 ring-2 ring-gray-700 transition-transform duration-300 hover:scale-110"
                        >
                            <span class="text-sm font-semibold text-white">
                                {{ $page.props.auth.user?.name?.charAt(0) || 'A' }}
                            </span>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">
                            {{ $page.props.auth.user?.name || 'Admin' }}
                        </p>
                        <p class="truncate text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
                <div class="mt-3">
                    <Link
                        href="/logout"
                        method="post"
                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors duration-300 hover:bg-gray-700 hover:text-white"
                    >
                        <LogOut class="h-5 w-5 transition-transform group-hover:rotate-90" />
                        <span>Logout</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main content wrapper -->
        <div class="lg:pl-64">
            <!-- Top navigation -->
            <div class="animate-fadeInDown sticky top-0 z-40 border-b border-gray-200 bg-white shadow-sm">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition-transform duration-300 hover:bg-gray-100 hover:text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none lg:hidden"
                            :class="sidebarOpen ? 'rotate-90' : ''"
                        >
                            <LayoutDashboard class="h-5 w-5" />
                        </button>

                        <!-- Page title -->
                        <div class="ml-3 lg:ml-0">
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ pageTitle }}
                            </h1>
                            <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500">
                                <span>Admin</span>
                                <span>{{ pageTitle }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <button
                            type="button"
                            class="relative rounded-lg bg-white p-2 text-gray-500 transition-transform duration-300 hover:scale-110 hover:bg-gray-50 hover:text-gray-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                        >
                            <Bell class="h-5 w-5" />
                            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500">
                                <span class="text-xs font-medium text-white">3</span>
                            </span>
                        </button>

                        <!-- View site link -->
                        <Link
                            href="/"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50 hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                        >
                            View Site
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="min-h-screen bg-gray-50">
                <div class="animate-fadeIn h-full px-4 py-6 sm:px-6">
                    <slot />
                </div>
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden">
                <div @click="sidebarOpen = false" class="bg-opacity-50 fixed inset-0 bg-gray-900 transition-opacity"></div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart2, Bell, CalendarCheck, LayoutDashboard, LogOut, Settings, Users, Wrench } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard',
    },
});

const page = usePage();
const sidebarOpen = ref(false);

const navItems = [
    {
        group: 'Tables',
        items: [
            { href: '/admin/bookings', label: 'Bookings', icon: CalendarCheck },
            { href: '/admin/users', label: 'Users', icon: Users },
            { href: '/admin/services', label: 'Services', icon: Wrench },
        ],
    },
    {
        group: 'Other',
        items: [
            { href: '/admin/dashboard', label: 'Dashboard', icon: LayoutDashboard },
            { href: '/admin/reports', label: 'Reports', icon: BarChart2 },
            { href: '/admin/settings', label: 'Settings', icon: Settings },
        ],
    },
];

const isActive = (path) => page.url.startsWith(path);
</script>

<style>
/* Simple fade animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fadeIn {
    animation: fadeIn 0.5s ease-out;
}
.animate-fadeInDown {
    animation: fadeInDown 0.5s ease-out;
}
.animate-fadeInUp {
    animation: fadeInUp 0.5s ease-out;
}
</style>

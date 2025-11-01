<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import {
    AlertCircle,
    Calendar,
    CheckCircle,
    DollarSign,
    Hash,
    ShieldCheck,
    X, // close icon
    XCircle,
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

type BookingStatus = 'pending' | 'confirmed' | 'completed' | 'cancelled';

interface Booking {
    id: number;
    booking_number: string;
    service_name: string;
    scheduled_datetime: string;
    status: BookingStatus;
    total_amount: number;
}

const bookings = ref<Booking[]>([]);
const selectedBooking = ref<Booking | null>(null);
const showModal = ref(false);

const getStatusClasses = (status: BookingStatus): string => {
    switch (status) {
        case 'completed':
            return 'text-green-400 bg-green-900/50';
        case 'confirmed':
            return 'text-blue-400 bg-blue-900/50';
        case 'pending':
            return 'text-yellow-400 bg-yellow-900/50';
        case 'cancelled':
            return 'text-red-400 bg-red-900/50';
        default:
            return 'text-gray-400 bg-gray-700';
    }
};

const getStatusIcon = (status: BookingStatus) => {
    switch (status) {
        case 'completed':
            return CheckCircle;
        case 'confirmed':
            return ShieldCheck;
        case 'pending':
            return AlertCircle;
        case 'cancelled':
            return XCircle;
    }
};

const formatDateTime = (isoString: string) => {
    const date = new Date(isoString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const formatCurrency = (amount: number) => {
    return amount.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
    });
};

const fetchUserBookings = async () => {
    try {
        const response = await axios.get('/user/mybookings/history');
        if (response.data.result === true) {
            bookings.value = response.data.data;
        }
    } catch (error) {
        console.log(error);
    }
};

const openModal = (booking: Booking) => {
    selectedBooking.value = booking;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedBooking.value = null;
};

onMounted(() => {
    fetchUserBookings();
});
const showSuccessModal = ref(false);
const successMessage = ref('');

const cancelBooking = async (booking_id: number) => {
    try {
        const response = await axios.post('/user/mybookings/cancel_booking', { booking_id });

        if (response.data.result === true) {
            const cancelledBooking = response.data.data;

            const index = bookings.value.findIndex((b) => b.id === cancelledBooking.id);
            if (index !== -1) {
                bookings.value[index].status = 'cancelled';
            }

            successMessage.value = 'Your booking has been successfully cancelled.';
            showSuccessModal.value = true;
            fetchUserBookings();
            closeModal();
        } else {
            console.warn(response.data.message);
        }
    } catch (error) {
        console.error('Error cancelling booking:', error);
    }
};
</script>

<template>
    <Head title="My Bookings" />
    <MainLayout>
        <!-- Header -->
        <section class="bg-gray-900 px-6 py-12 shadow-md">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-3xl font-bold tracking-tight text-white md:text-4xl">My Booking History</h1>
                <p class="mt-2 text-lg text-gray-300">View, track, and manage all your past and upcoming appointments.</p>
            </div>
        </section>

        <!-- Booking List -->
        <section class="mx-auto max-w-5xl px-6 py-12">
            <!-- Empty State -->
            <div v-if="bookings.length === 0" class="rounded-xl border border-dashed border-gray-700 bg-gray-800 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-white">No Bookings Found</h2>
                <p class="mt-2 text-gray-400">You haven't made any appointments yet.</p>
                <Link
                    href="/booking"
                    class="mt-6 inline-block rounded-lg bg-yellow-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-yellow-400"
                >
                    Book a Service Now
                </Link>
            </div>

            <!-- Booking Cards -->
            <div v-else class="space-y-6">
                <div
                    v-for="booking in bookings"
                    :key="booking.id"
                    class="rounded-xl border border-gray-700 bg-gray-800 shadow-lg transition-all hover:border-yellow-500/50 hover:shadow-yellow-500/10"
                >
                    <div class="flex flex-col items-start justify-between gap-4 border-b border-gray-700 p-4 sm:flex-row sm:items-center">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-yellow-400">
                            <Hash class="h-5 w-5" />
                            Booking #{{ booking.booking_number }}
                        </h2>
                        <div :class="getStatusClasses(booking.status)" class="flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium">
                            <component :is="getStatusIcon(booking.status)" class="h-4 w-4" />
                            <span class="capitalize">{{ booking.status }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="mb-4 text-xl font-semibold text-white">{{ booking.service_name }}</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="flex items-center gap-3">
                                <Calendar class="h-5 w-5 flex-shrink-0 text-gray-500" />
                                <div>
                                    <span class="block text-sm text-gray-400">Scheduled On</span>
                                    <span class="block font-medium text-gray-200">{{ formatDateTime(booking.scheduled_datetime) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <DollarSign class="h-5 w-5 flex-shrink-0 text-gray-500" />
                                <div>
                                    <span class="block text-sm text-gray-400">Total Amount</span>
                                    <span class="block font-medium text-gray-200">{{ formatCurrency(booking.total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-b-xl border-t border-gray-700 bg-gray-800/50 p-4 text-right">
                        <button
                            @click="openModal(booking)"
                            class="rounded-lg border border-yellow-400 px-5 py-2 text-sm font-semibold text-yellow-400 transition hover:bg-yellow-400 hover:text-gray-900"
                        >
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal -->
        <div v-if="showModal && selectedBooking" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-gray-900 p-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                    <h2 class="text-xl font-bold text-yellow-400">Booking #{{ selectedBooking.booking_number }}</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-white">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="mt-4 space-y-4 text-gray-300">
                    <p><span class="font-semibold text-white">Service:</span> {{ selectedBooking.service.service_name }}</p>
                    <p><span class="font-semibold text-white">Status:</span> {{ selectedBooking.status }}</p>
                    <p><span class="font-semibold text-white">Scheduled at:</span> {{ formatDateTime(selectedBooking.scheduled_datetime) }}</p>
                    <p><span class="font-semibold text-white">Total Amount:</span> ₱{{ formatCurrency(selectedBooking.total_amount) }}</p>
                </div>

                <div class="mt-6 text-right">
                    <button
                        v-if="selectedBooking.status === 'pending'"
                        @click="cancelBooking(selectedBooking.booking_id)"
                        class="mx-1 rounded-lg bg-red-500 px-6 py-2 font-semibold text-white hover:bg-red-600"
                    >
                        Cancel Booking
                    </button>

                    <button @click="closeModal" class="rounded-lg bg-yellow-500 px-6 py-2 font-semibold text-gray-900 hover:bg-yellow-400">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-gray-900 p-6 text-center shadow-xl">
                <CheckCircle class="mx-auto h-12 w-12 text-green-400" />
                <h2 class="mt-4 text-xl font-bold text-green-400">Booking Cancelled</h2>
                <p class="mt-2 text-gray-300">{{ successMessage }}</p>
                <div class="mt-6">
                    <button
                        @click="showSuccessModal = false"
                        class="rounded-lg bg-yellow-500 px-6 py-2 font-semibold text-gray-900 hover:bg-yellow-400"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

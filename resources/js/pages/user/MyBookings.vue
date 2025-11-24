<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { AlertCircle, Calendar, CheckCircle, DollarSign, Hash, ShieldCheck, X, XCircle } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

type BookingStatus = 'pending' | 'confirmed' | 'completed' | 'cancelled';

interface Booking {
    booking_id: number;
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
            return 'text-cyan-400 bg-cyan-900/50';
        case 'confirmed':
            return 'text-blue-400 bg-blue-900/50';
        case 'pending':
            return 'text-sky-400 bg-sky-900/50';
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
    return amount.toLocaleString('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const fetchUserBookings = async () => {
    try {
        const res = await axios.get('/user/mybookings/history');
        if (res.data.result) bookings.value = res.data.data;
    } catch (error) {
        console.log(error);
    }
};

onMounted(fetchUserBookings);

const openModal = (booking: Booking) => {
    selectedBooking.value = booking;
    showModal.value = true;
};
const closeModal = () => {
    showModal.value = false;
    selectedBooking.value = null;
};

const showSuccessModal = ref(false);
const successMessage = ref('');

const cancelBooking = async (booking_id: number) => {
    try {
        const res = await axios.post('/user/mybookings/cancel_booking', { booking_id });
        if (res.data.result) {
            const index = bookings.value.findIndex((b) => b.id === res.data.data.id);
            if (index !== -1) bookings.value[index].status = 'cancelled';
            successMessage.value = 'Your booking has been successfully cancelled.';
            showSuccessModal.value = true;
            fetchUserBookings();
            closeModal();
        }
    } catch (err) {
        console.error(err);
    }
};

// Payment modal
const showPaymentModal = ref(false);
const loadingPayment = ref(false);
const paymentSuccessMessage = ref('');
const paymentErrorMessage = ref('');
const preview = ref<string | null>(null);

const paymentForm = ref({
    booking_id: 0,
    amount: 0,
    reference_number: '',
    payment_proof: null as File | null,
});

const openPaymentModal = (booking: Booking) => {
    selectedBooking.value = booking;
    paymentForm.value.booking_id = booking.booking_id;
    paymentForm.value.amount = booking.total_amount;
    paymentForm.value.reference_number = '';
    paymentForm.value.payment_proof = null;
    preview.value = null;
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    preview.value = null;
};

const handleFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    paymentForm.value.payment_proof = file;
    const reader = new FileReader();
    reader.onload = (ev) => {
        preview.value = ev.target?.result as string;
    };
    reader.readAsDataURL(file);
};

const submitPayment = async () => {
    if (!paymentForm.value.reference_number || !paymentForm.value.payment_proof) {
        paymentErrorMessage.value = 'Please provide reference number and upload payment proof.';
        return;
    }

    loadingPayment.value = true;
    paymentErrorMessage.value = '';
    paymentSuccessMessage.value = '';

    try {
        const formData = new FormData();
        formData.append('booking_id', (paymentForm.value.booking_id ?? 0).toString());
        formData.append('amount', paymentForm.value.amount.toString());
        formData.append('reference_number', paymentForm.value.reference_number);
        formData.append('payment_proof', paymentForm.value.payment_proof);

        console.log('Submitting payment with data:', {
            booking_id: paymentForm.value.booking_id,
            amount: paymentForm.value.amount,
            reference_number: paymentForm.value.reference_number,
            payment_proof: paymentForm.value.payment_proof,
        });
        const res = await axios.post('/user/payments/store', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (res.data.result) {
            paymentSuccessMessage.value = 'Payment successfully added!';
            showPaymentModal.value = false;
            fetchUserBookings();
            alert('Payment successfully added!');
        } else {
            paymentErrorMessage.value = res.data.message || 'Failed to save payment.';
        }
    } catch (err) {
        console.error(err);
        paymentErrorMessage.value = 'An error occurred while submitting your payment.';
    } finally {
        loadingPayment.value = false;
    }
};

const viewQRCode = ref(false);
</script>

<template>
    <Head title="My Bookings" />
    <MainLayout>
        <!-- Header -->
        <section class="bg-blue-900 px-6 py-12 shadow-md">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-3xl font-bold tracking-tight text-white md:text-4xl">My Booking History</h1>
                <p class="mt-2 text-lg text-cyan-200">View, track, and manage all your past and upcoming appointments.</p>
            </div>
        </section>

        <!-- Booking List -->
        <section class="mx-auto max-w-5xl px-6 py-12">
            <!-- Empty State -->
            <div v-if="bookings.length === 0" class="rounded-xl border border-dashed border-cyan-400 bg-blue-800 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-white">No Bookings Found</h2>
                <p class="mt-2 text-cyan-200">You haven't made any appointments yet.</p>
                <Link
                    href="/booking"
                    class="mt-6 inline-block rounded-lg bg-cyan-500 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-transform hover:scale-105 hover:bg-blue-400"
                >
                    Book a Service Now
                </Link>
            </div>

            <!-- Booking Cards -->
            <div v-else class="space-y-6">
                <div
                    v-for="booking in bookings"
                    :key="booking.id"
                    class="rounded-xl border border-cyan-500 bg-blue-800 shadow-lg transition-all hover:border-cyan-400 hover:shadow-cyan-500/20"
                >
                    <div class="flex flex-col items-start justify-between gap-4 border-b border-cyan-500 p-4 sm:flex-row sm:items-center">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-cyan-400">
                            <Hash class="h-5 w-5" /> Booking #{{ booking.booking_number }}
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
                                <Calendar class="h-5 w-5 flex-shrink-0 text-cyan-200" />
                                <div>
                                    <span class="block text-sm text-cyan-200">Scheduled On</span>
                                    <span class="block font-medium text-white">{{ formatDateTime(booking.scheduled_datetime) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <DollarSign class="h-5 w-5 flex-shrink-0 text-cyan-200" />
                                <div>
                                    <span class="block text-sm text-cyan-200">Total Amount</span>
                                    <span class="block font-medium text-white">₱{{ formatCurrency(booking.total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-b-xl border-t border-cyan-500 bg-blue-900/50 p-4 text-right">
                        <button
                            v-if="booking.status === 'pending'"
                            @click="openPaymentModal(booking)"
                            class="mx-1 rounded-lg bg-green-500 px-6 py-2 font-semibold text-white hover:bg-green-600"
                        >
                            Add Payment
                        </button>

                        <button
                            @click="openModal(booking)"
                            class="rounded-lg border border-cyan-400 px-5 py-2 text-sm font-semibold text-cyan-400 transition hover:bg-cyan-400 hover:text-gray-900"
                        >
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal -->
        <div v-if="showModal && selectedBooking" class="fixed inset-0 z-50 flex items-center justify-center bg-blue-900/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-blue-800 p-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-cyan-500 pb-3">
                    <h2 class="text-xl font-bold text-cyan-400">Booking #{{ selectedBooking.booking_number }}</h2>
                    <button @click="closeModal" class="text-cyan-200 hover:text-white">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="mt-4 space-y-4 text-cyan-200">
                    <p><span class="font-semibold text-white">Service:</span> {{ selectedBooking.service_name }}</p>
                    <p><span class="font-semibold text-white">Status:</span> {{ selectedBooking.status }}</p>
                    <p><span class="font-semibold text-white">Scheduled at:</span> {{ formatDateTime(selectedBooking.scheduled_datetime) }}</p>
                    <p><span class="font-semibold text-white">Total Amount:</span> ₱{{ formatCurrency(selectedBooking.total_amount) }}</p>
                </div>

                <div class="mt-6 text-right">
                    <button
                        v-if="selectedBooking.status === 'pending'"
                        @click="cancelBooking(selectedBooking.id)"
                        class="mx-1 rounded-lg bg-red-500 px-6 py-2 font-semibold text-white hover:bg-red-600"
                    >
                        Cancel Booking
                    </button>
                    <button @click="closeModal" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-gray-900 hover:bg-blue-400">Close</button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-blue-900/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-blue-800 p-6 text-center shadow-xl">
                <CheckCircle class="mx-auto h-12 w-12 text-cyan-400" />
                <h2 class="mt-4 text-xl font-bold text-cyan-400">Booking Cancelled</h2>
                <p class="mt-2 text-cyan-200">{{ successMessage }}</p>
                <div class="mt-6">
                    <button @click="showSuccessModal = false" class="rounded-lg bg-cyan-500 px-6 py-2 font-semibold text-gray-900 hover:bg-blue-400">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Payments Modal -->
        <div v-if="showPaymentModal && selectedBooking" class="fixed inset-0 z-50 flex items-center justify-center bg-blue-900/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-blue-800 p-6 shadow-xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-cyan-500 pb-3">
                    <h2 class="text-xl font-bold text-cyan-400">Add Payment for Booking #{{ selectedBooking.booking_number }}</h2>
                    <button @click="closePaymentModal" class="text-cyan-200 hover:text-white">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!--GCash QR Code View-->
                <div class="mt-4 flex flex-col items-center">
                    <h3 class="mt-4 text-lg font-semibold text-white">GCash QR Code</h3>
                    <img
                        src="/qr_code/GCashQR.png"
                        alt="GCash QR Code"
                        class="mt-2 h-48 w-48 cursor-pointer rounded border object-cover transition hover:scale-105"
                        @click="viewQRCode = true"
                    />
                </div>

                <!-- Form -->
                <form @submit.prevent="submitPayment" class="mt-4 space-y-4 text-cyan-200">
                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium">Amount</label>
                        <input
                            v-model.number="paymentForm.amount"
                            type="number"
                            min="0"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-white focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                            readonly
                        />
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-medium">Reference Number</label>
                        <input
                            v-model="paymentForm.reference_number"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-white focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                            placeholder="Enter reference number"
                            required
                        />
                    </div>

                    <!-- Payment Proof -->
                    <div>
                        <label class="block text-sm font-medium">Payment Proof</label>
                        <input
                            @change="handleFileChange"
                            type="file"
                            accept="image/*"
                            class="w-full rounded-lg border px-3 py-2 text-white"
                            required
                        />
                        <img v-if="preview" :src="preview" class="mt-2 h-24 w-24 rounded border object-cover" />
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="closePaymentModal" class="rounded bg-gray-200 px-4 py-2 text-gray-900 hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loadingPayment" class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            {{ loadingPayment ? 'Saving...' : 'Save Payment' }}
                        </button>
                    </div>

                    <!-- Success / Error -->
                    <p v-if="paymentSuccessMessage" class="text-green-400">{{ paymentSuccessMessage }}</p>
                    <p v-if="paymentErrorMessage" class="text-red-400">{{ paymentErrorMessage }}</p>
                </form>
            </div>
        </div>

        <div v-if="viewQRCode" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="relative">
                <img src="/qr_code/GCashQR.png" class="h-[500px] w-[500px] rounded-lg border-4 border-cyan-400 object-cover shadow-2xl" />

                <button @click="viewQRCode = false" class="absolute -top-6 right-0 rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">
                    Close
                </button>
            </div>
        </div>
    </MainLayout>
</template>

<template>
    <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-xl border border-cyan-500/30 bg-blue-900 p-6 text-gray-100 shadow-2xl">
            <!-- Modal Header -->
            <div class="mb-4 flex items-center justify-between border-b border-cyan-500/40 pb-2">
                <h2 class="text-xl font-bold text-cyan-400">Book a Service</h2>
                <button @click="close" class="text-gray-400 transition hover:text-cyan-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form class="grid grid-cols-1 gap-4" @submit.prevent="submitBooking">
                <!-- Service Select -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-200">Service</label>
                    <multiselect
                        v-model="selectedService"
                        :options="services"
                        label="service_name"
                        track-by="service_id"
                        placeholder="Select a service"
                        :searchable="true"
                        :close-on-select="true"
                        :allow-empty="true"
                        class="w-full rounded-md border border-cyan-500/50 bg-blue-800 text-gray-100 shadow-sm"
                        :custom-label="serviceLabel"
                    />
                </div>

                <!-- Schedule -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-200">Schedule</label>
                    <input
                        type="datetime-local"
                        v-model="bookingForm.scheduled_datetime"
                        class="w-full rounded-md border border-cyan-500/50 bg-blue-800 px-3 py-2 text-gray-100 transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                    />
                </div>

                <!-- Notes -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-200">Notes</label>
                    <textarea
                        v-model="bookingForm.notes"
                        class="w-full rounded-md border border-cyan-500/50 bg-blue-800 px-3 py-2 text-gray-100 placeholder-gray-400 transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400"
                        placeholder="Any instructions (optional)"
                    ></textarea>
                </div>

                <!-- Buttons -->
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" @click="close" class="rounded bg-blue-700 px-4 py-2 text-gray-200 transition hover:bg-blue-600">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-2 font-semibold text-white transition hover:from-blue-500 hover:to-cyan-400"
                    >
                        Book Now
                    </button>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="mt-2 text-sm text-red-500">{{ errorMessage }}</div>
            </form>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';

const props = defineProps({ modelValue: { type: Boolean, default: false } });
const emit = defineEmits(['update:modelValue']);

const visible = ref(props.modelValue);
watch(
    () => props.modelValue,
    (v) => (visible.value = v),
);
watch(visible, (v) => emit('update:modelValue', v));

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
    } catch (err) {
        console.error(err);
    }
};

onMounted(() => fetchServices());

const serviceLabel = (service) => {
    if (!service) return '';
    return `${service.service_name} - ₱${Number(service.price).toLocaleString()}`;
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

            visible.value = false;

            window.alert('Booking Successful!');
        } else {
            errorMessage.value = response.data.message || 'Booking failed. Please try again.';
        }
    } catch (error) {
        console.error(error);
        errorMessage.value = 'An error occurred while submitting your booking.';
    }
};

const close = () => (visible.value = false);
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

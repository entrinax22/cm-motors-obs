<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import Multiselect from 'vue-multiselect';

const form = ref({
    booking_number: '',
    user_id: null as number | null,
    service_id: null as number | null,
    scheduled_datetime: '',
    status: 'pending',
    notes: '',
    total_amount: null as number | null,
});

// Options for selects
const users = ref<{ id: number; name: string }[]>([]);
const services = ref<{ service_id: number; service_name: string }[]>([]);

// Selected objects
const selectedUser = ref<{ id: number; name: string } | null>(null);
const selectedService = ref<{ service_id: number; service_name: string } | null>(null);

const fetchOptions = async () => {
    try {
        const [userRes, serviceRes] = await Promise.all([axios.get('/admin/users/selectList'), axios.get('/admin/services/selectList')]);

        if (userRes.data.result) {
            users.value = userRes.data.data;
        }
        if (serviceRes.data.result) {
            services.value = serviceRes.data.data;
        }
    } catch (error) {
        console.error('Error fetching select options:', error);
    }
};

const submitForm = async () => {
    try {
        // set IDs from selected objects
        form.value.user_id = selectedUser.value?.id || null;
        form.value.service_id = selectedService.value?.service_id || null;

        const response = await axios.post('/admin/bookings/store', form.value);

        if (response.data.result === true) {
            alert('✅ Booking created successfully!');

            // Reset form
            form.value = {
                booking_number: '',
                user_id: null,
                service_id: null,
                scheduled_datetime: '',
                status: 'pending',
                notes: '',
                total_amount: null,
            };
            selectedUser.value = null;
            selectedService.value = null;
        } else {
            alert('⚠️ ' + response.data.message);
        }
    } catch (error) {
        console.error('Error saving booking:', error);
        alert('❌ Failed to save booking.');
    }
};

const cancel = () => {
    window.history.back();
};

onMounted(fetchOptions);
</script>

<template>
    <Head title="Add Booking" />
    <AdminLayout :pageTitle="'Add Booking'">
        <div class="mx-auto max-w-5xl py-8">
            <form @submit.prevent="submitForm" class="rounded-lg bg-white p-6 shadow">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Booking Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Booking Number</label>
                        <input
                            v-model="form.booking_number"
                            type="number"
                            placeholder="Enter booking number"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <multiselect
                            v-model="selectedUser"
                            :options="users"
                            label="name"
                            track-by="id"
                            placeholder="Select a user"
                            :searchable="true"
                            :close-on-select="true"
                            :allow-empty="true"
                        />
                    </div>

                    <!-- Service -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service</label>
                        <multiselect
                            v-model="selectedService"
                            :options="services"
                            label="service_name"
                            track-by="service_id"
                            placeholder="Select a service"
                            :searchable="true"
                            :close-on-select="true"
                            :allow-empty="true"
                        />
                    </div>

                    <!-- Scheduled Datetime -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Datetime</label>
                        <input
                            v-model="form.scheduled_datetime"
                            type="datetime-local"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            v-model="form.status"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Total Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input
                            v-model="form.total_amount"
                            type="number"
                            step="0.01"
                            placeholder="Enter total amount"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            placeholder="Additional notes"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        ></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="cancel" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-600 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

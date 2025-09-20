<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import BaseTable from '@/components/BaseTable.vue';
import axios from 'axios';
import { Pencil, Trash2 } from 'lucide-vue-next';

// multiselect
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const search = ref('');
const bookings = ref<any[]>([]);
const pagination = ref({
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
});

// dropdown lists
const users = ref<any[]>([]);
const services = ref<any[]>([]);

const fetch = async () => {
    try {
        const response = await axios.get('/admin/bookings/list', {
            params: {
                search: search.value,
                page: pagination.value.current_page,
            }
        });
        if (response.data.result === true) {
            bookings.value = response.data.data;
            pagination.value = {
                total: response.data.pagination.total,
                per_page: response.data.pagination.per_page,
                current_page: response.data.pagination.current_page,
                last_page: response.data.pagination.last_page,
                from: response.data.pagination.from,
                to: response.data.pagination.to,
            };
        }
    } catch (e) {
        console.log(e);
    }
};

const fetchUsers = async () => {
    const response = await axios.get('/admin/users/selectList');
    if (response.data.result) {
        users.value = response.data.data.map((u: any) => ({
            id: u.id,
            name: u.name,
        }));
    }
};

const fetchServices = async () => {
    const response = await axios.get('/admin/services/selectList');
    if (response.data.result) {
        services.value = response.data.data.map((s: any) => ({
            id: s.service_id,
            name: s.service_name,
        }));
    }
};

onMounted(() => {
    fetch();
    fetchUsers();
    fetchServices();
});
watch(search, fetch);

const columns = [
    { key: 'booking_number', label: 'Booking #' },
    { key: 'user', label: 'Customer' },
    { key: 'service', label: 'Service' },
    { key: 'scheduled_datetime', label: 'Schedule' },
    { key: 'status', label: 'Status' },
    { key: 'total_amount', label: 'Total' },
    { key: 'actions', label: 'Actions', align: 'right' },
];

function addBooking() {
    router.get('/admin/bookings/create');
}

const showEditModal = ref(false);
const selectedBooking = ref<any>(null);

const formatDateTime = (datetime: string | null): string => {
    if (!datetime) return '';
    const date = new Date(datetime);

    const pad = (n: number) => (n < 10 ? '0' + n : n);

    const year = date.getFullYear();
    const month = pad(date.getMonth() + 1);
    const day = pad(date.getDate());
    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}


const openEditModal = async (booking_id: number) => {
    try {
        const response = await axios.get(`/admin/bookings/edit/${booking_id}`);
        if (response.data.result) {
            const booking = response.data.data;

            selectedBooking.value = {
                ...booking,
                user: booking.user ? { id: booking.user.id, name: booking.user.name } : null,
                service: booking.service ? { id: booking.service.service_id, name: booking.service.service_name } : null,
                scheduled_datetime: formatDateTime(booking.scheduled_datetime)
            };

            showEditModal.value = true;
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error("Error fetching booking:", error);
        alert("Failed to fetch booking details.");
    }
};

const updateBooking = async () => {
    if (!selectedBooking.value) return;

    const payload = {
        ...selectedBooking.value,
        user_id: selectedBooking.value.user?.id ?? null,
        service_id: selectedBooking.value.service?.id ?? null,
    };

    try {
        const response = await axios.post(`/admin/bookings/update`, payload);
        if (response.data.result) {
            alert("✅ Booking updated successfully!");
            showEditModal.value = false;
            fetch();
        } else {
            alert("⚠️ " + response.data.message);
        }
    } catch (error) {
        console.error("Error updating booking:", error);
        alert("❌ Failed to update booking.");
    }
};

const deleteBooking = async (booking_id: number) => {
    if (!confirm("Are you sure you want to delete this booking?")) return;

    try {
        const response = await axios.delete(`/admin/bookings/delete/${booking_id}`);
        if (response.data.result) {
            alert("✅ Booking deleted successfully!");
            fetch();
        } else {
            alert("⚠️ " + response.data.message);
        }
    } catch (error) {
        console.error("Error deleting booking:", error);
        alert("❌ Failed to delete booking.");
    }
};
</script>

<template>
    <Head title="Bookings" />
    <AdminLayout :pageTitle="'Bookings'">
        <div class="max-w-6xl mx-auto py-8">
            <!-- Search + Add -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search bookings..."
                    class="w-full md:w-64 rounded-lg border border-gray-300 px-3 py-2 text-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                    @click="addBooking"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white font-semibold shadow hover:bg-blue-700 transition-colors"
                >
                    <span>+ Add Booking</span>
                </button>
            </div>

            <!-- Table -->
            <BaseTable :columns="columns" :data="bookings" :pagination="pagination" @page-change="(page) => { pagination.current_page = page; fetch(); }">
                <!-- Customer -->
                <template #user="{ row }">
                    <span>{{ row.user?.name ?? 'N/A' }}</span>
                </template>

                <!-- Service -->
                <template #service="{ row }">
                    <span>{{ row.service?.service_name ?? 'N/A' }}</span>
                </template>

                <!-- Schedule -->
                <template #scheduled_datetime="{ row }">
                    {{ row.scheduled_datetime }}
                </template>

                <!-- Total -->
                <template #total_amount="{ row }">
                    ₱{{ parseFloat(row.total_amount).toFixed(2) }}
                </template>

                <!-- Status -->
                <template #status="{ row }">
                    <span :class="row.status === 'confirmed' ? 'text-green-600 font-bold' : 'text-gray-600 font-medium'">
                        {{ row.status }}
                    </span>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button
                        @click="openEditModal(row.booking_id)"
                        class="inline-flex items-center justify-center rounded p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition-colors mr-2"
                        title="Edit"
                    >
                        <Pencil class="h-5 w-5" />
                    </button>
                    <button
                        @click="deleteBooking(row.booking_id)"
                        class="inline-flex items-center justify-center rounded p-2 text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors"
                        title="Delete"
                    >
                        <Trash2 class="h-5 w-5" />
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 text-gray-700">
                <h2 class="text-lg font-bold mb-4">Edit Booking</h2>

                <form class="grid grid-cols-2 gap-4" @submit.prevent="updateBooking">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Booking #</label>
                        <input v-model="selectedBooking.booking_number" type="text" class="w-full border rounded px-3 py-2" disabled />
                    </div>

                    <!-- User -->
                    <div>
                        <label class="block text-sm font-medium">Customer</label>
                        <Multiselect
                            v-model="selectedBooking.user"
                            :options="users"
                            track-by="id"
                            label="name"
                            placeholder="Select user"
                            :searchable="true"
                            :allow-empty="false"
                        />
                    </div>

                    <!-- Service -->
                    <div>
                        <label class="block text-sm font-medium">Service</label>
                        <Multiselect
                            v-model="selectedBooking.service"
                            :options="services"
                            track-by="id"
                            label="name"
                            placeholder="Select service"
                            :searchable="true"
                            :allow-empty="false"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Schedule</label>
                        <input v-model="selectedBooking.scheduled_datetime" type="datetime-local" class="w-full border rounded px-3 py-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Status</label>
                        <select v-model="selectedBooking.status" class="w-full border rounded px-3 py-2">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Notes</label>
                        <textarea v-model="selectedBooking.notes" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="col-span-2 flex justify-end gap-2 mt-4">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

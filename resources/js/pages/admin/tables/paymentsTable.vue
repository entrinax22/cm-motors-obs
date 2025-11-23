<script setup lang="ts">
import BaseTable from '@/components/BaseTable.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

// multiselect
import 'vue-multiselect/dist/vue-multiselect.css';

const search = ref('');
const payments = ref<any[]>([]);
const pagination = ref({
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
});

const fetch = async () => {
    try {
        const response = await axios.get('/admin/payments/list', {
            params: {
                search: search.value,
                page: pagination.value.current_page,
            },
        });
        if (response.data.result === true) {
            payments.value = response.data.data;
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

onMounted(() => {
    fetch();
});
watch(search, fetch);

const columns = [
    { key: 'user_name', label: 'Customer' },
    { key: 'booking_number', label: 'Booking #' },
    { key: 'amount', label: 'Amount Paid' },
    { key: 'created_at', label: 'Payment Date' },
    { key: 'reference_number', label: 'Reference Number' },
    { key: 'payment_proof', label: 'Proof of Payment' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions', align: 'right' },
];

// function addBooking() {
//     router.get('/admin/bookings/create');
// }

const showEditModal = ref(false);
const selectedPayment = ref<any>(null);

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
};

const openEditModal = async (payment_id: number) => {
    try {
        const response = await axios.get(`/admin/payments/edit/${payment_id}`);
        if (response.data.result) {
            const payment = response.data.data;

            selectedPayment.value = {
                ...payment,
                created_at: formatDateTime(payment.created_at),
            };

            showEditModal.value = true;
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error('Error fetching payment:', error);
        alert('Failed to fetch payment details.');
    }
};

const updatePayment = async () => {
    try {
        const response = await axios.post('/admin/payments/update', selectedPayment.value);
        if (response.data.result) {
            alert('Payment updated successfully.');
            showEditModal.value = false;
            fetch();
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error('Error updating payment:', error);
        alert('Failed to update payment.');
    }
};

const deletePayment = async (payment_id: number) => {
    if (!confirm('Are you sure you want to delete this payment?')) return;

    try {
        const response = await axios.delete('/admin/payments/delete', {
            data: { payment_id },
        });
        if (response.data.result) {
            alert('Payment deleted successfully.');
            fetch();
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error('Error deleting payment:', error);
        alert('Failed to delete payment.');
    }
};
</script>
<template>
    <Head title="Payments" />
    <AdminLayout :pageTitle="'Payments'">
        <div class="w-full px-4 py-8">
            <!-- Search + Add -->
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search payments..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none md:w-64"
                />
                <!-- Example Add Payment Button -->
                <!-- <button
                    @click="addPayment"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow transition-colors hover:bg-blue-700"
                >
                    + Add Payment
                </button> -->
            </div>

            <!-- Table -->
            <BaseTable
                :columns="columns"
                :data="payments"
                :pagination="pagination"
                @page-change="
                    (page) => {
                        pagination.current_page = page;
                        fetch();
                    }
                "
            >
                <!-- User -->
                <template #user="{ row }">
                    <span>{{ row.user_name ?? 'N/A' }}</span>
                </template>

                <!-- Amount -->
                <template #amount="{ row }"> ₱{{ parseFloat(row.amount).toFixed(2) }} </template>

                <!-- Status -->
                <template #status="{ row }">
                    <span :class="row.status === 'confirmed' ? 'font-bold text-green-600' : 'font-medium text-gray-600'">
                        {{ row.status }}
                    </span>
                </template>

                <!-- Payment Proof -->
                <template #payment_proof="{ row }">
                    <img v-if="row.payment_proof" :src="`/storage/${row.payment_proof}`" alt="Payment Proof" class="h-12 w-12 rounded object-cover" />
                    <span v-else>N/A</span>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button
                        @click="openEditModal(row.payment_id)"
                        class="mr-2 inline-flex items-center justify-center rounded p-2 text-blue-600 transition-colors hover:bg-blue-50 hover:text-blue-800"
                        title="Edit"
                    >
                        <Pencil class="h-5 w-5" />
                    </button>
                    <button
                        @click="deletePayment(row.payment_id)"
                        class="inline-flex items-center justify-center rounded p-2 text-red-600 transition-colors hover:bg-red-50 hover:text-red-800"
                        title="Delete"
                    >
                        <Trash2 class="h-5 w-5" />
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 text-gray-700 shadow-lg">
                <h2 class="mb-4 text-lg font-bold">Edit Payment</h2>
                <form class="grid grid-cols-2 gap-4" @submit.prevent="updatePayment">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Reference Number</label>
                        <input v-model="selectedPayment.reference_number" type="text" class="w-full rounded border px-3 py-2" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Amount</label>
                        <input v-model.number="selectedPayment.amount" type="number" class="w-full rounded border px-3 py-2" readonly />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Status</label>
                        <select v-model="selectedPayment.status" class="w-full rounded border px-3 py-2">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-span-2 mt-4 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

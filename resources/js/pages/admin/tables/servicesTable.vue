<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import BaseTable from '@/components/BaseTable.vue';
import axios from 'axios';
import { Pencil, Trash2 } from 'lucide-vue-next';

const search = ref('');
const services = ref([]);
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
        const response = await axios.get('/admin/services/list', {
            params: {
                search: search.value,
                page: pagination.value.current_page,
            }
        });
        if (response.data.result === true) {
            services.value = response.data.data;
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

watch(search, () => {
    fetch();
});

const columns = [
    { key: 'image', label: 'Image' },
    { key: 'service_name', label: 'Name' },
    { key: 'description', label: 'Description' },
    { key: 'price', label: 'Price' },
    { key: 'duration_minutes', label: 'Duration (min)' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: 'Actions', align: 'right' },
];

function addService() {
    router.get('/admin/services/create');
}

const showEditModal = ref(false);
const selectedService = ref<any>(null);
const previewImage = ref<string | null>(null);

const openEditModal = async (service_id: number) => {
    try {
        const response = await axios.get(`/admin/services/edit/${service_id}`);
        if (response.data.result) {
            selectedService.value = { ...response.data.data };
            selectedService.value.is_active = response.data.data.is_active ? "1" : "0";
            previewImage.value = null; // reset preview
            showEditModal.value = true;
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error("Error fetching service:", error);
        alert("Failed to fetch service details.");
    }
};

// handle file upload
const handleImageUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        previewImage.value = URL.createObjectURL(file);
        selectedService.value.imageFile = file; // attach file to object
    }
};

// submit update
const updateService = async () => {
    if (!selectedService.value) return;

    try {
        const formData = new FormData();
        formData.append('service_id', String(selectedService.value.service_id));
        formData.append('service_name', selectedService.value.service_name);
        formData.append('description', selectedService.value.description);
        formData.append('price', String(selectedService.value.price));
        formData.append('duration_minutes', String(selectedService.value.duration_minutes));
        formData.append('is_active', selectedService.value.is_active); 

        if (selectedService.value.imageFile) {
            formData.append('image', selectedService.value.imageFile);
        }

        const response = await axios.post(`/admin/services/update`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.result) {
            alert("✅ Service updated successfully!");
            showEditModal.value = false;
            fetch(); 
        } else {
            alert("⚠️ " + response.data.message);
        }
    } catch (error) {
        console.error("Error updating service:", error);
        alert("❌ Failed to update service.");
    }
};

const deleteService = async (service_id: number) => {
    if (!confirm("Are you sure you want to delete this service? This action cannot be undone.")) {
        return;
    }

    try {
        const response = await axios.delete(`/admin/services/delete/${service_id}`);

        if (response.data.result) {
            alert("✅ Service deleted successfully!");
            showEditModal.value = false;
            fetch(); 
        } else {
            alert("⚠️ " + response.data.message);
        }
    } catch (error) {
        console.error("Error deleting service:", error);
        alert("❌ Failed to delete service.");
    }
};

</script>

<template>
    <Head title="Services" />
    <AdminLayout :pageTitle="'Services'">
        <div class="max-w-5xl mx-auto py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search services..."
                        class="w-full md:w-64 rounded-lg border border-gray-300 px-3 py-2 text-gray-600 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <button
                    @click="addService"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white font-semibold shadow hover:bg-blue-700 transition-colors"
                >
                    <span>+ Add Service</span>
                </button>
            </div>

            <BaseTable :columns="columns" :data="services" :pagination="pagination" @page-change="(page) => { pagination.current_page = page; fetch(); }">
                <!-- Service Image -->
                <template #image="{ row }">
                    <img :src="`/storage/${row.image}`" alt="Service Image" class="h-12 w-12 object-cover rounded" />
                </template>

                <!-- Price -->
                <template #price="{ row }">
                    ₱{{ parseFloat(row.price).toFixed(2) }}
                </template>

                <!-- Status -->
                <template #is_active="{ row }">
                    <span :class="row.is_active == '1' ? 'text-green-600 font-bold' : 'text-red-600 font-bold'">
                        {{ row.is_active == '1' ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button
                        @click="openEditModal(row.service_id)"
                        class="inline-flex items-center justify-center rounded p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition-colors mr-2"
                        title="Edit"
                    >
                        <Pencil class="h-5 w-5" />
                    </button>
                    <button
                        class="inline-flex items-center justify-center rounded p-2 text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors"
                        title="Delete"
                        @click="deleteService(row.service_id)"
                    >
                        <Trash2 class="h-5 w-5" />
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 text-gray-700">
                <h2 class="text-lg font-bold mb-4">Edit Service</h2>

                <form class="grid grid-cols-2 gap-4" @submit.prevent="updateService">

                    <!-- Service Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Service Name</label>
                        <input v-model="selectedService.service_name" type="text" class="w-full border rounded px-3 py-2 text-gray-700" />
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Description</label>
                        <textarea v-model="selectedService.description" class="w-full border rounded px-3 py-2 text-gray-700"></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium">Price</label>
                        <input v-model="selectedService.price" type="number" class="w-full border rounded px-3 py-2 text-gray-700" />
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium">Duration (minutes)</label>
                        <input v-model="selectedService.duration_minutes" type="number" class="w-full border rounded px-3 py-2 text-gray-700" />
                    </div>

                    <!-- Image -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Service Image</label>
                        <input type="file" @change="handleImageUpload" class="w-full border rounded px-3 py-2 text-gray-700" />

                        <!-- Preview uploaded image -->
                        <div v-if="previewImage" class="mt-2">
                            <img :src="previewImage" alt="Preview" class="h-24 w-24 object-cover rounded" />
                        </div>
                        <!-- Show existing image if no new upload -->
                        <div v-else-if="selectedService.image" class="mt-2">
                            <img :src="`/storage/${selectedService.image}`" alt="Current Image" class="h-24 w-24 object-cover rounded" />
                        </div>
                    </div>

                    <!-- Status (Dropdown) -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Status</label>
                        <select v-model="selectedService.is_active" class="w-full border rounded px-3 py-2 text-gray-700">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-span-2 flex justify-end gap-2 mt-4">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

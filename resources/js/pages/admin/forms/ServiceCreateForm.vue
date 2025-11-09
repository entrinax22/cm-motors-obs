<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const form = ref({
    service_name: '',
    description: '',
    price: null as number | null,
    duration_minutes: null as number | null,
    is_active: true,
    image: null as File | null,
    image_preview: null as string | null,
});

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.value.image = target.files[0];
        form.value.image_preview = URL.createObjectURL(target.files[0]);
    }
};

const submitForm = async () => {
    try {
        const formData = new FormData();
        formData.append('service_name', form.value.service_name);
        formData.append('description', form.value.description);
        formData.append('price', form.value.price?.toString() || '');
        formData.append('duration_minutes', form.value.duration_minutes?.toString() || '');
        formData.append('is_active', form.value.is_active ? '1' : '0');

        if (form.value.image) {
            formData.append('image', form.value.image);
        }

        const response = await axios.post('/admin/services/store', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data.result === true) {
            // Reset form
            form.value = {
                service_name: '',
                description: '',
                price: null,
                duration_minutes: null,
                is_active: true,
                image: null,
                image_preview: null,
            };
            alert('Service saved successfully!');
        }
    } catch (error) {
        console.error('Error saving service:', error);
    }
};

const cancel = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Add Service" />
    <AdminLayout :pageTitle="'Add Service'">
        <div class="mx-auto max-w-5xl py-8">
            <form @submit.prevent="submitForm" class="rounded-lg bg-white p-6 shadow">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Service Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Name</label>
                        <input
                            v-model="form.service_name"
                            type="text"
                            placeholder="Enter service name"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input
                            v-model="form.price"
                            type="number"
                            placeholder="Enter price"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duration (Minutes)</label>
                        <input
                            v-model="form.duration_minutes"
                            type="number"
                            placeholder="Enter duration in minutes"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            v-model="form.is_active"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                    </div>

                    <!-- Description (full width) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter service description"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Service Image</label>
                        <div
                            class="flex h-40 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition hover:bg-gray-100"
                            @click="$refs.fileInput.click()"
                        >
                            <svg
                                class="mb-2 h-12 w-12 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16V4m0 0l-3 3m3-3l3 3m7-7h2a2 2 0 012 2v2m-2-2l-3 3m0 0V4m0 3h-3m4 8h.01M12 12v3m0 0h3m-3 0H9"
                                />
                            </svg>
                            <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400">PNG, JPG up to 2MB</p>
                            <input ref="fileInput" type="file" class="hidden" @change="handleFileUpload" accept="image/png,image/jpeg" />
                        </div>

                        <!-- Preview -->
                        <div v-if="form.image_preview" class="mt-3 flex items-center gap-3">
                            <img :src="form.image_preview" alt="Preview" class="h-20 w-20 rounded-lg border object-cover" />
                            <span class="text-sm text-gray-600">{{ form.image?.name }}</span>
                        </div>
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

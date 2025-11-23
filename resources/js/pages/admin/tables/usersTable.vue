<script setup lang="ts">
import BaseTable from '@/components/BaseTable.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const search = ref('');
const users = ref([]);
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
        const response = await axios.get('/admin/users/list', {
            params: {
                search: search.value,
                page: pagination.value.current_page,
            },
        });
        if (response.data.result === true) {
            users.value = response.data.data;
            pagination.value = {
                total: response.data.pagination.total,
                per_page: response.data.pagination.per_page,
                current_page: response.data.pagination.current_page,
                last_page: response.data.pagination.last_page,
                from: response.data.pagination.from,
                to: response.data.pagination.to,
            };
            console.log('Success fetching users');
        }
    } catch (e) {
        console.log(e);
    }
};

onMounted(() => {
    fetch();
});

watch(search, (newValue) => {
    fetch();
});

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'is_admin', label: 'Role' },
    { key: 'actions', label: 'Actions', align: 'right' },
];

// function addUser() {
//     router.get('/admin/users/create');
// }

const showEditModal = ref(false);
const selectedUser = ref<any>(null);
const previewImage = ref<string | null>(null);

const openEditModal = async (id: number) => {
    try {
        const response = await axios.get(`/admin/users/edit/${id}`);
        if (response.data.result) {
            selectedUser.value = { ...response.data.data };
            selectedUser.value.is_active = response.data.data.is_active ? '1' : '0';
            previewImage.value = null;
            showEditModal.value = true;
        } else {
            alert(response.data.message);
        }
    } catch (error) {
        console.error('Error fetching user:', error);
        alert('Failed to fetch user details.');
    }
};

// // submit update
const updateUser = async () => {
    if (!selectedUser.value) return;

    try {
        const response = await axios.post(`/admin/users/update/${selectedUser.value.id}`, {
            name: selectedUser.value.name,
            email: selectedUser.value.email,
            is_admin: selectedUser.value.is_admin,
        });

        if (response.data.result) {
            alert('✅ User updated successfully!');
            showEditModal.value = false;
            fetch();
        } else {
            alert('⚠️ ' + response.data.message);
        }
    } catch (error) {
        console.error('Error updating user:', error);
        alert('❌ Failed to update user.');
    }
};

const deleteService = async (id: number) => {
    if (!confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await axios.delete(`/admin/users/delete/${id}`);

        if (response.data.result) {
            alert('✅ Service deleted successfully!');
            fetch();
        } else {
            alert('⚠️ ' + response.data.message);
        }
    } catch (error) {
        console.error('Error deleting service:', error);
        alert('❌ Failed to delete service.');
    }
};
</script>

<template>
    <Head title="Users" />
    <AdminLayout :pageTitle="'Users'">
        <div class="w-full px-4 py-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search users..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 focus:outline-none md:w-64"
                />
                <!-- <button
                    @click="addUser"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white font-semibold shadow hover:bg-blue-700 transition-colors"
                >
                    <span>+ Add User</span>
                </button> -->
            </div>
            <BaseTable :columns="columns" :data="users" :pagination="pagination" @page-change="(page) => {}">
                <template #profile_picture="{ row }">
                    <img :src="row.profile_picture" alt="Profile" class="h-8 w-8 rounded-full object-cover" v-if="row.profile_picture" />
                </template>
                <template #is_admin="{ row }">
                    <span :class="row.is_admin ? 'font-bold text-green-600' : 'text-gray-500'">
                        {{ row.is_admin ? 'Admin' : 'User' }}
                    </span>
                </template>
                <template #actions="{ row }">
                    <button
                        @click="openEditModal(row.id)"
                        class="mr-2 inline-flex items-center justify-center rounded p-2 text-blue-600 transition-colors hover:bg-blue-50 hover:text-blue-800"
                        title="Edit"
                    >
                        <Pencil class="h-5 w-5" />
                    </button>
                    <button
                        class="inline-flex items-center justify-center rounded p-2 text-red-600 transition-colors hover:bg-red-50 hover:text-red-800"
                        title="Delete"
                        @click="deleteService(row.id)"
                    >
                        <Trash2 class="h-5 w-5" />
                    </button>
                </template>
            </BaseTable>
        </div>

        <!-- Edit User Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 text-gray-700 shadow-lg">
                <h2 class="mb-4 text-lg font-bold">Edit User</h2>

                <form class="grid grid-cols-2 gap-4" @submit.prevent="updateUser">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Name</label>
                        <input v-model="selectedUser.name" type="text" class="w-full rounded border px-3 py-2 text-gray-700" />
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Email</label>
                        <input v-model="selectedUser.email" type="email" class="w-full rounded border px-3 py-2 text-gray-700" />
                    </div>

                    <!-- Role -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium">Role</label>
                        <select v-model="selectedUser.is_admin" class="w-full rounded border px-3 py-2 text-gray-700">
                            <option value="1">Admin</option>
                            <option value="0">User</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-2 mt-4 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="rounded bg-gray-200 px-4 py-2 hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

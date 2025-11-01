<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const page = usePage();
const user = ref<any>({});
const isEditing = ref(false);
const newPhoto = ref<File | null>(null);
const photoPreview = ref<string | null>(null);
const loading = ref(false);
const otpSent = ref(false);

// Modal state
const showPasswordModal = ref(false);
const otp = ref('');
const newPassword = ref('');
const confirmPassword = ref('');

// Fetch user profile
const fetchProfile = async () => {
    try {
        const res = await axios.get('/user/my_profile');
        if (res.data.result) {
            user.value = res.data.data;
            photoPreview.value = user.value.profile_photo_url || 'https://via.placeholder.com/150';
        }
    } catch (err) {
        console.error(err);
    }
};

// Handle new photo
const handlePhotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        newPhoto.value = file;
        const reader = new FileReader();
        reader.onload = (event) => (photoPreview.value = event.target?.result as string);
        reader.readAsDataURL(file);
    }
};

// Update profile
const updateProfile = async () => {
    const formData = new FormData();
    Object.entries(user.value).forEach(([key, value]) => {
        if (value !== null && key !== 'profile_photo_url') formData.append(key, value as string);
    });
    if (newPhoto.value) formData.append('profile_photo', newPhoto.value);

    try {
        loading.value = true;
        await axios.post('/profile/updateProfile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        isEditing.value = false;
        newPhoto.value = null;
        openSuccessModal('Profile updated successfully!');
        fetchProfile();
    } catch (err) {
        console.error(err);
        alert('Failed to update profile.');
    } finally {
        loading.value = false;
    }
};

// Send OTP via Semaphore
const sendOtp = async () => {
    try {
        const res = await axios.post('/user/send-password-otp');
        if (res.data.result) {
            otpSent.value = true;
            alert('OTP sent to your registered phone number.');
        }
    } catch (err) {
        console.error(err);
        alert('Failed to send OTP.');
    }
};

// Update password
const updatePassword = async () => {
    try {
        const res = await axios.post('/user/update-password', {
            otp: otp.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value,
        });

        if (res.data.result) {
            otp.value = '';
            newPassword.value = '';
            confirmPassword.value = '';
            otpSent.value = false;
            showPasswordModal.value = false;
            openSuccessModal('Password updated successfully!');
        }
    } catch (err) {
        console.error(err);
        alert('Failed to update password.');
    }
};

onMounted(() => fetchProfile());

const showSuccessModal = ref(false);
const successMessage = ref('');

// Function to show success modal
const openSuccessModal = (message: string) => {
    successMessage.value = message;
    showSuccessModal.value = true;
    setTimeout(() => {
        showSuccessModal.value = false;
    }, 2500); // auto-close after 2.5s
};
</script>

<template>
    <Head title="My Profile" />
    <MainLayout>
        <section class="bg-gradient-to-r from-gray-800 to-gray-900 py-10 text-center shadow-lg">
            <h1 class="text-3xl font-bold text-white md:text-4xl">My Profile</h1>
            <p class="mt-2 text-gray-300">Manage your account details and security settings</p>
        </section>

        <section class="mx-auto max-w-6xl space-y-10 px-6 py-10">
            <!-- Profile Card -->
            <div class="rounded-3xl bg-white p-8 shadow-lg transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl">
                <div class="flex flex-col items-center text-center">
                    <div class="relative">
                        <img :src="photoPreview" alt="Profile" class="h-32 w-32 rounded-full border-4 border-gray-200 object-cover shadow-lg" />
                        <label
                            v-if="isEditing"
                            class="absolute right-0 bottom-0 cursor-pointer rounded-full bg-gray-800 p-2 text-white hover:bg-gray-700"
                        >
                            <input type="file" class="hidden" accept="image/*" @change="handlePhotoChange" />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l6 6L20.364 9.636a2 2 0 00-2.828-2.828L9 13z"
                                />
                            </svg>
                        </label>
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-800">{{ user.name }}</h2>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>

                <!-- Info Form -->
                <div class="mt-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-700">Account Information</h3>
                        <button
                            v-if="!isEditing"
                            @click="isEditing = true"
                            class="rounded-lg bg-gray-800 px-5 py-2 text-sm text-white transition hover:bg-gray-700"
                        >
                            Edit Profile
                        </button>
                    </div>

                    <form @submit.prevent="updateProfile" class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-sm text-gray-500">Full Name</label>
                            <input v-if="isEditing" v-model="user.name" class="input-field" />
                            <p v-else class="mt-1 text-gray-800">{{ user.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Phone</label>
                            <input v-if="isEditing" v-model="user.phone" class="input-field" />
                            <p v-else class="mt-1 text-gray-800">{{ user.phone || '—' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">City</label>
                            <input v-if="isEditing" v-model="user.city" class="input-field" />
                            <p v-else class="mt-1 text-gray-800">{{ user.city || '—' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">ZIP Code</label>
                            <input v-if="isEditing" v-model="user.zip" class="input-field" />
                            <p v-else class="mt-1 text-gray-800">{{ user.zip || '—' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500">Address</label>
                            <textarea v-if="isEditing" v-model="user.address" class="input-field" rows="3"></textarea>
                            <p v-else class="mt-1 text-gray-800">{{ user.address || '—' }}</p>
                        </div>

                        <div v-if="isEditing" class="col-span-2 mt-4 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="isEditing = false"
                                class="rounded-lg border border-gray-300 px-6 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Cancel
                            </button>
                            <button type="submit" :disabled="loading" class="rounded-lg bg-gray-800 px-6 py-2 text-white hover:bg-gray-700">
                                {{ loading ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password -->
                <div class="mt-10 text-right">
                    <button @click="showPasswordModal = true" class="rounded-lg bg-red-600 px-5 py-2 text-sm text-white transition hover:bg-red-500">
                        Change Password
                    </button>
                </div>
            </div>
        </section>

        <!-- Password Modal -->
        <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
                <button @click="showPasswordModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>

                <h2 class="mb-4 text-xl font-semibold text-gray-800">Change Password</h2>

                <div v-if="!otpSent" class="text-center">
                    <p class="mb-4 text-gray-600">Send OTP to your registered number:</p>
                    <button @click="sendOtp" class="rounded-lg bg-gray-800 px-5 py-2 text-white hover:bg-gray-700">
                        Send OTP to {{ user.phone }}
                    </button>
                </div>

                <form v-else @submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-600">Enter OTP</label>
                        <input v-model="otp" class="input-field" placeholder="Enter OTP" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">New Password</label>
                        <input v-model="newPassword" type="password" class="input-field" placeholder="New password" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Confirm Password</label>
                        <input v-model="confirmPassword" type="password" class="input-field" placeholder="Confirm password" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-gray-800 px-6 py-2 text-white hover:bg-gray-700">Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ✅ Success Modal -->
        <transition name="fade">
            <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
                <div class="w-80 rounded-2xl bg-white p-8 text-center shadow-2xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mx-auto mb-4 h-12 w-12 text-green-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Success!</h3>
                    <p class="mt-2 text-gray-600">{{ successMessage }}</p>
                </div>
            </div>
        </transition>
    </MainLayout>
</template>

<style scoped>
@import '../../../css/app.css';
.input-field {
    @apply mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-gray-900 transition focus:ring-2 focus:ring-gray-700 focus:outline-none;
}
</style>

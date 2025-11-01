<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import axios from 'axios';
import { onUnmounted, ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

// Modal control
const showForgotModal = ref(false);
const showSuccessModal = ref(false);
const successMessage = ref('');

// Form state
const mobileNumber = ref('');
const otp = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const otpSent = ref(false);
const loading = ref(false);
const message = ref('');
const error = ref('');

// Resend OTP cooldown
const resendCooldown = ref(0);
let resendTimer: ReturnType<typeof setInterval> | null = null;

const startResendCooldown = () => {
    resendCooldown.value = 60;
    if (resendTimer) clearInterval(resendTimer);
    resendTimer = setInterval(() => {
        if (resendCooldown.value > 0) {
            resendCooldown.value--;
        } else {
            if (resendTimer) clearInterval(resendTimer);
            resendTimer = null;
        }
    }, 1000);
};

// Clean up interval on component unmount
onUnmounted(() => {
    if (resendTimer) clearInterval(resendTimer);
});

// Send OTP
const sendOtp = async () => {
    if (!mobileNumber.value) {
        error.value = 'Please enter your mobile number.';
        return;
    }

    error.value = '';
    loading.value = true;

    try {
        const res = await axios.post('/user/send-password-otp', { mobile_number: mobileNumber.value });
        if (res.data.result) {
            otpSent.value = true;
            message.value = 'OTP sent successfully! Please check your mobile.';
            startResendCooldown();
        } else {
            error.value = res.data.message || 'Failed to send OTP.';
        }
    } catch (err) {
        error.value = 'Server error while sending OTP.';
    } finally {
        loading.value = false;
    }
};

// Update password
const updatePassword = async () => {
    if (!otp.value || !newPassword.value || !confirmPassword.value) {
        error.value = 'Please fill in all fields.';
        return;
    }

    loading.value = true;
    error.value = '';
    message.value = '';

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
            showForgotModal.value = false;

            successMessage.value = res.data.message || 'Password updated successfully!';
            showSuccessModal.value = true;
        } else {
            error.value = res.data.message || 'Failed to update password.';
        }
    } catch (err) {
        error.value = 'Server error while updating password.';
    } finally {
        loading.value = false;
    }
};

// Resend OTP
const resendOtp = async () => {
    if (resendCooldown.value > 0 || loading.value) return;
    await sendOtp();
};

// Close success modal
const closeSuccessModal = () => (showSuccessModal.value = false);
</script>

<template>
    <div class="relative flex min-h-screen items-center justify-center bg-cover bg-center" style="background-image: url('/images/bg_motor.png')">
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-md rounded-xl border border-gray-700 bg-gray-800/95 p-8 shadow-2xl">
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
                <img src="/images/logo_cm_motors.png" alt="CM Motorparts Logo" class="h-20 w-20 rounded-full shadow-lg ring-4 ring-yellow-400" />
            </div>

            <h1 class="mb-2 text-center text-2xl font-extrabold text-white">Log in to <span class="text-yellow-400">CM Motorparts</span></h1>
            <p class="mb-6 text-center text-sm text-gray-300">Enter your email and password to access your account</p>

            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-400">{{ status }}</div>

            <!-- Login Form -->
            <Form method="post" action="/login" v-slot="{ errors, processing }" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-200">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100 shadow-sm focus:border-yellow-400 focus:ring-yellow-400"
                        placeholder="you@example.com"
                    />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-400">{{ errors.email }}</p>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-gray-200">Password</label>
                        <button type="button" class="text-xs text-yellow-400 hover:underline" @click="showForgotModal = true">
                            Forgot password?
                        </button>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100 shadow-sm focus:border-yellow-400 focus:ring-yellow-400"
                        placeholder="••••••••"
                    />
                    <p v-if="errors.password" class="mt-1 text-xs text-red-400">{{ errors.password }}</p>
                </div>

                <div class="flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-yellow-500 focus:ring-yellow-400"
                    />
                    <label for="remember" class="ml-2 text-sm text-gray-300">Remember me</label>
                </div>

                <button
                    type="submit"
                    class="flex w-full items-center justify-center rounded-md bg-yellow-500 px-4 py-2 text-sm font-bold text-gray-900 shadow-md transition hover:bg-yellow-400 disabled:opacity-50"
                    :disabled="processing"
                >
                    <svg
                        v-if="processing"
                        class="mr-2 h-4 w-4 animate-spin text-gray-900"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Log in
                </button>
            </Form>

            <div class="mt-6 text-center text-sm text-gray-300">
                Don’t have an account?
                <a href="/register" class="font-semibold text-yellow-400 hover:underline">Sign up</a>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div v-if="showForgotModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-lg bg-gray-900 p-6 shadow-xl">
                <h2 class="mb-4 text-center text-xl font-bold text-yellow-400">Forgot Password</h2>

                <div v-if="!otpSent">
                    <label class="mb-2 block text-sm text-gray-300">Enter your registered mobile number</label>
                    <input
                        v-model="mobileNumber"
                        type="text"
                        placeholder="09XXXXXXXXX"
                        class="w-full rounded-md border border-gray-700 bg-gray-800 p-2 text-gray-100 focus:border-yellow-400 focus:ring-yellow-400"
                    />
                    <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
                    <button
                        @click="sendOtp"
                        class="mt-4 w-full rounded-md bg-yellow-500 py-2 font-semibold text-gray-900 hover:bg-yellow-400"
                        :disabled="loading"
                    >
                        {{ loading ? 'Sending...' : 'Send OTP' }}
                    </button>
                </div>

                <div v-else>
                    <p class="mb-2 text-sm text-gray-300">
                        OTP has been sent to <span class="text-yellow-400">{{ mobileNumber }}</span
                        >.
                    </p>

                    <input
                        v-model="otp"
                        type="text"
                        placeholder="Enter OTP"
                        class="mb-2 w-full rounded-md border border-gray-700 bg-gray-800 p-2 text-gray-100 focus:border-yellow-400 focus:ring-yellow-400"
                    />
                    <input
                        v-model="newPassword"
                        type="password"
                        placeholder="New Password"
                        class="mb-2 w-full rounded-md border border-gray-700 bg-gray-800 p-2 text-gray-100 focus:border-yellow-400 focus:ring-yellow-400"
                    />
                    <input
                        v-model="confirmPassword"
                        type="password"
                        placeholder="Confirm Password"
                        class="mb-2 w-full rounded-md border border-gray-700 bg-gray-800 p-2 text-gray-100 focus:border-yellow-400 focus:ring-yellow-400"
                    />

                    <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
                    <p v-if="message" class="mt-2 text-sm text-green-400">{{ message }}</p>

                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-between">
                        <button
                            @click="updatePassword"
                            class="w-full flex-1 rounded-md bg-yellow-500 py-2 font-semibold text-gray-900 transition hover:bg-yellow-400 sm:w-auto"
                            :disabled="loading"
                        >
                            {{ loading ? 'Updating...' : 'Update Password' }}
                        </button>

                        <button
                            @click="resendOtp"
                            class="w-full flex-1 rounded-md border border-yellow-400 py-2 text-yellow-400 transition hover:bg-yellow-400 hover:text-gray-900 disabled:opacity-50 sm:w-auto"
                            :disabled="resendCooldown > 0 || loading"
                        >
                            <span v-if="resendCooldown > 0">Resend OTP in {{ resendCooldown }}s</span>
                            <span v-else>Resend OTP</span>
                        </button>
                    </div>

                    <p v-if="resendCooldown > 0" class="mt-2 text-center text-xs text-gray-400">You can request a new OTP after the timer ends.</p>
                </div>

                <button
                    @click="showForgotModal = false"
                    class="mt-4 w-full rounded-md border border-gray-700 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white"
                >
                    Cancel
                </button>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-lg bg-gray-900 p-6 shadow-xl">
                <h2 class="mb-4 text-center text-xl font-bold text-green-400">Success!</h2>
                <p class="mb-6 text-center text-gray-100">{{ successMessage }}</p>
                <button @click="closeSuccessModal" class="w-full rounded-md bg-green-500 py-2 font-semibold text-gray-900 hover:bg-green-400">
                    OK
                </button>
            </div>
        </div>
    </div>
</template>

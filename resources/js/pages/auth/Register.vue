<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-screen items-center justify-center bg-cover bg-center" style="background-image: url('/images/bg_motor.png')">
        <!-- Dark blurry overlay -->
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>

        <!-- Card -->
        <div class="relative z-10 w-full max-w-md rounded-xl border border-gray-700 bg-gray-800/95 p-8 shadow-2xl">
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
                <img src="/images/logo_cm_motors.png" alt="CM Motorparts Logo" class="h-20 w-20 rounded-full shadow-lg ring-4 ring-yellow-400" />
            </div>

            <!-- Title -->
            <h1 class="mb-2 text-center text-2xl font-extrabold text-white">
                Create your <span class="text-yellow-400">CM Motorparts</span> account
            </h1>
            <p class="mb-6 text-center text-sm text-gray-300">Enter your details below to get started</p>

            <Head title="Register" />

            <!-- Register Form -->
            <Form method="post" action="/register" v-slot="{ errors, processing }" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-200">Full name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100 shadow-sm focus:border-yellow-400 focus:ring-yellow-400"
                        placeholder="Juan Dela Cruz"
                    />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-400">{{ errors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-200">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100 shadow-sm focus:border-yellow-400 focus:ring-yellow-400"
                        placeholder="you@example.com"
                    />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-400">{{ errors.email }}</p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-200">Password</label>
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

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-200">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100 shadow-sm focus:border-yellow-400 focus:ring-yellow-400"
                        placeholder="••••••••"
                    />
                    <p v-if="errors.password_confirmation" class="mt-1 text-xs text-red-400">{{ errors.password_confirmation }}</p>
                </div>

                <!-- Submit Button -->
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
                    Create account
                </button>
            </Form>

            <!-- Login Link -->
            <div class="mt-6 text-center text-sm text-gray-300">
                Already have an account?
                <a href="/login" class="font-semibold text-yellow-400 hover:underline">Log in</a>
            </div>
        </div>
    </div>
</template>

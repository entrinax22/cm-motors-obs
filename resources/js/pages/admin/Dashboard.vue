<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Bar, Pie } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js';
import { CalendarCheck, Wrench, Users } from 'lucide-vue-next';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const barData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Bookings',
      backgroundColor: '#2563eb',
      data: [12, 19, 8, 15, 22, 13],
    },
  ],
};
const barOptions = {
  responsive: true,
  plugins: { legend: { display: false } },
};

const pieData = {
  labels: ['Services', 'Customers', 'Reports'],
  datasets: [
    {
      backgroundColor: ['#2563eb', '#2d3748', '#4b5563'],
      data: [40, 30, 30],
    },
  ],
};
const pieOptions = {
  responsive: true,
  plugins: { legend: { position: 'bottom' } },
};
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Quick Actions Tool -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <button class="flex flex-col items-center justify-center bg-gradient-to-br from-blue-500 to-blue-400 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform">
                    <CalendarCheck class="h-6 w-6 mb-1" />
                    <span class="font-semibold">New Booking</span>
                </button>
                <button class="flex flex-col items-center justify-center bg-gradient-to-br from-green-500 to-green-400 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform">
                    <Wrench class="h-6 w-6 mb-1" />
                    <span class="font-semibold">Add Service</span>
                </button>
                <button class="flex flex-col items-center justify-center bg-gradient-to-br from-yellow-500 to-yellow-400 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform">
                    <Users class="h-6 w-6 mb-1" />
                    <span class="font-semibold">Add Customer</span>
                </button>
                <button class="flex flex-col items-center justify-center bg-gradient-to-br from-gray-700 to-gray-500 text-white rounded-xl shadow-lg p-4 hover:scale-105 transition-transform">
                    <span class="h-6 w-6 mb-1 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-2v2a4 4 0 01-4 4h-1a4 4 0 01-4-4v-2m6-6a4 4 0 00-4-4V5a4 4 0 00-4-4H7a4 4 0 00-4 4v2" /></svg></span>
                    <span class="font-semibold">Settings</span>
                </button>
            </div>
            <!-- Recent Tabs -->
            <div class="bg-white shadow rounded-xl p-4 mb-4">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Recent Tabs</h2>
                <ul class="divide-y divide-gray-200">
                    <li class="py-2 flex items-center gap-2">
                        <CalendarCheck class="h-5 w-5 text-blue-500" />
                        <span class="text-gray-700">Bookings</span>
                        <span class="ml-auto text-xs text-gray-400">2 min ago</span>
                    </li>
                    <li class="py-2 flex items-center gap-2">
                        <Wrench class="h-5 w-5 text-green-500" />
                        <span class="text-gray-700">Services</span>
                        <span class="ml-auto text-xs text-gray-400">5 min ago</span>
                    </li>
                    <li class="py-2 flex items-center gap-2">
                        <Users class="h-5 w-5 text-yellow-500" />
                        <span class="text-gray-700">Customers</span>
                        <span class="ml-auto text-xs text-gray-400">10 min ago</span>
                    </li>
                </ul>
            </div>
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-600 to-blue-400 shadow-lg rounded-xl p-4 flex flex-col items-center">
                    <CalendarCheck class="h-8 w-8 mb-2 text-white drop-shadow" />
                    <span class="text-3xl font-extrabold text-white drop-shadow">152</span>
                    <span class="text-blue-100 font-medium">Total Bookings</span>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-300 shadow-lg rounded-xl p-4 flex flex-col items-center">
                    <Wrench class="h-8 w-8 mb-2 text-white drop-shadow" />
                    <span class="text-3xl font-extrabold text-white drop-shadow">87</span>
                    <span class="text-green-100 font-medium">Total Services</span>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-300 shadow-lg rounded-xl p-4 flex flex-col items-center">
                    <Users class="h-8 w-8 mb-2 text-white drop-shadow" />
                    <span class="text-3xl font-extrabold text-white drop-shadow">45</span>
                    <span class="text-yellow-100 font-medium">Total Customers</span>
                </div>
            </div>
            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded-xl p-4">
                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Bookings Over Time</h2>
                    <Bar :data="barData" :options="barOptions" />
                </div>
                <div class="bg-white shadow rounded-xl p-4">
                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Overview</h2>
                    <Pie :data="pieData" :options="pieOptions" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

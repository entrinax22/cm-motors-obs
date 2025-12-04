<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Overview of bookings and services</p>
                </div>

                <div class="flex items-center gap-3">
                    <select
                        v-model="selectedRange"
                        @change="loadDashboard"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-black shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="1">1 Month (Daily)</option>
                        <option value="3">3 Months (Daily)</option>
                        <option value="6">6 Months (Daily)</option>
                    </select>

                    <button
                        class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-lg transition-transform hover:scale-105 hover:bg-blue-700"
                        @click="generateReport"
                    >
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Stat Cards (new design) -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
                <!-- Total Bookings -->
                <div class="card">
                    <div class="card-left bg-gradient-to-br from-blue-600 to-blue-500">
                        <CalendarCheck class="icon" />
                    </div>
                    <div class="card-right">
                        <div class="card-value">{{ totalBookings }}</div>
                        <div class="card-label">Total Bookings</div>
                    </div>
                </div>

                <!-- Total Services -->
                <div class="card">
                    <div class="card-left bg-gradient-to-br from-green-500 to-emerald-400">
                        <Wrench class="icon" />
                    </div>
                    <div class="card-right">
                        <div class="card-value">{{ totalServices }}</div>
                        <div class="card-label">Total Services</div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="card">
                    <div class="card-left bg-gradient-to-br from-yellow-500 to-amber-400">
                        <Users class="icon" />
                    </div>
                    <div class="card-right">
                        <div class="card-value">{{ totalCustomers }}</div>
                        <div class="card-label">Total Customers</div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Bar Chart -->
                <div class="rounded-xl bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-semibold text-gray-700">Bookings Over Time (Completed)</h2>
                    <div v-if="barData.datasets[0].data.length">
                        <Bar :data="barData" :options="barOptions" />
                    </div>
                    <div v-else class="py-16 text-center text-gray-400">No data available</div>
                </div>

                <!-- Pie Chart -->
                <div class="rounded-xl bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-semibold text-gray-700">Booking Status Overview</h2>
                    <div v-if="pieData.datasets[0].data.some((d) => d > 0)">
                        <Pie :data="pieData" :options="pieOptions" />
                    </div>
                    <div class="py-16 text-center text-gray-400">No data available</div>
                </div>
            </div>

            <!-- Top Services -->
            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-semibold text-gray-700">Top Services</h2>

                <div v-if="topServices.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
                    <div
                        v-for="service in topServices"
                        :key="service.name"
                        class="rounded-xl bg-gray-50 p-4 text-center shadow transition-shadow hover:shadow-lg"
                    >
                        <span class="text-xl font-bold text-gray-800">{{ service.name }}</span>
                        <p class="mt-1 text-gray-500">Bookings: {{ service.count }}</p>
                    </div>
                </div>

                <div v-else class="py-16 text-center text-gray-400">No top services yet</div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

import { ArcElement, BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { CalendarCheck, Users, Wrench } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Bar, Pie } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const totalBookings = ref(0);
const totalServices = ref(0);
const totalCustomers = ref(0);
const topServices = ref([]);

const barData = ref({
    labels: [],
    datasets: [{ label: 'Completed Bookings', backgroundColor: '#2563eb', data: [] }],
});

const pieData = ref({
    labels: ['Completed', 'Pending', 'Canceled'],
    datasets: [{ backgroundColor: ['#2563eb', '#facc15', '#ef4444'], data: [0, 0, 0] }],
});

const barOptions = { responsive: true, plugins: { legend: { display: false } } };
const pieOptions = { responsive: true, plugins: { legend: { position: 'bottom' } } };

const selectedRange = ref(1);

const fetchDashboardData = async (range = 1) => {
    try {
        const { data } = await axios.get(`/admin/dashboard/data?range=${range}`);

        if (data.result) {
            totalBookings.value = data.totalBookings ?? 0;
            totalServices.value = data.totalServices ?? 0;
            totalCustomers.value = data.totalCustomers ?? 0;

            topServices.value = data.topServices ?? [];

            barData.value = {
                labels: data.labels || [],
                datasets: [
                    {
                        label: 'Completed Bookings',
                        backgroundColor: '#2563eb',
                        data: data.data || [],
                    },
                ],
            };

            pieData.value = {
                labels: ['Completed', 'Pending', 'Canceled'],
                datasets: [
                    {
                        backgroundColor: ['#2563eb', '#facc15', '#ef4444'],
                        data: [data.pieChart.completed ?? 0, data.pieChart.pending ?? 0, data.pieChart.canceled ?? 0],
                    },
                ],
            };
        }
    } catch (err) {
        console.error(err);
    }
};

const loadDashboard = () => {
    fetchDashboardData(selectedRange.value);
};

onMounted(() => fetchDashboardData(selectedRange.value));

/* -------------------
  REPORT GENERATION
--------------------*/
const generateReport = async () => {
    try {
        const { data } = await axios.get('/admin/dashboard/generateReport');

        if (!data.result) return;

        const bookings = data.bookings || [];
        const popularService = data.popularService || null;

        const generatedDate = new Date().toLocaleString();

        let reportHtml = `
        <html>
        <head>
            <title>CM Motorparts - Booking Report</title>
            <style>
                body { font-family: Arial; padding: 24px; }
                h1 { margin:0; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th, td { padding: 8px; border: 1px solid #ccc; font-size: 12px; }
                th { background: #111; color:white; }
                tr:nth-child(even) { background: #f9f9f9; }
                .highlight { background: #fef3c7; }
            </style>
        </head>
        <body>

            <h1>CM Motorparts</h1>
            <p>Generated: ${generatedDate}</p>

            <h2>Most Popular Service</h2>
        `;

        if (popularService) {
            reportHtml += `<p><strong>${popularService.name}</strong> — ${popularService.count} bookings</p>`;
        } else {
            reportHtml += `<p>No available data.</p>`;
        }

        reportHtml += `<h2>Bookings (Last 6 Months)</h2>`;

        if (bookings.length) {
            reportHtml += `
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

            bookings.forEach((b, i) => {
                reportHtml += `
                    <tr class="${b.status === 'Completed' ? 'highlight' : ''}">
                        <td>${i + 1}</td>
                        <td>${b.customer_name}</td>
                        <td>${b.customer_email}</td>
                        <td>${b.service_name}</td>
                        <td>${b.status}</td>
                        <td>${b.scheduled_datetime}</td>
                        <td>₱${b.total_amount}</td>
                    </tr>`;
            });

            reportHtml += `</tbody></table>`;
        } else {
            reportHtml += `<p>No bookings available.</p>`;
        }

        reportHtml += `</body></html>`;

        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.left = '-9999px';
        document.body.appendChild(iframe);

        iframe.contentDocument.open();
        iframe.contentDocument.write(reportHtml);
        iframe.contentDocument.close();

        iframe.onload = () => {
            iframe.contentWindow.print();
            iframe.contentWindow.onafterprint = () => document.body.removeChild(iframe);
        };
    } catch (err) {
        console.error('Report generation failed', err);
    }
};
</script>

<style scoped>
/* Card layout */
.card {
    display: flex;
    gap: 16px;
    align-items: center;
    background: white;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 6px 18px rgba(14, 30, 37, 0.06);
    transition:
        transform 0.12s ease,
        box-shadow 0.12s ease;
    min-height: 92px;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(14, 30, 37, 0.1);
}

.card-left {
    min-width: 64px;
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: white;
    flex-shrink: 0;
}

.card-right {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.card .icon {
    width: 28px;
    height: 28px;
}

/* Value and label */
.card-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a; /* slate-900 */
}

.card-label {
    font-size: 0.875rem;
    color: #6b7280; /* gray-500 */
    margin-top: 2px;
}

/* keep charts responsive */
@media (max-width: 768px) {
    .card {
        min-height: 84px;
        gap: 12px;
        padding: 10px;
    }
    .card-left {
        min-width: 56px;
        min-height: 56px;
        border-radius: 10px;
    }
    .card-value {
        font-size: 1.25rem;
    }
}

/* Print stylesheet small tweak */
@media print {
    body * {
        visibility: hidden;
    }
    #print-section,
    #print-section * {
        visibility: visible;
    }
}
</style>

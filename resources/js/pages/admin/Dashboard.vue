<template>
  <Head title="Dashboard" />
  <AdminLayout>
    <div class="flex flex-col gap-6 p-6">

      <!-- Generate Report Button -->
      <div class="flex justify-end">
        <button 
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-transform hover:scale-105"
          @click="generateReport"
        >
          Generate Report
        </button>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 shadow-lg rounded-xl p-6 flex flex-col items-center hover:scale-105 transition-transform">
          <CalendarCheck class="h-10 w-10 mb-3 text-white drop-shadow" />
          <span class="text-3xl font-extrabold text-white drop-shadow">{{ totalBookings }}</span>
          <span class="text-white/80 font-medium mt-1">Total Bookings</span>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-400 shadow-lg rounded-xl p-6 flex flex-col items-center hover:scale-105 transition-transform">
          <Wrench class="h-10 w-10 mb-3 text-white drop-shadow" />
          <span class="text-3xl font-extrabold text-white drop-shadow">{{ totalServices }}</span>
          <span class="text-white/80 font-medium mt-1">Total Services</span>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-400 shadow-lg rounded-xl p-6 flex flex-col items-center hover:scale-105 transition-transform">
          <Users class="h-10 w-10 mb-3 text-white drop-shadow" />
          <span class="text-3xl font-extrabold text-white drop-shadow">{{ totalCustomers }}</span>
          <span class="text-white/80 font-medium mt-1">Total Customers</span>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-400 shadow-lg rounded-xl p-6 flex flex-col items-center hover:scale-105 transition-transform">
          <Star class="h-10 w-10 mb-3 text-white drop-shadow" />
          <span class="text-3xl font-extrabold text-white drop-shadow">Settings</span>
          <span class="text-white/80 font-medium mt-1">Configure Platform</span>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Bar Chart -->
        <div class="bg-white shadow rounded-xl p-6 flex flex-col">
          <h2 class="text-lg font-semibold mb-4 text-gray-700">Bookings Over Time (Completed)</h2>
          <div v-if="barData.datasets[0].data.length && barData.labels.length">
            <Bar :data="barData" :options="barOptions" />
          </div>
          <div v-else class="text-gray-400 text-center py-16">No data available</div>
        </div>

        <!-- Pie Chart -->
        <div class="bg-white shadow rounded-xl p-6 flex flex-col">
          <h2 class="text-lg font-semibold mb-4 text-gray-700">Booking Status Overview</h2>
          <div v-if="pieData.datasets[0].data.some(d => d > 0)">
            <Pie :data="pieData" :options="pieOptions" />
          </div>
          <div v-else class="text-gray-400 text-center py-16">No data available</div>
        </div>
      </div>

      <!-- Top Services -->
      <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Top Services</h2>
        <div v-if="topServices.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <div v-for="service in topServices" :key="service.name" class="bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl p-4 flex flex-col items-center text-center hover:shadow-lg transition-shadow">
            <span class="text-xl font-bold text-gray-800">{{ service.name }}</span>
            <span class="text-gray-500 mt-1">Bookings: {{ service.count }}</span>
          </div>
        </div>
        <div v-else class="text-gray-400 py-16 text-center">No top services yet</div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { Bar, Pie } from 'vue-chartjs';
import axios from 'axios';
import { CalendarCheck, Wrench, Users, Star } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
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

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const totalBookings = ref(0);
const totalServices = ref(0);
const totalCustomers = ref(0);
const topServices = ref<{ name: string; count: number }[]>([]);

const barData = ref({ labels: [] as string[], datasets: [{ label: 'Completed Bookings', backgroundColor: '#2563eb', data: [] as number[] }] });
const pieData = ref({ labels: ['Completed','Pending','Canceled'], datasets: [{ backgroundColor: ['#2563eb','#facc15','#ef4444'], data: [0,0,0] }] });

const barOptions = { responsive: true, plugins: { legend: { display: false } } };
const pieOptions = { responsive: true, plugins: { legend: { position: 'bottom' as const } } };

const fetchDashboardData = async () => {
  try {
    const { data } = await axios.get('/admin/dashboard/data');
    if (data.result) {
      totalBookings.value = data.totalBookings ?? 0;
      totalServices.value = data.totalServices ?? 0;
      totalCustomers.value = data.totalCustomers ?? 0;
      topServices.value = data.topServices ?? [];
      barData.value = { labels: data.barChart.labels || [], datasets: [{ label: 'Completed Bookings', backgroundColor: '#2563eb', data: data.barChart.data || [] }] };
      pieData.value = { labels: ['Completed','Pending','Canceled'], datasets: [{ backgroundColor: ['#2563eb','#facc15','#ef4444'], data: [data.pieChart.completed ?? 0, data.pieChart.pending ?? 0, data.pieChart.canceled ?? 0] }] };
    }
  } catch (err) { console.error(err); }
};

onMounted(() => fetchDashboardData());

const reportData = ref<any>(null);
const printing = ref(false);
const generateReport = async () => {
  try {
    const { data } = await axios.get('/admin/dashboard/generateReport');

    if (data.result === true) {
      const bookings = data.bookings || [];
      const popularService = data.popularService || null;
      const generatedDate = new Date().toLocaleString();

      // Report HTML
      let reportHtml = `
        <html>
          <head>
            <title>CM Motorparts - Booking Report</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 24px; color: #1f2937; }
              .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #facc15; padding-bottom: 12px; margin-bottom: 24px; }
              .header img { height: 60px; }
              .header h1 { font-size: 28px; margin: 0; color: #111827; }
              .subtitle { font-size: 14px; color: #6b7280; margin-top: 4px; }
              h2 { margin-top: 28px; color: #2563eb; font-size: 18px; border-left: 4px solid #facc15; padding-left: 8px; }
              table { width: 100%; border-collapse: collapse; margin-top: 15px; }
              th, td { border: 1px solid #d1d5db; padding: 8px; font-size: 12px; text-align: left; }
              th { background: #1f2937; color: white; }
              tr:nth-child(even) { background: #f9fafb; }
              .highlight { background: #fef3c7; }
              .footer { margin-top: 40px; font-size: 11px; text-align: center; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
            </style>
          </head>
          <body>
            <div class="header">
              <div>
                <h1>CM Motorparts</h1>
                <div class="subtitle">Generated Report - ${generatedDate}</div>
              </div>
              <img src="/images/logo_cm_motors.png" alt="CM Motorparts Logo" />
            </div>

            <h2>Most Popular Service</h2>
      `;

      if (popularService) {
        reportHtml += `
          <p><strong>${popularService.name}</strong> — ${popularService.count} bookings</p>
        `;
      } else {
        reportHtml += `<p>No popular service data available.</p>`;
      }

      reportHtml += `<h2>Bookings (Last 6 Months)</h2>`;

      if (bookings.length) {
        reportHtml += `
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Customer Email</th>
                <th>Service</th>
                <th>Status</th>
                <th>Scheduled</th>
                <th>Total Amount</th>
              </tr>
            </thead>
            <tbody>
        `;

        bookings.forEach((b: any, i: number) => {
          reportHtml += `
            <tr class="${b.status === 'Completed' ? 'highlight' : ''}">
              <td>${i + 1}</td>
              <td>${b.customer_name}</td>
              <td>${b.customer_email}</td>
              <td>${b.service_name}</td>
              <td>${b.status}</td>
              <td>${b.scheduled_datetime}</td>
              <td>₱${b.total_amount}</td>
            </tr>
          `;
        });

        reportHtml += `</tbody></table>`;
      } else {
        reportHtml += `<p>No bookings available for this period.</p>`;
      }

      reportHtml += `
          <div class="footer">
            &copy; ${new Date().getFullYear()} CM Motorparts. All rights reserved.
          </div>
        </body>
        </html>
      `;

      // Create hidden iframe
      const iframe = document.createElement('iframe');
      iframe.style.position = 'absolute';
      iframe.style.top = '-9999px';
      iframe.style.left = '-9999px';
      document.body.appendChild(iframe);

      // Write content
      iframe.contentDocument.open();
      iframe.contentDocument.write(reportHtml);
      iframe.contentDocument.close();

      // Print when ready
      iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        iframe.contentWindow.onafterprint = () => {
          document.body.removeChild(iframe);
        };
      };
    }
  } catch (error) {
    console.error('Generate report failed', error);
  }
};


</script>

<style>
/* Ensure only the report prints */
@media print {
  body * {
    display: none !important;
    visibility: hidden !important;
  }

  #print-section {
    display: block !important;
    visibility: visible !important;
    position: static !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  #print-section * {
    display: initial !important;
    visibility: visible !important;
  }

  @page {
    margin: 20mm;
  }
}
</style>

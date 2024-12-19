<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import StaffAuthenticatedLayout from '@/Layouts/StaffAuthenticatedLayout.vue';
import { Chart, registerables } from 'chart.js';
//import { PieChart, LineChart } from 'vue-chart-3';
//import { CalendarIcon, ClipboardListIcon, UserGroupIcon, BellIcon } from '@heroicons/vue/outline';

Chart.register(...registerables);

const recentActivity = ref([
  { id: 1, action: 'Completed order #1234', time: '2 hours ago' },
  { id: 2, action: 'Updated inventory for Product A', time: '4 hours ago' },
  { id: 3, action: 'Resolved customer inquiry #5678', time: 'Yesterday' },
]);

const upcomingTasks = ref([
  { id: 1, task: 'Team meeting', time: 'Today, 2:00 PM' },
  { id: 2, task: 'Inventory check', time: 'Tomorrow, 10:00 AM' },
  { id: 3, task: 'Monthly report due', time: 'Friday, 5:00 PM' },
]);

const teamMembers = ref([
  { id: 1, name: 'John Doe', status: 'online' },
  { id: 2, name: 'Jane Smith', status: 'busy' },
  { id: 3, name: 'Mike Johnson', status: 'offline' },
]);

const pieChartData = {
  labels: ['Completed', 'In Progress', 'Pending'],
  datasets: [
    {
      backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
      data: [30, 50, 20],
    },
  ],
};

const lineChartData = {
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [
    {
      label: 'Orders',
      backgroundColor: '#3B82F6',
      data: [65, 59, 80, 81, 56, 55, 40],
    },
  ],
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

onMounted(() => {
  // Any necessary setup or data fetching
});
</script>

<template>
  <Head title="Staff Dashboard" />

  <StaffAuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Staff Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
              <div class="bg-indigo-100 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-indigo-800 mb-2">Total Orders</h3>
                <p class="text-3xl font-bold text-indigo-600">152</p>
              </div>
              <div class="bg-green-100 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Completed Orders</h3>
                <p class="text-3xl font-bold text-green-600">89</p>
              </div>
              <div class="bg-yellow-100 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Pending Orders</h3>
                <p class="text-3xl font-bold text-yellow-600">43</p>
              </div>
              <div class="bg-red-100 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Cancelled Orders</h3>
                <p class="text-3xl font-bold text-red-600">20</p>
              </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Left Column -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                  <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                      New Order
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      Update Inventory
                    </button>
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                      Customer Support
                    </button>
                    <button class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                      Generate Report
                    </button>
                  </div>
                </div>

                <!-- Performance Charts -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                  <h3 class="text-xl font-semibold mb-4">Performance Overview</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h4 class="text-lg font-medium mb-2">Order Status</h4>
                      <PieChart :chart-data="pieChartData" :options="chartOptions" />
                    </div>
                    <div>
                      <h4 class="text-lg font-medium mb-2">Weekly Orders</h4>
                      <LineChart :chart-data="lineChartData" :options="chartOptions" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Recent Activity -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                  <h3 class="text-xl font-semibold mb-4">Recent Activity</h3>
                  <ul class="space-y-3">
                    <li v-for="activity in recentActivity" :key="activity.id" class="flex items-center space-x-3">
                      <ClipboardListIcon class="h-5 w-5 text-gray-400" />
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ activity.action }}</p>
                        <p class="text-xs text-gray-500">{{ activity.time }}</p>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Upcoming Tasks -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                  <h3 class="text-xl font-semibold mb-4">Upcoming Tasks</h3>
                  <ul class="space-y-3">
                    <li v-for="task in upcomingTasks" :key="task.id" class="flex items-center space-x-3">
                      <CalendarIcon class="h-5 w-5 text-gray-400" />
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ task.task }}</p>
                        <p class="text-xs text-gray-500">{{ task.time }}</p>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Team Members -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                  <h3 class="text-xl font-semibold mb-4">Team Members</h3>
                  <ul class="space-y-3">
                    <li v-for="member in teamMembers" :key="member.id" class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <UserGroupIcon class="h-5 w-5 text-gray-400" />
                        <span class="text-sm font-medium text-gray-900">{{ member.name }}</span>
                      </div>
                      <span :class="{
                        'bg-green-100 text-green-800': member.status === 'online',
                        'bg-yellow-100 text-yellow-800': member.status === 'busy',
                        'bg-gray-100 text-gray-800': member.status === 'offline'
                      }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ member.status }}
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Announcements -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-xl font-semibold mb-4">Announcements</h3>
              <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <BellIcon class="h-5 w-5 text-yellow-400" />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      Important: New product launch meeting scheduled for next week. Please check your email for details.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StaffAuthenticatedLayout>
</template>

<style scoped>
/* Add any additional component-specific styles here */
</style>
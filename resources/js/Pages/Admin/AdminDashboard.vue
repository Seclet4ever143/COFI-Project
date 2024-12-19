<template>

  <Head title="COFI - ADMIN DASHBOARD"/>
  <AdminAuthenticatedLayout>
    <div class="py-12 bg-slate-700 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Activity Logs</h1>
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
              <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3 px-6">ID</th>
                    <th scope="col" class="py-3 px-6">Current User</th>
                    <th scope="col" class="py-3 px-6">User ID</th>
                    <th scope="col" class="py-3 px-6">Action</th>
                    <th scope="col" class="py-3 px-6">Tablename</th>
                    <th scope="col" class="py-3 px-6">Timestamp</th>
                    <th scope="col" class="py-3 px-6">Details</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="log in logs" :key="log.id" class="bg-white border-b hover:bg-gray-50">
                    <td class="py-4 px-6">{{ log.id }}</td>
                    <td class="py-4 px-6">{{ log.current_users}}</td>
                    <td class="py-4 px-6">{{ log.user_id}}</td>
                    <td class="py-4 px-6">{{ log.action_type }}</td>
                    <td class="py-4 px-6">{{ log.table_name }}</td>
                    <td class="py-4 px-6">{{ formatDate(log.created_at) }}</td>
                    <td class="py-4 px-6">
                      <button @click="openDetails(log.id)" class="font-medium text-blue-600 hover:underline flex items-center">
                        <EyeIcon class="h-5 w-5 mr-1" />
                        View Details
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Simple Modal -->
      <div v-if="selectedLog" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" @click="closeDetails">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
          <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Log Details</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500"><strong>ID:</strong> {{ selectedLog.id }}</p>
              <p class="text-sm text-gray-500"><strong>User:</strong> {{ selectedLog.current_users }}</p>
              <p class="text-sm text-gray-500"><strong>Action:</strong> {{ selectedLog.action_type }}</p>
              <p class="text-sm text-gray-500"><strong>Table:</strong> {{ selectedLog.table_name }}</p>
              <p class="text-sm text-gray-500"><strong>Timestamp:</strong> {{ formatDate(selectedLog.created_at) }}</p>
            </div>
            <div class="items-center px-4 py-3">
              <button
                @click="closeDetails"
                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </AdminAuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import { EyeIcon } from '@heroicons/vue/24/outline';
  import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
  import { Head } from '@inertiajs/inertia-vue3';
  
  // State management
  const logs = ref([]);
  const selectedLog = ref(null);
  const form = useForm({ id: null });
  
  // Fetch all activity logs
  const fetchLogs = async () => {
    try {
      const response = await axios.get('/activity-logs');
      console.log(response.data);
      logs.value = response.data;
    } catch (error) {
      console.error('Error fetching logs:', error);
    }
  };
  
  // Fetch and display specific log details
  const openDetails = async (id) => {
    form.id = id;
    try {
      const response = await axios.get(`/activity-logs/${id}`);
      selectedLog.value = response.data;
    } catch (error) {
      console.error('Error fetching log details:', error);
      alert('Failed to fetch log details.');
    }
  };
  
  // Close log details modal
  const closeDetails = () => {
    selectedLog.value = null;
  };
  
  // Format date
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
  };
  
  // Fetch logs on component mount
  onMounted(() => {
    fetchLogs();
  });
  </script>
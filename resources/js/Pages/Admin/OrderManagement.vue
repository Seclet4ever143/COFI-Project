<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
const orders = ref([
    { id: 1, customer: 'John Doe', total: 120.50, status: 'Pending' },
    { id: 2, customer: 'Jane Smith', total: 75.00, status: 'Completed' },
    { id: 3, customer: 'Mark Brown', total: 200.00, status: 'Processing' },
]);
</script>

<template>
    
    <Head title="Order Management" />

    <AdminAuthenticatedLayout>
    <div class="p-6 bg-white shadow rounded-lg">
        <h2 class="text-2xl font-semibold mb-4">Order Management</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Order ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Customer
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Total
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders" :key="order.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ order.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ order.customer }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ order.total.toFixed(2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ order.status }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</AdminAuthenticatedLayout>
</template>

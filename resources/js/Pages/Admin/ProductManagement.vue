<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, defineEmits } from 'vue';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import axios from 'axios';

const emit = defineEmits();

const products = ref([]);
const showModal = ref(false);
const form = ref({ id: null, name: '', price: '', qty: '', category: '' });
const isEditing = ref(false);
const successMessage = ref('');

// Categories list
const categories = ['COFFEE', 'DESSERT', 'BEVERAGE', 'FOOD'];

// Fetch products from the server
const fetchProducts = async () => {
    try {
        const response = await axios.get(route('products.index'));
        products.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

// Open modal for adding a product
const openAddModal = () => {
    isEditing.value = false;
    form.value = { id: null, name: '', price: '', qty: '', category: '' };
    showModal.value = true;
};

// Open modal for editing a product
const openEditModal = (product) => {
    isEditing.value = true;
    form.value = { ...product };
    showModal.value = true;
};

// Close the modal
const closeModal = () => {
    showModal.value = false;
};

// Handle form submission for adding/editing
const saveProduct = async () => {
    try {
        if (isEditing.value) {
            const response = await axios.put(
                route('products.update', { product: form.value.id }), // Pass the product ID correctly
                form.value // Send the updated product data
            );
            successMessage.value = response.data.message || 'Product updated successfully!';
        } else {
            const response = await axios.post(route('products.store'), form.value);
            successMessage.value = response.data.message || 'Product added successfully!';
        }
        emit('productUpdated');
        showModal.value = false;
        fetchProducts();
    } catch (error) {
        console.error(error);
        alert('An error occurred while saving the product.');
    }
};



// Delete a product
const deleteProduct = async (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        try {
            await axios.delete(route('products.destroy', id));
            successMessage.value = 'Product deleted successfully!';
            emit('productUpdated');
            fetchProducts();
        } catch (error) {
            console.error(error);
        }
    }
};

// Fetch products on component mount
onMounted(fetchProducts);
</script>

<template>
    <Head title="Product Management" />
    <AdminAuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Product Management</h1>

            <!-- Success Message -->
            <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                {{ successMessage }}
            </div>

            <!-- Add Product Button -->
            <button @click="openAddModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md mb-4">
                Add Product
            </button>

            <!-- Product Table -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4">{{ product.name }}</td>
                            <td class="px-6 py-4">{{ product.category }}</td>
                            <td class="px-6 py-4">P {{ product.price }}</td>
                            <td class="px-6 py-4">{{ product.qty }}</td>
                            <td class="px-6 py-4">
                                <button @click="openEditModal(product)" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal for Add/Edit Product -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-2xl font-bold mb-4">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h2>
                    <form @submit.prevent="saveProduct">
                        <div class="mb-4">
                            <label class="block text-gray-700">Name</label>
                            <input v-model="form.name" type="text" class="w-full p-2 border rounded" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Category</label>
                            <select v-model="form.category" class="w-full p-2 border rounded" required>
                                <option disabled value="">Select a category</option>
                                <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Price</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full p-2 border rounded" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Quantity</label>
                            <input v-model="form.qty" type="number" class="w-full p-2 border rounded" required />
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="closeModal" class="mr-3 text-gray-600">Cancel</button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                {{ isEditing ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>

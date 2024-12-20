<script setup>
import { reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminAuthenticatedLayout from '@/Layouts/AdminAuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import { PlusIcon } from '@heroicons/vue/24/outline';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { toRaw } from 'vue';
import { Inertia } from '@inertiajs/inertia';

// Remove one of these declarations
const rawFormData = {
    name: "",
    email: "",
    phone: "",
    password: "",
    confirmPassword: "",
    role_id: "",
};
// Define editForm as a reactive object
const editForm = reactive({
    id: null,
    name: '',
    email: '',
    phone: '',
    role_id: '',
    reset() {
        this.id = null;
        this.name = '';
        this.email = '';
        this.phone = '';
        this.role_id = '';
    },
});



const form = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    role_id: '3', // Default to "Customer"
});

// Success message state
const successMessage = ref('');
const errorMessage = ref('');
const showModal = ref(false); // Controls modal visibility


// Store users fetched from the backend
const users = reactive({
    admin: [],
    staff: [],
    customer: [],
});

// Modal state
const showEditModal = ref(false);
editForm.reset = function () {
    this.id = null;
    this.name = '';
    this.email = '';
    this.phone = '';
    this.role_id = '';
};


// Modal state for delete confirmation
const showDeleteModal = ref(false);
const deleteUserId = ref(null);  // Store the user ID to be deleted

// Function to create an account


const createAccount = async () => {
    if (form.value.password !== form.value.confirmPassword) {
        errorMessage.value = 'Passwords do not match!';
        showModal.value = false;
        return;
    }

    if (form.value.phone.length !== 11) {
        errorMessage.value = 'Invalid Phone number, Should not exceed 11 words.';
        showModal.value = false;
        return;
    }

    if (!form.value.phone.startsWith('0')) {
        errorMessage.value = 'Invalid Phone number.';
        showModal.value = false;
        return;
    }

    const strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;


    if (!strongPasswordPattern.test(form.value.password)) {
        errorMessage.value = 'Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.';
        showModal.value = false;
        return;
    }

    try {
        const rawFormData = toRaw(form.value);
        await axios.post(route('account.store'), rawFormData);
        form.value = {
            name: '',
            email: '',
            phone: '',
            password: '',
            confirmPassword: '',
            role_id: '3',
        };
        successMessage.value = 'Account created successfully!';
        errorMessage.value = null;
        showModal.value = false;
        fetchUsers();
    } catch (error) {
        const { errors } = error.response?.data || {};
        errorMessage.value = errors?.email?.[0] || errors?.phone?.[0] || 'An unexpected error occurred.';
        showModal.value = false;
        return;
    }
};


// Fetch users grouped by role
const fetchUsers = async () => {
    try {
        const response = await axios.get(route('users.grouped'), rawFormData);
        console.log(response.data); // Log the response to check its structure
        users.admin = response.data.admin || [];
        users.staff = response.data.staff || [];
        users.customer = response.data.customer || [];
    } catch (error) {
        console.error("Error fetching users:", error);
    }
};

// Fetch users on component mount
fetchUsers();

// Open Edit Modal
const openEditModal = (user) => {
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.phone = user.phone;
    editForm.role_id = user.role_id;  // Ensure role_id is also updated
    showEditModal.value = true;
};


// Update User
const updateUser = async () => {
    if (!editForm.name || !editForm.email || !editForm.phone || !editForm.role_id) {
        errorMessage.value = 'Please fill all fields.';
        return;
    }

    try {
        await axios.put(route('account.update', editForm.id), toRaw(editForm));
        successMessage.value = 'Account updated successfully!';
        fetchUsers();
        cancelEdit();
    } catch (error) {
        errorMessage.value = 'Failed to update user.';
    }
};




// Close Edit Modal
const cancelEdit = () => {
    showEditModal.value = false;
    editForm.reset();
};

// Open Delete Modal and set user ID
const confirmDeleteUser = (userId) => {
    deleteUserId.value = userId;  // Store the ID of the user to delete
    showDeleteModal.value = true;  // Show the delete confirmation modal
};


// Function to delete the user
const deleteUser1 = async () => {
    try {
        // Send delete request
        axios.delete(route('user.delete', deleteUserId.value));

        successMessage.value = 'User deleted successfully!';
        fetchUsers();  // Refresh the users list
        showDeleteModal.value = false;  // Close the delete modal
    } catch (error) {
        console.error('Error deleting user:', error.response);
        errorMessage.value = 'Failed to delete user.';
    }
};


// const createAccount = () => {
//     if (form.value.password !== form.value.confirmPassword) {
//         alert('Passwords do not match!');
//         return;
//     }
//     // Handle form submission here
//     console.log('Account created:', form.value);
//     // Reset form and close modal
//     form.value = { name: '', email: '', phone: '', password: '', confirmPassword: '', role_id: '3' };
//     showModal.value = false;
// };

// const deleteUser = () => {

//     console.log(users.id);


//     axios.delete(`Admin/Account/${users.id}`)
//         .then(() => {
//             console.log('User deleted');
//         })
//         .catch(error => {
//             console.error('Error:', error);
//         });
// };
const showDeleteModal1 = ref(false); // State for showing the delete modal
const deleteUserId1 = ref(null);

const confirmDeleteUser1 = (userId) => {
    deleteUserId.value = userId; // Store the user ID to delete
    showDeleteModal.value = true; // Show the modal
};

// const deleteUser = () => {
//     Inertia.delete(`/Admin/Account/delete/${deleteUserId.value}`, {

//         preserveScroll: true, // Optionally preserve the scroll position
//         onSuccess: () => {
//             successMessage.value = 'User deleted successfully!';
//             fetchUsers();  // Refresh the users list
//             showDeleteModal.value = false; // Close the delete modal on success
//         },
//     });
// };


</script>

<template>

    <Head title="Account Management" />

    <AdminAuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">


            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-5 text-center ">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
                    Account Management
                </span>
            </h1>

            <!-- Success Message -->
            <div v-if="successMessage"
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                {{ successMessage }}
            </div>

            <!-- General error message -->
            <div v-if="errorMessage"
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
                {{ errorMessage }}
            </div>

            <!-- Modal Trigger Button -->
            <!-- <button @click="showModal = true"
                class="bg-indigo-600 mb-10 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                Create Account
            </button> -->
            <button @click="showModal = true"
                class="group mb-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <PlusIcon
                    class="h-5 w-5 mr-2 transition-transform duration-300 ease-in-out transform group-hover:rotate-180" />
                Create Account
            </button>

            <!-- Display Users by Role -->
            <div class="grid grid-cols-1 gap-6 mb-10">
                <div v-for="(roleUsers, role) in users" :key="role"
                    class="bg-slate-400 p-4 rounded-lg shadow-md overflow-x-auto">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 capitalize">{{ role }}s</h2>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="bg-slate-300">
                                <th scope="col"
                                    class=" px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No.</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phone</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-200 divide-y divide-gray-200">
                            <tr v-for="(user, index) in roleUsers" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name
                                    }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="openEditModal(user)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-2">Update</button>
                                    <button @click="confirmDeleteUser1(user.id)"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div v-if="showModal"
                class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-auto relative">
                    <div class="absolute top-4 right-4">
                        <button @click="showModal = false"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                            <XMarkIcon class="h-6 w-6" />
                            <span class="sr-only">Close</span>
                        </button>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Create an Account</h2>

                    <form @submit.prevent="createAccount" class="space-y-6">
                        <div class="space-y-2">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input id="name" type="text" v-model="form.name"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white"
                                required />
                        </div>

                        <div class="space-y-2">
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input id="email" type="email" v-model="form.email"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white"
                                required />
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                                Number</label>
                            <input id="phone" type="tel" v-model="form.phone"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white"
                                required maxlength="11" minlength="11" pattern="\d{11}"
                                @input="form.phone = form.phone.replace(/\D/g, '')" />
                        </div>

                        <div class="space-y-2">
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input id="password" type="password" v-model="form.password"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white"
                                required />
                        </div>

                        <div class="space-y-2">
                            <label for="confirmPassword"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm
                                Password</label>
                            <input id="confirmPassword" type="password" v-model="form.confirmPassword"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white"
                                required />
                        </div>

                        <div class="space-y-2">
                            <label for="role"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select id="role" v-model="form.role_id"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                                <option value="2">Staff</option>
                                <option value="1">Admin</option>
                                <option value="3">Customer</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Account
                        </button>
                    </form>
                </div>
            </div>


            <!-- Edit User Modal -->
            <div v-if="showEditModal"
                class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-75 z-50 p-4">
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-2xl font-semibold text-gray-800">Edit Account</h3>
                        <button @click="cancelEdit" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Modal with form inside -->
                    <form @submit.prevent="updateUser" v-if="showEditModal" class="px-6 py-4 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input v-model="editForm.name" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="editForm.email" type="email"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input v-model="editForm.phone" type="tel"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select v-model="editForm.role_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Admin</option>
                                <option value="2">Staff</option>
                                <option value="3">Customer</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button @click="cancelEdit" type="button"
                                class="px-4 py-2 text-gray-500 border rounded-md hover:bg-gray-100 transition duration-150 ease-in-out">Cancel</button>
                            <PrimaryButton type="submit"
                                class=" hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                                Update User
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal"
                class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-75 z-50 p-4">
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-2xl font-semibold text-gray-800">Confirm Deletion</h3>
                        <button @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-4 text-gray-700">
                        <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                    </div>
                    <div class="flex justify-end space-x-3 p-4">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-500 border rounded-md hover:bg-gray-100 transition duration-150 ease-in-out">Cancel</button>
                        <PrimaryButton @click="deleteUser1" class="bg-red-600 hover:bg-red-700">Delete</PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminAuthenticatedLayout>
</template>

<style scoped>
/* Styles for modal */
.fixed {
    z-index: 50;
}
</style>

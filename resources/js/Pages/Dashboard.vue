<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />
    

    <AuthenticatedLayout>
        <template #header>
            
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>
    
        <div class="py-12 bg-green-600" >
            <div class="snowfall"></div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
.snowfall {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
    background-image: 
      radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.8) 1px, transparent 1px),
      radial-gradient(circle at 25% 75%, rgba(255, 255, 255, 0.8) 1px, transparent 1px),
      radial-gradient(circle at 75% 25%, rgba(255, 255, 255, 0.8) 1px, transparent 1px);
    background-size: 100px 100px;
    animation: snowfall 10s linear infinite;
  }
  
  @keyframes snowfall {
    0% {
      background-position: 0 0, 0 0, 0 0;
    }
    100% {
      background-position: 100px 100px, 50px 50px, -50px -50px;
    }
  }
</style>
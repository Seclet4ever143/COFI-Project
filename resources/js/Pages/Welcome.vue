<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const products = [
    {
        name: 'DARK HOT CHOCOLATE',
        description: 'Bold, smooth, and luxuriously rich',
        image: '/images/dark-hot-chocolate.png'
    },
    {
        name: 'PEPPERMINT COCOA',
        description: 'Rich, creamy cocoa with a refreshing peppermint twist',
        image: '/images/peppermint-cocoa.png'
    },
    {
        name: 'BISCOFF CREAM',
        description: 'A creamy coffee delight infused with the warm, spiced sweetness of Biscoff',
        image: '/images/biscoff-cream.png'
    }
];
</script>

<template>

    <Head title="COFI - Carry the Merry" />

    <div class="min-h-screen bg-[#2D5522] text-white overflow-hidden">
        <!-- Navigation -->
        <div class="w-full px-4 sm:px-6">
            <header class="flex flex-wrap items-center justify-between py-6">
                <!-- Logo -->
                <div class="text-2xl sm:text-3xl font-bold ml-10">
                    COFI
                </div>

                <!-- Main Navigation -->
                <nav class="hidden md:block">
                    <ul class="flex space-x-8">
                        <li><a href="#" class="hover:text-gray-200">MENU</a></li>
                        <li><a href="#" class="hover:text-gray-200">CONTACT US</a></li>
                    </ul>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 rounded-md hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>

                <!-- Auth Navigation -->
                <nav v-if="canLogin"
                    class="w-full md:w-auto mt-4 md:mt-0 flex flex-wrap items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-0 md:space-x-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="w-full md:w-auto rounded-md px-4 py-2 text-white ring-1 ring-white/20 transition hover:bg-white/10 text-center">
                    Dashboard
                    </Link>

                    <template v-else>
                        <Link :href="route('login')"
                            class="w-full md:w-auto rounded-md px-4 py-2 text-white ring-1 ring-white/20 transition hover:bg-white/10 text-center">
                        Log in
                        </Link>

                        <Link v-if="canRegister" :href="route('register')"
                            class="w-full md:w-auto rounded-md px-4 py-2 text-white ring-1 ring-white/20 transition hover:bg-white/10 text-center">
                        Register
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Hero Section -->
            <div class="relative flex flex-col md:flex-row min-h-[400px] items-center mt-8 md:mt-0">
                <!-- Text Content -->
                <div class="z-10 px-4 sm:px-6 md:ml-16 max-w-xl text-center md:text-left mb-12 md:mb-0">
                    <h1 class="mb-4 text-4xl sm:text-5xl md:text-6xl font-bold leading-tight tracking-tighter">
                        CARRY<br />
                        the <span class="font-serif italic">MERRY</span>
                    </h1>

                    <Link :href="route('login')"
                        class="mt-5 rounded-full border-2 border-white px-6 py-2 sm:px-8 sm:py-3 font-semibold uppercase tracking-wider transition hover:bg-white hover:text-[#2D5522]">
                    Order Now
                    </Link>


                </div>

                <!-- Product Display -->
                <div
                    class="w-full md:w-2/3 flex flex-col md:flex-row items-center justify-center md:justify-end space-y-8 md:space-y-0 md:space-x-4 px-4 sm:px-6 md:pr-6">
                    <div v-for="(product, index) in products" :key="product.name"
                        class="text-center transform w-full sm:w-2/3 md:w-1/3 max-w-xs" :class="{
                            'md:translate-y-16': index === 0,
                            'md:-translate-y-16': index === 2
                        }">
                        <div class="relative mb-2">
                            <img :src="product.image" :alt="product.name"
                                class="h-48 sm:h-64 w-auto object-contain mx-auto" />
                            <span
                                class="absolute top-0 left-1/2 md:left-0 transform -translate-x-1/2 md:translate-x-0 inline-block rounded-full bg-yellow-400 px-2 py-1 text-xs font-semibold text-[#2D5522]">NEW!</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold">{{ product.name }}</h3>
                        <p class="mt-1 text-sm text-white/80">{{ product.description }}</p>
                    </div>
                </div>

                <!-- Promotional Text -->
                <div
                    class="absolute left-1/2 md:left-auto md:right-1/3 top-1/2 transform -translate-x-1/2 md:translate-x-0 -translate-y-1/2 rotate-[-15deg] mt-8 md:mt-0">
                    <h2 class="text-3xl md:text-4xl font-bold text-yellow-400 drop-shadow-lg whitespace-nowrap">UP TO
                        50% OFF!!</h2>
                </div>

                <!-- Decorative Elements -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute left-1/4 top-1/4 h-4 w-4 rotate-45 bg-white/10"></div>
                    <div class="absolute right-1/3 top-1/2 h-2 w-2 rounded-full bg-white/20"></div>
                    <div class="absolute bottom-1/4 left-1/2 h-3 w-3 rotate-12 bg-white/15"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&display=swap');

.font-serif {
    font-family: 'Playfair Display', serif;
}
</style>

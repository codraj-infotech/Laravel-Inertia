<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Welcome to Laravel with Inertia.js
            </h1>
            <p class="text-gray-600 mb-6">
                You're now running Laravel {{ laravelVersion }} with Inertia.js and Vue 3!
            </p>
            <div class="space-y-2">
                <p class="text-sm text-gray-500">
                    <strong>Laravel Version:</strong> {{ laravelVersion }}
                </p>
                <p class="text-sm text-gray-500">
                    <strong>PHP Version:</strong> {{ phpVersion }}
                </p>
            </div>
            <div class="mt-6">
                <button
                    @click="counter++"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                >
                    Clicked {{ counter }} times
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    laravelVersion: String,
    phpVersion: String,
});

const counter = ref(0);
</script>

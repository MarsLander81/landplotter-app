<template>
    <Head title="Welcome | Land Plotter" />
    
    <div class="space-y-16">
        <!-- Hero Section -->
        <section class="text-center py-20">
            <h1 class="text-5xl font-bold text-slate-900 dark:text-white mb-6">
                Welcome to Land Plotter
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-300 mb-8 max-w-2xl mx-auto">
                Visualize and calculate land coordinates with precision. Plot bearings and distances to map out your survey data with ease.
            </p>
            <div class="flex gap-4 justify-center">
                <Link 
                    href="/plotter"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
                >
                    Get Started
                </Link>
                <Link 
                    href="#features"
                    class="px-8 py-3 border-2 border-slate-300 hover:border-slate-400 text-slate-900 dark:text-white font-semibold rounded-lg transition"
                >
                    Learn More
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-12">
            <h2 class="text-3xl font-bold text-center text-slate-900 dark:text-white mb-12">
                Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-slate-700 p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                    <div class="text-4xl mb-4">📍</div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Precise Plotting</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        Plot coordinates with high precision using bearings and distances. Ideal for land surveying and mapping.
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-700 p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Visual Representation</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        See your data visualized on an interactive canvas. Easy to understand and share with stakeholders.
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-700 p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                    <div class="text-4xl mb-4">⚡</div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">Fast & Reliable</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        Process large datasets quickly and accurately. Built for reliability and consistent performance.
                    </p>
                </div>
            </div>
        </section>

        <!-- Plotter Form Section -->
        <section id="plotter" class="py-12">
            <h2 class="text-3xl font-bold text-center text-slate-900 dark:text-white mb-12">
            Start Plotting
            </h2>
            <div class="max-w-4xl mx-auto bg-white dark:bg-slate-700 p-8 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="md:col-span-3">
                <label for="input_lotTitle" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Lot Title
                </label>
                <input 
                    type="text" 
                    name="input_lotTitle" 
                    id="input_lotTitle"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
                <div class="md:col-span-6">
                <label for="input_lotAddress" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Lot Address
                </label>
                <input 
                    type="text" 
                    name="input_lotAddress" 
                    id="input_lotAddress"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                <label for="input_Tieocation" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Tie Point
                </label>
                <select 
                    id="input_Tieocation" 
                    name="input_Tieocation"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600"
                ></select>
                </div>
                <div>
                <label for="input_TieCity" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    City
                </label>
                <select 
                    id="input_TieCity" 
                    name="input_TieCity" 
                    disabled
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-slate-100 dark:bg-slate-500 text-slate-900 dark:text-white opacity-50 cursor-not-allowed focus:outline-none"
                ></select>
                </div>
                <div>
                <label for="input_TiePOR" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    POR
                </label>
                <select 
                    id="input_TiePOR" 
                    name="input_TiePOR" 
                    disabled
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-slate-100 dark:bg-slate-500 text-slate-900 dark:text-white opacity-50 cursor-not-allowed focus:outline-none"
                ></select>
                </div>
            </div>
            </div>
        </section>

        
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

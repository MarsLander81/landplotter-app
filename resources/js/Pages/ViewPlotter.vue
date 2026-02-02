<template>
    <Head title="Land Plotter" />
    <div>
        <section class="text-center py-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
                Plotter Details
            </h1>
        </section>
        <!-- Plotter Form Section -->
        <section id="plotter-details" class="grid grid-cols-2 py-6">
            <pre>{{ props.plotRenderData }}</pre>
            <!-- Canvas -->
            <!-- Summary/Editor-->
        </section>
    </div>
</template>
<script setup>
import { ref } from 'vue';
const props = defineProps({
    plotRenderData: {
        type: [Object, Array],
        required: true,
    }
})
</script>
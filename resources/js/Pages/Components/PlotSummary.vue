<template>
    <div class="w-full">
        <pre>{{ props.renderData }}</pre>
        <!-- Header with location and save button -->
        <div class="bg-slate-600 text-white p-4 rounded-t-lg flex justify-between items-center">
            <div class="flex-1">
                <h3 class="text-lg font-semibold">LOCATION:</h3>
                <p class="text-sm">{{ props.renderData.lotAddress }}</p>
            </div>
            <div class="flex gap-2">
                <select v-model="selectedTiePoint" 
                    class="px-3 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option v-for="(lot, i) in props.renderData.lotItem" :key="i" :value="i">
                        Lot No: {{ i + 1 }}
                    </option>
                </select>
                <button @click="$emit('save')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
                    title="Save points">
                    SAVE
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border border-t-0 border-slate-300 dark:border-slate-600">
            <table class="w-full border-collapse">
                <thead class="bg-slate-600 text-white">
                    <tr>
                        <th class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-left font-semibold w-1/4">LINE</th>
                        <th class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-left font-semibold w-1/4">BEARING</th>
                        <th class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-left font-semibold w-1/2">DISTANCE</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-700">
                    <!-- Loop through each lot -->
                    <template v-for="(plot, plotIdx) in props.renderData.lotItem" :key="`lot-${plotIdx}`">
                        <!-- Tie point row -->
                        <tr class="bg-slate-100 dark:bg-slate-600">
                            <th class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white font-semibold">TIE POINT:</th>
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                {{ plot.tieLabel.Bearing }}
                            </td>
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                {{ plot.tieLabel.Distance }}m
                            </td>
                        </tr>

                        <!-- Data rows for each point in the lot -->
                        <tr v-for="(point, pointIdx) in props.renderData.lotItem[plotIdx].points" :key="`point-${plotIdx}-${pointIdx}`"
                            :id="`${plotIdx}-${pointIdx}`"
                            class="hover:bg-slate-50 dark:hover:bg-slate-600">
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                {{ point.pointLabel?.Line }}
                            </td>
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                {{ point.pointLabel?.Bearing }}
                            </td>
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                {{ point.pointLabel?.Distance }}m
                            </td>
                        </tr>

                        <!-- Error row if distance error exists -->
                        <tr v-if="plot.marginDistance && plot.marginDistance.distance > 0."
                            :id="`${plotIdx}-${plot.length}-E`"
                            class="bg-red-100 dark:bg-red-900 text-red-900 dark:text-red-100"
                            title="Missing or incorrect point.">
                            <td class="border border-red-300 dark:border-red-700 px-4 py-3">
                                <strong>Line margin</strong>
                            </td>
                            <td class="border border-red-300 dark:border-red-700 px-4 py-3">
                                {{ plot.marginDistance.pointLabel?.Bearing }}
                            </td>
                            <td class="border border-red-300 dark:border-red-700 px-4 py-3">
                                {{ plot.marginDistance.pointLabel?.Distance.toFixed(2) }}m
                            </td>
                        </tr>

                        <!-- Perimeter and area footer row -->
                        <tr class="bg-slate-100 dark:bg-slate-600 font-semibold">
                            <td colspan="2" class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white"
                                :class="`lot-per-${plotIdx}`">
                                PERIMETER: {{ calculatePerimeter(plot.points, plot.marginDistance?.pointLabel?.Distance) }}m
                            </td>
                            <td class="border border-slate-300 dark:border-slate-600 px-4 py-3 text-slate-900 dark:text-white">
                                Retrieving data...
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    renderData: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['save']);

const selectedTiePoint = ref(0);

// Calculate perimeter for a lot by summing distances
const calculatePerimeter = (lot, margin) => {
    let perimeter = 0;
    lot.forEach(point => {
        if (point.pointLabel?.Distance) {
            perimeter += point.pointLabel.Distance;
        }
    });
    if (margin) {
        perimeter += margin;
    }
    return perimeter.toFixed(2);
};
</script>
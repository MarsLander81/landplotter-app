<template>
    <div class="grid grid-cols-6">
        <select id="input_pointDir" v-model="objToEdit.direction"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="N">North</option>
            <option value="S">South</option>
        </select>
        <input type="number" id="input_pointDeg" min="0" max="360" v-model="objToEdit.degree"
            placeholder="Degree"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
        <input type="number" id="input_pointMin" min="0" max="360" v-model="objToEdit.minutes"
            placeholder="Minutes"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
        <select id="input_pointBer" v-model="objToEdit.bearing"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="E">East</option>
            <option value="W">West</option>
        </select>
        <input type="number" id="input_pointDist" min="0" v-model="objToEdit.distance" placeholder="Distance"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>
</template>
<script setup>
import { ref } from 'vue';
const props = defineProps({
    objToEdit: Object,
});

let objArrUpdate = ref(buildFields());

function populateFields(point) {
    objArrUpdate.value = buildFields(objToEdit);
};

function buildFields(objToEdit = '') {
    return {
        direction: objToEdit.direction || '',
        degree: objToEdit.degree || 0,
        minutes: objToEdit.minutes || 0,
        bearing: objToEdit.bearing || '',
        distance: objToEdit.distance || 0
    }
}

</script>
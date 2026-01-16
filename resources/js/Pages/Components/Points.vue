<template>
    <div class="grid grid-cols-6 gap-4 mt-4 p-4">
        <div v-for="point in plotObj.points" class="border border-slate-300 dark:border-slate-500 rounded-lg p-4">
            <div @click="populatePoint(point)">
                <p class="float-start">P:<i>{{ point.direction }} {{
                    point.degree }}° {{ point.minutes }}' {{ point.bearing }} {{ point.distance
                        }}m</i></p>
                <div class="float-end">
                    <button :disabled="pointMinLimit(plotObj.points.length)"
                        @click="$emit('removePoint', plotObj.id, point.id)">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-6">
        <select id="input_pointDir" v-model="pointArrUpdate.direction"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="N">North</option>
            <option value="S">South</option>
        </select>
        <input type="number" id="input_pointDeg" min="0" max="360" v-model="pointArrUpdate.degree"
            placeholder="Degree"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
        <input type="number" id="input_pointMin" min="0" max="360" v-model="pointArrUpdate.minutes"
            placeholder="Minutes"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
        <select id="input_pointBer" v-model="pointArrUpdate.bearing"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="E">East</option>
            <option value="W">West</option>
        </select>
        <input type="number" id="input_pointDist" min="0" v-model="pointArrUpdate.distance" placeholder="Distance"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
        <button @click="editPoint(pointArrUpdate)">Save</button>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import * as Mapper from '../../mapper.js';
const props = defineProps({
    pointMinLimit: Function,
    plotObj: Object
})
const emit = defineEmits(['removePoint', 'editPoint']);

let plotToUpdate = ref('');
let pointToUpdate = ref('');
let pointArrUpdate = ref(buildPoints());

function buildPoints(pointArr = '') {
    return {
        direction: pointArr.direction || '',
        degree: pointArr.degree || 0,
        minutes: pointArr.minutes || 0,
        bearing: pointArr.bearing || '',
        distance: pointArr.distance || 0
    }
}

function populatePoint(point) {
    plotToUpdate.value = props.plotObj.id;
    pointToUpdate.value = point.id;
    pointArrUpdate.value = buildPoints(point);
    console.log(pointArrUpdate.value);
};

function editPoint(pointArrUpdate) {
    const updatedPoint = {
        id: pointToUpdate.value,
        direction: pointArrUpdate.direction,
        degree: pointArrUpdate.degree,
        minutes: pointArrUpdate.minutes,
        bearing: pointArrUpdate.bearing,
        distance: pointArrUpdate.distance
    };
    emit('editPoint', plotToUpdate.value, pointToUpdate.value, updatedPoint);
    pointArrUpdate = buildPoints();
}

</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style src="../../css/multiselect.css"></style>
<template>

    <Head title="Welcome | Land Plotter" />
    <div>
        <!-- Hero Section -->
        <section class="text-center py-20">
            <h1 class="text-5xl font-bold text-slate-900 dark:text-white mb-6">
                Welcome to Land Plotter
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-300 mb-8 max-w-2xl mx-auto">
                Visualize and calculate land coordinates with precision. Plot bearings and distances to map out your
                survey data with ease.
            </p>
        </section>

        <!-- Plotter Form Section -->
        <section id="plotter" class="py-12">
            <h2 class="text-3xl font-bold text-center text-slate-900 dark:text-white mb-12">
                Start Plotting
            </h2>
            <div
                class="max-w-full mx-auto bg-white dark:bg-slate-700 p-6 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="md:col-span-3">
                        <input type="text" id="input_lotTitle" v-model="lotTitle" placeholder="Lot title"
                            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <h2 class="block font-semibold text-slate-900 dark:text-white mt-3 mb-3">
                    Tie Point
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <multiselect id="input_Tielocation" v-model="tieLoc" placeholder="Select location"
                            :options="tieLocOpts" :disabled="fieldStates.tieLoc" :show-labels="false"></multiselect>
                    </div>
                    <div>
                        <multiselect id="input_Tiecity" v-model="tieCity" placeholder="Select location"
                            :options="tieCityOpts" :disabled="fieldStates.tieCity" :show-labels="false"></multiselect>
                    </div>
                    <div>
                        <multiselect id="input_Tiepor" v-model="tiePOR" placeholder="Select location"
                            :options="tiePOROpts" :disabled="fieldStates.tiePOR" :show-labels="false"></multiselect>
                    </div>
                </div>
                <h2 class="block font-semibold text-slate-900 dark:text-white mt-3 mb-3">
                    Coordinates
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <input type="text" id="input_TieLatitude" v-model="tieLatitude" placeholder="Latitude"
                            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <input type="text" id="input_TieLongitude" v-model="tieLongitude" placeholder="Longitude"
                            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 justify-center mt-6 mb-4">
                    <Button @click="addNewPlot()" :disabled="plotMaxLimit(lotitems.length)">Add Plot</Button>
                    <Button @click="generatePreview()">Preview Layout</Button>
                </div>

                <div v-for="plot in lotitems" :key="plot.id"
                    class="bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-500 rounded-lg p-4 m-6 ">
                    <Plots :plot="plot" :plot-min-limit="plotMinLimit" :toggle-object="toggleObject"
                        :plot-length="lotitems.length" v-on:deletePlot="deletePlot" v-on:collapsePoints="collapsePoints"
                        @click="selectObject(plot.tie, plot.id)" />
                    <hr class="my-2" />
                    <div>
                        <Button @click="addNewPoint(plot.id)" :disabled="pointMaxLimit(plot.points.length)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M9 11.5A2.5 2.5 0 0 0 11.5 9A2.5 2.5 0 0 0 9 6.5A2.5 2.5 0 0 0 6.5 9A2.5 2.5 0 0 0 9 11.5M9 2c3.86 0 7 3.13 7 7c0 5.25-7 13-7 13S2 14.25 2 9a7 7 0 0 1 7-7m6 15h3v-3h2v3h3v2h-3v3h-2v-3h-3z" />
                            </svg>
                        </Button>
                        <div class="flex flex-wrap m-4">
                            <div v-for="point in plot.points" :key="point.id"
                                :class="[toggleObject(point.id, 'point') ? 'bg-blue-400 border border-blue-700' : 'bg-slate-300 hover:bg-slate-400 border border-slate-300']"
                                class="rounded-4xl m-1 p-1">
                                <Points :plot-id="plot.id" :point-obj="point" :point-min-limit="pointMinLimit"
                                    :point-length="plot.points.length" v-on:removePoint="deletePoint"
                                    @click="selectObject(point, plot.id, point.id)" />
                            </div>
                        </div>
                    </div>
                </div>
                <FieldEditor v-show="selPlotId" :obj-to-edit="objToEdit" class="mt-4 mb-4" />
            </div>
            <pre class="text-white">
                {{ lotitems }}
            </pre>
        </section>
    </div>
</template>


<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Multiselect from 'vue-multiselect';
import Button from './Components/Button.vue';
import Plots from './Components/Plots.vue';
import Points from './Components/Points.vue';
import FieldEditor from './Components/FieldEditor.vue';
import * as Mapper from '../mapper.js';

let plotMaxLimit = (len) => len >= 6;
let plotMinLimit = (len) => len <= 1;
let pointMaxLimit = (len) => len >= 24;
let pointMinLimit = (len) => len <= 3;

let lotTitle = ref('');
let tieLoc = ref('');
let tieCity = ref('');
let tiePOR = ref('');
let tieLatitude = ref('');
let tieLongitude = ref('');

let tieLocOpts = Mapper.tieLocationList;
let tieCityOpts = [];
let tiePOROpts = [];

let lotitems = ref([]);
let objToEdit = ref({});
let selPlotId = ref('');
let selPointId = ref('');

defineEmits(['removePoint']);

const fieldStates = computed(() => {
    return {
        tieLoc: tieLocOpts.length === 0,
        tieCity: tieCityOpts.length === 0,
        tiePOR: tiePOROpts.length === 0
    };
});

const disabledClasses = computed(() => {
    return 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-500';
});

const collapsePoints = (id) => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleObject = (id, type) =>{
    return type === 'plot' ? selPlotId.value === id && !selPointId.value : selPointId.value === id
}

const selectObject = (obj, plid, poid = null) => {
    objToEdit.value = obj;
    selPlotId.value = plid;
    selPointId.value = poid;
    console.log('selected ' + plid + ' ' + poid);
};

const addNewPlot = () => {
    const newPlot = Mapper.createPlotItem(`New Plot`);
    for (let i = 0; i < 4; i++) {
        const newPoint = Mapper.createPlotPoint();
        newPlot.points.push(newPoint);
    }
    lotitems.value.push(newPlot);
};

const deletePlot = (plotId) => {
    lotitems.value = lotitems.value.filter(p => p.id !== plotId);
};

const addNewPoint = (plotId) => {
    console.log('add point' + plotId)
    const plot = lotitems.value.find(p => p.id === plotId);
    if (plot) {
        const newPoint = Mapper.createPlotPoint();
        plot.points.push(newPoint);
    }
};

const deletePoint = (plotId, pointId) => {
    const plot = lotitems.value.find(p => p.id === plotId);
    if (plot) {
        //console.log('delete point' + JSON.stringify(plot));
        plot.points = plot.points.filter(p => p.id !== pointId);
    }
};

const saveObjAdjustments = (plotId, pointId, updatedObj) => {
    console.log('save adjustments ' + plotId + ' ' + pointId + ' ' + JSON.stringify(updatedObj));
    /*
    const plot = lotitems.value.find(p => p.id === plotId);
    console.log(plotId);
    if (plot) {
        const pointIndex = plot.points.findIndex(p => p.id === pointId);
        console.log('save point' + pointIndex);
        if (pointIndex !== -1) {
            plot.points[pointIndex] = updatedObj;
        }
    }*/
};

addNewPlot();
</script>
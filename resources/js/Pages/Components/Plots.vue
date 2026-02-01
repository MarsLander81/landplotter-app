<template>
    <div class="grid grid-cols-2 items-center mt-2 mb-4">
        <div class="flex text-xl rounded-4xl font-medium text-slate-900 items-center">
            <div>
                <b v-show="!plot?.editing" @dblclick="setPlotEditing(plot)">{{ plot.plotname }}</b>
                <input :key="plot.id" type="text" :id="plot.id" v-model="plot.plotname"
                @blur="setPlotEditing(plot)"
                v-show="plot?.editing"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-500 rounded-lg bg-white dark:bg-slate-600 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            <div class="mx-4 p-2 rounded-2xl flex items-center"
                @click="$emit('selectPlot', plot.tie, plot.id)"
                :class="[toggleObject(plot.id, 'plot') ? 'bg-blue-400 border border-blue-700' : 'bg-slate-300 hover:bg-slate-400 border border-slate-300']">
                <svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-700" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path
                        d="m15 19l-6-2.11V5l6 2.11M20.5 3h-.16L15 5.1L9 3L3.36 4.9c-.21.07-.36.25-.36.48V20.5a.5.5 0 0 0 .5.5c.05 0 .11 0 .16-.03L9 18.9l6 2.1l5.64-1.9c.21-.1.36-.25.36-.48V3.5a.5.5 0 0 0-.5-.5" />
                </svg>{{ plot.tie.direction }} {{
                    plot.tie.degree }}° {{ plot.tie.minutes }}' {{ plot.tie.bearing }} {{ plot.tie.distance
                }}m
            </div><i class="text-lg">Points #{{ plot.points.length }}</i>
        </div>
        <div>
            <Button @click="$emit('collapsePoints')" class="bg-transparent! p-0! float-end">
                <svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-600 hover:fill-slate-900" width="36"
                    height="36" viewBox="0 0 24 24">
                    <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6l-6 6z" />
                </svg>
            </Button>
            <Button class="float-end" @click="$emit('deletePlot', plot.id)" :disabled="plotMinLimit(plotLength)"><svg
                    xmlns="http://www.w3.org/2000/svg" class="fill-slate-50" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6z" />
                </svg></Button>
        </div>
    </div>
</template>
<script setup>
import { nextTick, ref } from 'vue';
import Button from './Button.vue';

const props = defineProps({
    plotMinLimit: Function,
    toggleObject: Function,
    plot: Object,
    plotLength: Number,
})

const titleEditing = ref(false);
const setPlotEditing = async (plot, state) => {
  plot.editing = (typeof state === "boolean") ? state : !plot.editing;
  if (plot.editing){
    await nextTick();
    document.getElementById(plot.id).focus();
  }
};
</script>
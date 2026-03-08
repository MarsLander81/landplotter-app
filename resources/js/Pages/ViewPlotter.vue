<template>
    <Head title="Land Plotter" />
    <div>
        <section class="text-center py-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
                {{ props.plotRenderData?.plotName || 'Plot Details' }}
            </h1>
        </section>
        <!-- Plotter Form Section -->
        <section id="plotter-details" class="grid grid-cols-2 py-6">
            <!-- Summary/Editor-->
             <div>
                <PlotSummary :render-data="props.plotRenderData"></PlotSummary>
             </div>
            <!-- Canvas -->
            <div id='cnv_div' class="border border-gray-500">
                <Canvas :render-data="renderedPlotItems" :canvas-data="canvas"></Canvas>
            </div>
        </section>
    </div>
</template>
<script setup>
import { computed, ref } from 'vue';
import * as Mapper from '../mapper.js';
import Canvas from './Components/Canvas.vue';
import PlotSummary from './Components/PlotSummary.vue';
const props = defineProps({
    plotRenderData: {
        type: [Object, Array],
        required: true,
    }
})
let canvas = {width:600, height:600}

// Gather all points and endpoints as first items for extrapolation
const allPointsWithEndpoints = props.plotRenderData.lotItem.flatMap(lot => {
    let arr = [];
    if (lot.endPoint) {
        arr.push({ latitude: lot.endPoint.latitude, longitude: lot.endPoint.longitude });
    }
    return arr.concat(lot.points);
});

const convertData = Mapper.extrapolatePoints(allPointsWithEndpoints, canvas);

const renderedPlotItems = computed(() => {
    if (!props.plotRenderData?.lotItem?.length) return [];

    return props.plotRenderData.lotItem.map(plot => {
        let points = plot.points.map(pt => {
            const { x, y } = convertData(pt.latitude, pt.longitude);
            return { ...pt, canvasX: x, canvasY: y };
        });
        // Add endPoint as the first item if it exists
        if (plot.endPoint) {
            const { x, y } = convertData(plot.endPoint.latitude, plot.endPoint.longitude);
            points = [{ pointId:"PPT-tie", pointNumber:0,...plot.endPoint, canvasX: x, canvasY: y }, ...points];
        }
        return { ...plot, points };
    });
});

</script>
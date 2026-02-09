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
            <pre>{{ renderedPlotItems }}</pre>
            <!-- Canvas -->
            <!-- Summary/Editor-->
        </section>
    </div>
</template>
<script setup>
import { computed, ref } from 'vue';
import * as Mapper from '../mapper.js';
const props = defineProps({
    plotRenderData: {
        type: [Object, Array],
        required: true,
    }
})
let canvas = {width:400, height:400}

const convertData = Mapper.extrapolatePoints(props.plotRenderData.lotItem.flatMap(lot => lot.points), canvas);
const renderedPlotItems = computed(() => {
    if (!props.plotRenderData?.lotItem?.length) return [];

    return props.plotRenderData.lotItem.map(plot => ({
        ...plot,
        points: plot.points.map(pt => {
            const { x, y } = convertData(pt.latitude, pt.longitude);
            return { ...pt, canvasX: x, canvasY: y };
        })
    }))
})

</script>
<template>
    <div class="grid grid-cols-6 gap-4 mt-4 p-4">
        <div v-for="point in plotObj.points" class="border border-slate-300 dark:border-slate-500 rounded-lg p-4">
            <p class="float-start">Point : <i>{{ point.direction }} {{
                point.degree }}° {{ point.minutes }}' {{ point.bearing }} {{ point.distance
                    }}m</i></p>
            <button 
            v-if="plotObj.points.length > 3"
            @click="$emit('removePoint', plotObj.id, point.id)">Delete</button>
        </div>
    </div>

</template>
<script setup>
import { ref } from 'vue';
import * as Mapper from '../../mapper.js';
defineProps({
    plotObj: Object
})


</script>
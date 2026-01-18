<template>
        <div class="flex gap-1 items-center"><svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-700" width="24" height="24"
                viewBox="0 0 24 24">
                <path
                    d="M8.17 2.76A10.1 10.1 0 0 1 12 2c1.31 0 2.61.26 3.83.76c1.21.5 2.31 1.24 3.24 2.17s1.67 2.03 2.17 3.24c.5 1.22.76 2.52.76 3.83c0 2.65-1.05 5.2-2.93 7.07A9.97 9.97 0 0 1 12 22a10.1 10.1 0 0 1-3.83-.76a10 10 0 0 1-3.24-2.17A9.97 9.97 0 0 1 2 12c0-2.65 1.05-5.2 2.93-7.07c.93-.93 2.03-1.67 3.24-2.17M12 17l1.56-3.42L17 12l-3.44-1.56L12 7l-1.57 3.44L7 12l3.43 1.58z" />
            </svg><i>{{ pointObj.direction }} {{
                pointObj.degree }}° {{ pointObj.minutes }}' {{ pointObj.bearing }} {{ pointObj.distance
                }}m</i>

            <Button class="p-0! bg-transparent! hover:bg-slate-900! rounded-4xl!" :disabled="pointMinLimit(pointLength)"
                @click="$emit('removePoint', plotId, pointObj.id)">
                <svg xmlns="http://www.w3.org/2000/svg" class="fill-slate-900 hover:fill-slate-50" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path
                        d="M20 6.91L17.09 4L12 9.09L6.91 4L4 6.91L9.09 12L4 17.09L6.91 20L12 14.91L17.09 20L20 17.09L14.91 12z" />
                </svg>
            </Button>
        </div>
</template>
<script setup>
import { ref } from 'vue';
import * as Mapper from '../../mapper.js';
import Button from './Button.vue';
const props = defineProps({
    pointMinLimit: Function,
    plotId: String,
    pointObj: Object,
    pointLength: Number
})
</script>
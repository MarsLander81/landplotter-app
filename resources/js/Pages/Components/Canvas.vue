<template>
    <v-stage :config="stageConfig">
        <v-layer :config="stageDisplayLayer">
            <v-group>
                <v-line :config="plotPolygonLines"></v-line>
                <v-circle :config="plotPolygonDots"></v-circle>
            </v-group>
        </v-layer>
    </v-stage>
</template>
<script setup>
    import { Stage as VStage, Layer as VLayer, Group as VGroup, Line as VLine, Circle as VCircle, } from 'vue-konva';
    const stageConfig = {
        width: stageWidth,
        height: stageHeight,
        draggable: false,
    };
    const stageDisplayLayer = {};
    const plotPolygonLines = {};
    const plotPolygonDots = {}
</script>
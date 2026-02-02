<template>
    <v-stage :config="stageConfig">
        <v-layer :config="stageDisplayLayer">
            <v-group>
                <v-line v-for="lineObj in plotLineArr" :config="lineObj"></v-line>
                <v-circle  v-for="dotObj in dotPointArr" :config="dotObj"></v-circle>
                <v-text-path v-for="textObj in textLblArr" :config="textObj"></v-text-path>
            </v-group>
        </v-layer>
    </v-stage>
</template>
<script setup>
    import { Stage as VStage, Layer as VLayer, Group as VGroup, Line as VLine, Circle as VCircle, TextPath as VTextPath} from 'vue-konva';
    const stageConfig = {
        width: stageWidth,
        height: stageHeight,
        draggable: false,
    };
    
</script>
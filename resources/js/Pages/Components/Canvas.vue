<template>
    <v-stage :config="stageConfig">
        <v-layer>
            <v-group>
                <v-line :config="areaObjConfig"></v-line>
                <v-line v-for="line in plotLineList" :config="line"></v-line>
                <v-circle v-for="dot in plotDotList" :config="dot"></v-circle>
                <v-text-path v-for="text in plotTextList" :config="text"></v-text-path>
            </v-group>
        </v-layer>
    </v-stage>
</template>
<script setup>
import { Stage as VStage, Layer as VLayer, Group as VGroup, Line as VLine, Circle as VCircle, TextPath as VTextPath } from 'vue-konva';
const props = defineProps({
    renderData: {
        type: [Object, Array],
        required: true,
    },
    canvasData: {
        type: Object,
        required: true,
    }
});
const stageWidth = props.canvasData.width;
const stageHeight = props.canvasData.height;
const pointList = buildPointList(props.renderData);
const stageConfig = {
    width: stageWidth,
    height: stageHeight,
    draggable: false,
};
const areaObjConfig = {
    points: pointList,
    fill: '#cccccc',
    stroke: 'black',
    strokeWidth: 0,
    closed: true,
    id: 'lot1'
};
const plotLineList = buildLineList(props.renderData);
const plotDotList = buildDotList(props.renderData);
const plotTextList = buildTextList(props.renderData);

function buildPointList(data) {
    if (!data?.length) return [];
    const points = [];
    data.forEach(plot => {
        plot.points.forEach(pt => {
            points.push(pt.canvasX, pt.canvasY);
        });
    });
    return points;
}

function buildDotList(data) {
    if (!data?.length) return [];
    const dots = [];
    data.forEach(plot => {
        plot.points.forEach(pt => {
            dots.push({
                x: pt.canvasX,
                y: pt.canvasY,
                radius: 3,
                fill: 'black',
                stroke: 'black',
                id: `dot-${plot.id}-${pt.id}`
            });
        });
    });
    return dots;
}

function buildLineList(data) {
    if (!data?.length) return [];
    const lines = [];
    data.forEach(plot => {
        for (let i = 0; i < plot.points.length - 1; i++) {
            const pt1 = plot.points[i];
            const pt2 = plot.points[i + 1];
            lines.push({
                points: [pt1.canvasX, pt1.canvasY, pt2.canvasX, pt2.canvasY],
                stroke: '#333',
                strokeWidth: 2,
                id: `line-${i + 1}`
            });
        }
        if (plot.marginDistance.distance > 0.1) {
            const firstPt = plot.points[0];
            const lastPt = plot.points[plot.points.length - 1];
            lines.push({
                points: [lastPt.canvasX, lastPt.canvasY, firstPt.canvasX, firstPt.canvasY],
                stroke: 'red',
                strokeWidth: 2,
                dash: [8, 4],
                id: `line-margin`
            })
        }
    });
    return lines;
}

function buildTextList(data) {
    if (!data?.length) return [];

    const texts = [];

    data.forEach(plot => {
        for (let i = 0; i < plot.points.length - 1; i++) {
            const curX = Number(plot.points[i].canvasX);
            const curY = Number(plot.points[i].canvasY);
            const nextX = Number(plot.points[i + 1].canvasX);
            const nextY = Number(plot.points[i + 1].canvasY);
            const textDirX = nextX - curX;
            const textDirY = nextY - curY;
            const textLen = Math.sqrt(textDirX * textDirX + textDirY * textDirY);
            const textOffset = -8;
            const offsetX = -(textDirY / textLen) * textOffset;
            const offsetY = (textDirX / textLen) * textOffset;

            const textStartX = Math.round(curX + offsetX, 2);
            const textStartY = Math.round(curY + offsetY, 2);
            const textEndX = Math.round(nextX + offsetX, 2);
            const textEndY = Math.round(nextY + offsetY, 2);

            let textPoint = (textStartX > textEndX) ?
                `M${textEndX},${textEndY} ${textStartX},${textStartY}` :
                `M${textStartX},${textStartY} ${textEndX},${textEndY}`;

            texts.push({
                fill: '#333',
                fontSize: 11,
                fontFamily: 'Arial',
                textBaseline: 'middle',
                align: 'center',
                text: `${plot.points[i + 1].pointLabel?.Line}. ${plot.points[i + 1].pointLabel?.Bearing} ${plot.points[i + 1].pointLabel?.Distance}m`,
                data: textPoint,
                offsetX: 0,
                offsetY: 0
            });
        }
    });

    return texts;
}




</script>
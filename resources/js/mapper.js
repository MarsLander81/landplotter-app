const tieLocationList = ['Laguna', 'Cavite', 'Batangas'];
const tieCityList = ['San Pedro', 'Biñan', 'Santa Rosa'];
const tiePORList = ['Por 1', 'Por 2', 'Por 3'];

function createPlotItem(plotname, dir, deg, min, bear, dist) {
    return {
        id: 'PI-' + generateId(),
        plotname: plotname,
        tie: {
            direction: dir ?? 'N',
            degree: deg ?? 0,
            minutes: min ?? 0,
            bearing: bear ?? 'E',
            distance: dist ?? 0
        },
        points: []
    };
}

function createPlotPoint(dir, deg, min, bear, dist) {
    return {
        id: 'PPT-' + generateId(),
        direction: dir ?? 'N',
        degree: deg ?? 0,
        minutes: min ?? 0,
        bearing: bear ?? 'E',
        distance: dist ?? 0
    };
}

function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substring(2, 5);
}

function debounce(fn, delay = 300) {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

function extrapolatePoints (pointsArray, canvas, padding = 25) {
    let latitudes = pointsArray.map(pt => pt.latitude);
    let longitudes = pointsArray.map(pt => pt.longitude);

    const latMin = Math.min(...latitudes);
    const latMax = Math.max(...latitudes);
    const lonMin = Math.min(...longitudes);
    const lonMax = Math.max(...longitudes);

    const refPoint = (latMin + latMax) / 2;
    const metersPerDegreeLat = 111320;
    const metersPerDegreeLon = 111320 * Math.cos(refPoint * Math.PI / 180);

    const widthInMeters = Math.max((lonMax - lonMin) * metersPerDegreeLon, 1);
    const heightInMeters = Math.max((latMax - latMin) * metersPerDegreeLat, 1);

    const scaleX = (canvas.width - 2 * padding) / widthInMeters;
    const scaleY = (canvas.height - 2 * padding) / heightInMeters;
    const scale = Math.min(scaleX, scaleY);

    const offsetX = (canvas.width - widthInMeters * scale) / 2;
    const offsetY = (canvas.height - heightInMeters * scale) / 2;

    function latLonToCanvas(lat, lon) {
        const xCoord = (lon - lonMin) * metersPerDegreeLon * scale + offsetX;
        const yCoord = (latMax - lat) * metersPerDegreeLat * scale + offsetY;

        return { x: xCoord.toFixed(2), y: yCoord.toFixed(2) };
    }

    return latLonToCanvas;
}

export { createPlotItem, createPlotPoint, tieLocationList, tieCityList, tiePORList, debounce, extrapolatePoints };
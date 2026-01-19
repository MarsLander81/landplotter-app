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

function extrapolatePoints (pointsArray, canvas, padding) {
    let latitudes = pointsArray.map(pt => pt.latitude);
    let longitudes = pointsArray.map(pt => pt.longitude);

    let latMin = Infinity, latMax = -Infinity;
    let lonMin = Infinity, lonMax = -Infinity;

    pointsArray.forEach(pt => {
        latMin = Math.min(latMin, pt.latitude);
        latMax = Math.max(latMax, pt.latitude);
        lonMin = Math.min(lonMin, pt.longitude);
        lonMax = Math.max(lonMax, pt.longitude);
    });

    const refPoint = (latMin + latMax) / 2;
    const metersPerDegreeLat = 111320;
    const metersPerDegreeLon = 111320 * Math.cos(refPoint * Math.PI / 180);

    const widthhInMeters = (lonMax - lonMin) * metersPerDegreeLon;
    const heightInMeters = (latMax - latMin) * metersPerDegreeLat;

    const scaleX = (canvas.Width - 2 * padding) / widthhInMeters;
    const scaleY = (canvas.Height - 2 * padding) / heightInMeters;
    const scale = Math.min(scaleX, scaleY);

    const offsetX = (canvas.Width - widthhInMeters * scale) / 2;
    const offsetY = (canvas.Height - heightInMeters * scale) / 2;

    function latLonToCanvas(lat, lon) {
        const xCoord = (lon - lonMin) * metersPerDegreeLon * scale + offsetX;
        const yCoord = (latMax - lat) * metersPerDegreeLat * scale + offsetY;

        return { x: xCoord, y: yCoord };
    }

    return latLonToCanvas;
}

export { createPlotItem, createPlotPoint, tieLocationList, tieCityList, tiePORList, debounce, extrapolatePoints };
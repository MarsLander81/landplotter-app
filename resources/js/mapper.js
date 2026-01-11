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

export { extrapolatePoints };
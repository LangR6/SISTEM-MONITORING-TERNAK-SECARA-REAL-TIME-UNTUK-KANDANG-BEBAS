/**
 * Konversi lat/lng <-> posisi persen pada peta ilustrasi.
 * Cermin dari App\Support\MapProjection (PHP); batas dikirim server lewat
 * atribut data-bounds pada elemen peta agar keduanya selalu sama.
 */

const METERS_PER_DEGREE = 111320;

export function createProjection({ west, east, north, south }) {
    return {
        toLatLng(x, y) {
            return {
                lat: (north - (y / 100) * (north - south)).toFixed(6),
                lng: (west + (x / 100) * (east - west)).toFixed(6),
            };
        },

        toXY(lat, lng) {
            return {
                x: ((lng - west) / (east - west)) * 100,
                y: ((north - lat) / (north - south)) * 100,
            };
        },

        /** Luas poligon (hektar) dengan rumus shoelace pada koordinat meter. */
        areaHectares(points) {
            if (points.length < 3) return 0;

            const width = (east - west) * METERS_PER_DEGREE;
            const height = (north - south) * METERS_PER_DEGREE;
            let sum = 0;

            points.forEach((a, i) => {
                const b = points[(i + 1) % points.length];
                sum += ((a.x / 100) * width) * ((b.y / 100) * height) - ((b.x / 100) * width) * ((a.y / 100) * height);
            });

            return Math.abs(sum) / 2 / 10000;
        },
    };
}

export function centroid(points) {
    if (!points.length) return { x: 50, y: 50 };

    return {
        x: points.reduce((sum, p) => sum + p.x, 0) / points.length,
        y: points.reduce((sum, p) => sum + p.y, 0) / points.length,
    };
}

/** Posisi label area: di atas tepi teratas poligon (cermin MapProjection::labelAnchor). */
export function labelAnchor(points) {
    if (!points.length) return { x: 50, y: 50 };

    return {
        x: centroid(points).x,
        y: Math.max(Math.min(...points.map((p) => p.y)) - 7, 6),
    };
}

export function formatHectares(value) {
    return `${value.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} ha`;
}

export function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
}

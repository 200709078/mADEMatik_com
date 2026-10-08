const BOUNDARY_TOLERANCE = 1e-10;
let provinceData;

function isValidCoordinate(latitude, longitude) {
    return Number.isFinite(latitude) && Math.abs(latitude) <= 90
        && Number.isFinite(longitude) && Math.abs(longitude) <= 180;
}

function classifyPointInRing(ring, longitude, latitude) {
    let inside = false;

    for (let index = 0, previous = ring.length - 1; index < ring.length; previous = index++) {
        const [startLongitude, startLatitude] = ring[previous];
        const [endLongitude, endLatitude] = ring[index];
        const longitudeDistance = endLongitude - startLongitude;
        const latitudeDistance = endLatitude - startLatitude;
        const crossProduct = (longitude - startLongitude) * latitudeDistance
            - (latitude - startLatitude) * longitudeDistance;
        const tolerance = BOUNDARY_TOLERANCE * Math.hypot(longitudeDistance, latitudeDistance);

        if (Math.abs(crossProduct) <= tolerance
            && longitude >= Math.min(startLongitude, endLongitude) - BOUNDARY_TOLERANCE
            && longitude <= Math.max(startLongitude, endLongitude) + BOUNDARY_TOLERANCE
            && latitude >= Math.min(startLatitude, endLatitude) - BOUNDARY_TOLERANCE
            && latitude <= Math.max(startLatitude, endLatitude) + BOUNDARY_TOLERANCE) {
            return 0;
        }

        if ((startLatitude > latitude) !== (endLatitude > latitude)
            && longitude < longitudeDistance * (latitude - startLatitude) / latitudeDistance + startLongitude) {
            inside = !inside;
        }
    }

    return inside ? 1 : -1;
}

function polygonContainsPoint(rings, longitude, latitude) {
    if (classifyPointInRing(rings[0], longitude, latitude) < 0) {
        return false;
    }

    return rings.slice(1).every((ring) => classifyPointInRing(ring, longitude, latitude) !== 1);
}

export function geometryContainsPoint(geometry, longitude, latitude) {
    if (!isValidCoordinate(latitude, longitude)) {
        return false;
    }
    if (geometry.type === 'Polygon') {
        return polygonContainsPoint(geometry.coordinates, longitude, latitude);
    }
    if (geometry.type === 'MultiPolygon') {
        return geometry.coordinates.some((polygon) => polygonContainsPoint(polygon, longitude, latitude));
    }

    return false;
}

export async function getProvinceName(latitude, longitude) {
    if (!isValidCoordinate(latitude, longitude)) {
        return null;
    }

    try {
        provinceData ??= import('./turkey-provinces.json', { with: { type: 'json' } })
            .then((module) => module.default.provinces)
            .catch((error) => {
                provinceData = undefined;
                throw error;
            });
        const provinces = await provinceData;
        const province = provinces.find(({ bounds, geometry }) => longitude >= bounds[0] - BOUNDARY_TOLERANCE
            && latitude >= bounds[1] - BOUNDARY_TOLERANCE
            && longitude <= bounds[2] + BOUNDARY_TOLERANCE
            && latitude <= bounds[3] + BOUNDARY_TOLERANCE
            && geometryContainsPoint(geometry, longitude, latitude));

        return province?.name ?? null;
    } catch {
        return null;
    }
}

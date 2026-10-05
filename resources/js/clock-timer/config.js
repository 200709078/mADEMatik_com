export const DEFAULT_DURATION_MS = 5 * 60 * 1000;

// The duration form accepts up to 99 hours, 59 minutes and 59 seconds.
export const MAX_DURATION_MS = (99 * 3600 + 59 * 60 + 59) * 1000;
export const DEFAULT_LOCATION_ID = 'istanbul';
export const MIN_ZOOM = 0.6;
export const MAX_ZOOM = 3;

export const WORLD_CITIES = [
    { id: 'new-york', names: { tr: 'New York', en: 'New York' }, timeZone: 'America/New_York' },
    { id: 'london', names: { tr: 'Londra', en: 'London' }, timeZone: 'Europe/London' },
    { id: 'berlin', names: { tr: 'Berlin', en: 'Berlin' }, timeZone: 'Europe/Berlin' },
    { id: 'istanbul', names: { tr: 'İstanbul', en: 'Istanbul' }, timeZone: 'Europe/Istanbul' },
    { id: 'mecca', names: { tr: 'Mekke', en: 'Mecca' }, timeZone: 'Asia/Riyadh' },
    { id: 'beijing', names: { tr: 'Pekin', en: 'Beijing' }, timeZone: 'Asia/Shanghai' },
    { id: 'sydney', names: { tr: 'Sidney', en: 'Sydney' }, timeZone: 'Australia/Sydney' },
];

const coordinates = {
    'new-york': { latitude: 40.7128, longitude: -74.0060 },
    london: { latitude: 51.5074, longitude: -0.1278 },
    berlin: { latitude: 52.5200, longitude: 13.4050 },
    istanbul: { latitude: 41.0082, longitude: 28.9784 },
    mecca: { latitude: 21.3891, longitude: 39.8579 },
    beijing: { latitude: 39.9042, longitude: 116.4074 },
    sydney: { latitude: -33.8688, longitude: 151.2093 },
};

export const LOCATIONS = [
    { ...WORLD_CITIES.find((city) => city.id === DEFAULT_LOCATION_ID), ...coordinates.istanbul },
    { id: 'ankara', names: { tr: 'Ankara', en: 'Ankara' }, latitude: 39.9334, longitude: 32.8597, timeZone: 'Europe/Istanbul' },
    { id: 'izmir', names: { tr: 'İzmir', en: 'Izmir' }, latitude: 38.4237, longitude: 27.1428, timeZone: 'Europe/Istanbul' },
    ...WORLD_CITIES.filter((city) => city.id !== DEFAULT_LOCATION_ID).map((city) => ({ ...city, ...coordinates[city.id] })),
];

const dayMilliseconds = 86_400_000;
const radians = Math.PI / 180;
const calendarFormatters = new Map();

function getCalendarDate(date, timeZone) {
    if (!calendarFormatters.has(timeZone)) {
        calendarFormatters.set(timeZone, new Intl.DateTimeFormat('en-GB', {
            timeZone, year: 'numeric', month: '2-digit', day: '2-digit',
        }));
    }

    const parts = Object.fromEntries(calendarFormatters.get(timeZone).formatToParts(date)
        .filter((part) => part.type !== 'literal').map((part) => [part.type, Number(part.value)]));

    return Date.UTC(parts.year, parts.month - 1, parts.day);
}

function solarTerms(calendarTimestamp, solarHour = 12) {
    const date = new Date(calendarTimestamp);
    const year = date.getUTCFullYear();
    const yearStart = Date.UTC(year, 0, 1);
    const yearLength = (Date.UTC(year + 1, 0, 1) - yearStart) / dayMilliseconds;
    const dayOfYear = (calendarTimestamp - yearStart) / dayMilliseconds + 1;
    const fraction = (2 * Math.PI / yearLength) * (dayOfYear - 1 + (solarHour - 12) / 24);
    const equationOfTime = 229.18 * (0.000075 + 0.001868 * Math.cos(fraction) - 0.032077 * Math.sin(fraction)
        - 0.014615 * Math.cos(2 * fraction) - 0.040849 * Math.sin(2 * fraction));
    const declination = 0.006918 - 0.399912 * Math.cos(fraction) + 0.070257 * Math.sin(fraction)
        - 0.006758 * Math.cos(2 * fraction) + 0.000907 * Math.sin(2 * fraction)
        - 0.002697 * Math.cos(3 * fraction) + 0.00148 * Math.sin(3 * fraction);

    return { equationOfTime, declination };
}

function eventMinutes(calendarTimestamp, latitude, longitude, isSunrise, solarHour = 12) {
    const { equationOfTime, declination } = solarTerms(calendarTimestamp, solarHour);
    const latitudeRadians = latitude * radians;
    const hourAngleCosine = Math.cos(90.833 * radians) / (Math.cos(latitudeRadians) * Math.cos(declination))
        - Math.tan(latitudeRadians) * Math.tan(declination);

    if (!Number.isFinite(hourAngleCosine) || hourAngleCosine < -1 || hourAngleCosine > 1) {
        return null;
    }

    const hourAngle = Math.acos(hourAngleCosine) / radians * (isSunrise ? 1 : -1);

    return 720 - 4 * (longitude + hourAngle) - equationOfTime;
}

/**
 * Local approximate sunrise/sunset using NOAA's fractional-year solar equations.
 * Reference: https://gml.noaa.gov/grad/solcalc/solareqns.PDF
 * Zenith 90.833° accounts for the solar disc and typical atmospheric refraction.
 * Values are estimates; polar dates with no event return null.
 */
export function getSunTimes(date, location) {
    const fallback = { sunrise: null, sunset: null };

    if (!(date instanceof Date) || !Number.isFinite(date.getTime()) || !location
        || !Number.isFinite(location.latitude) || Math.abs(location.latitude) > 90
        || !Number.isFinite(location.longitude) || Math.abs(location.longitude) > 180
        || typeof location.timeZone !== 'string') {
        return fallback;
    }

    try {
        const calendarTimestamp = getCalendarDate(date, location.timeZone);
        const getEvent = (isSunrise) => {
            let minutes = eventMinutes(calendarTimestamp, location.latitude, location.longitude, isSunrise);

            if (minutes === null) {
                return null;
            }

            minutes = eventMinutes(calendarTimestamp, location.latitude, location.longitude, isSunrise,
                minutes / 60 + location.longitude / 15);

            return minutes === null ? null : new Date(calendarTimestamp + minutes * 60_000);
        };

        return { sunrise: getEvent(true), sunset: getEvent(false) };
    } catch {
        return fallback;
    }
}

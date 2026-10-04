const formatters = new Map();

function getFormatter(language, type) {
    const locale = language === 'en' ? 'en-GB' : 'tr-TR';
    const key = `${locale}:${type}`;

    if (!formatters.has(key)) {
        const options = type === 'time'
            ? { hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23' }
            : { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };

        formatters.set(key, new Intl.DateTimeFormat(locale, options));
    }

    return formatters.get(key);
}

export function formatClock(date, language = 'tr') {
    return getFormatter(language, 'time').format(date);
}

export function formatCalendar(date, language = 'tr') {
    const parts = Object.fromEntries(getFormatter(language, 'date').formatToParts(date)
        .filter((part) => part.type !== 'literal').map((part) => [part.type, part.value]));

    return `${parts.day} ${parts.month} ${parts.year}, ${parts.weekday}`;
}

export function getIsoWeek(date) {
    if (!(date instanceof Date) || !Number.isFinite(date.getTime())) {
        return null;
    }

    const day = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const weekday = day.getUTCDay() || 7;
    day.setUTCDate(day.getUTCDate() + 4 - weekday);
    const firstDay = new Date(Date.UTC(day.getUTCFullYear(), 0, 1));

    return Math.ceil(((day - firstDay) / 86_400_000 + 1) / 7);
}

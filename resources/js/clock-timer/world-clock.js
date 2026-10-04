import { WORLD_CITIES } from './config.js';

const formatters = new Map();

export function getWorldClocks(date, language = 'tr') {
    const selectedLanguage = language === 'en' ? 'en' : 'tr';

    return WORLD_CITIES.map((city) => {
        const key = `${selectedLanguage}:${city.timeZone}`;

        if (!formatters.has(key)) {
            formatters.set(key, new Intl.DateTimeFormat(selectedLanguage === 'en' ? 'en-GB' : 'tr-TR', {
                timeZone: city.timeZone,
                hour: '2-digit',
                minute: '2-digit',
                hourCycle: 'h23',
            }));
        }

        return { id: city.id, name: city.names[selectedLanguage], time: formatters.get(key).format(date), timeZone: city.timeZone };
    });
}

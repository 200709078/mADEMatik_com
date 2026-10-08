import '../../css/clock-timer.css';
import { MAX_DURATION_MS, MIN_ZOOM, MAX_ZOOM } from './config.js';
import { formatCalendar, formatClock, getIsoWeek } from './clock.js';
import { getWorldClocks } from './world-clock.js';
import { getSunTimes } from './sun.js';
import { createStorage, STORAGE_PREFIX } from './storage.js';
import { createTimerState, restoreTimerState, reconcileTimer, getRemainingMs, startTimer, pauseTimer, resumeTimer, resetTimer, formatDuration } from './timer.js';
import { translate } from './i18n.js';
import { createBrowserFeatures } from './browser.js';
import { createDeviceLocation } from './location.js';
import { getProvinceName } from './province.js';

export function mountClockTimer(root) {
    const storage = createStorage();
    const browser = createBrowserFeatures(root);
    const find = (id) => root.querySelector(`#${id}`);
    const defaultLanguage = root.dataset.language === 'en' ? 'en' : 'tr';
    let language;
    let view = 'clock';
    let zoom;
    let sound;
    let location = null;
    let provinceName = null;
    let provinceStatus = 'unavailable';
    let provinceLookupVersion = 0;
    let timer = restoreTimerState(storage.read('timer', null));
    let lastSecond = '';
    let lastSolarDate = '';
    let lastStatus = '';
    let messageKey = timer.status === 'completed' ? 'completed' : '';
    const deviceLocation = createDeviceLocation((state) => {
        location = state.location;
        provinceName = null;
        provinceStatus = location ? 'locating' : 'unavailable';
        const version = ++provinceLookupVersion;
        lastSolarDate = '';
        renderSolar(new Date());
        if (location) {
            void resolveProvince(location, version);
        }
    });

    async function resolveProvince(currentLocation, version) {
        let name;
        try {
            name = await getProvinceName(currentLocation.latitude, currentLocation.longitude);
        } catch {
            name = null;
        }
        if (version !== provinceLookupVersion) {
            return;
        }
        provinceName = name;
        provinceStatus = name ? 'available' : 'unavailable';
        renderSolar(new Date());
    }

    function loadPreferences() {
        const savedLanguage = storage.read('language', defaultLanguage);
        language = ['tr', 'en'].includes(savedLanguage) ? savedLanguage : defaultLanguage;
        const savedZoom = storage.read('zoom', {});
        const validZoom = (value) => typeof value === 'number' && Number.isFinite(value) ? Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, value)) : 1;
        zoom = { clock: validZoom(savedZoom?.clock), timer: validZoom(savedZoom?.timer) };
        sound = storage.read('sound', false) === true;
    }

    const t = (key, parameters) => translate(language, key, parameters);

    function showMessage(key = '') {
        messageKey = key;
        find('app-message').textContent = key ? t(key) : '';
        find('app-message').hidden = !key;
    }

    function persist(name, value) {
        if (!storage.write(name, value)) {
            showMessage('storageUnavailable');
        }
    }

    function populateDuration() {
        const seconds = Math.floor(timer.durationMs / 1000);
        find('duration-hours').value = Math.floor(seconds / 3600);
        find('duration-minutes').value = Math.floor(seconds / 60) % 60;
        find('duration-seconds').value = seconds % 60;
    }

    function renderPreferences() {
        document.documentElement.lang = language;
        document.title = t('pageTitle');
        root.querySelectorAll('[data-i18n]').forEach((element) => {
            element.textContent = t(element.dataset.i18n);
        });
        root.querySelectorAll('[data-i18n-label]').forEach((element) => {
            element.setAttribute('aria-label', t(element.dataset.i18nLabel));
        });
        root.querySelectorAll('[data-i18n-title]').forEach((element) => {
            element.title = t(element.dataset.i18nTitle);
        });
        const targetLanguage = language === 'tr' ? 'en' : 'tr';
        const languageButton = find('language-toggle');
        languageButton.dataset.language = targetLanguage;
        languageButton.lang = language;
        languageButton.textContent = targetLanguage.toUpperCase();
        const languageLabel = t(targetLanguage === 'en' ? 'switchToEnglish' : 'switchToTurkish');
        languageButton.setAttribute('aria-label', languageLabel);
        languageButton.title = languageLabel;
        root.querySelectorAll('[data-view]').forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.view === view));
        });
        find('clock-panel').hidden = view !== 'clock';
        find('timer-panel').hidden = view !== 'timer';
        root.dataset.view = view;
        root.style.setProperty('--display-scale-max', MAX_ZOOM);
        root.style.setProperty('--display-scale', zoom[view]);
        const zoomPercent = Math.round(zoom[view] * 100);
        find('zoom-value').textContent = `${zoomPercent}%`;
        find('zoom-value').setAttribute('aria-label', t('zoomResetLabel', { percent: zoomPercent }));
        find('zoom-out').disabled = zoom[view] <= MIN_ZOOM;
        find('zoom-in').disabled = zoom[view] >= MAX_ZOOM;
        const soundButton = find('sound-toggle');
        const soundLabel = t(sound ? 'soundOn' : 'soundOff');
        soundButton.hidden = view !== 'timer';
        soundButton.setAttribute('aria-label', soundLabel);
        soundButton.title = soundLabel;
        soundButton.setAttribute('aria-pressed', String(sound));
        soundButton.querySelector('[data-sound-icon="off"]').toggleAttribute('hidden', sound);
        soundButton.querySelector('[data-sound-icon="on"]').toggleAttribute('hidden', !sound);
        renderFullscreen();

        root.querySelectorAll('[data-preset]').forEach((button) => {
            button.textContent = t('preset', { minutes: button.dataset.preset });
        });
        const world = find('world-clocks');
        world.replaceChildren(...getWorldClocks(new Date(), language).map((city) => {
            const item = document.createElement('div');
            item.className = 'world-clock';
            item.dataset.city = city.id;
            const name = document.createElement('span');
            name.className = 'world-clock__name';
            name.textContent = city.name;
            const time = document.createElement('time');
            time.className = 'world-clock__time';
            time.dataset.timezone = city.timeZone;
            time.textContent = city.time;
            item.append(name, time);
            return item;
        }));
        lastSecond = '';
        lastSolarDate = '';
        lastStatus = '';
        showMessage(messageKey);
        tick();
    }

    function renderFullscreen() {
        const button = find('fullscreen-toggle');
        const key = browser.isFullscreen() ? 'exitFullscreen' : 'fullscreen';
        button.setAttribute('aria-label', t(key));
        button.querySelector('[data-fullscreen-icon="enter"]').toggleAttribute('hidden', browser.isFullscreen());
        button.querySelector('[data-fullscreen-icon="exit"]').toggleAttribute('hidden', !browser.isFullscreen());
        button.title = t(browser.supportsFullscreen() ? key : 'fullscreenUnavailable');
        button.setAttribute('aria-pressed', String(browser.isFullscreen()));
    }

    function renderSolar(date) {
        find('sun-information').hidden = !location;
        if (!location) {
            find('location-status').textContent = '';
            find('sun-times').hidden = true;
            find('sunrise-time').textContent = '—';
            find('sunset-time').textContent = '—';
            find('solar-message').hidden = true;
            return;
        }
        find('location-status').textContent = provinceName
            ?? t(provinceStatus === 'locating' ? 'provinceLocating' : 'provinceUnavailable');
        try {
            const calendarDate = new Intl.DateTimeFormat('en-CA', {
                timeZone: location.timeZone, year: 'numeric', month: '2-digit', day: '2-digit',
            }).format(date);
            const cacheKey = `${location.latitude}:${location.longitude}:${location.timeZone}:${calendarDate}:${language}`;
            if (cacheKey === lastSolarDate) {
                return;
            }
            lastSolarDate = cacheKey;
            const times = getSunTimes(date, location);
            const formatter = new Intl.DateTimeFormat(language === 'tr' ? 'tr-TR' : 'en-GB', {
                timeZone: location.timeZone, hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
            });
            find('sunrise-time').textContent = times.sunrise ? formatter.format(times.sunrise) : '—';
            find('sunset-time').textContent = times.sunset ? formatter.format(times.sunset) : '—';
            find('sun-times').hidden = !times.sunrise && !times.sunset;
            find('solar-message').textContent = times.sunrise && times.sunset ? '' : t('sunUnavailable');
            find('solar-message').hidden = Boolean(times.sunrise && times.sunset);
        } catch {
            find('sun-times').hidden = true;
            find('sunrise-time').textContent = '—';
            find('sunset-time').textContent = '—';
            find('solar-message').textContent = t('sunUnavailable');
            find('solar-message').hidden = false;
        }
    }

    function renderTimer(now) {
        const previousStatus = timer.status;
        timer = reconcileTimer(timer, now);
        if (previousStatus !== timer.status) {
            persist('timer', timer);
            if (timer.status === 'completed') {
                showMessage('completed');
                if (sound && !document.hidden) {
                    browser.playCompletion();
                }
            }
        }
        const remaining = getRemainingMs(timer, now);
        find('timer-display').textContent = formatDuration(remaining);
        find('timer-progress')?.setAttribute('value', String(remaining / timer.durationMs));
        if (lastStatus === timer.status) {
            return;
        }
        lastStatus = timer.status;
        find('timer-status').textContent = t(timer.status);
        root.dataset.timerStatus = timer.status;
        find('timer-start').hidden = !['idle', 'completed'].includes(timer.status);
        find('timer-pause').hidden = timer.status !== 'running';
        find('timer-pause').disabled = timer.status !== 'running';
        find('timer-resume').hidden = timer.status !== 'paused';
        find('timer-resume').disabled = timer.status !== 'paused';
        find('timer-reset').disabled = timer.status === 'idle';
        const isActive = ['running', 'paused'].includes(timer.status);
        find('duration-form').querySelectorAll('input, button').forEach((control) => {
            control.disabled = isActive;
        });
        root.querySelectorAll('[data-preset]').forEach((button) => {
            button.disabled = isActive;
        });
    }

    function tick() {
        const now = Date.now();
        const date = new Date(now);
        const second = Math.floor(now / 1000);
        if (second !== lastSecond) {
            lastSecond = second;
            find('clock-display').textContent = formatClock(date, language);
            find('calendar-date').textContent = formatCalendar(date, language);
            find('calendar-week').textContent = t('week', { number: getIsoWeek(date) });
            for (const city of getWorldClocks(date, language)) {
                const time = root.querySelector(`[data-city="${city.id}"] time`);
                if (time) {
                    time.textContent = city.time;
                }
            }
            renderSolar(date);
        }
        renderTimer(now);
    }

    function updateTimer(nextTimer) {
        timer = nextTimer;
        showMessage();
        persist('timer', timer);
        populateDuration();
        tick();
    }

    loadPreferences();
    persist('timer', timer);
    populateDuration();
    renderPreferences();
    void deviceLocation.refresh();

    root.querySelectorAll('button[data-language]').forEach((button) => {
        button.addEventListener('click', () => {
            language = button.dataset.language;
            persist('language', language);
            renderPreferences();
        });
    });
    root.querySelectorAll('button[data-view]').forEach((button) => {
        button.addEventListener('click', () => {
            view = button.dataset.view;
            renderPreferences();
            root.querySelector('.app-scroll').scrollTop = 0;
        });
    });
    find('duration-form').addEventListener('submit', (event) => {
        event.preventDefault();
        const inputs = ['duration-hours', 'duration-minutes', 'duration-seconds'].map(find);
        const values = inputs.map((input) => Number(input.value));
        const duration = (values[0] * 3600 + values[1] * 60 + values[2]) * 1000;
        if (!inputs.every((input) => input.checkValidity()) || !values.every(Number.isInteger)
            || duration < 1000 || duration > MAX_DURATION_MS) {
            showMessage('invalidDuration');
            return;
        }
        updateTimer(createTimerState(duration));
    });
    root.querySelectorAll('button[data-preset]').forEach((button) => {
        button.addEventListener('click', () => updateTimer(createTimerState(Number(button.dataset.preset) * 60000)));
    });
    find('timer-start').addEventListener('click', () => {
        if (sound) {
            void browser.enableSound();
        }
        updateTimer(startTimer(timer));
    });
    find('timer-pause').addEventListener('click', () => updateTimer(pauseTimer(timer)));
    find('timer-resume').addEventListener('click', () => {
        if (sound) {
            void browser.enableSound();
        }
        updateTimer(resumeTimer(timer));
    });
    find('timer-reset').addEventListener('click', () => updateTimer(resetTimer(timer)));
    find('sound-toggle').addEventListener('click', async () => {
        sound = !sound;
        if (sound && !await browser.enableSound()) {
            sound = false;
            showMessage('soundUnavailable');
        }
        persist('sound', sound);
        renderPreferences();
    });
    for (const [id, step] of [['zoom-out', -0.1], ['zoom-in', 0.1]]) {
        find(id).addEventListener('click', () => {
            zoom[view] = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, Math.round((zoom[view] + step) * 10) / 10));
            persist('zoom', zoom);
            renderPreferences();
        });
    }
    find('zoom-value').addEventListener('click', () => {
        zoom[view] = 1;
        persist('zoom', zoom);
        renderPreferences();
    });
    find('fullscreen-toggle').addEventListener('click', async () => {
        if (!browser.supportsFullscreen()) {
            showMessage('fullscreenUnavailable');
            return;
        }
        try {
            await browser.toggleFullscreen();
            showMessage();
        } catch {
            showMessage('fullscreenError');
        }
    });
    document.addEventListener('fullscreenchange', renderFullscreen);
    const refreshTimeAndLocation = () => {
        tick();
        if (!document.hidden) {
            void deviceLocation.refresh();
        }
    };
    document.addEventListener('visibilitychange', refreshTimeAndLocation);
    window.addEventListener('pageshow', refreshTimeAndLocation);
    window.addEventListener('online', refreshTimeAndLocation);
    window.addEventListener('storage', (event) => {
        if (event.key !== null && !event.key.startsWith(STORAGE_PREFIX)) {
            return;
        }
        timer = restoreTimerState(storage.read('timer', null));
        loadPreferences();
        populateDuration();
        renderPreferences();
    });
    const interval = window.setInterval(tick, 250);
    window.addEventListener('pagehide', (event) => {
        if (!event.persisted) {
            window.clearInterval(interval);
            deviceLocation.dispose();
        }
    });
}

const root = document.getElementById('clock-timer');
if (root) {
    mountClockTimer(root);
}

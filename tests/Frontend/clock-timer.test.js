import assert from 'node:assert/strict';
import test from 'node:test';
import { DEFAULT_DURATION_MS, LOCATIONS, MAX_DURATION_MS } from '../../resources/js/clock-timer/config.js';
import { formatCalendar, formatClock, getIsoWeek } from '../../resources/js/clock-timer/clock.js';
import { createStorage, STORAGE_PREFIX } from '../../resources/js/clock-timer/storage.js';
import { getSunTimes } from '../../resources/js/clock-timer/sun.js';
import {
    createTimerState, formatDuration, getRemainingMs, pauseTimer, reconcileTimer,
    resetTimer, restoreTimerState, resumeTimer, startTimer,
} from '../../resources/js/clock-timer/timer.js';
import { getWorldClocks } from '../../resources/js/clock-timer/world-clock.js';

function memoryStorage(initial = []) {
    const values = new Map(initial);

    return {
        values,
        getItem: (key) => values.get(key) ?? null,
        setItem: (key, value) => values.set(key, value),
    };
}

test('running timer keeps its absolute deadline after a reload and a sleeping tab', () => {
    const now = Date.parse('2026-10-03T09:00:00Z');
    const initial = createTimerState(30 * 60_000);
    const running = startTimer(initial, now);
    const saved = JSON.parse(JSON.stringify(running));

    const restored = restoreTimerState(saved, now + 10 * 60_000);

    assert.equal(restored.status, 'running');
    assert.equal(restored.endAt, Date.parse('2026-10-03T09:30:00Z'));
    assert.equal(restored.remainingMs, 20 * 60_000);
    assert.equal(getRemainingMs(restored, now + 25 * 60_000), 5 * 60_000);
    assert.equal(initial.status, 'idle');
    assert.equal(running.remainingMs, 30 * 60_000);
});

test('paused timer does not elapse after reopening and resumes with a new deadline', () => {
    const now = Date.parse('2026-10-03T09:00:00Z');
    const running = startTimer(createTimerState(300_000), now);
    const paused = pauseTimer(running, now + 70_000);

    const reopened = restoreTimerState(JSON.parse(JSON.stringify(paused)), now + 86_400_000);
    const resumed = resumeTimer(reopened, now + 86_400_000);

    assert.deepEqual(reopened, { version: 1, status: 'paused', durationMs: 300_000, remainingMs: 230_000, endAt: null });
    assert.equal(getRemainingMs(resumed, now + 86_430_000), 200_000);
    assert.equal(reopened.status, 'paused');
});

test('an expired timer restores as completed and can be restarted or reset', () => {
    const now = 1_000_000;
    const running = startTimer(createTimerState(60_000), now);

    const completed = restoreTimerState(running, now + 60_001);

    assert.deepEqual(completed, { version: 1, status: 'completed', durationMs: 60_000, remainingMs: 0, endAt: null });
    assert.equal(reconcileTimer(running, now + 60_000).status, 'completed');
    assert.equal(pauseTimer(running, now + 70_000).status, 'completed');
    assert.equal(restoreTimerState(JSON.parse(JSON.stringify(completed)), now + 100_000).status, 'completed');
    assert.equal(startTimer(completed, now + 100_000).endAt, now + 160_000);
    assert.deepEqual(resetTimer(completed), createTimerState(60_000));
});

test('long timers preserve hours after closing the browser', () => {
    const now = 1_000_000;
    const running = startTimer(createTimerState(MAX_DURATION_MS), now);

    const restored = restoreTimerState(JSON.parse(JSON.stringify(running)), now + 25 * 3_600_000);

    assert.equal(formatDuration(restored.remainingMs), '74:59:59');
    assert.equal(formatDuration(running.remainingMs), '99:59:59');
});

test('invalid or outdated saved states return a safe idle default', async (suite) => {
    const valid = startTimer(createTimerState(60_000), 1_000_000);
    const cases = {
        absent: null,
        primitive: 'running',
        oldVersion: { ...valid, version: 0 },
        unknownStatus: { ...valid, status: 'alarm' },
        nonnumericDuration: { ...valid, durationMs: '60000' },
        excessiveDuration: { ...valid, durationMs: MAX_DURATION_MS + 1 },
        negativeRemaining: { ...valid, remainingMs: -1 },
        excessiveRemaining: { ...valid, remainingMs: 60_001 },
        missingDeadline: { ...valid, endAt: null },
        impossibleDeadline: { ...valid, endAt: 9_000_000_000 },
        fractionalDeadline: { ...valid, endAt: 1_060_000.5 },
        invalidPausedDeadline: { ...valid, status: 'paused' },
        zeroPausedRemaining: { ...valid, status: 'paused', remainingMs: 0, endAt: null },
        invalidCompletedRemaining: { ...valid, status: 'completed', endAt: null },
        invalidIdleRemaining: { ...valid, status: 'idle', remainingMs: 20_000, endAt: null },
    };

    for (const [name, saved] of Object.entries(cases)) {
        await suite.test(name, () => {
            assert.deepEqual(restoreTimerState(saved, 1_000_000), createTimerState());
        });
    }

    assert.equal(createTimerState(0).durationMs, DEFAULT_DURATION_MS);
    assert.equal(createTimerState(Number.POSITIVE_INFINITY).durationMs, DEFAULT_DURATION_MS);
});

test('countdown display rounds up partial seconds and retains hours at boundaries', () => {
    const durations = [[0, '00:00'], [1, '00:01'], [59_001, '01:00'], [59_000, '00:59'],
        [3_599_000, '59:59'], [3_600_000, '01:00:00'], [27_723_000, '07:42:03'], [-1, '00:00'], [NaN, '00:00']];

    for (const [duration, expected] of durations) {
        assert.equal(formatDuration(duration), expected);
    }
});

test('local clock and calendar display use the selected language and 24-hour time', () => {
    const date = new Date(2026, 9, 3, 18, 7, 9);

    assert.equal(formatClock(date, 'tr'), '18:07:09');
    assert.equal(formatClock(date, 'en'), '18:07:09');
    assert.equal(formatCalendar(date, 'tr'), '3 Ekim 2026, Cumartesi');
    assert.equal(formatCalendar(date, 'en'), '3 October 2026, Saturday');
});

test('ISO weeks belong to the correct ISO year around New Year and leap years', () => {
    const dates = [[2021, 0, 1, 53], [2021, 0, 4, 1], [2018, 11, 31, 1], [2020, 11, 31, 53],
        [2024, 1, 29, 9], [2026, 9, 3, 40]];

    for (const [year, month, day, expected] of dates) {
        assert.equal(getIsoWeek(new Date(year, month, day, 12)), expected);
    }

    assert.equal(getIsoWeek(new Date('invalid')), null);
});

test('world clocks handle US and European daylight saving transitions', () => {
    const newYorkBefore = getWorldClocks(new Date('2026-03-08T06:59:00Z'), 'en');
    const newYorkAfter = getWorldClocks(new Date('2026-03-08T07:01:00Z'), 'en');
    const europeBefore = getWorldClocks(new Date('2026-03-29T00:59:00Z'), 'tr');
    const europeAfter = getWorldClocks(new Date('2026-03-29T01:01:00Z'), 'tr');

    assert.equal(newYorkBefore.find((city) => city.id === 'new-york').time, '01:59');
    assert.equal(newYorkAfter.find((city) => city.id === 'new-york').time, '03:01');
    assert.equal(europeBefore.find((city) => city.id === 'london').time, '00:59');
    assert.equal(europeAfter.find((city) => city.id === 'london').time, '02:01');
    assert.equal(europeBefore.find((city) => city.id === 'berlin').time, '01:59');
    assert.equal(europeAfter.find((city) => city.id === 'berlin').time, '03:01');
    assert.equal(europeAfter.find((city) => city.id === 'istanbul').time, '04:01');
    assert.equal(europeAfter.find((city) => city.id === 'london').name, 'Londra');
    assert.equal(newYorkBefore.find((city) => city.id === 'london').name, 'London');
});

test('Sydney daylight saving uses southern hemisphere seasons', () => {
    const summer = getWorldClocks(new Date('2026-01-01T00:00:00Z'), 'en');
    const winter = getWorldClocks(new Date('2026-07-01T00:00:00Z'), 'en');

    assert.equal(summer.find((city) => city.id === 'sydney').time, '11:00');
    assert.equal(winter.find((city) => city.id === 'sydney').time, '10:00');
});

test('local solar estimates match published NOAA sunrise and sunset within four minutes', () => {
    const atlanta = { latitude: 33.733, longitude: -84.383, timeZone: 'America/New_York' };

    const result = getSunTimes(new Date('2026-06-01T16:00:00Z'), atlanta);

    // NOAA table: https://gml.noaa.gov/grad/solcalc/table.php?lat=33.733&lon=-84.383&year=2026
    assert.ok(Math.abs(result.sunrise.getTime() - Date.parse('2026-06-01T10:28:00Z')) < 4 * 60_000);
    assert.ok(Math.abs(result.sunset.getTime() - Date.parse('2026-06-02T00:43:00Z')) < 4 * 60_000);
});

test('sunrise and sunset follow the selected location calendar date across UTC midnight', () => {
    const newYork = LOCATIONS.find((location) => location.id === 'new-york');
    const istanbul = LOCATIONS.find((location) => location.id === 'istanbul');

    const newYorkEvening = getSunTimes(new Date('2026-06-02T02:00:00Z'), newYork);
    const newYorkSameDay = getSunTimes(new Date('2026-06-01T16:00:00Z'), newYork);
    const istanbulAfterMidnight = getSunTimes(new Date('2026-06-01T22:00:00Z'), istanbul);
    const istanbulSameDay = getSunTimes(new Date('2026-06-02T10:00:00Z'), istanbul);

    assert.deepEqual(newYorkEvening, newYorkSameDay);
    assert.deepEqual(istanbulAfterMidnight, istanbulSameDay);
    assert.equal(istanbulAfterMidnight.sunrise.toISOString().slice(0, 10), '2026-06-02');
});

test('polar days and failed solar calculations return null events safely', () => {
    const tromso = { latitude: 69.6492, longitude: 18.9553, timeZone: 'Europe/Oslo' };
    const fallback = { sunrise: null, sunset: null };

    assert.deepEqual(getSunTimes(new Date('2026-06-21T12:00:00Z'), tromso), fallback);
    assert.deepEqual(getSunTimes(new Date('2026-12-21T12:00:00Z'), tromso), fallback);
    assert.deepEqual(getSunTimes(new Date('invalid'), tromso), fallback);
    assert.deepEqual(getSunTimes(new Date(), { ...tromso, timeZone: 'invalid/timezone' }), fallback);
    assert.deepEqual(getSunTimes(new Date(), { ...tromso, latitude: 91 }), fallback);
    assert.deepEqual(getSunTimes(new Date(), null), fallback);
});

test('saved state is namespaced and remains readable through a new storage adapter', () => {
    const backing = memoryStorage([['other-app.setting', 'unchanged']]);
    const storage = createStorage(() => backing);
    const running = startTimer(createTimerState(60_000), 1_000_000);

    assert.equal(storage.write('timer', running), true);
    const reopened = createStorage(() => backing);

    assert.deepEqual(reopened.read('timer'), running);
    assert.equal(backing.values.get('other-app.setting'), 'unchanged');
    assert.deepEqual([...backing.values.keys()], ['other-app.setting', `${STORAGE_PREFIX}timer`]);
});

test('blocked localStorage still keeps timer state and preferences in memory', () => {
    const storage = createStorage(() => { throw new Error('SecurityError'); });

    assert.equal(storage.write('language', 'en'), false);
    assert.equal(storage.read('language', 'tr'), 'en');
    assert.equal(storage.read('missing', 'default'), 'default');
});

test('removing a stored preference also clears its cached value', () => {
    const backing = memoryStorage();
    let available = true;
    const storage = createStorage(() => {
        if (!available) {
            throw new Error('SecurityError');
        }

        return backing;
    });
    storage.write('language', 'en');
    backing.values.delete(`${STORAGE_PREFIX}language`);

    assert.equal(storage.read('language', 'tr'), 'tr');
    available = false;
    assert.equal(storage.read('language', 'tr'), 'tr');
});

test('quota failures keep the latest preference rather than an older saved value', () => {
    const backing = memoryStorage([[`${STORAGE_PREFIX}language`, JSON.stringify('tr')]]);
    backing.setItem = () => { throw new Error('QuotaExceededError'); };
    const storage = createStorage(() => backing);

    assert.equal(storage.read('language'), 'tr');
    assert.equal(storage.write('language', 'en'), false);
    assert.equal(storage.read('language'), 'en');
});

test('corrupt storage and unserializable values safely preserve working defaults', () => {
    const backing = memoryStorage([[`${STORAGE_PREFIX}timer`, '{broken json']]);
    const storage = createStorage(() => backing);
    const cyclic = {};
    cyclic.self = cyclic;

    assert.deepEqual(storage.read('timer', createTimerState()), createTimerState());
    assert.equal(storage.write('language', 'en'), true);
    assert.equal(storage.write('language', cyclic), false);
    assert.equal(storage.write('language', undefined), false);
    assert.equal(storage.read('language'), 'en');
});

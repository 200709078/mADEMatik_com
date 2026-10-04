import { DEFAULT_DURATION_MS, MAX_DURATION_MS } from './config.js';

const statuses = new Set(['idle', 'running', 'paused', 'completed']);
const maximumTimestamp = 8_640_000_000_000_000;

function isDuration(value) {
    return Number.isSafeInteger(value) && value > 0 && value <= MAX_DURATION_MS;
}

function currentTimestamp(now) {
    return Number.isSafeInteger(now) && now >= 0 && now <= maximumTimestamp ? now : Date.now();
}

export function createTimerState(durationMs = DEFAULT_DURATION_MS) {
    const duration = isDuration(durationMs) ? durationMs : DEFAULT_DURATION_MS;

    return { version: 1, status: 'idle', durationMs: duration, remainingMs: duration, endAt: null };
}

export function restoreTimerState(value, now = Date.now()) {
    if (!value || typeof value !== 'object' || value.version !== 1 || !statuses.has(value.status)
        || !isDuration(value.durationMs) || !Number.isSafeInteger(value.remainingMs)
        || value.remainingMs < 0 || value.remainingMs > value.durationMs) {
        return createTimerState();
    }

    const state = {
        version: 1,
        status: value.status,
        durationMs: value.durationMs,
        remainingMs: value.remainingMs,
        endAt: value.endAt,
    };

    if (state.status === 'running') {
        const timestamp = currentTimestamp(now);

        if (!Number.isSafeInteger(state.endAt) || state.endAt < 0 || state.endAt > maximumTimestamp
            || state.remainingMs === 0 || state.endAt - timestamp > state.durationMs) {
            return createTimerState();
        }

        const remainingMs = Math.max(0, state.endAt - timestamp);

        return remainingMs === 0
            ? { ...state, status: 'completed', remainingMs: 0, endAt: null }
            : { ...state, remainingMs };
    }

    if (state.endAt !== null
        || (state.status === 'idle' && state.remainingMs !== state.durationMs)
        || (state.status === 'paused' && state.remainingMs === 0)
        || (state.status === 'completed' && state.remainingMs !== 0)) {
        return createTimerState();
    }

    return state;
}

export function reconcileTimer(state, now = Date.now()) {
    return restoreTimerState(state, now);
}

export function getRemainingMs(state, now = Date.now()) {
    return restoreTimerState(state, now).remainingMs;
}

export function startTimer(state, now = Date.now()) {
    const timestamp = currentTimestamp(now);
    const restored = restoreTimerState(state, timestamp);

    if (restored.status === 'running' || restored.status === 'paused') {
        return restored;
    }

    return {
        ...restored,
        status: 'running',
        remainingMs: restored.durationMs,
        endAt: timestamp + restored.durationMs,
    };
}

export function pauseTimer(state, now = Date.now()) {
    const restored = restoreTimerState(state, now);

    return restored.status === 'running' ? { ...restored, status: 'paused', endAt: null } : restored;
}

export function resumeTimer(state, now = Date.now()) {
    const timestamp = currentTimestamp(now);
    const restored = restoreTimerState(state, timestamp);

    return restored.status === 'paused'
        ? { ...restored, status: 'running', endAt: timestamp + restored.remainingMs }
        : restored;
}

export function resetTimer(state) {
    return createTimerState(isDuration(state?.durationMs) ? state.durationMs : DEFAULT_DURATION_MS);
}

export function formatDuration(milliseconds) {
    const totalSeconds = Number.isFinite(milliseconds) ? Math.max(0, Math.ceil(milliseconds / 1000)) : 0;
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    const parts = [minutes, seconds].map((value) => String(value).padStart(2, '0'));

    if (hours > 0) {
        parts.unshift(String(hours).padStart(2, '0'));
    }

    return parts.join(':');
}

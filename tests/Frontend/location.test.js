import assert from 'node:assert/strict';
import test from 'node:test';
import { createDeviceLocation } from '../../resources/js/clock-timer/location.js';

function deviceEnvironment(initialState = 'granted') {
    const listeners = new Set();
    const requests = [];
    const permission = {
        state: initialState,
        addEventListener: (event, listener) => listeners.add(listener),
        removeEventListener: (event, listener) => listeners.delete(listener),
        change(state) {
            this.state = state;
            for (const listener of listeners) {
                listener();
            }
        },
    };
    const environment = {
        isSecureContext: true,
        Intl: {
            DateTimeFormat: class {
                resolvedOptions() {
                    return { timeZone: 'Europe/Istanbul' };
                }
            },
        },
        navigator: {
            permissions: { query: async () => permission },
            geolocation: {
                getCurrentPosition(success, failure, options) {
                    requests.push({ success, failure, options });
                },
            },
        },
    };

    return { environment, permission, requests, listeners };
}

test('previously granted permission finds the device location without another prompt', async () => {
    const { environment, requests } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

    await deviceLocation.refresh();
    requests[0].success({ coords: { latitude: 41.0082, longitude: 28.9784 } });

    assert.deepEqual(states, [
        { status: 'locating', location: null },
        { status: 'available', location: { latitude: 41.0082, longitude: 28.9784, timeZone: 'Europe/Istanbul' } },
    ]);
    assert.equal(requests[0].options.enableHighAccuracy, false);
    assert.ok(requests[0].options.timeout > 0 && requests[0].options.timeout <= 10_000);
    assert.ok(requests[0].options.maximumAge <= 300_000);
});

test('prompt permission requests location once and preserves the pending request on refresh', async () => {
    const { environment, requests, listeners } = deviceEnvironment('prompt');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

    await deviceLocation.refresh();
    await deviceLocation.refresh();
    await deviceLocation.refresh();

    assert.equal(requests.length, 1);
    assert.equal(listeners.size, 1);
    assert.deepEqual(states.at(-1), { status: 'locating', location: null });

    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 41, longitude: 29, timeZone: 'Europe/Istanbul' },
    });
});

test('permission grant during an active prompt does not duplicate the native request', async () => {
    const { environment, permission, requests } = deviceEnvironment('prompt');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.change('granted');
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    assert.equal(requests.length, 1);
    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 41, longitude: 29, timeZone: 'Europe/Istanbul' },
    });
});

test('refresh preserves a successful native result while permission status still reports prompt', async () => {
    const { environment, permission, requests } = deviceEnvironment('prompt');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    await deviceLocation.refresh();

    assert.equal(permission.state, 'prompt');
    assert.equal(requests.length, 1);
    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 41, longitude: 29, timeZone: 'Europe/Istanbul' },
    });
});

test('a rejected or dismissed prompt is not requested again on later refreshes', async (suite) => {
    for (const state of ['prompt', 'denied']) {
        await suite.test(state, async () => {
            const { environment, permission, requests } = deviceEnvironment('prompt');
            const states = [];
            const deviceLocation = createDeviceLocation((value) => states.push(value), environment);
            await deviceLocation.refresh();

            permission.change(state);
            requests[0].failure({ code: 1 });
            await deviceLocation.refresh();
            await deviceLocation.refresh();

            assert.equal(requests.length, 1);
            assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
        });
    }
});

test('denied permission reports unavailable without requesting location', async () => {
    const { environment, requests } = deviceEnvironment('denied');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

    await deviceLocation.refresh();
    await deviceLocation.refresh();

    assert.equal(requests.length, 0);
    assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
});

test('unsupported or insecure browsers report unavailable without querying location', async (suite) => {
    const cases = {
        insecure: (environment) => { environment.isSecureContext = false; },
        unknownSecurity: (environment) => { delete environment.isSecureContext; },
        missingNavigator: (environment) => { delete environment.navigator; },
        missingGeolocation: (environment) => { delete environment.navigator.geolocation; },
    };

    for (const [name, customize] of Object.entries(cases)) {
        await suite.test(name, async () => {
            const { environment, requests } = deviceEnvironment();
            let queryCount = 0;
            environment.navigator.permissions.query = async () => { queryCount++; };
            customize(environment);
            const states = [];
            const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

            await deviceLocation.refresh();

            assert.deepEqual(states, [{ status: 'unavailable', location: null }]);
            assert.equal(queryCount, 0);
            assert.equal(requests.length, 0);
        });
    }
});

test('missing or unsupported permission queries use one native request and retain its successful location', async (suite) => {
    const cases = {
        missingPermissions: (environment) => { delete environment.navigator.permissions; },
        missingQuery: (environment) => { delete environment.navigator.permissions.query; },
        rejected: (environment) => {
            environment.navigator.permissions.query = async () => { throw new Error('Permissions unavailable'); };
        },
        missingStatus: (environment) => { environment.navigator.permissions.query = async () => null; },
        unknownState: (environment) => { environment.navigator.permissions.query = async () => ({ state: 'unknown' }); },
    };

    for (const [name, customize] of Object.entries(cases)) {
        await suite.test(name, async () => {
            const { environment, requests } = deviceEnvironment();
            customize(environment);
            const states = [];
            const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

            await deviceLocation.refresh();
            await deviceLocation.refresh();

            assert.equal(requests.length, 1);
            assert.deepEqual(states.at(-1), { status: 'locating', location: null });

            requests[0].success({ coords: { latitude: 41, longitude: 29 } });
            await deviceLocation.refresh();

            assert.deepEqual(states.at(-1), {
                status: 'available', location: { latitude: 41, longitude: 29, timeZone: 'Europe/Istanbul' },
            });
            assert.equal(requests.length, 1);
        });
    }
});

test('failed fallback location is not requested again without permission status support', async () => {
    const { environment, requests } = deviceEnvironment();
    delete environment.navigator.permissions;
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

    await deviceLocation.refresh();
    requests[0].failure({ code: 1 });
    await deviceLocation.refresh();

    assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
    assert.equal(requests.length, 1);
});

test('location acquisition errors clear the pending state', async (suite) => {
    for (const code of [1, 2, 3]) {
        await suite.test(`geolocation error ${code}`, async () => {
            const { environment, requests } = deviceEnvironment();
            const states = [];
            const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

            await deviceLocation.refresh();
            requests[0].failure({ code });

            assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
        });
    }
});

test('throwing geolocation and timezone APIs do not interrupt the application', async (suite) => {
    await suite.test('geolocation', async () => {
        const { environment } = deviceEnvironment();
        environment.navigator.geolocation.getCurrentPosition = () => { throw new Error('Device unavailable'); };
        const states = [];
        const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

        await deviceLocation.refresh();

        assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
    });

    await suite.test('timezone', async () => {
        const { environment, requests } = deviceEnvironment();
        environment.Intl.DateTimeFormat = class {
            constructor() { throw new Error('Timezone unavailable'); }
        };
        const states = [];
        const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

        await deviceLocation.refresh();
        requests[0].success({ coords: { latitude: 41, longitude: 29 } });

        assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
    });
});

test('invalid coordinates never become available', async (suite) => {
    const cases = {
        missingPosition: null,
        missingCoordinates: {},
        stringLatitude: { coords: { latitude: '41', longitude: 29 } },
        nanLatitude: { coords: { latitude: NaN, longitude: 29 } },
        infiniteLongitude: { coords: { latitude: 41, longitude: Infinity } },
        excessiveLatitude: { coords: { latitude: 90.01, longitude: 29 } },
        excessiveLongitude: { coords: { latitude: 41, longitude: -180.01 } },
    };

    for (const [name, position] of Object.entries(cases)) {
        await suite.test(name, async () => {
            const { environment, requests } = deviceEnvironment();
            const states = [];
            const deviceLocation = createDeviceLocation((state) => states.push(state), environment);

            await deviceLocation.refresh();
            requests[0].success(position);

            assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
        });
    }
});

test('a permission grant after denial automatically finds location and revocation clears it', async () => {
    const { environment, permission, requests } = deviceEnvironment('denied');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.change('granted');
    requests[0].success({ coords: { latitude: 0, longitude: 0 } });
    permission.change('denied');

    assert.deepEqual(states, [
        { status: 'unavailable', location: null },
        { status: 'locating', location: null },
        { status: 'available', location: { latitude: 0, longitude: 0, timeZone: 'Europe/Istanbul' } },
        { status: 'unavailable', location: null },
    ]);
    assert.equal(requests.length, 1);
});

test('a position arriving after permission revocation is ignored', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.change('denied');
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    assert.deepEqual(states, [
        { status: 'locating', location: null },
        { status: 'unavailable', location: null },
    ]);
});

test('a prompt result arriving after explicit permission revocation is ignored', async () => {
    const { environment, permission, requests } = deviceEnvironment('prompt');
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.change('denied');
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });
    await deviceLocation.refresh();

    assert.equal(requests.length, 1);
    assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
    assert.equal(states.some((state) => state.status === 'available'), false);
});

test('a new grant after revocation accepts only the new location request', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.change('denied');
    permission.change('granted');
    requests[0].success({ coords: { latitude: 41.0082, longitude: 28.9784 } });

    assert.equal(requests.length, 2);
    assert.deepEqual(states.at(-1), { status: 'locating', location: null });

    requests[1].success({ coords: { latitude: 51.5074, longitude: -0.1278 } });

    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 51.5074, longitude: -0.1278, timeZone: 'Europe/Istanbul' },
    });
    assert.equal(states.filter((state) => state.status === 'available').length, 1);
});

test('permission state is checked again before accepting a position', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    permission.state = 'prompt';
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    assert.deepEqual(states.at(-1), { status: 'unavailable', location: null });
});

test('permission revocation during a locating update cannot trigger a browser prompt', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => {
        states.push(state);
        if (state.status === 'locating') {
            permission.change('prompt');
        }
    }, environment);

    await deviceLocation.refresh();

    assert.equal(requests.length, 0);
    assert.deepEqual(states, [
        { status: 'locating', location: null },
        { status: 'unavailable', location: null },
    ]);
});

test('new refresh results replace stale requests without duplicating permission listeners', async () => {
    const { environment, requests, listeners } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();
    requests[0].success({ coords: { latitude: 41.0082, longitude: 28.9784 } });

    await deviceLocation.refresh();
    requests[1].success({ coords: { latitude: 51.5074, longitude: -0.1278 } });
    requests[0].success({ coords: { latitude: 41.0082, longitude: 28.9784 } });

    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 51.5074, longitude: -0.1278, timeZone: 'Europe/Istanbul' },
    });
    assert.equal(states.filter((state) => state.status === 'available').length, 2);
    assert.equal(requests.length, 2);
    assert.equal(listeners.size, 1);
});

test('refreshes preserve an active granted request rather than losing its result', async () => {
    const { environment, requests, listeners } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    await deviceLocation.refresh();
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });

    assert.equal(requests.length, 1);
    assert.equal(listeners.size, 1);
    assert.deepEqual(states.at(-1), {
        status: 'available', location: { latitude: 41, longitude: 29, timeZone: 'Europe/Istanbul' },
    });
});

test('stale permission queries cannot request location after a newer refresh', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    let resolveFirstQuery;
    const pendingQuery = new Promise((resolve) => { resolveFirstQuery = resolve; });
    environment.navigator.permissions.query = () => pendingQuery;
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    const firstRefresh = deviceLocation.refresh();
    environment.navigator.permissions.query = async () => ({ state: 'denied' });

    await deviceLocation.refresh();
    resolveFirstQuery(permission);
    await firstRefresh;

    assert.deepEqual(states, [{ status: 'unavailable', location: null }]);
    assert.equal(requests.length, 0);
});

test('disposal removes permission listeners and ignores pending locations and refreshes', async () => {
    const { environment, permission, requests, listeners } = deviceEnvironment();
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    await deviceLocation.refresh();

    deviceLocation.dispose();
    permission.change('granted');
    requests[0].success({ coords: { latitude: 41, longitude: 29 } });
    await deviceLocation.refresh();

    assert.equal(listeners.size, 0);
    assert.equal(requests.length, 1);
    assert.deepEqual(states, [{ status: 'locating', location: null }]);
});

test('disposal before a permission query resolves never requests location', async () => {
    const { environment, permission, requests } = deviceEnvironment();
    let resolveQuery;
    environment.navigator.permissions.query = () => new Promise((resolve) => { resolveQuery = resolve; });
    const states = [];
    const deviceLocation = createDeviceLocation((state) => states.push(state), environment);
    const refresh = deviceLocation.refresh();

    deviceLocation.dispose();
    resolveQuery(permission);
    await refresh;

    assert.deepEqual(states, []);
    assert.equal(requests.length, 0);
});

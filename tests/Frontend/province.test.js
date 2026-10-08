import assert from 'node:assert/strict';
import test from 'node:test';
import { geometryContainsPoint, getProvinceName } from '../../resources/js/clock-timer/province.js';

test('device coordinates display the containing Turkish province name', async (suite) => {
    const cities = [
        ['İstanbul', 41.0082, 28.9784],
        ['Ankara', 39.9334, 32.8597],
        ['İzmir', 38.412726, 27.138376],
        ['Antalya', 36.90812, 30.69556],
    ];

    for (const [name, latitude, longitude] of cities) {
        await suite.test(name, async () => {
            assert.equal(await getProvinceName(latitude, longitude), name);
        });
    }
});

test('coordinates outside Turkey or in the sea do not guess a nearby province', async () => {
    assert.equal(await getProvinceName(51.5074, -0.1278), null);
    assert.equal(await getProvinceName(37.9838, 23.7275), null);
    assert.equal(await getProvinceName(36, 30), null);
});

test('invalid device coordinates cannot resolve a province', async (suite) => {
    const coordinates = [
        ['latitude out of range', 91, 29],
        ['longitude out of range', 41, -181],
        ['missing latitude', undefined, 29],
        ['missing longitude', 41, null],
        ['latitude string', '41', 29],
        ['longitude string', 41, '29'],
        ['not a number', NaN, 29],
        ['infinity', 41, Infinity],
    ];

    for (const [name, latitude, longitude] of coordinates) {
        await suite.test(name, async () => {
            assert.equal(await getProvinceName(latitude, longitude), null);
        });
    }
});

test('a polygon includes its interior, edges and vertices but excludes its exterior', () => {
    const geometry = { type: 'Polygon', coordinates: [[[0, 0], [4, 0], [4, 4], [0, 4], [0, 0]]] };

    assert.equal(geometryContainsPoint(geometry, 1, 1), true);
    assert.equal(geometryContainsPoint(geometry, 0, 2), true);
    assert.equal(geometryContainsPoint(geometry, 4, 4), true);
    assert.equal(geometryContainsPoint(geometry, 5, 2), false);
    assert.equal(geometryContainsPoint(geometry, 2, 5), false);
});

test('a polygon hole excludes its interior and includes its boundary', () => {
    const geometry = {
        type: 'Polygon',
        coordinates: [
            [[0, 0], [8, 0], [8, 8], [0, 8], [0, 0]],
            [[2, 2], [6, 2], [6, 6], [2, 6], [2, 2]],
        ],
    };

    assert.equal(geometryContainsPoint(geometry, 1, 4), true);
    assert.equal(geometryContainsPoint(geometry, 4, 4), false);
    assert.equal(geometryContainsPoint(geometry, 2, 4), true);
});

test('a multipolygon contains separate islands and excludes the area between them', () => {
    const geometry = {
        type: 'MultiPolygon',
        coordinates: [
            [[[0, 0], [2, 0], [2, 2], [0, 2], [0, 0]]],
            [[[4, 0], [6, 0], [6, 2], [4, 2], [4, 0]]],
        ],
    };

    assert.equal(geometryContainsPoint(geometry, 1, 1), true);
    assert.equal(geometryContainsPoint(geometry, 5, 1), true);
    assert.equal(geometryContainsPoint(geometry, 3, 1), false);
});

test('ring direction does not change which province contains a point', () => {
    const geometry = { type: 'Polygon', coordinates: [[[0, 0], [0, 4], [4, 4], [4, 0], [0, 0]]] };

    assert.equal(geometryContainsPoint(geometry, 1, 1), true);
    assert.equal(geometryContainsPoint(geometry, 5, 1), false);
});

test('unsupported geometry and invalid points are not treated as province areas', () => {
    const geometry = { type: 'Polygon', coordinates: [[[0, 0], [4, 0], [4, 4], [0, 4], [0, 0]]] };

    assert.equal(geometryContainsPoint({ type: 'Point', coordinates: [1, 1] }, 1, 1), false);
    assert.equal(geometryContainsPoint(geometry, NaN, 1), false);
    assert.equal(geometryContainsPoint(geometry, 1, -91), false);
});

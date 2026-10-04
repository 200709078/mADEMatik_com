export const STORAGE_PREFIX = 'madematik.clockTimer.';

export function createStorage(getStorage = () => globalThis.localStorage) {
    const memory = new Map();
    const pendingWrites = new Set();

    return {
        read(name, fallback = null) {
            const key = STORAGE_PREFIX + name;
            let serialized = memory.get(key);

            try {
                if (!pendingWrites.has(key)) {
                    const storage = getStorage();

                    if (storage) {
                        serialized = storage.getItem(key);

                        if (serialized === null) {
                            memory.delete(key);
                        }
                    }
                }
            } catch {
                // A blocked storage getter must not prevent the in-memory tool from working.
            }

            if (typeof serialized !== 'string') {
                return fallback;
            }

            try {
                const value = JSON.parse(serialized);
                memory.set(key, serialized);

                return value;
            } catch {
                memory.delete(key);

                return fallback;
            }
        },

        write(name, value) {
            const key = STORAGE_PREFIX + name;
            let serialized;

            try {
                serialized = JSON.stringify(value);
            } catch {
                return false;
            }

            if (typeof serialized !== 'string') {
                return false;
            }

            memory.set(key, serialized);
            pendingWrites.add(key);

            try {
                const storage = getStorage();

                if (!storage) {
                    return false;
                }

                storage.setItem(key, serialized);
                pendingWrites.delete(key);

                return true;
            } catch {
                return false;
            }
        },
    };
}

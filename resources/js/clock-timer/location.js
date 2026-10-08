export function createDeviceLocation(onChange, environment = globalThis) {
    let disposed = false;
    let requestVersion = 0;
    let requestInProgress = false;
    let permissionPromptRequested = false;
    let permission;
    let removePermissionListener = () => {};

    function isCurrent(version) {
        return !disposed && version === requestVersion;
    }

    function emit(status, location = null) {
        if (!disposed) {
            onChange({ status, location });
        }
    }

    function unsubscribePermission() {
        try {
            removePermissionListener();
        } catch {
            // Permission support is secondary to the clock and timer.
        }
        removePermissionListener = () => {};
        permission = undefined;
    }

    function acquireLocation(currentPermission, version, allowPermissionRequest = false) {
        if (!isCurrent(version)) {
            return;
        }
        const requestingPermission = currentPermission?.state !== 'granted';
        if (currentPermission?.state === 'denied'
            || (requestingPermission && !allowPermissionRequest)) {
            emit('unavailable');
            return;
        }
        if (requestingPermission && permissionPromptRequested) {
            return;
        }

        emit('locating');
        if (!isCurrent(version)) {
            return;
        }
        if (currentPermission?.state === 'denied'
            || (!requestingPermission && currentPermission?.state !== 'granted')) {
            emit('unavailable');
            return;
        }
        if (requestingPermission) {
            permissionPromptRequested = true;
        }
        requestInProgress = true;
        let settled = false;
        const complete = (location = null) => {
            if (!isCurrent(version) || settled) {
                return;
            }
            settled = true;
            requestInProgress = false;
            if (currentPermission?.state === 'denied'
                || (!requestingPermission && currentPermission?.state !== 'granted')) {
                emit('unavailable');
                return;
            }
            emit(location ? 'available' : 'unavailable', location);
        };

        try {
            environment.navigator.geolocation.getCurrentPosition((position) => {
                if (!isCurrent(version) || settled) {
                    return;
                }
                const latitude = position?.coords?.latitude;
                const longitude = position?.coords?.longitude;
                if (!Number.isFinite(latitude) || Math.abs(latitude) > 90
                    || !Number.isFinite(longitude) || Math.abs(longitude) > 180) {
                    complete();
                    return;
                }

                try {
                    const deviceIntl = environment.Intl ?? Intl;
                    const timeZone = new deviceIntl.DateTimeFormat().resolvedOptions().timeZone;
                    if (typeof timeZone !== 'string' || !timeZone) {
                        complete();
                        return;
                    }
                    complete({ latitude, longitude, timeZone });
                } catch {
                    complete();
                }
            }, () => complete(), {
                enableHighAccuracy: false,
                timeout: 10_000,
                maximumAge: 300_000,
            });
        } catch {
            complete();
        }
    }

    function subscribePermission(currentPermission) {
        permission = currentPermission;
        const onPermissionChange = () => {
            if (!disposed && permission === currentPermission) {
                if (currentPermission.state === 'granted' && requestInProgress) {
                    return;
                }
                requestInProgress = false;
                acquireLocation(currentPermission, ++requestVersion);
            }
        };

        try {
            if (typeof currentPermission.addEventListener === 'function') {
                currentPermission.addEventListener('change', onPermissionChange);
                removePermissionListener = () => currentPermission.removeEventListener('change', onPermissionChange);
            } else {
                const previousListener = currentPermission.onchange;
                currentPermission.onchange = onPermissionChange;
                removePermissionListener = () => {
                    if (currentPermission.onchange === onPermissionChange) {
                        currentPermission.onchange = previousListener;
                    }
                };
            }
        } catch {
            // A later visibility refresh can recheck unsupported permission events.
        }
    }

    return {
        async refresh() {
            if (disposed || requestInProgress) {
                return;
            }
            const version = ++requestVersion;
            unsubscribePermission();

            try {
                const deviceNavigator = environment.navigator;
                if (environment.isSecureContext !== true
                    || typeof deviceNavigator?.geolocation?.getCurrentPosition !== 'function') {
                    emit('unavailable');
                    return;
                }

                let currentPermission;
                try {
                    currentPermission = await deviceNavigator.permissions?.query?.({ name: 'geolocation' });
                } catch {
                    // Geolocation can request consent when permission queries are unsupported.
                }
                if (!isCurrent(version)) {
                    return;
                }
                if (!currentPermission || !['granted', 'prompt', 'denied'].includes(currentPermission.state)) {
                    acquireLocation(null, version, true);
                    return;
                }

                subscribePermission(currentPermission);
                acquireLocation(currentPermission, version, true);
            } catch {
                if (isCurrent(version)) {
                    emit('unavailable');
                }
            }
        },
        dispose() {
            disposed = true;
            requestInProgress = false;
            requestVersion++;
            unsubscribePermission();
        },
    };
}

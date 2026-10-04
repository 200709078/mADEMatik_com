export function createBrowserFeatures(element) {
    let audioContext;

    return {
        supportsFullscreen: () => Boolean(document.fullscreenEnabled && element.requestFullscreen),
        isFullscreen: () => document.fullscreenElement === element,
        async toggleFullscreen() {
            if (document.fullscreenElement) {
                await document.exitFullscreen();
            } else if (document.fullscreenEnabled && element.requestFullscreen) {
                await element.requestFullscreen();
            }
        },
        async enableSound() {
            try {
                const AudioContext = window.AudioContext ?? window.webkitAudioContext;
                if (!AudioContext) {
                    return false;
                }
                audioContext ??= new AudioContext();
                await audioContext.resume();
                return audioContext.state === 'running';
            } catch {
                return false;
            }
        },
        playCompletion() {
            if (audioContext?.state !== 'running') {
                return;
            }
            try {
                const oscillator = audioContext.createOscillator();
                const gain = audioContext.createGain();
                const now = audioContext.currentTime;
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(660, now);
                oscillator.frequency.setValueAtTime(880, now + 0.18);
                gain.gain.setValueAtTime(0, now);
                gain.gain.linearRampToValueAtTime(0.12, now + 0.02);
                gain.gain.linearRampToValueAtTime(0, now + 0.55);
                oscillator.connect(gain);
                gain.connect(audioContext.destination);
                oscillator.start(now);
                oscillator.stop(now + 0.6);
                oscillator.onended = () => {
                    oscillator.disconnect();
                    gain.disconnect();
                };
            } catch {
                // Sound is secondary: device/audio errors must not interrupt the timer.
            }
        },
    };
}

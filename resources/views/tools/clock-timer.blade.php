<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'en' ? 'en' : 'tr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0085a1">
    <title>{{ app()->getLocale() === 'en' ? 'Clock & Timer' : 'Saat & Sayaç' }} · mADEMatik</title>
    <link rel="icon" type="image/png" href="{{ asset('img/adem-varol-favicon.png') }}">
    @vite('resources/js/clock-timer/index.js')
</head>
<body class="clock-timer" id="clock-timer" data-language="{{ app()->getLocale() === 'en' ? 'en' : 'tr' }}">
    <header class="app-header">
        <div class="header-inner">
            <div class="brand">
                <img src="{{ asset('img/adem-varol-logo.png') }}" width="58" height="48" alt="" aria-hidden="true">
                <div>
                    <span class="brand-name">mADEMatik</span>
                    <h1 data-i18n="toolName">Saat &amp; Sayaç</h1>
                </div>
            </div>

            <nav class="view-navigation" aria-label="Saat ve sayaç" data-i18n-label="toolName">
                <button type="button" data-view="clock" aria-pressed="true" aria-controls="clock-panel" data-i18n="clockNav">SAAT</button>
                <button type="button" data-view="timer" aria-pressed="false" aria-controls="timer-panel" data-i18n="timerNav">SAYAÇ</button>
                <a href="{{ route('homepage') }}" data-i18n="homeNav">ANA SAYFA</a>
            </nav>

            <div class="language-switch" role="group" aria-label="Dil seçimi" data-i18n-label="language">
                <button type="button" data-language="tr" aria-pressed="true" lang="tr">TR</button>
                <span aria-hidden="true">/</span>
                <button type="button" data-language="en" aria-pressed="false" lang="en">EN</button>
            </div>
        </div>
    </header>

    <main class="app-main">
        <div class="workspace">
            <section id="clock-panel" aria-labelledby="clock-heading">
                <div class="display-surface clock-surface">
                    <h2 class="eyebrow" id="clock-heading" data-i18n="clockHeading">Şimdi</h2>
                    <div class="time-display" id="clock-display" role="timer" aria-label="Saat" data-i18n-label="clock">00:00:00</div>
                    <div class="calendar-information">
                        <p id="calendar-date">—</p>
                        <p id="calendar-week">—</p>
                    </div>
                    <p class="local-time-label" data-i18n="localTime">Cihazınızın yerel saati</p>
                </div>

                <div class="secondary-information">
                    <div class="sun-information">
                        <div class="location-field">
                            <label for="location-select" data-i18n="location">Güneş bilgileri için konum</label>
                            <select id="location-select"></select>
                        </div>
                        <dl class="sun-times">
                            <div>
                                <dt>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 19h18M5 16a7 7 0 0 1 14 0M12 2v7m-3-4 3-3 3 3M2 12l2 1m16 0 2-1"/></svg>
                                    <span data-i18n="sunrise">Gün doğumu</span>
                                </dt>
                                <dd id="sunrise-time">—</dd>
                            </div>
                            <div>
                                <dt>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 19h18M5 16a7 7 0 0 1 14 0M12 2v7m-3-3 3 3 3-3M2 12l2 1m16 0 2-1"/></svg>
                                    <span data-i18n="sunset">Gün batımı</span>
                                </dt>
                                <dd id="sunset-time">—</dd>
                            </div>
                        </dl>
                    </div>
                    <p class="secondary-message" id="solar-message" role="status" hidden></p>
                    <div class="world-information">
                        <h3 class="eyebrow" data-i18n="worldClocks">Dünya saatleri</h3>
                        <div class="world-clocks" id="world-clocks"></div>
                    </div>
                </div>
            </section>

            <section id="timer-panel" aria-labelledby="timer-heading" hidden>
                <div class="display-surface timer-surface">
                    <h2 class="eyebrow" id="timer-heading" data-i18n="timerHeading">Bir işe zaman ayırın</h2>
                    <div class="time-display" id="timer-display" role="timer" aria-label="Geri Sayım Sayacı" data-i18n-label="timer">05:00</div>
                    <p class="timer-status" id="timer-status" role="status" aria-live="polite" data-i18n="idle">Hazır</p>
                    <progress id="timer-progress" class="timer-progress" max="1" value="1" aria-label="Geri Sayım Sayacı" data-i18n-label="timer"></progress>

                    <div class="timer-actions">
                        <button type="button" class="button button-primary" id="timer-start" data-i18n="start">Başlat</button>
                        <button type="button" class="button" id="timer-pause" data-i18n="pause" disabled hidden>Duraklat</button>
                        <button type="button" class="button" id="timer-resume" data-i18n="resume" disabled hidden>Devam Et</button>
                        <button type="button" class="button" id="timer-reset" data-i18n="reset">Sıfırla</button>
                    </div>
                </div>

                <div class="duration-settings">
                    <form class="duration-form" id="duration-form">
                        <div class="duration-fields">
                            <div>
                                <label for="duration-hours" data-i18n="hours">Saat</label>
                                <input id="duration-hours" name="hours" type="number" inputmode="numeric" min="0" max="99" step="1" value="0" required>
                            </div>
                            <span class="duration-separator" aria-hidden="true">:</span>
                            <div>
                                <label for="duration-minutes" data-i18n="minutes">Dakika</label>
                                <input id="duration-minutes" name="minutes" type="number" inputmode="numeric" min="0" max="59" step="1" value="5" required>
                            </div>
                            <span class="duration-separator" aria-hidden="true">:</span>
                            <div>
                                <label for="duration-seconds" data-i18n="seconds">Saniye</label>
                                <input id="duration-seconds" name="seconds" type="number" inputmode="numeric" min="0" max="59" step="1" value="0" required>
                            </div>
                        </div>
                        <button type="submit" class="button" data-i18n="setDuration">Süreyi ayarla</button>
                    </form>
                    <div class="preset-settings">
                        <p class="settings-label" data-i18n="quickDurations">Hazır süreler</p>
                        <div class="preset-buttons" role="group" aria-label="Hazır süreler" data-i18n-label="quickDurations">
                            <button type="button" class="button preset-button" data-preset="1">1 dk</button>
                            <button type="button" class="button preset-button" data-preset="5">5 dk</button>
                            <button type="button" class="button preset-button" data-preset="10">10 dk</button>
                            <button type="button" class="button preset-button" data-preset="15">15 dk</button>
                            <button type="button" class="button preset-button" data-preset="20">20 dk</button>
                            <button type="button" class="button preset-button" data-preset="40">40 dk</button>
                        </div>
                    </div>
                    <div class="timer-preferences">
                        <p data-i18n="timerHint">Süreyi seçin, başlatın. Sayfayı kapatsanız da sayacınız devam eder.</p>
                        <button type="button" class="button button-subtle" id="sound-toggle" aria-pressed="false" data-i18n="soundOff">Ses kapalı</button>
                    </div>
                </div>
            </section>

            <div class="display-tools">
                <div class="zoom-controls" role="group" aria-label="Görüntü boyutu" data-i18n-label="zoom">
                    <button type="button" class="button button-subtle" id="zoom-out" data-i18n="zoomOut">Zoom −</button>
                    <output id="zoom-value" aria-live="polite">100%</output>
                    <button type="button" class="button button-subtle" id="zoom-in" data-i18n="zoomIn">Zoom +</button>
                </div>
                <button type="button" class="button" id="fullscreen-toggle" data-i18n="fullscreen">Tam ekran</button>
            </div>
        </div>

        <p class="app-message" id="app-message" role="status" hidden></p>
        <noscript><p class="app-message" data-i18n="noScript">Saat ve sayaç için tarayıcınızda JavaScript’i etkinleştirin.</p></noscript>
    </main>

    <footer class="app-footer">
        <span class="footer-mark" aria-hidden="true"></span>
        <p data-i18n="classroom">Sınıfınız için, zamanı sade tutun.</p>
    </footer>
</body>
</html>

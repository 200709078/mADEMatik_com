{{--
    mADEMatik toast bileşeni (OAL_kelebek AppLayout toast sisteminin Blade uyarlaması).

    Kaynaklar:
      - session('success') : Controller'lardaki ->with('success', ...) flash mesajları
      - session('error')   : Controller'lardaki ->with('error', ...) flash mesajları
      - $errors            : $request->validate() doğrulama hataları

    Harici paket gerektirmez. Kullanım: </body>'den hemen önce @include('layouts.toast')
--}}
@php
    $kelebekToasts = [];
    if (session('success')) {
        $kelebekToasts[] = ['type' => 'success', 'title' => '', 'messages' => [(string) session('success')]];
    }
    if (session('error')) {
        $kelebekToasts[] = ['type' => 'error', 'title' => '', 'messages' => [(string) session('error')]];
    }
    if (isset($errors) && $errors->any()) {
        $kelebekToasts[] = ['type' => 'error', 'title' => 'Hata', 'messages' => $errors->all()];
    }
@endphp

@if (count($kelebekToasts) > 0)
    <style>
        @keyframes madematik-toast-in {
            from { opacity: 0; transform: translateY(0.75rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        #madematik-toasts {
            position: fixed; bottom: 1rem; right: 1rem; z-index: 9999;
            display: flex; flex-direction: column; gap: 0.75rem;
            width: min(22rem, calc(100vw - 2rem));
        }
        .madematik-toast {
            display: flex; align-items: flex-start; gap: 0.75rem;
            background: #fff; border-left: 4px solid #16a34a; border-radius: 0.5rem;
            padding: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,.18);
            font-size: 0.875rem; line-height: 1.4; color: #111827;
            animation: madematik-toast-in 250ms ease;
        }
        .madematik-toast.toast-error { border-left-color: #dc2626; }
        .madematik-toast svg { flex-shrink: 0; width: 1.25rem; height: 1.25rem; }
        .madematik-toast .toast-success-icon { color: #16a34a; }
        .madematik-toast .toast-error-icon { color: #dc2626; }
        .madematik-toast .toast-body { flex: 1; min-width: 0; }
        .madematik-toast .toast-title { font-weight: 600; margin-bottom: 0.25rem; }
        .madematik-toast ul { margin: 0; padding-left: 1rem; }
        .madematik-toast .toast-close {
            flex-shrink: 0; border: 0; background: transparent; cursor: pointer;
            color: #9ca3af; padding: 0.1rem; line-height: 1;
        }
        .madematik-toast .toast-close:hover { color: #dc2626; }
        .madematik-toast .toast-close svg { width: 1.1rem; height: 1.1rem; }
    </style>
    <div id="madematik-toasts" aria-live="polite">
        @foreach ($kelebekToasts as $index => $toast)
            <div class="madematik-toast {{ $toast['type'] === 'success' ? 'toast-success' : 'toast-error' }}" role="status" id="madematik-toast-{{ $index }}">
                @if ($toast['type'] === 'success')
                    <svg class="toast-success-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @else
                    <svg class="toast-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
                <div class="toast-body">
                    @if ($toast['title'] !== '')
                        <div class="toast-title">{{ $toast['title'] }}</div>
                    @endif
                    @if (count($toast['messages']) > 1)
                        <ul>
                            @foreach ($toast['messages'] as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div>{{ $toast['messages'][0] }}</div>
                    @endif
                </div>
                <button type="button" class="toast-close" title="Kapat" aria-label="Kapat" onclick="document.getElementById('madematik-toast-{{ $index }}').remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endforeach
    </div>
    <script>
        (function () {
            setTimeout(function () {
                var el = document.getElementById('madematik-toasts');
                if (el) el.remove();
            }, 6000);
        })();
    </script>
@endif

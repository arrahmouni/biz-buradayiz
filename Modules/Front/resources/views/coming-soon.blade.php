@php
    $websiteLaunchDate = getSetting('website_launch_date');
    $launchAtMs = null;
    if (! empty($websiteLaunchDate)) {
        try {
            $launchAtMs = \Illuminate\Support\Carbon::parse($websiteLaunchDate)
                ->timezone(config('app.timezone'))
                ->startOfDay()
                ->getTimestamp() * 1000;
        } catch (\Throwable) {
            $launchAtMs = null;
        }
    }
    $logoUrl = getSetting('web_logo', asset('images/default/logos/web_logo.svg'));
    $faviconUrl = getSetting('app_favicon', asset('images/default/logos/favicon.png'));
    $features = __('coming_soon.features');
    if (! is_array($features)) {
        $features = [];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur'], true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ $faviconUrl }}" type="image/png">
    <title>{{ __('coming_soon.page_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative min-h-screen overflow-x-hidden antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-rose-50/30 to-red-100/90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-white/80 via-transparent to-transparent"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_120%_80%_at_100%_0%,rgba(220,38,38,0.14),transparent_55%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_90%_70%_at_0%_100%,rgba(252,165,165,0.35),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_50%,rgba(255,255,255,0.5),transparent_70%)]"></div>
    </div>
    <div class="relative z-0 flex min-h-screen items-center justify-center p-6">
        <div class="grid w-full max-w-6xl items-center gap-8 lg:grid-cols-2">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-3 rounded-2xl border border-red-100 bg-white px-5 py-4 shadow-xl">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-4 border-red-100 bg-red-50 text-2xl font-bold text-red-600" aria-hidden="true">&#128295;</div>
                    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="h-10 w-auto max-w-[200px] object-contain">
                </div>

                <div>
                    <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-red-600">{{ __('coming_soon.badge_label') }}</p>
                    <h1 class="mb-6 text-4xl font-black leading-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-7xl">
                        {{ __('coming_soon.heading') }}
                    </h1>
                    <p class="max-w-xl text-lg leading-relaxed text-gray-600 md:text-xl">
                        {{ __('coming_soon.tagline') }}
                    </p>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-gray-600 md:text-lg">
                        {{ __('coming_soon.description') }}
                    </p>
                </div>

                @if ($launchAtMs !== null)
                    <div id="coming-soon-countdown" data-launch-ms="{{ $launchAtMs }}" class="space-y-4">
                        <div id="countdown-active" class="flex flex-wrap gap-4">
                            <div class="countdown-box min-w-[110px] rounded-3xl border border-red-100 bg-white px-6 py-7 text-center shadow-xl">
                                <div class="countdown-days text-4xl font-black text-red-600 tabular-nums">00</div>
                                <div class="mt-2 text-sm text-gray-500">{{ __('coming_soon.days') }}</div>
                            </div>
                            <div class="countdown-box min-w-[110px] rounded-3xl border border-red-100 bg-white px-6 py-7 text-center shadow-xl">
                                <div class="countdown-hours text-4xl font-black text-red-600 tabular-nums">00</div>
                                <div class="mt-2 text-sm text-gray-500">{{ __('coming_soon.hours') }}</div>
                            </div>
                            <div class="countdown-box min-w-[110px] rounded-3xl border border-red-100 bg-white px-6 py-7 text-center shadow-xl">
                                <div class="countdown-minutes text-4xl font-black text-red-600 tabular-nums">00</div>
                                <div class="mt-2 text-sm text-gray-500">{{ __('coming_soon.minutes') }}</div>
                            </div>
                            <div class="countdown-box min-w-[110px] rounded-3xl border border-red-100 bg-white px-6 py-7 text-center shadow-xl">
                                <div class="countdown-seconds text-4xl font-black text-red-600 tabular-nums">00</div>
                                <div class="mt-2 text-sm text-gray-500">{{ __('coming_soon.seconds') }}</div>
                            </div>
                        </div>
                        <p id="countdown-launched" class="hidden rounded-3xl border border-green-200 bg-green-50 px-6 py-4 text-center font-semibold text-green-800">
                            {{ __('coming_soon.launched') }}
                        </p>
                    </div>
                @endif

                @if (count($features) > 0)
                    <div class="flex flex-wrap gap-4 text-sm font-medium text-gray-700">
                        @foreach ($features as $feature)
                            <span class="rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-md">{{ $feature }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="relative">
                <div class="rounded-[32px] border border-gray-100 bg-white p-8 shadow-2xl">
                    <div class="relative flex aspect-square items-center justify-center overflow-hidden rounded-[28px] bg-gradient-to-br from-slate-900 via-red-950 to-red-800">
                        <div class="pointer-events-none absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_30%_20%,rgba(252,165,165,0.4),transparent_40%)]"></div>
                        <div class="relative z-10 p-8 text-center text-white">
                            <div class="mb-6 text-7xl sm:text-8xl" aria-hidden="true">&#128666;</div>
                            <h2 class="mb-4 text-3xl font-bold sm:text-4xl">{{ __('coming_soon.card_title') }}</h2>
                            <p class="text-base opacity-90 sm:text-lg">{{ __('coming_soon.card_subtitle') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($launchAtMs !== null)
        <script>
            (function () {
                var root = document.getElementById('coming-soon-countdown');
                if (!root) return;
                var target = parseInt(root.getAttribute('data-launch-ms') || '0', 10);
                if (!target) return;
                var activeEl = document.getElementById('countdown-active');
                var launchedEl = document.getElementById('countdown-launched');
                var dEl = root.querySelector('.countdown-days');
                var hEl = root.querySelector('.countdown-hours');
                var mEl = root.querySelector('.countdown-minutes');
                var sEl = root.querySelector('.countdown-seconds');

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function tick() {
                    var now = Date.now();
                    var diff = target - now;
                    if (diff <= 0) {
                        if (activeEl) activeEl.classList.add('hidden');
                        if (launchedEl) launchedEl.classList.remove('hidden');
                        return;
                    }
                    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                    var minutes = Math.floor((diff / (1000 * 60)) % 60);
                    var seconds = Math.floor((diff / 1000) % 60);
                    if (dEl) dEl.textContent = pad(days);
                    if (hEl) hEl.textContent = pad(hours);
                    if (mEl) mEl.textContent = pad(minutes);
                    if (sEl) sEl.textContent = pad(seconds);
                }

                tick();
                setInterval(tick, 1000);
            })();
        </script>
    @endif
</body>
</html>

<div class="text-center">
    <a href="{{ route('front.index') }}" class="inline-block">
        <img src="{{ getSetting(\Modules\Config\Constatnt::WEB_LOGO, asset('images/default/logos/web_logo.svg')) }}" alt="{{ config('app.name') }}" class="h-12 mx-auto mb-4">
    </a>
    <h2 class="text-3xl font-extrabold text-gray-900">{{ $title }}</h2>
    @if (isset($subtitle) && filled($subtitle))
        <p class="mt-2 text-sm text-gray-600">{{ $subtitle }}</p>
    @endif
</div>

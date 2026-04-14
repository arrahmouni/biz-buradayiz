@props([
    'package',
    'billingLabel' => '',
    'connectionsLabel' => '',
    'centerLonePopular' => false,
])

@php
    $isPopular = (bool) $package->is_popular;
    $featureLines = $package->features_segments ?? [];
    if (! is_array($featureLines)) {
        $featureLines = [];
    }
    $currencySymbol = getCurrencySymbol($package->currency);
@endphp

<div @class([
    'bg-white rounded-2xl shadow-lg border-2 transition-all duration-300 flex flex-col hover:shadow-xl hover:-translate-y-0.5',
    'border-red-200 ring-2 ring-red-500 scale-105 md:scale-100 z-10 hover:border-red-500 hover:ring-red-600 order-first md:order-none' => $isPopular,
    'border-transparent hover:border-red-400' => ! $isPopular,
    'lg:col-start-2' => $centerLonePopular,
])>
    @if ($isPopular)
        <div class="relative">
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-red-600 text-white text-xs font-bold px-4 py-1 rounded-full shadow-md">
                {{ __('front::provider_register.package_popular') }}
            </div>
        </div>
    @endif
    <div class="p-6 flex-1 flex flex-col {{ $isPopular ? 'pt-8' : '' }}">
        <h3 class="text-xl font-bold text-gray-800">{{ $package->smartTrans('name') ?: ('#'.$package->id) }}</h3>
        <div class="mt-4 flex items-baseline flex-wrap gap-x-1">
            <span class="text-4xl font-extrabold text-gray-900">{{ $currencySymbol }}{{ number_format((float) $package->price, 2) }}</span>
            @if (filled($billingLabel))
                <span class="text-gray-500 text-sm font-semibold">/</span>
                <span class="text-gray-500 text-sm">{{ $billingLabel }}</span>
            @endif
        </div>
        @if (filled($package->smartTrans('description')))
            <p class="text-gray-500 text-sm mt-2">{{ $package->smartTrans('description') }}</p>
        @endif
        @if (filled($connectionsLabel))
            <p class="text-gray-600 text-sm mt-2 flex items-center gap-1"><i class="fas fa-exchange-alt text-red-400"></i> {{ $connectionsLabel }}</p>
        @endif
        @if ($featureLines !== [])
            <ul class="mt-6 space-y-3 flex-1">
                @foreach ($featureLines as $line)
                    <li class="flex items-start gap-2 text-gray-600 text-sm">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                        <span>{{ $line }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="px-6 pb-6">
        {{ $slot }}
    </div>
</div>

{{-- @include: $icon, $code, $titleKey, $messageKey --}}
<div class="flex flex-col items-center text-center md:items-start md:text-left">
    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-2xl text-red-600 ring-1 ring-red-100/80">
        <i class="fas {{ $icon }}" aria-hidden="true"></i>
    </div>
    <p class="text-xs font-bold uppercase tracking-[0.2em] text-red-600">{{ $code }}</p>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
        {{ __($titleKey) }}
    </h1>
    <p class="mt-4 max-w-md text-base leading-relaxed text-gray-600 md:text-lg">
        {{ __($messageKey) }}
    </p>
    <div class="mt-8 flex w-full flex-wrap items-center justify-center gap-3 md:justify-start">
        <a
            href="{{ route('front.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-600/25 transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"
        >
            <i class="fas fa-home" aria-hidden="true"></i>
            {{ __('front::error.cta_home') }}
        </a>
    </div>
    <p class="mt-10 text-sm text-gray-500">{!! trans('front::error.footer') !!}</p>
</div>

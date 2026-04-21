@props([
    'heading' => '',
    'breadcrumbLabel' => null,
    'headingTag' => 'h1',
])

@php
    $resolvedHeadingTag = in_array($headingTag, ['h1', 'h2', 'h3', 'p'], true) ? $headingTag : 'h1';
@endphp

<div class="relative overflow-hidden bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 md:py-16">
    <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 front-cta-dot-pattern"></div>
    </div>
    <div class="container mx-auto px-5 lg:px-8 relative z-10">
        <div class="max-w-3xl js-front-reveal front-reveal">
            @isset($breadcrumb)
                <nav
                    class="text-sm text-gray-300 mb-3"
                    @if ($breadcrumbLabel)
                        aria-label="{{ $breadcrumbLabel }}"
                    @endif
                >
                    {{ $breadcrumb }}
                </nav>
            @endisset

            <{{ $resolvedHeadingTag }} class="text-3xl md:text-5xl font-extrabold tracking-tight">
                {{ $heading }}
            </{{ $resolvedHeadingTag }}>
            <div class="w-16 h-1 bg-red-500 mt-4 rounded-full"></div>

            {{ $belowDivider ?? '' }}
        </div>
    </div>
</div>

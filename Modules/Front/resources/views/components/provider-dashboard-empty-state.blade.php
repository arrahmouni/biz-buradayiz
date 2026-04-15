@props([
    'message' => '',
    'tone' => 'neutral',
    'embedded' => false,
])

@php
    $isAmber = $tone === 'amber';
    $iconWrap = $isAmber
        ? 'bg-amber-100 text-amber-700'
        : 'bg-gray-200/80 text-gray-500';
    $textClass = $isAmber ? 'text-amber-950/80' : 'text-gray-600';

    if ($embedded) {
        $frame = '';
    } else {
        $frame = $isAmber
            ? 'rounded-xl border border-dashed border-amber-200/80 bg-amber-50/50'
            : 'rounded-xl border border-dashed border-gray-200 bg-gray-50/80';
    }
@endphp

<div {{ $attributes->class([
    'flex flex-col items-center justify-center gap-3 px-6 py-10 text-center',
    $frame,
]) }}>
    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $iconWrap }}" aria-hidden="true">
        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M2.25 13.5H6.10942C6.96166 13.5 7.74075 13.9815 8.12188 14.7438L8.37812 15.2562C8.75925 16.0185 9.53834 16.5 10.3906 16.5H13.6094C14.4617 16.5 15.2408 16.0185 15.6219 15.2562L15.8781 14.7438C16.2592 13.9815 17.0383 13.5 17.8906 13.5H21.75M2.25 13.8383V18C2.25 19.2426 3.25736 20.25 4.5 20.25H19.5C20.7426 20.25 21.75 19.2426 21.75 18V13.8383C21.75 13.614 21.7165 13.391 21.6505 13.1766L19.2387 5.33831C18.9482 4.39423 18.076 3.75 17.0882 3.75H6.91179C5.92403 3.75 5.05178 4.39423 4.76129 5.33831L2.3495 13.1766C2.28354 13.391 2.25 13.614 2.25 13.8383Z" />
        </svg>
    </span>
    <p class="max-w-md text-sm {{ $textClass }}">{{ $message }}</p>
</div>

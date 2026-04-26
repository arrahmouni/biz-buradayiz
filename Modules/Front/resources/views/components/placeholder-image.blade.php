@props(['src' => ''])
@php
    $placeholder = app_placeholder_url();
    $resolved = ($src === null || $src === '') ? $placeholder : (string) $src;
    $onErrorHandler = 'this.onerror=null;this.src='.(string) \Illuminate\Support\Js::from($placeholder);
@endphp
<img
    src="{{ $resolved }}"
    onerror="{{ $onErrorHandler }}"
    {{ $attributes }}
>

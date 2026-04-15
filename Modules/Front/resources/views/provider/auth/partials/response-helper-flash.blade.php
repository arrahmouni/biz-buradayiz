@php
    $payload = session('message');
    $isResponseHelper = is_array($payload) && array_key_exists('success', $payload);
    $msg = $isResponseHelper ? ($payload['message'] ?? null) : null;
    $description = is_array($msg) ? ($msg['description'] ?? null) : null;
    $title = is_array($msg) ? ($msg['title'] ?? null) : null;
    $isError = $isResponseHelper && ($payload['success'] ?? true) === false && filled($description);
    $isSuccess = $isResponseHelper && ($payload['success'] ?? false) === true && filled($description);
@endphp
@if ($isError)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        @if (filled($title))
            <p class="font-semibold text-red-900">{{ $title }}</p>
        @endif
        <p class="{{ filled($title) ? 'mt-1' : '' }}">{{ $description }}</p>
    </div>
@elseif ($isSuccess)
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
        @if (filled($title))
            <p class="font-semibold text-emerald-900">{{ $title }}</p>
        @endif
        <p class="{{ filled($title) ? 'mt-1' : '' }}">{{ $description }}</p>
    </div>
@endif

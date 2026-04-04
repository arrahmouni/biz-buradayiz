@if(isset($item['title']) && !empty($item['title']))
    <span class="menu-title">
        {{ $item['title'] }}
        @if(($item['badge_count'] ?? 0) > 0)
            <span class="badge badge-sm badge-light-primary fw-semibold ms-2">{{ $item['badge_count'] }}</span>
        @endif
    </span>
@endif

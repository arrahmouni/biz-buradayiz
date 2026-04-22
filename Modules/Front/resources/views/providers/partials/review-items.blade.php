@foreach ($reviews as $review)
    <li class="py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
            <div class="min-w-0 flex flex-col flex-wrap items-start gap-x-3 gap-y-1.5">
                @if (filled($review->reviewer_display_name))
                    <span class="text-sm font-semibold text-gray-900">{{ $review->reviewer_display_name }}</span>
                @endif
                <span
                    class="inline-flex items-center gap-0.5 text-yellow-400"
                    role="img"
                    aria-label="{{ __('front::home.provider_detail_review_list_rating_aria', ['n' => (int) $review->rating]) }}"
                >
                    @for ($s = 1; $s <= 5; $s++)
                        <i class="{{ $s <= (int) $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star text-sm sm:text-[0.9375rem]" aria-hidden="true"></i>
                    @endfor
                </span>
            </div>
            @if ($review->created_at)
                <time
                    datetime="{{ $review->created_at->toIso8601String() }}"
                    class="shrink-0 text-xs font-medium text-gray-500 tabular-nums sm:pt-0.5 sm:text-right"
                >
                    {{ $review->created_at->isoFormat('LL') }}
                </time>
            @endif
        </div>
        @if (filled($review->body))
            <p class="mt-4 border-l-2 border-red-100 pl-4 text-sm leading-relaxed text-gray-700">{{ $review->body }}</p>
        @endif
    </li>
@endforeach


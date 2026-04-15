<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Models\Review;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class ReviewSubmissionService
{
    /**
     * Persist a review. Structural validation (phone length, rating range, etc.) is expected to be done by the caller's Form Request when used from HTTP.
     *
     * @param  array{user_id: int, phone: string, rating: int, body?: string|null, reviewer_display_name?: string|null}  $input
     */
    public function submit(array $input): Review
    {
        $provider = User::active()->findOrFail($input['user_id']);
        $normalized = VerimorPhoneNormalizer::canonicalize($input['phone']);

        return DB::transaction(function () use ($provider, $normalized, $input) {
            $event = VerimorCallEvent::query()
                ->where('user_id', $provider->id)
                ->where('direction', VerimorCallDirection::Inbound)
                ->where('answered', true)
                ->where('caller_number_normalized', $normalized)
                ->whereDoesntHave('review')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($event === null) {
                throw ValidationException::withMessages([
                    'phone' => [trans('platform::reviews.submission.no_matching_call')],
                ]);
            }

            return Review::query()->create([
                'user_id' => $provider->id,
                'verimor_call_event_id' => $event->id,
                'rating' => (int) $input['rating'],
                'status' => ReviewStatus::Pending,
                'body' => $input['body'] ?? null,
                'reviewer_display_name' => $input['reviewer_display_name'] ?? null,
                'reviewer_phone_normalized' => $normalized,
            ]);
        });
    }
}

<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Models\Review;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class ReviewSubmissionService
{
    /**
     * @param  array{user_id: int, phone: string, rating: int, body?: string|null, reviewer_display_name?: string|null}  $input
     */
    public function submit(array $input): Review
    {
        $validated = validator($input, [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'phone' => ['required', 'string', 'max:64'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:5000'],
            'reviewer_display_name' => ['nullable', 'string', 'max:120'],
        ])->validate();

        $provider = User::query()->findOrFail($validated['user_id']);
        if ($provider->type !== UserType::ServiceProvider) {
            throw ValidationException::withMessages([
                'user_id' => [trans('platform::reviews.submission.invalid_provider_type')],
            ]);
        }

        $normalized = VerimorPhoneNormalizer::canonicalize($validated['phone']);
        if ($normalized === '') {
            throw ValidationException::withMessages([
                'phone' => [trans('platform::reviews.submission.invalid_phone')],
            ]);
        }

        return DB::transaction(function () use ($provider, $normalized, $validated) {
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
                'rating' => $validated['rating'],
                'status' => ReviewStatus::Pending,
                'body' => $validated['body'] ?? null,
                'reviewer_display_name' => $validated['reviewer_display_name'] ?? null,
                'reviewer_phone_normalized' => $normalized,
            ]);
        });
    }
}

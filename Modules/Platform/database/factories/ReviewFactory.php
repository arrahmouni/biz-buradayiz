<?php

namespace Modules\Platform\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Models\Review;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'verimor_call_event_id' => null,
            'status' => fake()->randomElement(ReviewStatus::cases()),
            'rating' => fake()->numberBetween(1, 5),
            'body' => fake()->optional(0.8)->paragraph(),
            'reviewer_display_name' => fake()->name(),
            'reviewer_phone_normalized' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Review $review) {
            if ($review->user_id === null) {
                $user = User::factory()->create([
                    'type' => UserType::ServiceProvider,
                    'central_phone' => '+90555'.fake()->unique()->numerify('#######'),
                ]);
                $review->user_id = $user->id;
            }

            if ($review->verimor_call_event_id === null) {
                $user = User::query()->findOrFail($review->user_id);
                $payload = self::createHangupCallForProvider($user);
                $review->verimor_call_event_id = $payload['id'];
                $review->reviewer_phone_normalized = $payload['caller_normalized'];
            }
        });
    }

    public function forUser(User $user): static
    {
        $payload = self::createHangupCallForProvider($user);

        return $this->state(fn () => [
            'user_id' => $user->id,
            'verimor_call_event_id' => $payload['id'],
            'reviewer_phone_normalized' => $payload['caller_normalized'],
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => ReviewStatus::Approved,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => ReviewStatus::Pending,
        ]);
    }

    /**
     * @return array{id: int, caller_normalized: string}
     */
    private static function createHangupCallForProvider(User $user): array
    {
        $destinationNorm = VerimorPhoneNormalizer::canonicalize($user->central_phone);
        $callerNorm = '90530'.fake()->unique()->numerify('########');

        $event = VerimorCallEvent::query()->create([
            'call_uuid' => (string) Str::uuid(),
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => $destinationNorm !== '' ? $destinationNorm : null,
            'caller_number_normalized' => $callerNorm,
            'user_id' => $user->id,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => [
                'caller_id_number' => $callerNorm,
                'destination_number' => $user->central_phone,
            ],
        ]);

        return [
            'id' => $event->id,
            'caller_normalized' => $callerNorm,
        ];
    }
}

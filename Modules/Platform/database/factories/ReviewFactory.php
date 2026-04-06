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
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'central_phone' => '+90555'.fake()->unique()->numerify('#######'),
        ]);

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
            'user_id' => $user->id,
            'verimor_call_event_id' => $event->id,
            'status' => fake()->randomElement(ReviewStatus::cases()),
            'rating' => fake()->numberBetween(1, 5),
            'body' => fake()->optional(0.8)->paragraph(),
            'reviewer_display_name' => fake()->optional(0.5)->firstName(),
            'reviewer_phone_normalized' => $callerNorm,
        ];
    }
}

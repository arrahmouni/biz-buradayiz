<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Http\Services\ReviewSubmissionService;
use Modules\Platform\Models\Review;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;
use Tests\TestCase;

class ReviewSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_review_when_matching_answered_inbound_call_exists(): void
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'central_phone' => '+905551112233',
        ]);

        $callerRaw = '05321234567';
        $callerNorm = VerimorPhoneNormalizer::canonicalize($callerRaw);

        VerimorCallEvent::query()->create([
            'call_uuid' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => VerimorPhoneNormalizer::canonicalize($user->central_phone),
            'caller_number_normalized' => $callerNorm,
            'user_id' => $user->id,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => [],
        ]);

        $service = app(ReviewSubmissionService::class);
        $review = $service->submit([
            'user_id' => $user->id,
            'phone' => $callerRaw,
            'rating' => 5,
            'body' => 'Great service.',
            'reviewer_display_name' => 'Test',
        ]);

        $this->assertInstanceOf(Review::class, $review);
        $this->assertSame(5, (int) $review->rating);
        $this->assertSame('Great service.', $review->body);
        $this->assertSame($callerNorm, $review->reviewer_phone_normalized);
        $this->assertSame(ReviewStatus::Pending, $review->status);
        $this->assertSame(1, Review::query()->count());
    }

    public function test_rejects_when_no_matching_call(): void
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'central_phone' => '+905551112233',
        ]);

        $service = app(ReviewSubmissionService::class);

        $this->expectException(ValidationException::class);

        $service->submit([
            'user_id' => $user->id,
            'phone' => '05320000000',
            'rating' => 4,
        ]);
    }
}

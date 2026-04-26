<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\ReviewStatus;
use Modules\Platform\Http\Services\ReviewSubmissionService;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;
use Tests\TestCase;

class ServiceProviderRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_review_does_not_affect_stored_aggregate(): void
    {
        $user = $this->makeProviderWithInboundCall('05321234567');

        app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05321234567',
            'rating' => 5,
        ]);

        $user->refresh();

        $this->assertSame(0, $user->approved_reviews_count);
        $this->assertNull($user->review_rating_average);
    }

    public function test_approve_review_updates_stored_aggregate(): void
    {
        $user = $this->makeProviderWithInboundCall('05321234567');

        $review = app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05321234567',
            'rating' => 5,
        ]);

        $review->update(['status' => ReviewStatus::Approved]);

        $user->refresh();

        $this->assertSame(1, $user->approved_reviews_count);
        $this->assertEquals('5.00', $user->review_rating_average);
    }

    public function test_reject_review_clears_aggregate_when_only_review(): void
    {
        $user = $this->makeProviderWithInboundCall('05321234567');

        $review = app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05321234567',
            'rating' => 4,
        ]);

        $review->update(['status' => ReviewStatus::Approved]);
        $user->refresh();
        $this->assertSame(1, $user->approved_reviews_count);

        $review->update(['status' => ReviewStatus::Rejected]);

        $user->refresh();

        $this->assertSame(0, $user->approved_reviews_count);
        $this->assertNull($user->review_rating_average);
    }

    public function test_soft_deleting_approved_review_updates_aggregate(): void
    {
        $user = $this->makeProviderWithInboundCall('05321234567');

        $review = app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05321234567',
            'rating' => 5,
        ]);

        $review->update(['status' => ReviewStatus::Approved]);
        $user->refresh();
        $this->assertSame(1, $user->approved_reviews_count);

        $review->delete();

        $user->refresh();

        $this->assertSame(0, $user->approved_reviews_count);
        $this->assertNull($user->review_rating_average);
    }

    public function test_average_uses_only_approved_reviews(): void
    {
        $user = $this->makeProviderWithInboundCall('05321234567');

        $r1 = app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05321234567',
            'rating' => 4,
        ]);
        $r1->update(['status' => ReviewStatus::Approved]);

        $this->makeInboundCallForUser($user, '05339876543');
        $r2 = app(ReviewSubmissionService::class)->submit([
            'user_id' => $user->id,
            'phone' => '05339876543',
            'rating' => 2,
        ]);

        $user->refresh();
        $this->assertSame(1, $user->approved_reviews_count);
        $this->assertEquals('4.00', $user->review_rating_average);

        $r2->update(['status' => ReviewStatus::Approved]);

        $user->refresh();

        $this->assertSame(2, $user->approved_reviews_count);
        $this->assertEquals('3.00', $user->review_rating_average);
    }

    private function makeProviderWithInboundCall(string $callerRaw): User
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'central_phone' => '+905551112233',
        ]);

        $this->makeInboundCallForUser($user, $callerRaw);

        return $user;
    }

    private function makeInboundCallForUser(User $user, string $callerRaw): VerimorCallEvent
    {
        $callerNorm = VerimorPhoneNormalizer::canonicalize($callerRaw);

        return VerimorCallEvent::query()->create([
            'call_uuid' => (string) Str::uuid(),
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
    }
}

<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Jobs\ExpireDuePackageSubscriptionsJob;
use Modules\Platform\Models\PackageSubscription;
use Tests\TestCase;

class ExpireDuePackageSubscriptionsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_active_subscriptions_expired_when_ends_at_has_passed(): void
    {
        Carbon::setTestNow('2026-04-10 12:00:00');

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
        ]);

        $due = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
            'starts_at' => Carbon::parse('2026-03-01 10:00:00'),
            'ends_at' => Carbon::parse('2026-04-09 18:00:00'),
            'cancelled_at' => null,
            'paid_at' => Carbon::parse('2026-03-01 10:00:00'),
            'remaining_connections' => 3,
            'admin_notes' => null,
        ]);

        (new ExpireDuePackageSubscriptionsJob)->handle();

        $due->refresh();
        $this->assertSame(PackageSubscriptionStatus::Expired, $due->status);
    }

    public function test_leaves_active_subscriptions_whose_ends_at_is_still_in_the_future(): void
    {
        Carbon::setTestNow('2026-04-10 12:00:00');

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
        ]);

        $future = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
            'starts_at' => Carbon::parse('2026-04-01 10:00:00'),
            'ends_at' => Carbon::parse('2026-04-15 23:59:59'),
            'cancelled_at' => null,
            'paid_at' => Carbon::parse('2026-04-01 10:00:00'),
            'remaining_connections' => 10,
            'admin_notes' => null,
        ]);

        (new ExpireDuePackageSubscriptionsJob)->handle();

        $future->refresh();
        $this->assertSame(PackageSubscriptionStatus::Active, $future->status);
    }

    public function test_does_not_expire_cancelled_subscriptions_even_if_ends_at_passed(): void
    {
        Carbon::setTestNow('2026-04-10 12:00:00');

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
        ]);

        $cancelled = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
            'starts_at' => Carbon::parse('2026-03-01 10:00:00'),
            'ends_at' => Carbon::parse('2026-04-05 12:00:00'),
            'cancelled_at' => Carbon::parse('2026-04-01 10:00:00'),
            'paid_at' => Carbon::parse('2026-03-01 10:00:00'),
            'remaining_connections' => 0,
            'admin_notes' => null,
        ]);

        (new ExpireDuePackageSubscriptionsJob)->handle();

        $cancelled->refresh();
        $this->assertSame(PackageSubscriptionStatus::Active, $cancelled->status);
    }
}

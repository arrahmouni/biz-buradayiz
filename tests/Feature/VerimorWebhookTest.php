<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\PackageSubscriptionSnapshot;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;
use Tests\TestCase;

class VerimorWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['verimor.webhook_token' => 'test-webhook-token']);
    }

    public function test_decrements_remaining_connections_on_answered_inbound_hangup(): void
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'central_phone' => '+905551112233',
        ]);

        $package = Package::factory()->create(['connections_count' => 10]);
        $subscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::Other,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'paid_at' => now()->subDay(),
            'remaining_connections' => 5,
        ]);
        $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($package));

        $response = $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', [
            'event_type' => 'hangup',
            'direction' => 'inbound',
            'call_uuid' => '11111111-1111-1111-1111-111111111111',
            'destination_number' => '905551112233',
            'caller_id_number' => '05321234567',
            'answered' => 'true',
        ]);

        $response->assertOk();
        $subscription->refresh();
        $this->assertSame(4, (int) $subscription->remaining_connections);

        $event = VerimorCallEvent::query()->where('call_uuid', '11111111-1111-1111-1111-111111111111')->first();
        $this->assertNotNull($event);
        $this->assertSame(VerimorCallEventType::Hangup, $event->event_type);
        $this->assertSame(VerimorCallDirection::Inbound, $event->direction);
        $this->assertTrue($event->consumed_quota);
        $this->assertSame($subscription->id, $event->package_subscription_id);
        $this->assertSame(
            VerimorPhoneNormalizer::canonicalize('05321234567'),
            $event->caller_number_normalized
        );
    }

    public function test_does_not_decrement_when_unanswered(): void
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'central_phone' => '05551112233',
        ]);

        $package = Package::factory()->create();
        $subscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::Other,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'paid_at' => now()->subDay(),
            'remaining_connections' => 5,
        ]);
        $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($package));

        $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', [
            'event_type' => 'hangup',
            'direction' => 'inbound',
            'call_uuid' => '22222222-2222-2222-2222-222222222222',
            'destination_number' => '905551112233',
            'caller_id_number' => '02161234567',
            'answered' => 'false',
        ])->assertOk();

        $subscription->refresh();
        $this->assertSame(5, (int) $subscription->remaining_connections);

        $event = VerimorCallEvent::query()->where('call_uuid', '22222222-2222-2222-2222-222222222222')->first();
        $this->assertNotNull($event);
        $this->assertSame(VerimorCallEventType::Hangup, $event->event_type);
        $this->assertSame(VerimorCallDirection::Inbound, $event->direction);
        $this->assertFalse($event->consumed_quota);
        $this->assertSame(
            VerimorPhoneNormalizer::canonicalize('02161234567'),
            $event->caller_number_normalized
        );
    }

    public function test_idempotent_duplicate_call_uuid(): void
    {
        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'central_phone' => '+905551112233',
        ]);

        $package = Package::factory()->create();
        $subscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::Other,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'paid_at' => now()->subDay(),
            'remaining_connections' => 5,
        ]);
        $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($package));

        $payload = [
            'event_type' => 'hangup',
            'direction' => 'inbound',
            'call_uuid' => '33333333-3333-3333-3333-333333333333',
            'destination_number' => '905551112233',
            'caller_id_number' => '905309998877',
            'answered' => 'true',
        ];

        $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', $payload)->assertOk();
        $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', $payload)->assertOk();

        $subscription->refresh();
        $this->assertSame(4, (int) $subscription->remaining_connections);
        $this->assertSame(1, VerimorCallEvent::query()->where('call_uuid', '33333333-3333-3333-3333-333333333333')->count());
    }

    public function test_wrong_token_returns_403(): void
    {
        $response = $this->call('POST', '/api/v1/webhooks/verimor/crm/wrong-token', [
            'event_type' => 'hangup',
            'direction' => 'inbound',
            'call_uuid' => '44444444-4444-4444-4444-444444444444',
            'destination_number' => '905551112233',
            'answered' => 'true',
        ]);

        $response->assertForbidden();
    }

    public function test_unconfigured_webhook_token_returns_503(): void
    {
        config(['verimor.webhook_token' => '']);

        $response = $this->call('POST', '/api/v1/webhooks/verimor/crm/any-token', [
            'event_type' => 'hangup',
            'direction' => 'inbound',
            'call_uuid' => '55555555-5555-5555-5555-555555555555',
        ]);

        $response->assertStatus(503);
    }

    public function test_ringing_event_is_ignored(): void
    {
        $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', [
            'event_type' => 'ringing',
            'direction' => 'inbound',
            'call_uuid' => '66666666-6666-6666-6666-666666666666',
            'destination_number' => '905551112233',
        ])->assertOk();

        $this->assertSame(0, VerimorCallEvent::query()->count());
    }

    public function test_outbound_hangup_is_ignored(): void
    {
        $this->call('POST', '/api/v1/webhooks/verimor/crm/test-webhook-token', [
            'event_type' => 'hangup',
            'direction' => 'outbound',
            'call_uuid' => '77777777-7777-7777-7777-777777777777',
            'destination_number' => '905551112233',
            'answered' => 'true',
        ])->assertOk();

        $this->assertSame(0, VerimorCallEvent::query()->count());
    }
}

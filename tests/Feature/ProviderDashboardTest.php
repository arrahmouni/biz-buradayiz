<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Http\Services\PackageSubscriptionService;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\Service;
use Modules\Platform\Models\ServiceTranslation;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Tests\TestCase;

class ProviderDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
        ]);
    }

    public function test_guest_redirected_from_dashboard(): void
    {
        $response = $this->get(route('front.provider.dashboard', [], false));

        $response->assertRedirect();
    }

    public function test_provider_can_view_dashboard(): void
    {
        [$service, $user] = $this->makeProviderWithService();

        $freePackage = $this->makeCatalogPackage($service, true);

        app(PackageSubscriptionService::class)->createModel([
            'user_id' => $user->id,
            'package_id' => $freePackage->id,
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'payment_method' => PackageSubscriptionPaymentMethod::Other->value,
        ]);

        $response = $this->actingAs($user)->get(route('front.provider.dashboard', [], false));

        $response->assertOk();
        $response->assertSee($user->email, false);
    }

    public function test_guest_redirected_from_subscription_history_fragment(): void
    {
        $response = $this->post(route('front.provider.dashboard.fragments.subscription-history', [], false), [
            'page' => 1,
        ]);

        $response->assertRedirect();
    }

    public function test_guest_redirected_from_call_log_fragment(): void
    {
        $response = $this->post(route('front.provider.dashboard.fragments.call-log', [], false), [
            'page' => 1,
        ]);

        $response->assertRedirect();
    }

    public function test_provider_subscription_history_fragment_returns_html_json(): void
    {
        [$service, $user] = $this->makeProviderWithService();

        $freePackage = $this->makeCatalogPackage($service, true);
        app(PackageSubscriptionService::class)->createModel([
            'user_id' => $user->id,
            'package_id' => $freePackage->id,
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'payment_method' => PackageSubscriptionPaymentMethod::Other->value,
        ]);

        $response = $this->actingAs($user)->postJson(route('front.provider.dashboard.fragments.subscription-history', [], false), [
            'page' => 1,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['html']);
        $this->assertStringContainsString('<table', $response->json('html'));
    }

    public function test_provider_call_log_fragment_returns_html_json(): void
    {
        [$service, $user] = $this->makeProviderWithService();

        VerimorCallEvent::query()->create([
            'call_uuid' => '33333333-3333-3333-3333-333333333333',
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => '905551111111',
            'caller_number_normalized' => '905552222222',
            'user_id' => $user->id,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => [],
        ]);

        $response = $this->actingAs($user)->postJson(route('front.provider.dashboard.fragments.call-log', [], false), [
            'page' => 1,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['html']);
        $this->assertStringContainsString('33333333-3333-3333-3333-333333333333', $response->json('html'));
    }

    public function test_call_log_lists_only_current_provider_events(): void
    {
        [$service, $userA] = $this->makeProviderWithService();
        [, $userB] = $this->makeProviderWithService('b-'.uniqid().'@example.test');

        VerimorCallEvent::query()->create([
            'call_uuid' => '11111111-1111-1111-1111-111111111111',
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => '905551111111',
            'caller_number_normalized' => '905552222222',
            'user_id' => $userA->id,
            'package_subscription_id' => null,
            'answered' => true,
            'consumed_quota' => false,
            'raw_payload' => [],
        ]);

        VerimorCallEvent::query()->create([
            'call_uuid' => '22222222-2222-2222-2222-222222222222',
            'event_type' => VerimorCallEventType::Hangup,
            'direction' => VerimorCallDirection::Inbound,
            'destination_number_normalized' => '905553333333',
            'caller_number_normalized' => '905554444444',
            'user_id' => $userB->id,
            'package_subscription_id' => null,
            'answered' => false,
            'consumed_quota' => false,
            'raw_payload' => [],
        ]);

        $response = $this->actingAs($userA)->get(route('front.provider.dashboard', [], false));

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString('11111111-1111-1111-1111-111111111111', $body);
        $this->assertStringNotContainsString('22222222-2222-2222-2222-222222222222', $body);
    }

    public function test_provider_can_request_paid_package_while_free_tier_active(): void
    {
        [$service, $user] = $this->makeProviderWithService();

        $freePackage = $this->makeCatalogPackage($service, true);
        $paidPackage = $this->makeCatalogPackage($service, false);

        app(PackageSubscriptionService::class)->createModel([
            'user_id' => $user->id,
            'package_id' => $freePackage->id,
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'payment_method' => PackageSubscriptionPaymentMethod::Other->value,
        ]);

        $response = $this->actingAs($user)->post(route('front.provider.subscriptions.request', [], false), [
            'package_id' => $paidPackage->id,
        ]);

        $response->assertRedirect(route('front.provider.dashboard', [], false));

        $this->assertDatabaseHas('package_subscriptions', [
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::PendingPayment->value,
            'payment_status' => PackageSubscriptionPaymentStatus::AwaitingVerification->value,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer->value,
        ]);
    }

    /**
     * @return array{0: Service, 1: User}
     */
    private function makeProviderWithService(?string $email = null): array
    {
        $service = Service::query()->create([
            'show_in_search_filters' => true,
            'icon' => 'fas fa-truck',
        ]);
        foreach (['en', 'tr'] as $locale) {
            ServiceTranslation::query()->create([
                'service_id' => $service->id,
                'locale' => $locale,
                'name' => 'Test service '.$service->id,
                'description' => 'Test',
            ]);
        }

        $email ??= 'provider-'.uniqid().'@example.test';

        $user = User::query()->create([
            'first_name' => 'Test',
            'last_name' => 'Provider',
            'email' => $email,
            'password' => Hash::make('password'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'service_id' => $service->id,
            'phone_number' => '+90'.uniqid(),
            'lang' => 'en',
        ]);

        return [$service, $user];
    }

    private function makeCatalogPackage(Service $service, bool $freeTier): Package
    {
        $package = Package::query()->create([
            'price' => $freeTier ? 0 : 99.99,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 10,
            'is_free_tier' => $freeTier,
            'is_popular' => false,
        ]);

        foreach (['en', 'tr'] as $locale) {
            $package->translations()->create([
                'locale' => $locale,
                'name' => $freeTier ? 'Free test package' : 'Paid test package',
                'slug' => 'test-pkg-'.$package->id.'-'.$locale,
                'description' => null,
                'features' => null,
            ]);
        }

        $package->services()->sync([$service->id]);

        return $package;
    }
}

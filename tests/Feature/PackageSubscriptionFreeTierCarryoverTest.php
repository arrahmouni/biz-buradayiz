<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\PackageSubscriptionSnapshot;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;
use Modules\Zms\Models\Country;
use Modules\Zms\Models\State;
use Tests\TestCase;

class PackageSubscriptionFreeTierCarryoverTest extends TestCase
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

    private function createServiceWithTranslation(): int
    {
        $service = Service::query()->create([
            'show_in_search_filters' => true,
            'icon' => 'fas fa-wrench',
        ]);
        $service->translateOrNew('en')->name = 'Test service';
        $service->translateOrNew('en')->description = 'Desc';
        $service->save();

        return (int) $service->id;
    }

    /**
     * @return array{city_id: int}
     */
    private function createCityWithTranslation(): array
    {
        $country = Country::query()->create([
            'native_name' => 'Testland',
            'iso3' => 'TUR',
            'iso2' => 'TR',
            'phone_code' => '90',
        ]);
        $country->translateOrNew('en')->name = 'Testland';
        $country->save();

        $state = State::query()->create([
            'country_id' => $country->id,
            'native_name' => 'Test state',
        ]);
        $state->translateOrNew('en')->name = 'Test state';
        $state->save();

        $city = City::query()->create([
            'state_id' => $state->id,
            'native_name' => 'Test city',
        ]);
        $city->translateOrNew('en')->name = 'Test city';
        $city->save();

        return ['city_id' => (int) $city->id];
    }

    public function test_first_paid_activation_adds_active_free_tier_remaining_connections_and_cancels_free(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'carryover@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'C',
            'last_name' => 'O',
            'phone_number' => '+905551119999',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $freePackage = Package::query()->create([
            'price' => 0,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 5,
            'is_free_tier' => true,
            'is_popular' => false,
        ]);
        $freePackage->translateOrNew('en')->name = 'Free tier';
        $freePackage->save();

        $paidPackage = Package::query()->create([
            'price' => 99,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 1,
            'connections_count' => 10,
            'is_free_tier' => false,
            'is_popular' => false,
        ]);
        $paidPackage->services()->sync([$serviceId]);
        $paidPackage->translateOrNew('en')->name = 'Paid tier';
        $paidPackage->save();

        $start = now()->subDay();
        $freeSubscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::Other,
            'starts_at' => $start,
            'ends_at' => now()->addMonth(),
            'cancelled_at' => null,
            'paid_at' => $start,
            'remaining_connections' => 4,
        ]);
        $freeSubscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($freePackage));

        $service = app(PackageSubscriptionService::class);

        $pendingPaid = $service->createModel([
            'user_id' => $user->id,
            'package_id' => $paidPackage->id,
            'status' => PackageSubscriptionStatus::PendingPayment->value,
            'payment_status' => PackageSubscriptionPaymentStatus::AwaitingVerification->value,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer->value,
        ]);

        $this->assertSame(10, (int) $pendingPaid->remaining_connections);

        $activated = $service->updateModel($pendingPaid, [
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'notify_user' => false,
        ]);

        $this->assertSame(14, (int) $activated->remaining_connections);

        $freeSubscription->refresh();
        $this->assertSame(PackageSubscriptionStatus::Cancelled, $freeSubscription->status);
        $this->assertSame(0, (int) $freeSubscription->remaining_connections);
    }

    public function test_first_paid_activation_without_free_tier_leaves_package_connection_quota_unchanged(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'nocarry@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'N',
            'last_name' => 'C',
            'phone_number' => '+905551118888',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $paidPackage = Package::query()->create([
            'price' => 99,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 1,
            'connections_count' => 10,
            'is_free_tier' => false,
            'is_popular' => false,
        ]);
        $paidPackage->services()->sync([$serviceId]);
        $paidPackage->translateOrNew('en')->name = 'Paid only';
        $paidPackage->save();

        $service = app(PackageSubscriptionService::class);

        $pendingPaid = $service->createModel([
            'user_id' => $user->id,
            'package_id' => $paidPackage->id,
            'status' => PackageSubscriptionStatus::PendingPayment->value,
            'payment_status' => PackageSubscriptionPaymentStatus::AwaitingVerification->value,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer->value,
        ]);

        $activated = $service->updateModel($pendingPaid, [
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'notify_user' => false,
        ]);

        $this->assertSame(10, (int) $activated->remaining_connections);
    }

    public function test_provider_dashboard_shows_free_tier_carryover_notice(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'notice@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'N',
            'last_name' => 'T',
            'phone_number' => '+905551117777',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $freePackage = Package::query()->create([
            'price' => 0,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 3,
            'is_free_tier' => true,
            'is_popular' => false,
        ]);
        $freePackage->translateOrNew('en')->name = 'Free tier';
        $freePackage->save();

        $start = now()->subDay();
        $freeSubscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::Other,
            'starts_at' => $start,
            'ends_at' => now()->addMonth(),
            'cancelled_at' => null,
            'paid_at' => $start,
            'remaining_connections' => 2,
        ]);
        $freeSubscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($freePackage));

        $response = $this->actingAs($user, 'web')->get(route('front.provider.dashboard'));

        $response->assertOk();
        $response->assertSee(__('front::provider_dashboard.free_tier_carryover_title'), false);
    }
}

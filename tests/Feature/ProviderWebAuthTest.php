<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Http\Services\UserCrudService;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\PackageSubscriptionSnapshot;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;
use Modules\Zms\Models\Country;
use Modules\Zms\Models\State;
use Tests\TestCase;

class ProviderWebAuthTest extends TestCase
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
     * @return array{state_id: int, city_id: int}
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

        Cache::forget('TUR_phone_code');

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

        return [
            'state_id' => (int) $state->id,
            'city_id' => (int) $city->id,
        ];
    }

    public function test_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'reset@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'R',
            'last_name' => 'S',
            'phone_number' => '+905551117777',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->post(route('front.provider.password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('front.provider.password.request'));
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_register_creates_pending_service_provider(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $response = $this->from(route('front.provider.register.form'))->post(route('front.provider.register.store'), [
            'first_name' => 'Pat',
            'last_name' => 'Provider',
            'email' => 'pat-provider@example.test',
            'phone_number' => '5551234567',
            'password' => 'Str0ng!Pass',
            'password_confirmation' => 'Str0ng!Pass',
            'service_id' => $serviceId,
            'state_id' => $location['state_id'],
            'city_id' => $location['city_id'],
        ]);

        $response->assertRedirect(route('front.provider.login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'pat-provider@example.test',
            'status' => AdminStatus::PENDING,
            'type' => UserType::ServiceProvider->value,
        ]);
    }

    public function test_register_landing_lists_paid_packages_excludes_free_tier_and_form_reachable(): void
    {
        $serviceId = $this->createServiceWithTranslation();

        $paid = Package::query()->create([
            'price' => 10,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 5,
            'is_free_tier' => false,
            'is_popular' => false,
        ]);
        $paid->services()->sync([$serviceId]);
        $paid->translateOrNew('en')->name = 'PaidPlanUniqueName';
        $paid->save();

        $free = Package::query()->create([
            'price' => 0,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 1,
            'is_free_tier' => true,
            'is_popular' => false,
        ]);
        $free->services()->sync([$serviceId]);
        $free->translateOrNew('en')->name = 'FreeTierUniqueName';
        $free->save();

        $this->get(route('front.provider.register.form'))
            ->assertOk()
            ->assertSee('id="front-provider-register-form"', false);

        $response = $this->get(route('front.provider.register'));
        $response->assertOk();
        $response->assertSee('PaidPlanUniqueName');
        $response->assertDontSee('FreeTierUniqueName');
    }

    public function test_register_landing_highlights_package_marked_popular(): void
    {
        $serviceId = $this->createServiceWithTranslation();

        $plain = Package::query()->create([
            'price' => 10,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 5,
            'is_free_tier' => false,
            'is_popular' => false,
        ]);
        $plain->services()->sync([$serviceId]);
        $plain->translateOrNew('en')->name = 'PlainPlanUniqueName';
        $plain->save();

        $popular = Package::query()->create([
            'price' => 20,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 1,
            'connections_count' => 10,
            'is_free_tier' => false,
            'is_popular' => true,
        ]);
        $popular->services()->sync([$serviceId]);
        $popular->translateOrNew('en')->name = 'PopularPlanUniqueName';
        $popular->save();

        $response = $this->get(route('front.provider.register'));
        $response->assertOk();
        $response->assertSee('PopularPlanUniqueName');
        $response->assertSee(__('front::provider_register.package_popular'), false);
    }

    public function test_pending_provider_login_shows_pending_message(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'pending@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::PENDING,
            'first_name' => 'P',
            'last_name' => 'Q',
            'phone_number' => '+905551119999',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->from(route('front.provider.login'))->post(route('front.provider.login.store'), [
            'email' => $user->email,
            'password' => 'Str0ng!Pass',
        ]);

        $response->assertRedirect(route('front.provider.login'));
        $response->assertSessionHasErrors();
    }

    public function test_active_provider_can_view_dashboard(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'active@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'A',
            'last_name' => 'B',
            'phone_number' => '+905551118888',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->actingAs($user, 'web')->get(route('front.provider.dashboard'));

        $response->assertOk();
        $response->assertSee(__('front::auth.dashboard_title'), false);
    }

    public function test_first_activation_grants_free_package_once(): void
    {
        Package::query()->create([
            'price' => 0,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 1,
            'is_free_tier' => true,
            'is_popular' => false,
        ]);

        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'approve@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::PENDING,
            'first_name' => 'Apr',
            'last_name' => 'Rove',
            'phone_number' => '+905551116666',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $this->assertNull($user->welcome_free_package_granted_at);
        $this->assertSame(0, PackageSubscription::query()->count());

        $payload = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'central_phone' => null,
            'type' => UserType::ServiceProvider->value,
            'lang' => $user->lang,
            'status' => AdminStatus::ACTIVE,
            'service_id' => $user->service_id,
            'city_id' => $user->city_id,
            'password' => null,
            'image_remove' => false,
        ];

        app(UserCrudService::class)->updateModel($user, $payload);

        $user->refresh();

        $this->assertNotNull($user->welcome_free_package_granted_at);
        $this->assertSame(1, PackageSubscription::query()->count());

        $payloadSuspended = array_merge($payload, ['status' => AdminStatus::SUSPENDED]);
        app(UserCrudService::class)->updateModel($user, $payloadSuspended);

        $user->refresh();

        app(UserCrudService::class)->updateModel($user, $payload);

        $this->assertSame(1, PackageSubscription::query()->count());
    }

    public function test_guest_cannot_view_provider_account_settings(): void
    {
        $response = $this->get(route('front.provider.account'));

        $response->assertRedirect();
    }

    public function test_active_provider_can_view_account_settings(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'account-view@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'V',
            'last_name' => 'U',
            'phone_number' => '+905551110001',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->actingAs($user, 'web')->get(route('front.provider.account'));

        $response->assertOk();
        $response->assertSee(__('front::provider_account.page_title'), false);
    }

    public function test_active_provider_can_update_profile_without_changing_phones(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'account-upd@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'O',
            'last_name' => 'G',
            'phone_number' => '+905551110002',
            'central_phone' => '+905551110003',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->actingAs($user, 'web')->from(route('front.provider.account'))->put(route('front.provider.account.update'), [
            'first_name' => 'N1',
            'last_name' => 'N2',
            'email' => 'account-upd-new@example.test',
            'service_id' => $serviceId,
            'state_id' => $location['state_id'],
            'city_id' => $location['city_id'],
        ]);

        $response->assertRedirect(route('front.provider.account'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('N1', $user->first_name);
        $this->assertSame('N2', $user->last_name);
        $this->assertSame('account-upd-new@example.test', $user->email);
        $this->assertSame('+905551110002', $user->phone_number);
        $this->assertSame('+905551110003', $user->central_phone);
        $this->assertSame('n1-n2', $user->profile_slug);
    }

    public function test_provider_profile_update_keeps_profile_slug_when_only_email_changes(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'slug-keep@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'Slug',
            'last_name' => 'Keep',
            'phone_number' => '+905551110099',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $slugBefore = $user->fresh()->profile_slug;
        $this->assertNotNull($slugBefore);

        $response = $this->actingAs($user, 'web')->from(route('front.provider.account'))->put(route('front.provider.account.update'), [
            'first_name' => 'Slug',
            'last_name' => 'Keep',
            'email' => 'slug-keep-new@example.test',
            'service_id' => $serviceId,
            'state_id' => $location['state_id'],
            'city_id' => $location['city_id'],
        ]);

        $response->assertRedirect(route('front.provider.account'));

        $user->refresh();
        $this->assertSame($slugBefore, $user->profile_slug);
        $this->assertSame('slug-keep-new@example.test', $user->email);
    }

    public function test_provider_profile_update_rejects_duplicate_email(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        User::query()->create([
            'email' => 'taken@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'T',
            'last_name' => 'K',
            'phone_number' => '+905551110004',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $user = User::query()->create([
            'email' => 'free@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'F',
            'last_name' => 'R',
            'phone_number' => '+905551110005',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $response = $this->actingAs($user, 'web')->from(route('front.provider.account'))->put(route('front.provider.account.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => 'taken@example.test',
            'service_id' => $serviceId,
            'state_id' => $location['state_id'],
            'city_id' => $location['city_id'],
        ]);

        $response->assertSessionHasErrors('email');
        $user->refresh();
        $this->assertSame('free@example.test', $user->email);
    }

    public function test_provider_can_change_password_with_correct_old_password(): void
    {
        $serviceId = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'pwd@example.test',
            'password' => 'Str0ng!Pass',
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'P',
            'last_name' => 'W',
            'phone_number' => '+905551110006',
            'lang' => 'en',
            'service_id' => $serviceId,
            'city_id' => $location['city_id'],
        ]);

        $bad = $this->actingAs($user, 'web')->from(route('front.provider.account'))->post(route('front.provider.account.password'), [
            'old_password' => 'WrongPass!',
            'new_password' => 'NewStr0ng!Pass',
            'new_password_confirmation' => 'NewStr0ng!Pass',
        ]);
        $bad->assertSessionHasErrors('old_password');

        $ok = $this->actingAs($user, 'web')->from(route('front.provider.account'))->post(route('front.provider.account.password'), [
            'old_password' => 'Str0ng!Pass',
            'new_password' => 'NewStr0ng!Pass',
            'new_password_confirmation' => 'NewStr0ng!Pass',
        ]);
        $ok->assertRedirect(route('front.provider.account'));
        $ok->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('NewStr0ng!Pass', $user->password));
    }

    public function test_provider_cannot_change_service_while_active_non_free_subscription_exists(): void
    {
        $serviceId1 = $this->createServiceWithTranslation();
        $serviceId2 = $this->createServiceWithTranslation();
        $location = $this->createCityWithTranslation();

        $user = User::query()->create([
            'email' => 'svc-guard@example.test',
            'password' => Hash::make('Str0ng!Pass'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'S',
            'last_name' => 'G',
            'phone_number' => '+905551110007',
            'lang' => 'en',
            'service_id' => $serviceId1,
            'city_id' => $location['city_id'],
        ]);

        $paid = Package::query()->create([
            'price' => 99,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 5,
            'is_free_tier' => false,
            'is_popular' => false,
        ]);
        $paid->services()->sync([$serviceId1]);
        $paid->translateOrNew('en')->name = 'GuardPaidPlan';
        $paid->save();

        $start = now()->subDay();
        $subscription = PackageSubscription::query()->create([
            'user_id' => $user->id,
            'status' => PackageSubscriptionStatus::Active,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
            'starts_at' => $start,
            'ends_at' => now()->addMonth(),
            'cancelled_at' => null,
            'paid_at' => $start,
            'remaining_connections' => 10,
        ]);
        $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($paid));

        $response = $this->actingAs($user, 'web')->from(route('front.provider.account'))->put(route('front.provider.account.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'service_id' => $serviceId2,
            'state_id' => $location['state_id'],
            'city_id' => $location['city_id'],
        ]);

        $response->assertSessionHasErrors('service_id');
        $user->refresh();
        $this->assertSame($serviceId1, $user->service_id);
    }
}

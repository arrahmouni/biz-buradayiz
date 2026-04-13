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
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
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

        $response = $this->from(route('front.provider.register'))->post(route('front.provider.register.store'), [
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
        Package::factory()->create(['is_free_tier' => true]);

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
}

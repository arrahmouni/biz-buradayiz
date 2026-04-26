<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\Admin\Enums\AdminStatus;
use Modules\Admin\Models\Admin;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Enums\Gender;
use Modules\Permission\Enums\SystemDefaultRoles;
use Modules\Permission\Models\Role;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;

class ServiceProviderViewTest extends TestCase
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

    public function test_service_provider_view_returns_ok_and_shows_email(): void
    {
        $admin = $this->makeRootAdmin();

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'service_id' => null,
            'city_id' => null,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get($this->localizedAdminUrl('auth.users.show', [
                'userType' => UserType::ServiceProvider->value,
                'model' => $user->id,
            ]));

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString($user->email, $body);
        $this->assertStringContainsString($user->full_name, $body);
    }

    public function test_view_with_mismatched_user_type_returns_not_found(): void
    {
        $admin = $this->makeRootAdmin();

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'service_id' => null,
            'city_id' => null,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'type' => UserType::ServiceProvider->value,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get($this->localizedAdminUrl('auth.users.show', [
                'userType' => UserType::Customer->value,
                'model' => $user->id,
            ]));

        $response->assertNotFound();
    }

    public function test_provider_subscriptions_datatable_returns_json(): void
    {
        $admin = $this->makeRootAdmin();

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'service_id' => null,
            'city_id' => null,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->getJson($this->localizedAdminUrl('auth.users.showSubscriptionsDatatable', [
                'userType' => UserType::ServiceProvider->value,
                'model' => $user->id,
            ]));

        $response->assertOk();
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }

    public function test_provider_call_events_datatable_returns_json(): void
    {
        $admin = $this->makeRootAdmin();

        $user = User::factory()->create([
            'type' => UserType::ServiceProvider,
            'service_id' => null,
            'city_id' => null,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->getJson($this->localizedAdminUrl('auth.users.showCallEventsDatatable', [
                'userType' => UserType::ServiceProvider->value,
                'model' => $user->id,
            ]));

        $response->assertOk();
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }

    private function makeRootAdmin(): Admin
    {
        $role = Role::withoutGlobalScope('withoutRoot')->firstOrCreate(['name' => SystemDefaultRoles::ROOT_ROLE]);

        $admin = Admin::query()->create([
            'status' => AdminStatus::ACTIVE,
            'full_name' => 'Test Root',
            'username' => 'test-root-'.uniqid(),
            'email' => 'root-'.uniqid().'@example.test',
            'password' => Hash::make('password'),
            'lang' => 'en',
            'gender' => Gender::MALE,
        ]);

        BouncerFacade::assign($role)->to($admin);

        return $admin;
    }

    /**
     * Relative path for named admin routes (matches RouteServiceProvider registration in this app).
     */
    private function localizedAdminUrl(string $name, array $parameters = []): string
    {
        return route($name, $parameters, false);
    }
}

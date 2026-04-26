<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
use Modules\Platform\Models\Review;
use Modules\Platform\Models\Service;
use Modules\Platform\Models\ServiceTranslation;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;
use Modules\Verimor\Models\VerimorCallEvent;
use Modules\Verimor\Support\VerimorPhoneNormalizer;
use Tests\TestCase;

class ProviderReviewSubmissionWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LocaleSessionRedirect::class,
            LocaleCookieRedirect::class,
            ValidateCsrfToken::class,
        ]);
    }

    /**
     * Active public-listing provider (matches {@see ProviderController::publicProviderQuery()}).
     */
    private function makeServiceProvider(): User
    {
        $service = Service::query()->create([
            'show_in_search_filters' => true,
            'icon' => 'fas fa-truck',
        ]);
        foreach (['en', 'tr'] as $locale) {
            ServiceTranslation::query()->create([
                'service_id' => $service->id,
                'locale' => $locale,
                'name' => 'Review test service '.$service->id,
                'description' => 'Test',
            ]);
        }

        $user = User::query()->create([
            'email' => uniqid('sp', true).'@example.test',
            'password' => Hash::make('password'),
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::ACTIVE,
            'first_name' => 'Test',
            'last_name' => 'Provider',
            'phone_number' => '+90'.uniqid(),
            'central_phone' => '+905551112233',
            'lang' => 'en',
            'email_verified_at' => now(),
            'service_id' => $service->id,
            'city_id' => null,
        ]);
        $user->forceFill(['approved_at' => now()])->save();

        $package = Package::query()->create([
            'price' => 0,
            'currency' => 'TRY',
            'billing_period' => BillingPeriod::Monthly,
            'sort_order' => 0,
            'connections_count' => 10,
            'is_free_tier' => true,
            'is_popular' => false,
        ]);
        foreach (['en', 'tr'] as $locale) {
            $package->translations()->create([
                'locale' => $locale,
                'name' => 'Free review test pkg '.$package->id,
                'slug' => 'rev-pkg-'.$package->id.'-'.$locale,
                'description' => null,
                'features' => null,
            ]);
        }
        $package->services()->sync([$service->id]);

        app(PackageSubscriptionService::class)->createModel([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'payment_method' => PackageSubscriptionPaymentMethod::Other->value,
        ]);

        return $user->refresh();
    }

    public function test_submits_review_when_matching_answered_inbound_call_exists(): void
    {
        $user = $this->makeServiceProvider();

        $callerRaw = '05321234567';
        $callerNorm = VerimorPhoneNormalizer::canonicalize($callerRaw);

        VerimorCallEvent::query()->create([
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

        $slug = $user->profile_slug;
        $this->assertNotEmpty($slug);

        $response = $this->post(route('front.provider.reviews.store', ['provider' => $slug]), [
            'rating' => 5,
            'phone' => $callerRaw,
            'comment' => 'Great service.',
            'display_name' => 'Test Caller',
        ]);

        $response->assertRedirect();
        $this->assertSame(1, Review::query()->count());
        $this->assertSame('Great service.', Review::query()->first()->body);
    }

    public function test_rejects_when_phone_has_no_matching_call(): void
    {
        $user = $this->makeServiceProvider();

        $slug = $user->profile_slug;
        $this->assertNotEmpty($slug);

        $response = $this->from(route('front.provider.show', ['provider' => $slug]))
            ->post(route('front.provider.reviews.store', ['provider' => $slug]), [
                'rating' => 4,
                'phone' => '05320000000',
                'comment' => 'n/a',
                'display_name' => 'Caller',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('phone');
        $this->assertSame(0, Review::query()->count());
    }
}

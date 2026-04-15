<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Models\Review;
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
        ]);
    }

    /**
     * Create a service provider without UserFactory media download (avoids flaky remote avatar URLs in tests).
     */
    private function makeServiceProvider(): User
    {
        return User::query()->create([
            'email' => uniqid('sp', true).'@example.test',
            'password' => bcrypt('password'),
            'type' => UserType::ServiceProvider,
            'first_name' => 'Test',
            'last_name' => 'Provider',
            'phone_number' => '+90'.uniqid(),
            'central_phone' => '+905551112233',
            'lang' => 'en',
            'email_verified_at' => now(),
            'service_id' => null,
            'city_id' => null,
        ]);
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
                'comment' => null,
                'display_name' => null,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('phone');
        $this->assertSame(0, Review::query()->count());
    }
}

<?php

namespace Modules\Platform\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\PackageSubscriptionSnapshot;

/**
 * @extends Factory<PackageSubscription>
 */
class PackageSubscriptionFactory extends Factory
{
    protected $model = PackageSubscription::class;

    /**
     * When fewer service providers exist than this value, the factory creates
     * additional provider users so subscriptions are not all tied to a tiny set
     * (e.g. only the two accounts seeded in a fresh database).
     */
    private const MIN_SERVICE_PROVIDER_USER_POOL_SIZE = 10;

    /** @var list<int>|null */
    private ?array $cachedServiceProviderUserIds = null;

    public function definition(): array
    {
        $scenario = fake()->randomElement(['pending', 'active_paid', 'cancelled', 'expired']);

        return match ($scenario) {
            'pending' => [
                'user_id' => $this->resolveUserId(),
                'status' => PackageSubscriptionStatus::PendingPayment,
                'payment_status' => fake()->randomElement([
                    PackageSubscriptionPaymentStatus::Pending,
                    PackageSubscriptionPaymentStatus::AwaitingVerification,
                ]),
                'payment_method' => fake()->randomElement(PackageSubscriptionPaymentMethod::cases()),
                'starts_at' => null,
                'ends_at' => null,
                'cancelled_at' => null,
                'paid_at' => null,
                'remaining_connections' => fake()->optional(0.5)->numberBetween(1, 50),
                'admin_notes' => null,
            ],
            'active_paid' => [
                'user_id' => $this->resolveUserId(),
                'status' => PackageSubscriptionStatus::Active,
                'payment_status' => PackageSubscriptionPaymentStatus::Paid,
                'payment_method' => fake()->randomElement(PackageSubscriptionPaymentMethod::cases()),
                'starts_at' => $start = fake()->dateTimeBetween('-3 months', 'now'),
                'ends_at' => (clone $start)->modify('+1 month'),
                'cancelled_at' => null,
                'paid_at' => $start,
                'remaining_connections' => fake()->numberBetween(1, 50),
                'admin_notes' => fake()->optional(0.15)->sentence(),
            ],
            'cancelled' => [
                'user_id' => $this->resolveUserId(),
                'status' => PackageSubscriptionStatus::Cancelled,
                'payment_status' => fake()->randomElement([
                    PackageSubscriptionPaymentStatus::Cancelled,
                    PackageSubscriptionPaymentStatus::Failed,
                    PackageSubscriptionPaymentStatus::Pending,
                ]),
                'payment_method' => fake()->randomElement(PackageSubscriptionPaymentMethod::cases()),
                'starts_at' => null,
                'ends_at' => null,
                'cancelled_at' => fake()->dateTimeBetween('-2 months', 'now'),
                'paid_at' => null,
                'remaining_connections' => 0,
                'admin_notes' => fake()->optional(0.2)->sentence(),
            ],
            'expired' => [
                'user_id' => $this->resolveUserId(),
                'status' => PackageSubscriptionStatus::Expired,
                'payment_status' => PackageSubscriptionPaymentStatus::Paid,
                'payment_method' => fake()->randomElement(PackageSubscriptionPaymentMethod::cases()),
                'starts_at' => $start = fake()->dateTimeBetween('-1 year', '-2 months'),
                'ends_at' => fake()->dateTimeBetween($start, '-1 month'),
                'cancelled_at' => null,
                'paid_at' => $start,
                'remaining_connections' => 0,
                'admin_notes' => null,
            ],
        };
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PackageSubscription $subscription) {
            $package = Package::query()->with('translations')->inRandomOrder()->first();

            if ($package) {
                $attrs = PackageSubscriptionSnapshot::attributesFromPackage($package);
                $subscription->snapshot()->create($attrs);

                if ($subscription->remaining_connections === null && $package->connections_count !== null) {
                    $subscription->update(['remaining_connections' => $package->connections_count]);
                }

                return;
            }

            $subscription->snapshot()->create([
                'source_package_id' => null,
                'name_translations' => [
                    'en' => fake()->words(3, true),
                    'tr' => fake()->words(3, true),
                ],
                'price' => fake()->randomFloat(2, 29, 499),
                'currency' => 'TRY',
                'billing_period' => fake()->randomElement(BillingPeriod::cases())->value,
                'connections_count' => fake()->numberBetween(1, 30),
            ]);
        });
    }

    private function resolveUserId(): int
    {
        if ($this->cachedServiceProviderUserIds === null) {
            $this->ensureMinimumServiceProviderUsers();
            $this->cachedServiceProviderUserIds = User::query()
                ->where('type', UserType::ServiceProvider)
                ->pluck('id')
                ->all();
        }

        if ($this->cachedServiceProviderUserIds === []) {
            $this->cachedServiceProviderUserIds = [User::factory()->create()->id];
        }

        return fake()->randomElement($this->cachedServiceProviderUserIds);
    }

    private function ensureMinimumServiceProviderUsers(): void
    {
        $count = User::query()->where('type', UserType::ServiceProvider)->count();
        $needed = self::MIN_SERVICE_PROVIDER_USER_POOL_SIZE - $count;

        if ($needed > 0) {
            User::factory()->count($needed)->create();
        }
    }
}

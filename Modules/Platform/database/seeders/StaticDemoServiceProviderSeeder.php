<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\Address;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Jobs\RecalculateProviderRankingsJob;
use Modules\Platform\Models\PackageSubscription;
use Modules\Platform\Models\Review;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;

class StaticDemoServiceProviderSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('platform.seeders.demo_service_provider_email');
        $password = config('platform.seeders.demo_service_provider_password');

        if (! filled($email) || ! filled($password)) {
            if ($this->command !== null) {
                $this->command->warn('Skipping static demo service provider: set SEED_DEMO_SERVICE_PROVIDER_EMAIL and SEED_DEMO_SERVICE_PROVIDER_PASSWORD in .env to populate platform.seeders config.');
            }

            return;
        }

        $service = Service::query()->orderBy('id')->first();
        $city = City::query()->whereNull('disabled_at')->with(['state.country'])->orderBy('id')->first();

        if ($service === null || $city === null || $city->state === null) {
            if ($this->command !== null) {
                $this->command->warn('Skipping static demo service provider: seed ZMS geography and services first.');
            }

            return;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Demo',
                'last_name' => 'Hizmet Sağlayıcı',
                'type' => UserType::ServiceProvider,
                'phone_number' => '+905550010001',
                'central_phone' => '+9034221112233',
                'password' => $password,
                'lang' => 'tr',
                'email_verified_at' => now(),
                'service_id' => $service->id,
                'status' => AdminStatus::ACTIVE,
                'city_id' => $city->id,
                'approved_at' => now(),
            ]
        );

        $user->generateSlug();
        $user->save();

        $this->seedDemoAvatar($user);

        $user->packageSubscriptions()->delete();
        $user->reviews()->forceDelete();

        $this->seedPastSubscriptions($user);
        PackageSubscription::factory()
            ->for($user)
            ->activePaidSubscription()
            ->create();

        $this->seedReviews($user);

        sync_service_provider_rating((int) $user->id);
        RecalculateProviderRankingsJob::dispatchSync();
    }

    private function seedPastSubscriptions(User $user): void
    {
        $expiredStartOld = now()->subYears(2)->subMonth();
        PackageSubscription::factory()
            ->for($user)
            ->create([
                'status' => PackageSubscriptionStatus::Expired,
                'payment_status' => PackageSubscriptionPaymentStatus::Paid,
                'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
                'starts_at' => $expiredStartOld,
                'ends_at' => $expiredStartOld->copy()->addYear(),
                'cancelled_at' => null,
                'paid_at' => $expiredStartOld,
                'remaining_connections' => 0,
                'admin_notes' => 'İlk dönem abonelik (süresi doldu).',
            ]);

        $expiredStartRecent = now()->subMonths(14);
        PackageSubscription::factory()
            ->for($user)
            ->create([
                'status' => PackageSubscriptionStatus::Expired,
                'payment_status' => PackageSubscriptionPaymentStatus::Paid,
                'payment_method' => PackageSubscriptionPaymentMethod::OnlineGateway,
                'starts_at' => $expiredStartRecent,
                'ends_at' => $expiredStartRecent->copy()->addYear(),
                'cancelled_at' => null,
                'paid_at' => $expiredStartRecent,
                'remaining_connections' => 0,
                'admin_notes' => null,
            ]);

        PackageSubscription::factory()
            ->for($user)
            ->create([
                'status' => PackageSubscriptionStatus::Cancelled,
                'payment_status' => PackageSubscriptionPaymentStatus::Cancelled,
                'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer,
                'starts_at' => now()->subMonths(8),
                'ends_at' => null,
                'cancelled_at' => now()->subMonths(3),
                'paid_at' => now()->subMonths(8),
                'remaining_connections' => 0,
                'admin_notes' => 'Kullanıcı talebiyle iptal.',
            ]);
    }

    private function seedReviews(User $user): void
    {
        Review::withoutEvents(function () use ($user): void {
            Review::factory()
                ->forUser($user)
                ->approved()
                ->create([
                    'rating' => 5,
                    'body' => 'Çok profesyonel ekip, randevuya tam zamanında geldiler. Kesinlikle tavsiye ederim.',
                    'reviewer_display_name' => 'Ayşe K.',
                ]);

            Review::factory()
                ->forUser($user)
                ->approved()
                ->create([
                    'rating' => 5,
                    'body' => 'İşini bilen bir firma. Fiyat performans olarak gayet iyiydi.',
                    'reviewer_display_name' => 'Mehmet Y.',
                ]);

            Review::factory()
                ->forUser($user)
                ->approved()
                ->create([
                    'rating' => 4,
                    'body' => 'Genel olarak memnun kaldım, iletişim biraz yoğun saatlerde gecikti ama sonuç güzeldi.',
                    'reviewer_display_name' => 'Zeynep D.',
                ]);

            Review::factory()
                ->forUser($user)
                ->approved()
                ->create([
                    'rating' => 5,
                    'body' => 'Tekrar çalışmak isterim, teşekkürler.',
                    'reviewer_display_name' => 'Can T.',
                ]);

            Review::factory()
                ->forUser($user)
                ->pending()
                ->create([
                    'rating' => 5,
                    'body' => 'Onay bekleyen örnek yorum (moderasyon kuyruğu için).',
                    'reviewer_display_name' => 'Deneme Müşteri',
                ]);
        });
    }

    private function seedDemoAvatar(User $user): void
    {
        $avatarPath = public_path('modules/admin/metronic/demo/media/avatars/300-1.jpg');

        if (! is_file($avatarPath)) {
            return;
        }

        $user->clearMediaCollection(User::MEDIA_COLLECTION);

        $user->addMedia($avatarPath)
            ->preservingOriginal()
            ->toMediaCollection(User::MEDIA_COLLECTION);
    }
}



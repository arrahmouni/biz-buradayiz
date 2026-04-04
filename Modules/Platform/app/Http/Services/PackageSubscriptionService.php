<?php

namespace Modules\Platform\Http\Services;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;

class PackageSubscriptionService
{
    public function syncForServiceProvider(User $user, ?int $packageId, ?int $serviceId = null): void
    {
        $type = $user->type;
        $typeValue = $type instanceof UserType ? $type->value : $type;

        if ($typeValue !== UserType::ServiceProvider->value) {
            return;
        }

        DB::transaction(function () use ($user, $packageId, $serviceId) {
            $this->cancelActiveForUser($user);

            if ($packageId === null || $packageId === 0) {
                return;
            }

            $sid = $serviceId ?? $user->service_id;
            if (! $sid) {
                return;
            }

            $exists = Package::query()
                ->whereKey($packageId)
                ->whereHas('services', fn ($q) => $q->where('services.id', $sid))
                ->exists();

            if (! $exists) {
                return;
            }

            PackageSubscription::query()->create([
                'user_id' => $user->id,
                'package_id' => $packageId,
                'status' => PackageSubscriptionStatus::Active,
                'starts_at' => now(),
                'ends_at' => null,
                'cancelled_at' => null,
            ]);
        });
    }

    private function cancelActiveForUser(User $user): void
    {
        PackageSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', PackageSubscriptionStatus::Active->value)
            ->update([
                'status' => PackageSubscriptionStatus::Cancelled->value,
                'cancelled_at' => now(),
            ]);
    }
}

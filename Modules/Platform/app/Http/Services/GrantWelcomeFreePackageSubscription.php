<?php

namespace Modules\Platform\Http\Services;

use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;

class GrantWelcomeFreePackageSubscription
{
    public function __construct(private PackageSubscriptionService $packageSubscriptionService) {}

    public function grantIfEligible(User $user): void
    {
        if ($user->type !== UserType::ServiceProvider) {
            return;
        }

        if ($user->welcome_free_package_granted_at !== null) {
            return;
        }

        $package = Package::query()
            ->where('is_free_tier', true)
            ->whereHas('services', function ($query) use ($user) {
                $query->where('services.id', $user->service_id);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($package === null) {
            return;
        }

        $this->packageSubscriptionService->createModel([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => PackageSubscriptionStatus::Active->value,
            'payment_status' => PackageSubscriptionPaymentStatus::Paid->value,
            'payment_method' => PackageSubscriptionPaymentMethod::Other->value,
        ]);

        $user->forceFill([
            'welcome_free_package_granted_at' => now(),
        ])->save();
    }
}

<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;

class ProviderRequestPackageSubscriptionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User && $user->type === UserType::ServiceProvider;
    }

    public function rules(): array
    {
        return [
            'package_id' => [
                'required',
                'integer',
                Rule::exists(Package::class, 'id'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var User $user */
            $user = Auth::guard('web')->user();
            $packageId = (int) $this->input('package_id');

            if ($user->service_id === null) {
                $validator->errors()->add(
                    'package_id',
                    __('front::provider_dashboard.validation.service_required')
                );

                return;
            }

            $package = Package::query()->find($packageId);
            if (! $package || $package->is_free_tier) {
                $validator->errors()->add(
                    'package_id',
                    __('front::provider_dashboard.validation.invalid_package')
                );

                return;
            }

            $packageSupportsService = Package::query()
                ->whereKey($packageId)
                ->whereHas(
                    'services',
                    fn ($q) => $q->where('services.id', $user->service_id)
                )
                ->exists();

            if (! $packageSupportsService) {
                $validator->errors()->add(
                    'package_id',
                    __('front::provider_dashboard.validation.package_not_for_service')
                );

                return;
            }

            $hasBlockingActive = PackageSubscription::query()
                ->where('user_id', $user->id)
                ->activeNonFreeTierSubscription()
                ->exists();

            if ($hasBlockingActive) {
                $validator->errors()->add(
                    'package_id',
                    __('front::provider_dashboard.validation.already_has_active_paid_package')
                );

                return;
            }

            $hasPendingPaidRequest = PackageSubscription::query()
                ->where('user_id', $user->id)
                ->pendingNonFreeTierPaymentRequest()
                ->exists();

            if ($hasPendingPaidRequest) {
                $validator->errors()->add(
                    'package_id',
                    __('front::provider_dashboard.validation.pending_paid_request_exists')
                );
            }
        });
    }
}

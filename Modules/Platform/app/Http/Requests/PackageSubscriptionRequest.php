<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;

class PackageSubscriptionRequest extends BaseRequest
{
    public function rules(): array
    {
        if ($this->routeIs('platform.package_subscriptions.postUpdate')) {
            return [
                'status' => ['required', Rule::enum(PackageSubscriptionStatus::class)],
                'payment_status' => ['required', Rule::enum(PackageSubscriptionPaymentStatus::class)],
                'notify_user' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'user_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'package_id' => [
                'required',
                'integer',
                Rule::exists(Package::class, 'id'),
            ],
            'status' => ['required', Rule::enum(PackageSubscriptionStatus::class)],
            'payment_status' => ['required', Rule::enum(PackageSubscriptionPaymentStatus::class)],
            'payment_method' => ['required', Rule::enum(PackageSubscriptionPaymentMethod::class)],
            'admin_notes' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $status = $this->input('status');
                $paymentStatus = $this->input('payment_status');

                if ($status === PackageSubscriptionStatus::Active->value
                    && $paymentStatus !== PackageSubscriptionPaymentStatus::Paid->value) {
                    $validator->errors()->add(
                        'payment_status',
                        trans('admin::validation.package_subscription.active_requires_paid')
                    );
                }

                if ($paymentStatus === PackageSubscriptionPaymentStatus::Paid->value
                    && $status === PackageSubscriptionStatus::Cancelled->value) {
                    $validator->errors()->add(
                        'status',
                        trans('admin::validation.package_subscription.paid_cannot_be_cancelled')
                    );
                }

                if ($this->routeIs('platform.package_subscriptions.postUpdate')
                    && $paymentStatus === PackageSubscriptionPaymentStatus::Paid->value
                    && filled($status)
                    && $status !== PackageSubscriptionStatus::Active->value) {
                    $validator->errors()->add(
                        'status',
                        trans('admin::validation.package_subscription.paid_requires_active_status')
                    );
                }
            },
            function (Validator $validator) {
                $packageId = $this->input('package_id');
                $userId = $this->input('user_id');

                if (! filled($packageId)) {
                    return;
                }

                $user = User::query()->find($userId);
                if (! $user
                    || $user->type !== UserType::ServiceProvider
                    || $user->service_id === null) {
                    return;
                }

                $packageSupportsService = Package::query()
                    ->whereKey((int) $packageId)
                    ->whereHas(
                        'services',
                        fn ($q) => $q->where('services.id', $user->service_id)
                    )
                    ->exists();

                if (! $packageSupportsService) {
                    $validator->errors()->add(
                        'package_id',
                        trans('admin::validation.package_must_cover_provider_service')
                    );
                }
            },
            function (Validator $validator) {
                if (! $this->routeIs('platform.package_subscriptions.postCreate')) {
                    return;
                }

                if ($validator->errors()->has('user_id')) {
                    return;
                }

                $userId = $this->input('user_id');
                if ($userId === null || $userId === '') {
                    return;
                }

                $hasActive = PackageSubscription::query()
                    ->where('user_id', (int) $userId)
                    ->activeSubscription()
                    ->exists();

                if ($hasActive) {
                    $validator->errors()->add(
                        'user_id',
                        trans('admin::validation.package_subscription.provider_already_has_active_package')
                    );
                }
            },
            function (Validator $validator) {
                if (! $this->routeIs('platform.package_subscriptions.postCreate')) {
                    return;
                }

                $packageId = $this->input('package_id');
                $userId = $this->input('user_id');
                if (! filled($packageId) || ! filled($userId) || $validator->errors()->has('package_id') || $validator->errors()->has('user_id')) {
                    return;
                }

                $package = Package::query()->find((int) $packageId);
                if (! $package || ! $package->is_free_tier) {
                    return;
                }

                $alreadySubscribed = PackageSubscription::query()
                    ->where('user_id', (int) $userId)
                    ->whereHas('snapshot', fn ($q) => $q->where('source_package_id', (int) $packageId))
                    ->exists();

                if ($alreadySubscribed) {
                    $validator->errors()->add(
                        'package_id',
                        trans('admin::validation.package_subscription.free_tier_already_subscribed')
                    );
                }
            },
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

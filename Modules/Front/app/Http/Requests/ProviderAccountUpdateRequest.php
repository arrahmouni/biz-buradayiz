<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Requests\BaseRequest;

class ProviderAccountUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $user = Auth::guard('web')->user();

        return $user instanceof User && $user->type === UserType::ServiceProvider;
    }

    public function rules(): array
    {
        $user = Auth::guard('web')->user();
        if (! $user instanceof User) {
            return [];
        }

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'state_id' => ['required', 'integer', Rule::exists('states', 'id')],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($q) => $q->where('state_id', (int) $this->input('state_id'))),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = Auth::guard('web')->user();
            if (! $user instanceof User) {
                return;
            }

            $newServiceId = (int) $this->input('service_id');
            if ($newServiceId === (int) $user->service_id) {
                return;
            }

            // Block service changes while a paid (non–free-tier) path is active or awaiting bank confirmation.
            if ($user->packageSubscriptions()->pendingNonFreeTierPaymentRequest()->exists()) {
                $validator->errors()->add(
                    'service_id',
                    __('front::provider_account.validation.service_change_blocked_pending_payment')
                );

                return;
            }

            if ($user->packageSubscriptions()->activeNonFreeTierSubscription()->exists()) {
                $validator->errors()->add(
                    'service_id',
                    __('front::provider_account.validation.service_change_blocked_active_paid')
                );
            }
        });
    }
}

<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Models\Package;

class PackageRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('sort_order') === '' || $this->input('sort_order') === null) {
            $this->merge(['sort_order' => 0]);
        }

        if ($this->routeIs('platform.packages.postUpdate')) {
            $modelParam = $this->route('model');
            $packageId = $modelParam instanceof Package ? $modelParam->id : (int) $modelParam;
            $package = Package::query()->find($packageId);
            if ($package) {
                $this->merge([
                    'is_free_tier' => (bool) $package->is_free_tier,
                ]);
                if ($package->is_free_tier) {
                    $this->merge(['price' => $package->price]);
                }
            }

            return;
        }

        $this->merge(['is_free_tier' => $this->boolean('is_free_tier')]);
        if ($this->boolean('is_free_tier')) {
            $this->merge(['price' => 0]);
        }
    }

    public function rules(): array
    {
        $base = [
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3', Rule::exists('countries', 'currency')],
            'billing_period' => ['required', Rule::enum(BillingPeriod::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'connections_count' => ['required', 'integer', 'min:1'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', Rule::exists('services', 'id')],
            'name' => ['required', 'array'],
            'description' => ['nullable', 'array'],
            'features' => ['nullable', 'array'],
        ];

        if ($this->routeIs('platform.packages.postUpdate')) {
            return $base;
        }

        return array_merge($base, [
            'is_free_tier' => ['boolean'],
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $this->validateBaseInput(validator: $validator, data: $this->name, inputName: 'name', atLeastOneLocaleWithSize: true);
                $this->validateBaseInput(validator: $validator, data: $this->description, inputName: 'description', atLeastOneLocaleWithSize: false, textarea: true);
                $this->validateBaseInput(validator: $validator, data: $this->features, inputName: 'features', atLeastOneLocaleWithSize: false, textarea: true);
                $this->validateFieldsWithoutTitle($validator, $this->all(), 'name', ['description', 'features']);
            },
            function (Validator $validator) {
                if (! $this->routeIs('platform.packages.postCreate') || ! $this->boolean('is_free_tier')) {
                    return;
                }

                if (abs((float) $this->input('price')) > 0.00001) {
                    $validator->errors()->add(
                        'price',
                        trans('admin::validation.package_free_tier_price_must_be_zero')
                    );
                }
            },
            function (Validator $validator) {
                if (! $this->routeIs('platform.packages.postCreate') || ! $this->boolean('is_free_tier')) {
                    return;
                }

                if (Package::query()->where('is_free_tier', true)->exists()) {
                    $validator->errors()->add(
                        'is_free_tier',
                        trans('admin::validation.package_free_tier_already_exists')
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

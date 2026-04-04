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

        $this->merge(['is_free_tier' => $this->boolean('is_free_tier')]);
    }

    public function rules(): array
    {
        return [
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
            'is_free_tier' => ['boolean'],
        ];
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
                if (! $this->boolean('is_free_tier')) {
                    return;
                }

                $routeModel = $this->route('model');
                $excludeId = $routeModel instanceof Package ? $routeModel->id : null;

                $exists = Package::query()
                    ->where('is_free_tier', true)
                    ->when($excludeId !== null, fn ($q) => $q->where('id', '!=', $excludeId))
                    ->exists();

                if ($exists) {
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

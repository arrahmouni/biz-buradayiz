<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Platform\Enums\BillingPeriod;

class PackageRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('sort_order') === '' || $this->input('sort_order') === null) {
            $this->merge(['sort_order' => 0]);
        }
    }

    public function rules(): array
    {
        return [
            'price'             => ['required', 'numeric', 'min:0'],
            'currency'          => ['required', 'string', 'max:3', Rule::exists('countries', 'currency')],
            'billing_period'    => ['required', Rule::enum(BillingPeriod::class)],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'connections_count' => ['required', 'integer', 'min:1'],
            'service_ids'       => ['required', 'array', 'min:1'],
            'service_ids.*'     => ['required', 'integer', Rule::exists('services', 'id')],
            'name'              => ['required', 'array'],
            'description'       => ['nullable', 'array'],
            'features'          => ['nullable', 'array'],
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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

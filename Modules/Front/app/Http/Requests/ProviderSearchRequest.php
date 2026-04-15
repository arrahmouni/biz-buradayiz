<?php

namespace Modules\Front\Http\Requests;

use Modules\Base\Http\Requests\BaseRequest;

class ProviderSearchRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service_id' => $this->input('service_id') ?: null,
            'state_id' => $this->input('state_id') ?: null,
            'city_id' => $this->input('city_id') ?: null,
        ]);
    }
}

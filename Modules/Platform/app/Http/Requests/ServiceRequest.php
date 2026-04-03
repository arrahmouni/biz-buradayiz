<?php

namespace Modules\Platform\Http\Requests;

use Modules\Base\Http\Requests\BaseRequest;

class ServiceRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('show_in_search_filters')) {
            $this->merge(['show_in_search_filters' => false]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'description' => ['nullable', 'array'],
            'show_in_search_filters' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $this->validateBaseInput(validator:$validator, data:$this->name, inputName: 'name', atLeastOneLocaleWithSize:true);
                $this->validateBaseInput(validator:$validator, data:$this->description, inputName: 'description', atLeastOneLocaleWithSize:false, textarea:true);
            }
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

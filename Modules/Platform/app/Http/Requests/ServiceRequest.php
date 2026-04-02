<?php

namespace Modules\Platform\Http\Requests;

use Modules\Base\Http\Requests\BaseRequest;

class ServiceRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $this->validateBaseInput(validator:$validator, data:$this->name, inputName: 'name', atLeastOneLocaleWithSize:true);
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

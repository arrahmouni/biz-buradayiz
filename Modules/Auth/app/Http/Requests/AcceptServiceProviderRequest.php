<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptServiceProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'central_phone' => ['required', 'string', 'max:255', 'regex:/^\+?[0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'central_phone.required' => trans('admin::cruds.users.central_phone_required_approval'),
            'central_phone.regex' => trans('admin::validation.central_phone_regex'),
        ];
    }
}

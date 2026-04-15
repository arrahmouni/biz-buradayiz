<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Modules\Base\Http\Requests\BaseRequest;

class ProviderResetPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Modules\Front\Http\Requests;

use Modules\Base\Http\Requests\BaseRequest;

class ProviderForgotPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

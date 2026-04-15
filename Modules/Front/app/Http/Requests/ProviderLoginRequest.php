<?php

namespace Modules\Front\Http\Requests;

use Modules\Base\Http\Requests\BaseRequest;

class ProviderLoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class ProviderRegisterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'min:9', 'max:20', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'state_id' => ['required', 'integer', Rule::exists('states', 'id')],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($q) => $q->where('state_id', (int) $this->input('state_id'))),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $raw = (string) $this->input('phone_number', '');
        if ($raw === '') {
            return;
        }

        $normalized = VerimorPhoneNormalizer::canonicalize($raw);
        if ($normalized !== '') {
            $this->merge([
                'phone_number' => '+'.$normalized,
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }
}

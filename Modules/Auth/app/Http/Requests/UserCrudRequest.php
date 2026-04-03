<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Modules\Auth\Enums\UserType;
use Modules\Base\Http\Requests\BaseRequest;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Admin\Enums\AdminStatus;

class UserCrudRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->route('userType') !== null) {
            $this->merge(['type' => $this->route('userType')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $type = $this->input('type');

        $rules = [
            'image'         => ['nullable', 'image', File::image()->types(config('base.file.image.accepted_types'))->max(config('base.file.image.max_size') . 'mb')],
            'image_remove'  => ['nullable', 'boolean'],
            'type'          => ['required', Rule::in(UserType::values())],
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->model)],
            'phone_number'  => ['required', 'string', 'min:9', 'max:20', Rule::unique('users', 'phone_number')->ignore($this->model)],
            'central_phone' => ['nullable', 'string', 'max:255', 'regex:/^\+?[0-9]*$/'],
            'password'      => ['confirmed', Password::defaults()],
            'lang'          => ['required', 'in:' . implode(',', LaravelLocalization::getSupportedLanguagesKeys())],
            'status'        => ['required', 'in:' . implode(',', AdminStatus::all())],
        ];

        if ($type === UserType::ServiceProvider->value) {
            $rules['service_id'] = ['required', 'exists:services,id'];
            $rules['city_id']    = ['required', 'exists:cities,id'];
        } else {
            $rules['service_id'] = ['prohibited'];
            $rules['city_id']    = ['prohibited'];
        }

        if ($this->isUpdate()) {
            array_push($rules['password'], 'nullable');
        } else {
            array_push($rules['password'], 'required');
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'central_phone.regex' => trans('admin::validation.central_phone_regex'),
        ];
    }
}

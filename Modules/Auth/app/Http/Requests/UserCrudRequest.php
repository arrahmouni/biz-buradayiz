<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Requests\BaseRequest;

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

        $userId = $this->route('model');
        $imageFileRules = ['nullable', 'image', File::image(allowSvg: true)->types(config('base.file.image.accepted_types'))->max(config('base.file.image.max_size').'mb')];

        $rules = [
            'image' => $imageFileRules,
            'image_remove' => ['nullable', 'boolean'],
            'type' => ['required', Rule::in(UserType::values())],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['required', 'string', 'min:9', 'max:20', Rule::unique('users', 'phone_number')->ignore($userId)],
            'central_phone' => $this->centralPhoneRules(),
            'password' => ['confirmed', Password::defaults()],
            'lang' => ['required', 'in:'.implode(',', LaravelLocalization::getSupportedLanguagesKeys())],
            'status' => ['required', 'in:'.implode(',', AdminStatus::all())],
        ];

        if ($type === UserType::ServiceProvider->value) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['service_id'] = ['required', 'exists:services,id'];
            $rules['city_id'] = ['required', 'exists:cities,id'];
            $rules['service_image'] = array_merge(
                [Rule::requiredIf(fn () => $this->needsServiceProviderServiceImage())],
                $imageFileRules
            );
            $rules['service_image_remove'] = ['nullable', 'boolean'];
            $rules['image'] = array_merge(
                [Rule::requiredIf(fn () => $this->needsServiceProviderPersonalImage())],
                $imageFileRules
            );
        } else {
            $rules['company_name'] = ['prohibited'];
            $rules['service_id'] = ['prohibited'];
            $rules['city_id'] = ['prohibited'];
            $rules['service_image'] = ['prohibited'];
            $rules['service_image_remove'] = ['prohibited'];
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
            'central_phone.required' => trans('admin::cruds.users.central_phone_required_when_active'),
            'central_phone.regex' => trans('admin::validation.central_phone_regex'),
        ];
    }

    /**
     * @return list<string>
     */
    private function centralPhoneRules(): array
    {
        $rules = ['string', 'max:255', 'regex:/^\+?[0-9]*$/'];

        if ($this->isUpdate() && $this->input('status') === AdminStatus::ACTIVE) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    private function needsServiceProviderPersonalImage(): bool
    {
        if ($this->input('type') !== UserType::ServiceProvider->value) {
            return false;
        }

        if (! $this->isUpdate()) {
            return true;
        }

        $user = $this->userFromRoute();
        if ($user === null) {
            return true;
        }

        if ($this->boolean('image_remove')) {
            return true;
        }

        return $user->getFirstMedia(User::MEDIA_COLLECTION) === null;
    }

    private function needsServiceProviderServiceImage(): bool
    {
        if ($this->input('type') !== UserType::ServiceProvider->value) {
            return false;
        }

        if (! $this->isUpdate()) {
            return true;
        }

        $user = $this->userFromRoute();
        if ($user === null) {
            return true;
        }

        if ($this->boolean('service_image_remove')) {
            return true;
        }

        return $user->getFirstMedia(User::SERVICE_IMAGE_MEDIA_COLLECTION) === null;
    }

    private function userFromRoute(): ?User
    {
        $id = $this->route('model');
        if ($id === null || $id === '') {
            return null;
        }

        return User::query()->find($id);
    }
}

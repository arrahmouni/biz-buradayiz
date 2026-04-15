<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Validation\Validator;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Verimor\Support\VerimorPhoneNormalizer;

class StoreProviderReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:64'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:5000'],
            'display_name' => ['required', 'string', 'max:120'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $slug = $this->route('provider');

            if (! is_string($slug) || $slug === '') {
                return;
            }

            $normalized = VerimorPhoneNormalizer::canonicalize((string) $this->input('phone'));
            if ($normalized === '') {
                $validator->errors()->add('phone', trans('platform::reviews.submission.invalid_phone'));
            }

        });
    }
}

<?php

namespace Modules\Seo\Http\Requests;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Http\Requests\BaseRequest;

class SeoRequest extends BaseRequest
{
    public function rules(): array
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());

        $rules = [
            'meta_title' => ['required', 'array'],
            'meta_description' => ['nullable', 'array'],
            'meta_keywords' => ['nullable', 'array'],
            'og_title' => ['nullable', 'array'],
            'og_description' => ['nullable', 'array'],
            'og_image' => ['nullable', 'array'],
            'robots' => ['nullable', 'array'],
            'canonical_url' => ['nullable', 'array'],
        ];

        foreach ($locales as $locale) {
            $rules['meta_title.'.$locale] = ['nullable', 'string', 'max:255'];
            $rules['meta_description.'.$locale] = ['nullable', 'string', 'max:65535'];
            $rules['meta_keywords.'.$locale] = ['nullable', 'string', 'max:512'];
            $rules['og_title.'.$locale] = ['nullable', 'string', 'max:255'];
            $rules['og_description.'.$locale] = ['nullable', 'string', 'max:65535'];
            $rules['og_image.'.$locale] = ['nullable', 'string', 'max:2048'];
            $rules['robots.'.$locale] = ['nullable', 'string', 'max:128'];
            $rules['canonical_url.'.$locale] = ['nullable', 'string', 'max:2048'];
        }

        if ($this->isCreate()) {
            $rules['page_target'] = ['required', 'string', 'max:512'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $titles = $this->input('meta_title', []);
            if (! is_array($titles)) {
                return;
            }
            $hasNonEmpty = false;
            foreach ($titles as $value) {
                if (is_string($value) && trim($value) !== '') {
                    $hasNonEmpty = true;
                    break;
                }
            }
            if (! $hasNonEmpty) {
                $validator->errors()->add('meta_title', trans('seo::validation.meta_title_required'));
            }
        });
    }
}

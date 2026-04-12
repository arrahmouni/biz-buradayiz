<?php

namespace Modules\Seo\Http\Requests;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;
use Modules\Seo\Models\Seo;
use Modules\Seo\Models\SeoStaticPage;

class SeoRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $metaTitle = $this->input('meta_title');
        if (! is_array($metaTitle)) {
            $this->merge(['meta_title' => []]);
        }
    }

    public function rules(): array
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());

        $rules = [
            'meta_title' => ['array'],
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
        $locales = array_keys(LaravelLocalization::getSupportedLocales());
        $firstLocale = $locales[0] ?? 'en';

        $validator->after(function ($validator) use ($firstLocale) {
            $titles = $this->input('meta_title', []);
            if (! is_array($titles)) {
                $validator->errors()->add('meta_title.'.$firstLocale, trans('seo::validation.meta_title_required'));

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
                $validator->errors()->add('meta_title.'.$firstLocale, trans('seo::validation.meta_title_required'));
            }
        });

        $validator->after(function ($validator) {
            if (! $this->isCreate()) {
                return;
            }

            $pageTarget = $this->input('page_target');
            if (! is_string($pageTarget) || $pageTarget === '') {
                return;
            }

            $parts = explode('|', $pageTarget, 2);
            if (count($parts) !== 2) {
                $validator->errors()->add('page_target', trans('seo::validation.invalid_page_target'));

                return;
            }

            [$class, $id] = $parts;

            if (! in_array($class, [SeoStaticPage::class, Content::class], true)) {
                $validator->errors()->add('page_target', trans('seo::validation.invalid_page_target'));

                return;
            }

            $model = $class::query()->find($id);

            if (! $model) {
                $validator->errors()->add('page_target', trans('seo::validation.page_not_found'));

                return;
            }

            if ($model instanceof Content) {
                if (! in_array($model->type, [BaseContentTypes::PAGES, BaseContentTypes::BLOGS, BaseContentTypes::FAQS], true)) {
                    $validator->errors()->add('page_target', trans('seo::validation.invalid_content_type'));

                    return;
                }
            }

            $duplicate = Seo::query()
                ->where('model_type', $model->getMorphClass())
                ->where('model_id', $model->getKey())
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('page_target', trans('seo::validation.duplicate_seo'));
            }
        });
    }
}

<div class="separator separator-dashed my-10"></div>
<div class="mb-5">
    <h4 class="mb-6">@lang('seo::fields.section_urls')</h4>
</div>
@foreach ($_ALL_LOCALE_ as $locale => $lang)
    <div class="card shadow-sm mb-5">
        <div class="card-body">
        <div class="fw-semibold mb-5">{{ $lang['native'] ?? $locale }}</div>
        <div class="row">
            <div class="col-12 mb-5 form-group">
                @include('admin::components.inputs.text', [
                    'options' => [
                        'name' => 'og_image['.$locale.']',
                        'label' => trans('seo::fields.og_image'),
                        'placeholder' => trans('seo::fields.og_image_placeholder'),
                        'subText' => trans('seo::fields.og_image_help'),
                        'required' => false,
                        'value' => isset($model) ? $model->translate($locale)?->og_image : old('og_image.'.$locale),
                    ],
                ])
            </div>
            <div class="col-12 mb-5 form-group">
                @include('admin::components.inputs.text', [
                    'options' => [
                        'name' => 'canonical_url['.$locale.']',
                        'label' => trans('seo::fields.canonical_url'),
                        'placeholder' => trans('seo::fields.canonical_url_placeholder'),
                        'subText' => trans('seo::fields.canonical_url_help'),
                        'required' => false,
                        'value' => isset($model) ? $model->translate($locale)?->canonical_url : old('canonical_url.'.$locale),
                    ],
                ])
            </div>
        </div>
        </div>
    </div>
@endforeach

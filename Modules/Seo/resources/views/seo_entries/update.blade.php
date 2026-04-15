@extends('admin::layouts.master', ['title' => trans('seo::seo_entries.edit')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('seo::seo_entries.edit'),
            'backUrl'           => route('seo.entries.index'),
            'actions'           => [
                'save'          => true,
                'back'          => true,
            ],
        ]
    ])
@endsection

@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-2"></div>
            <div class="col-xxl-8 col-12">
                <div class="card card-bordered mb-5">
                    <div class="card-header">
                        <h3 class="card-title">
                            @lang('seo::seo_entries.edit')
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'            => true,
                                    'action'            => route('seo.entries.postUpdate', ['model' => $model->id]),
                                    'method'            => 'PUT',
                                ]
                            ])
                            @slot('fields')
                                <div class="row mb-10">
                                    <div class="col-12">
                                        <label class="form-label">@lang('seo::seo_entries.current_target')</label>
                                        <div class="form-control form-control-solid">
                                            {{ $model->adminTargetLabel() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        @include('admin::components.other.lang_crud', [
                                            'options' => [
                                                'tabContentId' => 'seoLangTabContent',
                                                'tabId' => 'seo_lang_tab_',
                                                'langTabId' => 'seo_lang_pane_',
                                                'title' => [
                                                    'show' => true,
                                                    'required' => true,
                                                    'name' => 'meta_title',
                                                    'label' => trans('seo::fields.meta_title'),
                                                    'placeholder' => trans('seo::fields.meta_title_placeholder'),
                                                    'value' => function ($m, $locale) {
                                                        return $m->translate($locale)?->meta_title;
                                                    },
                                                ],
                                                // 'name' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'og_title',
                                                //     'label' => trans('seo::fields.og_title'),
                                                //     'placeholder' => trans('seo::fields.og_title_placeholder'),
                                                //     'value' => function ($m, $locale) {
                                                //         return $m->translate($locale)?->og_title;
                                                //     },
                                                // ],
                                                'description' => [
                                                    'show' => true,
                                                    'required' => false,
                                                    'name' => 'meta_description',
                                                    'label' => trans('seo::fields.meta_description'),
                                                    'placeholder' => trans('seo::fields.meta_description_placeholder'),
                                                    'value' => function ($m, $locale) {
                                                        return $m->translate($locale)?->meta_description;
                                                    },
                                                ],
                                                'short_description' => [
                                                    'show' => true,
                                                    'required' => false,
                                                    'name' => 'meta_keywords',
                                                    'label' => trans('seo::fields.meta_keywords'),
                                                    'placeholder' => trans('seo::fields.meta_keywords_placeholder'),
                                                    'value' => function ($m, $locale) {
                                                        return $m->translate($locale)?->meta_keywords;
                                                    },
                                                ],
                                                // 'long_description' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'og_description',
                                                //     'label' => trans('seo::fields.og_description'),
                                                //     'placeholder' => trans('seo::fields.og_description_placeholder'),
                                                //     'value' => function ($m, $locale) {
                                                //         return $m->translate($locale)?->og_description;
                                                //     },
                                                // ],
                                                // 'features' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'robots',
                                                //     'label' => trans('seo::fields.robots'),
                                                //     'placeholder' => trans('seo::fields.robots_placeholder'),
                                                //     'value' => function ($m, $locale) {
                                                //         return $m->translate($locale)?->robots;
                                                //     },
                                                // ],
                                            ]
                                        ])
                                        {{-- @include('seo::seo_entries.partials.og_image_canonical') --}}
                                    </div>
                                </div>
                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
@endsection

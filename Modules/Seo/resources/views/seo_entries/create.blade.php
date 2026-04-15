@extends('admin::layouts.master', ['title' => trans('seo::seo_entries.add')])

@section('toolbar')
    @include('admin::includes.toolbar', [
        'options'               => [
            'title'             => trans('seo::seo_entries.add'),
            'backUrl'           => route('seo.entries.index'),
            'createUrl'         => route('seo.entries.create'),
            'actions'           => [
                'save'              => true,
                'saveAndCreateNew'  => true,
                'back'              => true,
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
                            @lang('seo::seo_entries.add')
                        </h3>
                    </div>
                    <div class="card-body">
                        @component('admin::components.forms.form', [
                                'options'       => [
                                    'isAjax'            => true,
                                    'action'            => route('seo.entries.postCreate'),
                                ]
                            ])
                            @slot('fields')
                                <div class="row">
                                    <div class="col-12 mb-5">
                                        @include('admin::components.alerts.alert', [
                                            'options'           => [
                                                'color'         => 'primary',
                                                'description'   => trans('seo::seo_entries.create_note'),
                                            ]
                                        ])
                                    </div>
                                </div>
                                <div class="row mb-10">
                                    <div class="col-12 form-group">
                                        @include('admin::components.inputs.select', [
                                            'options' => [
                                                'name' => 'page_target',
                                                'label' => trans('seo::seo_entries.page_target'),
                                                'placeholder' => trans('seo::seo_entries.page_target_placeholder'),
                                                'help' => trans('seo::seo_entries.page_target_help'),
                                                'required' => true,
                                                'searchable' => true,
                                                'clearable' => false,
                                                'data' => collect($page_targets ?? []),
                                                'text' => function ($key, $row) {
                                                    return $row['text'];
                                                },
                                                'values' => function ($key, $row) {
                                                    return $row['id'];
                                                },
                                            ]
                                        ])
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
                                                    'value' => null,
                                                ],
                                                // 'name' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'og_title',
                                                //     'label' => trans('seo::fields.og_title'),
                                                //     'placeholder' => trans('seo::fields.og_title_placeholder'),
                                                //     'value' => null,
                                                // ],
                                                'description' => [
                                                    'show' => true,
                                                    'required' => false,
                                                    'name' => 'meta_description',
                                                    'label' => trans('seo::fields.meta_description'),
                                                    'placeholder' => trans('seo::fields.meta_description_placeholder'),
                                                    'value' => null,
                                                ],
                                                'short_description' => [
                                                    'show' => true,
                                                    'required' => false,
                                                    'name' => 'meta_keywords',
                                                    'label' => trans('seo::fields.meta_keywords'),
                                                    'placeholder' => trans('seo::fields.meta_keywords_placeholder'),
                                                    'subText' => trans('seo::fields.meta_keywords_help'),
                                                    'value' => null,
                                                ],
                                                // 'long_description' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'og_description',
                                                //     'label' => trans('seo::fields.og_description'),
                                                //     'placeholder' => trans('seo::fields.og_description_placeholder'),
                                                //     'value' => null,
                                                // ],
                                                // 'features' => [
                                                //     'show' => true,
                                                //     'required' => false,
                                                //     'name' => 'robots',
                                                //     'label' => trans('seo::fields.robots'),
                                                //     'placeholder' => trans('seo::fields.robots_placeholder'),
                                                //     'value' => null,
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

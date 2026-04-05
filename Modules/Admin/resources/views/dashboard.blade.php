@php
    use Carbon\Carbon;
    $adminUser = auth('admin')->user();
    $dashboardDisplayName = $adminUser?->full_name ?: $adminUser?->username;
@endphp

@extends('admin::layouts.master')

@section('toolbar')
    @component('admin::includes.toolbar', [
            'options'               => [
                'title'             => trans('admin::dashboard.aside_menu.home'),
                'actions'           => [
                    'filter'        => false,
                    'search'        => false,
                ],
            ]
        ])

        {{-- @slot('otherActions')
            <div class="admin-dashboard-toolbar-shell">
                @component('admin::components.forms.form', [
                        'options'               => [
                            'id'                => 'dashboard-form',
                            'class'             => 'admin-dashboard-toolbar-form',
                            'isAjax'            => false,
                            'action'            => route('admin.dashboard.index'),
                            'changeTracking'    => false,
                        ]
                    ])
                    @slot('fields')
                        <div class="admin-dashboard-toolbar-panel">
                            <div class="admin-dashboard-toolbar-filter" id="dashboard-filter-form">
                                <div class="admin-dashboard-daterange-wrap">
                                    @include('admin::components.inputs.date_range_picker', [
                                        'options'           => [
                                            'name'          => 'date_range',
                                            'startDate'     => Carbon::parse('01-01-2024')->format('Y-m-d'),
                                            'endDate'       => Carbon::now()->format('Y-m-d'),
                                            'placeholder'   => trans('admin::dashboard.page.date_range_placeholder'),
                                        ]
                                    ])
                                </div>

                                @component('admin::components.buttons.submit', [
                                    'options'               => [
                                        'label'             => trans('filter'),
                                        'class'             => 'btn-sm btn-primary admin-dashboard-filter-submit',
                                        'progress_label'    => trans('admin::strings.select2_messages.searching')
                                    ]
                                ])
                                @endcomponent
                            </div>
                        </div>
                    @endslot
                @endcomponent
            </div>
        @endslot --}}
    @endcomponent
@endsection

@section('content')
    <div class="admin-dashboard-page flex-column-fluid">
        <div id="kt_content_container" class="container-fluid admin-dashboard-container">
            <header class="admin-dashboard-hero card border-0 shadow-sm mb-8 mb-lg-10">
                <div class="card-body p-6 p-lg-8">
                    <div class="row align-items-start g-6">
                        <div class="col-lg-8">
                            <p class="admin-dashboard-hero-eyebrow fw-semibold text-uppercase mb-2">
                                {{ trans('admin::dashboard.page.statistics_heading') }}
                            </p>
                            <h1 class="admin-dashboard-hero-title fw-bold text-gray-900 mb-3">
                                {{ trans('admin::dashboard.page.welcome_line', ['name' => $dashboardDisplayName ?: trans('admin::dashboard.page.fallback_name')]) }}
                            </h1>
                            <p class="admin-dashboard-hero-lead text-gray-600 mb-0 fs-6 lh-lg">
                                {{ trans('admin::dashboard.page.overview') }}
                            </p>
                        </div>
                        <div class="col-lg-4">
                            <div class="admin-dashboard-hero-meta rounded-3 p-4 h-100">
                                <span class="admin-dashboard-hero-meta-label d-block fw-semibold text-gray-700 mb-1">
                                    {{ now()->translatedFormat('l, j F Y') }}
                                </span>
                                <span class="text-gray-500 fs-7">
                                    {{ config('app.name') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            @foreach ($statistics ?? [] as $key => $statistic)
                <section class="admin-dashboard-section mb-10 mb-lg-12" aria-labelledby="dashboard-section-{{ $key }}">
                    <div class="admin-dashboard-section-head d-flex flex-wrap align-items-center gap-3 mb-6">
                        <span class="admin-dashboard-section-accent" aria-hidden="true"></span>
                        <h2 id="dashboard-section-{{ $key }}" class="admin-dashboard-section-title fs-3 fw-bold text-gray-900 mb-0">
                            @lang('admin::cruds.'.$key.'.title')
                        </h2>
                    </div>
                    <div class="admin-dashboard-stat-grid">
                        @foreach($statistic ?? [] as $stat)
                            @empty($stat) @continue @endempty
                            @php
                                $toneIndex = $loop->index % 4;
                            @endphp
                            <a
                                href="{{ $stat['route'] }}"
                                class="admin-stat-card admin-stat-card--tone-{{ $toneIndex }} text-gray-800"
                                aria-labelledby="stat-label-{{ $stat['key'] }}"
                                title="{{ e($stat['label']) }}"
                            >
                                <span class="admin-stat-card__glow" aria-hidden="true"></span>
                                <span class="admin-stat-card__inner">
                                    <span class="admin-stat-card__icon-wrap d-flex align-items-center justify-content-center">
                                        <i class="{{ $stat['icon'] }} admin-stat-card__icon" aria-hidden="true"></i>
                                    </span>
                                    <span class="admin-stat-card__text d-flex flex-column align-items-start">
                                        <span id="stat-{{ $stat['key'] }}" class="admin-stat-card__value fw-bold lh-1">
                                            {{ $stat['count'] }}
                                        </span>
                                        <span id="stat-label-{{ $stat['key'] }}" class="admin-stat-card__label fw-semibold">
                                            {{ $stat['label'] }}
                                        </span>
                                    </span>
                                    <span class="admin-stat-card__arrow d-flex align-items-center justify-content-center" aria-hidden="true">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#dashboard-form').on('submit', function (e) {
                e.preventDefault();
                let form        = $(this);
                let url         = form.attr('action');
                let data        = form.serialize();
                let method      = form.attr('method');
                let button      = form.find('button[type="submit"]');

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    beforeSend: function () {
                        button.attr('data-kt-indicator', 'on');
                        button.attr('disabled', true);
                    },
                }).done(function(response) {
                    if (response.success && response.data.statistics) {
                        Object.values(response.data.statistics).forEach(group => {
                            group.forEach(stat => {
                                if (!stat || !stat.key) {
                                    console.warn("Invalid stat object:", stat);
                                    return;
                                }
                                let el = $('#stat-' + stat.key);
                                if (el.length) {
                                    el.text(stat.count);
                                }
                            });
                        });
                    }
                }).fail(function (response) {
                    GLOBAL.TOASTR.INIT('error');
                }).always(function () {
                    button.attr('data-kt-indicator', 'of');
                    button.attr('disabled', false);
                });
            });
        });
    </script>
@endpush

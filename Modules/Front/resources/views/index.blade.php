@extends('front::layouts.master')

@section('content')
    @include('front::home.sections.hero')
    @include('front::home.sections.how-it-works')
    @include('front::home.sections.services')
    @include('front::home.sections.cta')
@endsection


@push('script')
    <script>
        (function ($) {
            const form = document.getElementById('heroSearchForm');
            if (!form) {
                return;
            }

            const $state = $('#hero_state_id');
            const $city = $('#hero_city_id');
            const statesUrl = form.dataset.statesListUrl;
            const citiesUrl = form.dataset.citiesListUrl;
            const countryId = form.dataset.defaultCountryId;
            const locale = form.dataset.locale || 'ar';

            function apiHeaders() {
                return { locale: locale };
            }

            function mapListResponse(data, key) {
                const payload = data && data.data ? data.data : {};
                const rows = payload[key] || [];
                return $.map(rows, function (row) {
                    return { id: row.id, text: row.name };
                });
            }

            const ajaxDefaults = {
                dataType: 'json',
                delay: 250,
                cache: false,
                headers: apiHeaders(),
            };

            $state.select2({
                width: '100%',
                placeholder: $state.find('option:first').text(),
                allowClear: true,
                ajax: $.extend({}, ajaxDefaults, {
                    url: statesUrl,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            country_id: countryId,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: mapListResponse(data, 'states'),
                            pagination: { more: false },
                        };
                    },
                }),
                minimumInputLength: 0,
            });

            $city.select2({
                width: '100%',
                placeholder: $city.find('option:first').text(),
                allowClear: true,
                ajax: $.extend({}, ajaxDefaults, {
                    url: citiesUrl,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            state_id: $state.val() || '',
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: mapListResponse(data, 'cities'),
                            pagination: { more: false },
                        };
                    },
                }),
                minimumInputLength: 0,
            });

            function syncCityEnabled() {
                const hasState = Boolean($state.val());
                $city.prop('disabled', !hasState);
            }

            $state.on('change', function () {
                $city.val(null).trigger('change');
                syncCityEnabled();
            });

            syncCityEnabled();
        })(jQuery);
    </script>
@endpush

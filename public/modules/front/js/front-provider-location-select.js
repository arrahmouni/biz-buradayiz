(function ($) {
    function mapListResponse(data, key) {
        const payload = data && data.data ? data.data : {};
        const rows = payload[key] || [];
        return $.map(rows, function (row) {
            return { id: row.id, text: row.name };
        });
    }

    function initProviderLocationSearchRoot(root) {
        const $root = $(root);
        const $state = $root.find('.js-pls-state');
        const $city = $root.find('.js-pls-city');
        if (!$state.length || !$city.length) {
            return;
        }

        const statesUrl = root.dataset.statesListUrl;
        const citiesUrl = root.dataset.citiesListUrl;
        const countryId = root.dataset.defaultCountryId;
        const locale = root.dataset.locale || document.documentElement.lang || 'en';
        const selectedStateId = root.dataset.selectedStateId || '';
        const selectedStateName = root.dataset.selectedStateName || '';
        const selectedCityId = root.dataset.selectedCityId || '';
        const selectedCityName = root.dataset.selectedCityName || '';

        function apiHeaders() {
            return { locale: locale };
        }

        const ajaxDefaults = {
            dataType: 'json',
            delay: 250,
            cache: false,
            headers: apiHeaders(),
        };

        const $authCard = $root.closest('.front-auth-card-panel');
        const baseSelect2 = {
            width: '100%',
            allowClear: true,
        };
        if ($authCard.length) {
            baseSelect2.dropdownParent = $(document.body);
        }

        $state.select2($.extend({}, baseSelect2, {
            placeholder: $state.find('option:first').text(),
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
        }));

        $city.select2($.extend({}, baseSelect2, {
            placeholder: $city.find('option:first').text(),
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
        }));

        function syncCityEnabled() {
            const hasState = Boolean($state.val());
            $city.prop('disabled', !hasState);
            if (!$city.hasClass('select2-hidden-accessible')) {
                return;
            }
            try {
                if (hasState) {
                    $city.select2('enable');
                } else {
                    $city.select2('disable');
                }
            } catch (e) {
                /* select2 build without enable/disable */
            }
        }

        $state.on('change', function () {
            $city.val(null).trigger('change');
            syncCityEnabled();
        });

        if (selectedStateId && selectedStateName) {
            const stateOption = new Option(selectedStateName, selectedStateId, true, true);
            $state.append(stateOption).trigger('change');
        }

        syncCityEnabled();

        if (selectedCityId && selectedCityName && selectedStateId) {
            const cityOption = new Option(selectedCityName, selectedCityId, true, true);
            $city.append(cityOption).trigger('change');
        }
    }

    function run() {
        $('.js-provider-location-search').each(function () {
            initProviderLocationSearchRoot(this);
        });
    }

    if (typeof $ === 'undefined' || !$.fn || !$.fn.select2) {
        return;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})(window.jQuery);

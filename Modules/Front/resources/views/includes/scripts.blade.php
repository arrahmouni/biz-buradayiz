<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/page-loader.js') }}"></script>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenuWrapper = document.getElementById('mobileMenuWrapper');

    function setMobileMenuOpen(open) {
        if (!menuBtn || !mobileMenuWrapper) {
            return;
        }
        mobileMenuWrapper.classList.toggle('is-open', open);
        menuBtn.classList.toggle('is-open', open);
        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (menuBtn && mobileMenuWrapper) {
        menuBtn.addEventListener('click', () => {
            const next = !mobileMenuWrapper.classList.contains('is-open');
            setMobileMenuOpen(next);
        });
    }

    const langDropdown = document.getElementById('langDropdown');
    const servicesNavDropdown = document.getElementById('servicesNavDropdown');

    function closeDetailsIfOpen(detailsEl) {
        if (detailsEl && detailsEl.open) {
            detailsEl.removeAttribute('open');
        }
    }

    if (langDropdown && servicesNavDropdown) {
        langDropdown.addEventListener('toggle', () => {
            if (langDropdown.open) {
                closeDetailsIfOpen(servicesNavDropdown);
            }
        });
        servicesNavDropdown.addEventListener('toggle', () => {
            if (servicesNavDropdown.open) {
                closeDetailsIfOpen(langDropdown);
            }
        });
    }

    if (langDropdown) {
        document.addEventListener('click', (e) => {
            if (!langDropdown.open) {
                return;
            }
            if (!langDropdown.contains(e.target)) {
                langDropdown.removeAttribute('open');
            }
        });
    }

    if (servicesNavDropdown) {
        document.addEventListener('click', (e) => {
            if (!servicesNavDropdown.open) {
                return;
            }
            if (!servicesNavDropdown.contains(e.target)) {
                servicesNavDropdown.removeAttribute('open');
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') {
            return;
        }
        if (menuBtn && mobileMenuWrapper && mobileMenuWrapper.classList.contains('is-open')) {
            setMobileMenuOpen(false);
            return;
        }
        closeDetailsIfOpen(langDropdown);
        closeDetailsIfOpen(servicesNavDropdown);
    });

    document.querySelectorAll('a[href*="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const hrefAttr = this.getAttribute('href');
            if (!hrefAttr || hrefAttr === '#') {
                return;
            }

            let url;
            try {
                url = new URL(this.href);
            } catch {
                return;
            }

            if (url.origin !== window.location.origin) {
                return;
            }

            const hash = url.hash;
            if (!hash || hash === '#') {
                return;
            }

            if (url.pathname !== window.location.pathname || url.search !== window.location.search) {
                return;
            }

            const target = document.querySelector(hash);
            if (!target) {
                return;
            }

            e.preventDefault();
            const smoothScroll = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            target.scrollIntoView({ behavior: smoothScroll ? 'smooth' : 'auto' });
            history.pushState(null, '', hash);
            if (mobileMenuWrapper && mobileMenuWrapper.classList.contains('is-open')) {
                setMobileMenuOpen(false);
            }
        });
    });
</script>

<script>
    (function ($) {
        function mapListResponse(data, key) {
            const payload = data && data.data ? data.data : {};
            const rows = payload[key] || [];
            return $.map(rows, function (row) {
                return { id: row.id, text: row.name };
            });
        }

        function initProviderLocationSearchForm(form) {
            const $form = $(form);
            const $state = $form.find('.js-pls-state');
            const $city = $form.find('.js-pls-city');
            if (!$state.length || !$city.length) {
                return;
            }

            const statesUrl = form.dataset.statesListUrl;
            const citiesUrl = form.dataset.citiesListUrl;
            const countryId = form.dataset.defaultCountryId;
            const locale = form.dataset.locale || document.documentElement.lang || 'en';
            const selectedStateId = form.dataset.selectedStateId || '';
            const selectedStateName = form.dataset.selectedStateName || '';
            const selectedCityId = form.dataset.selectedCityId || '';
            const selectedCityName = form.dataset.selectedCityName || '';

            function apiHeaders() {
                return { locale: locale };
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

        $('form.js-provider-location-search').each(function () {
            initProviderLocationSearchForm(this);
        });
    })(jQuery);
</script>

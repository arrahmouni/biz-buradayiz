<div id="pageLoader" class="page-loader" aria-busy="true" aria-live="polite">
    <div class="page-loader__stage">
        <div class="page-loader__speed-track" aria-hidden="true">
            <span class="page-loader__speed-line"></span>
            <span class="page-loader__speed-line"></span>
            <span class="page-loader__speed-line"></span>
            <span class="page-loader__speed-line"></span>
            <span class="page-loader__speed-line"></span>
        </div>
        <div class="page-loader__logo-row">
            <div class="page-loader__logo-anchor" data-page-loader-logo-anchor>
                <img
                    src="{{ getSetting('loader_logo', asset('images/default/logos/loader_logo.svg')) }}"
                    alt="{{ __('front::home.brand') }}"
                    class="page-loader__logo"
                >
            </div>
        </div>
        <div class="page-loader__progress" aria-hidden="true">
            <div class="page-loader__progress-fill"></div>
        </div>
    </div>
    <span class="page-loader__status">{{ __('front::home.page_loading_status') }}</span>
</div>

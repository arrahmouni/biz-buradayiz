@if (isStaging())
    <div class="app-staging-environment-banner" role="status" aria-live="polite">
        <div class="app-staging-environment-banner__inner">
            <span class="app-staging-environment-banner__label">{{ __('staging.banner_label') }}</span>
            <span class="app-staging-environment-banner__sep" aria-hidden="true"></span>
            <span class="app-staging-environment-banner__text">{{ __('staging.banner_message', ['app' => config('app.name')]) }}</span>
        </div>
    </div>
@endif
